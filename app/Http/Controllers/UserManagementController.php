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
        return view('users.index', ['users' => User::with('location')->latest()->paginate(20), 'locations' => Location::where('is_active', true)->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
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
        abort_if($user->id === $request->user()->id && ($data['role'] ?? null) !== 'super_admin', 422, 'Anda tidak dapat menurunkan role akun sendiri.');
        $old = $user->only(['name', 'email', 'role', 'location_id', 'is_active']);
        $user->update($data);
        AuditLog::record('user.updated', $user, $old, $user->only(array_keys($old)));

        return back()->with('success', 'Akun pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 422, 'Akun sendiri tidak dapat dinonaktifkan.');
        $user->update(['is_active' => false]);
        AuditLog::record('user.deactivated', $user);

        return back()->with('success', 'Akun pengguna dinonaktifkan.');
    }

    public function matrix()
    {
        return view('users.rbac', ['roles' => config('rbac.roles'), 'permissions' => config('rbac.permissions')]);
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(array_keys(config('rbac.roles')))],
            'phone' => ['nullable', 'string', 'max:30'], 'location_id' => ['nullable', 'exists:locations,id'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
