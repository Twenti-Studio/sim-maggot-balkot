<x-layouts.app title="Gaji & Bonus" subtitle="Manajemen pembayaran petugas">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-1 flex-wrap gap-2">
            <select name="staff_id" class="field max-w-56">
                <option value="">Semua petugas</option>
                @foreach($staffMembers as $staff)
                    <option value="{{ $staff->id }}" @selected((string) request('staff_id') === (string) $staff->id)>{{ $staff->name }}</option>
                @endforeach
            </select>
            <select name="status" class="field max-w-44">
                <option value="">Semua status</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="paid" @selected(request('status') === 'paid')>Dibayar</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
            </select>
            <button class="btn-secondary">Filter</button>
        </form>
        <a href="{{ route('payroll.create') }}" class="btn-primary"><x-heroicon-o-plus class="size-4" /> Tambah gaji</a>
    </div>

    @if($payrollRecords->count())
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Petugas</th><th>Periode</th><th>Gaji pokok</th><th>Bonus</th><th>Potongan</th><th>Total</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @foreach($payrollRecords as $payroll)
                        <tr>
                            <td><p class="font-semibold text-slate-900">{{ $payroll->staff->name }}</p><p class="text-xs text-slate-500">{{ $payroll->staff->employee_code }} · {{ $payroll->staff->location->name }}</p></td>
                            <td>{{ $payroll->period_start->format('d/m/Y') }} - {{ $payroll->period_end->format('d/m/Y') }}<p class="text-xs text-slate-500">{{ $payroll->paid_at ? 'Dibayar '.$payroll->paid_at->format('d/m/Y') : 'Belum ada tanggal bayar' }}</p></td>
                            <td>Rp{{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($payroll->bonus, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($payroll->deductions, 0, ',', '.') }}</td>
                            <td class="font-semibold text-slate-900">Rp{{ number_format($payroll->total_paid, 0, ',', '.') }}</td>
                            <td><x-status-badge :status="$payroll->status" /></td>
                            <td><div class="flex justify-end gap-1"><a href="{{ route('payroll.edit', $payroll) }}" class="grid size-10 place-items-center rounded-xl text-slate-500 hover:bg-stone-100" title="Edit"><x-heroicon-o-pencil-square class="size-5" /></a><form method="POST" action="{{ route('payroll.destroy', $payroll) }}" onsubmit="return confirm('Arsipkan data gaji ini?')">@csrf @method('DELETE')<button class="grid size-10 place-items-center rounded-xl text-red-500 hover:bg-red-50" title="Arsipkan"><x-heroicon-o-archive-box class="size-5" /></button></form></div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $payrollRecords->links() }}</div>
    @else
        <x-empty-state title="Belum ada data gaji" description="Catat gaji pokok, bonus, potongan, dan status pembayaran petugas."><a href="{{ route('payroll.create') }}" class="btn-primary mt-5">Tambah gaji</a></x-empty-state>
    @endif
</x-layouts.app>
