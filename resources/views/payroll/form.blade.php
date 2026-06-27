@php($editing = $payroll->exists)
<x-layouts.app :title="$editing ? 'Edit Gaji & Bonus' : 'Tambah Gaji & Bonus'" subtitle="Gaji pokok, bonus, potongan, dan status pembayaran">
    <form method="POST" action="{{ $editing ? route('payroll.update', $payroll) : route('payroll.store') }}" class="card mx-auto max-w-4xl p-5 sm:p-7">
        @csrf @if($editing) @method('PUT') @endif
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="label">Petugas</label>
                <select name="staff_id" required class="field">
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}" @selected((string) old('staff_id', $payroll->staff_id) === (string) $staff->id)>{{ $staff->employee_code }} · {{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Status</label>
                <select name="status" required class="field">
                    @foreach(['draft' => 'Draft', 'paid' => 'Dibayar', 'cancelled' => 'Dibatalkan'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $payroll->status ?? 'draft') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="label">Periode mulai</label><input type="date" name="period_start" value="{{ old('period_start', $payroll->period_start?->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d')) }}" required class="field"></div>
            <div><label class="label">Periode selesai</label><input type="date" name="period_end" value="{{ old('period_end', $payroll->period_end?->format('Y-m-d') ?? now()->endOfMonth()->format('Y-m-d')) }}" required class="field"></div>
            <div><label class="label">Gaji pokok</label><input type="number" name="base_salary" min="0" step="0.01" value="{{ old('base_salary', $payroll->base_salary ?? 0) }}" required class="field"></div>
            <div><label class="label">Bonus</label><input type="number" name="bonus" min="0" step="0.01" value="{{ old('bonus', $payroll->bonus ?? 0) }}" required class="field"></div>
            <div><label class="label">Potongan</label><input type="number" name="deductions" min="0" step="0.01" value="{{ old('deductions', $payroll->deductions ?? 0) }}" required class="field"></div>
            <div><label class="label">Tanggal bayar</label><input type="date" name="paid_at" value="{{ old('paid_at', $payroll->paid_at?->format('Y-m-d')) }}" class="field"></div>
            <div class="md:col-span-2"><label class="label">Catatan</label><textarea name="notes" rows="3" class="field">{{ old('notes', $payroll->notes) }}</textarea></div>
        </div>
        <div class="mt-7 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end"><a href="{{ route('payroll.index') }}" class="btn-secondary">Batal</a><button class="btn-primary"><x-heroicon-o-check class="size-5" /> Simpan gaji</button></div>
    </form>
</x-layouts.app>
