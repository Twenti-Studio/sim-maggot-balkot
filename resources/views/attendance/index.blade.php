<x-layouts.app title="Absensi" subtitle="Kehadiran dan jam kerja petugas">
    @if(auth()->user()->hasPermission('attendance.create') && auth()->user()->staff)
        <section class="mb-6 card overflow-hidden">
            <div class="grid gap-5 bg-gradient-to-r from-brand-800 to-emerald-700 p-5 text-white sm:grid-cols-[1fr_auto] sm:items-center sm:p-6">
                <div><p class="text-sm font-medium text-emerald-100">{{ today()->translatedFormat('l, d F Y') }}</p><h2 class="mt-1 text-2xl font-bold">Halo, {{ auth()->user()->staff->name }}</h2><p class="mt-1 text-sm text-emerald-100">Catat waktu kerja langsung dari perangkat ini.</p></div>
                <div class="flex gap-2">
                    @if(!$todayAttendance?->check_in_at)<form method="POST" action="{{ route('attendance.check-in') }}">@csrf<button class="btn bg-white text-brand-800 hover:bg-emerald-50"><x-heroicon-o-arrow-right-end-on-rectangle class="size-5" /> Check-in</button></form>
                    @elseif(!$todayAttendance?->check_out_at)<form method="POST" action="{{ route('attendance.check-out') }}">@csrf<button class="btn bg-white text-brand-800 hover:bg-emerald-50"><x-heroicon-o-arrow-left-start-on-rectangle class="size-5" /> Check-out</button></form>
                    @else<span class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-3 text-sm font-semibold"><x-heroicon-o-check-circle class="size-5" /> Absensi selesai</span>@endif
                </div>
            </div>
            @if($todayAttendance)<div class="grid grid-cols-2 divide-x divide-stone-200 p-4 text-center"><div><p class="text-xs text-slate-500">Check-in</p><p class="mt-1 text-lg font-bold text-slate-900">{{ $todayAttendance->check_in_at?->format('H:i') ?? '—' }}</p></div><div><p class="text-xs text-slate-500">Check-out</p><p class="mt-1 text-lg font-bold text-slate-900">{{ $todayAttendance->check_out_at?->format('H:i') ?? '—' }}</p></div></div>@endif
        </section>
    @endif

    <form method="GET" class="mb-5 flex flex-wrap gap-2"><input type="date" name="date_from" value="{{ request('date_from') }}" class="field max-w-44"><input type="date" name="date_to" value="{{ request('date_to') }}" class="field max-w-44"><button class="btn-secondary"><x-heroicon-o-funnel class="size-4" /> Filter</button></form>
    @if($attendances->count())
        <div class="table-wrap"><table class="data-table"><thead><tr><th>Tanggal</th><th>Petugas</th><th>Check-in</th><th>Check-out</th><th>Durasi</th><th>Status</th></tr></thead><tbody>
            @foreach($attendances as $attendance)
                <tr><td class="font-medium">{{ $attendance->attendance_date->translatedFormat('d M Y') }}</td><td><p class="font-semibold text-slate-900">{{ $attendance->staff->name }}</p><p class="text-xs text-slate-500">{{ $attendance->staff->employee_code }}</p></td><td>{{ $attendance->check_in_at?->format('H:i') ?? '—' }}</td><td>{{ $attendance->check_out_at?->format('H:i') ?? '—' }}</td><td>{{ $attendance->check_in_at && $attendance->check_out_at ? $attendance->check_in_at->diff($attendance->check_out_at)->format('%Hj %Im') : '—' }}</td><td><x-status-badge :status="$attendance->status" /></td></tr>
            @endforeach
        </tbody></table></div><div class="mt-5">{{ $attendances->links() }}</div>
    @else
        <x-empty-state title="Belum ada absensi" description="Riwayat check-in dan check-out akan tampil di sini." />
    @endif
</x-layouts.app>
