<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AuditLog;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private const ATTENDANCE_TIMEZONE = 'Asia/Makassar';

    public function index(Request $request)
    {
        $today = $this->localNow()->toDateString();
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
            'todayAttendance' => $request->user()->staff?->attendances()->whereDate('attendance_date', $today)->first(),
            'attendanceTimezone' => self::ATTENDANCE_TIMEZONE,
            'localNow' => $this->localNow(),
        ]);
    }

    public function checkIn(Request $request)
    {
        abort_unless($request->user()->staff, 422, 'Akun belum ditautkan ke data petugas.');
        $now = $this->localNow();
        $attendance = Attendance::firstOrCreate(
            ['staff_id' => $request->user()->staff->id, 'attendance_date' => $now->toDateString()],
            ['created_by' => $request->user()->id, 'status' => 'present']
        );
        abort_if($attendance->check_in_at, 422, 'Anda sudah mencatat jam masuk hari ini.');
        $attendance->update(['check_in_at' => $now]);
        AuditLog::record('attendance.checked_in', $attendance);

        return back()->with('success', 'Jam masuk berhasil dicatat.');
    }

    public function checkOut(Request $request)
    {
        abort_unless($request->user()->staff, 422);
        $now = $this->localNow();
        $attendance = Attendance::where('staff_id', $request->user()->staff->id)->whereDate('attendance_date', $now->toDateString())->firstOrFail();
        abort_unless($attendance->check_in_at && ! $attendance->check_out_at, 422, 'Jam masuk belum ada atau jam pulang sudah dicatat.');
        $attendance->update(['check_out_at' => $now]);
        AuditLog::record('attendance.checked_out', $attendance);

        return back()->with('success', 'Jam pulang berhasil dicatat.');
    }

    private function localNow(): CarbonImmutable
    {
        return CarbonImmutable::now(self::ATTENDANCE_TIMEZONE);
    }
}
