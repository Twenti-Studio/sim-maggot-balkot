<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Location;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::with(['location', 'user'])->latest();
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->q.'%')->orWhere('employee_code', 'like', '%'.$request->q.'%'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('staff.index', ['staffMembers' => $query->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        return view('staff.form', ['staff' => new Staff, 'locations' => Location::where('is_active', true)->get(), 'users' => User::whereDoesntHave('staff')->where('is_active', true)->get()]);
    }

    public function store(Request $request)
    {
        $staff = Staff::create($this->validated($request));
        AuditLog::record('staff.created', $staff);

        return redirect()->route('staff.index')->with('success', 'Data petugas berhasil ditambahkan.');
    }

    public function edit(Staff $staff)
    {
        return view('staff.form', ['staff' => $staff, 'locations' => Location::where('is_active', true)->get(), 'users' => User::where(fn ($q) => $q->whereDoesntHave('staff')->orWhere('id', $staff->user_id))->where('is_active', true)->get()]);
    }

    public function update(Request $request, Staff $staff)
    {
        $old = $staff->toArray();
        $staff->update($this->validated($request, $staff));
        AuditLog::record('staff.updated', $staff, $old, $staff->fresh()->toArray());

        return redirect()->route('staff.index')->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy(Staff $staff)
    {
        $staff->update(['status' => 'inactive']);
        $staff->delete();
        AuditLog::record('staff.archived', $staff);

        return back()->with('success', 'Petugas dinonaktifkan dan diarsipkan.');
    }

    private function validated(Request $request, ?Staff $staff = null): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('staff', 'user_id')->ignore($staff?->id)],
            'location_id' => ['required', 'exists:locations,id'],
            'employee_code' => ['required', 'string', 'max:50', Rule::unique('staff')->ignore($staff?->id)],
            'name' => ['required', 'string', 'max:255'], 'position' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'], 'joined_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])], 'notes' => ['nullable', 'string'],
        ]);
    }
}
