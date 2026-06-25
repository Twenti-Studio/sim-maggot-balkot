<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('staff')->latest('attendance_date');
        if ($request->user()->role === 'operator') {
            abort_unless($request->user()->staff, 403, 'Akun belum ditautkan ke data petugas.');
            $query->where('staff_id', $request->user()->staff->id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date('date_to'));
        }

        return view('attendance.index', [
            'attendances' => $query->paginate(20)->withQueryString(),
            'todayAttendance' => $request->user()->staff?->attendances()->whereDate('attendance_date', today())->first(),
        ]);
    }

    public function checkIn(Request $request)
    {
        abort_unless($request->user()->staff, 422, 'Akun belum ditautkan ke data petugas.');
        $attendance = Attendance::firstOrCreate(
            ['staff_id' => $request->user()->staff->id, 'attendance_date' => today()],
            ['created_by' => $request->user()->id, 'status' => 'present']
        );
        abort_if($attendance->check_in_at, 422, 'Anda sudah melakukan check-in hari ini.');
        $attendance->update(['check_in_at' => now()]);
        AuditLog::record('attendance.checked_in', $attendance);

        return back()->with('success', 'Check-in berhasil dicatat.');
    }

    public function checkOut(Request $request)
    {
        abort_unless($request->user()->staff, 422);
        $attendance = Attendance::where('staff_id', $request->user()->staff->id)->whereDate('attendance_date', today())->firstOrFail();
        abort_unless($attendance->check_in_at && ! $attendance->check_out_at, 422, 'Check-in belum ada atau check-out sudah dilakukan.');
        $attendance->update(['check_out_at' => now()]);
        AuditLog::record('attendance.checked_out', $attendance);

        return back()->with('success', 'Check-out berhasil dicatat.');
    }
}
