<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $roleOptions = config('rbac.roles');
        if (! request()->user()->isSuperAdmin()) {
            unset($roleOptions['super_admin']);
        }

        return view('users.index', [
            'users' => User::with('location')->latest()->paginate(20),
            'locations' => Location::where('is_active', true)->get(),
            'roleOptions' => $roleOptions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->ensureRoleCanBeManaged($request, $data['role']);
        $this->ensureSingleSuperAdmin($data['role']);
        $user = User::create($data);
        AuditLog::record('user.created', $user, [], $user->only(['name', 'email', 'role', 'location_id', 'is_active']));

        return back()->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        $this->ensureRoleCanBeManaged($request, $data['role'], $user);
        abort_if($user->id === $request->user()->id && ($data['role'] ?? null) !== 'super_admin', 422, 'Anda tidak dapat menurunkan jenis pengguna akun sendiri.');
        $this->ensureSingleSuperAdmin($data['role'], $user);
        $old = $user->only(['name', 'email', 'role', 'location_id', 'is_active']);
        $user->update($data);
        AuditLog::record('user.updated', $user, $old, $user->only(array_keys($old)));

        return back()->with('success', 'Akun pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 422, 'Akun sendiri tidak dapat dinonaktifkan.');
        $this->ensureRoleCanBeManaged($request, $user->role, $user);
        $user->update(['is_active' => false]);
        AuditLog::record('user.deactivated', $user);

        return back()->with('success', 'Akun pengguna dinonaktifkan.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(array_keys(config('rbac.roles')))],
            'phone' => ['nullable', 'string', 'max:30'], 'location_id' => ['nullable', 'exists:locations,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($data['role'] === 'user') {
            $request->validate(['location_id' => ['required', 'exists:locations,id']]);
        }

        return $data;
    }

    private function ensureSingleSuperAdmin(string $role, ?User $user = null): void
    {
        if ($role !== 'super_admin') {
            return;
        }

        $exists = User::where('role', 'super_admin')
            ->when($user, fn ($query) => $query->whereKeyNot($user->id))
            ->exists();

        abort_if($exists, 422, 'Super Admin hanya boleh satu akun.');
    }

    private function ensureRoleCanBeManaged(Request $request, string $role, ?User $targetUser = null): void
    {
        if ($request->user()->isSuperAdmin()) {
            return;
        }

        abort_if($role === 'super_admin' || $targetUser?->role === 'super_admin', 403, 'Hanya Super Admin yang dapat mengelola akun Super Admin.');
    }
}
