@php
    $editing = $transaction->exists;
    $typeLabels = ['cash_in' => 'Kas masuk', 'cashier_sale' => 'Kasir transaksi', 'cash_out' => 'Kas keluar'];
@endphp
<x-layouts.app :title="$editing ? 'Edit Transaksi Keuangan' : 'Tambah Transaksi Keuangan'" subtitle="Pencatatan kas masuk, transaksi kasir, dan kas keluar">
    <form method="POST" action="{{ $editing ? route('finance.update', $transaction) : route('finance.store') }}" class="card mx-auto max-w-5xl p-5 sm:p-7">
        @csrf @if($editing) @method('PUT') @endif
        <div class="grid gap-5 md:grid-cols-2">
            <div><label class="label">Tanggal transaksi</label><input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}" required class="field"></div>
            <div><label class="label">Tipe transaksi</label><select name="type" required class="field">@foreach($typeLabels as $value => $label)<option value="{{ $value }}" @selected(old('type', $transaction->type) === $value)>{{ $label }}</option>@endforeach</select></div>
            <div><label class="label">Lokasi</label><select name="location_id" class="field"><option value="">Semua lokasi</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string) old('location_id', $transaction->location_id) === (string) $location->id)>{{ $location->name }}</option>@endforeach</select></div>
            <div><label class="label">Status</label><select name="status" required class="field">@foreach(['draft' => 'Draft', 'validated' => 'Tervalidasi', 'void' => 'Dibatalkan'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $transaction->status ?? 'draft') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div><label class="label">Kategori</label><input name="category" value="{{ old('category', $transaction->category) }}" class="field" placeholder="Contoh: Penjualan, operasional, perawatan"></div>
            <div><label class="label">Nomor referensi</label><input name="reference_no" value="{{ old('reference_no', $transaction->reference_no) }}" class="field" placeholder="INV/KWT/2026/001"></div>
            <div class="md:col-span-2"><label class="label">Deskripsi</label><input name="description" value="{{ old('description', $transaction->description) }}" required class="field" placeholder="Contoh: Penjualan maggot basah"></div>
            <div><label class="label">Nominal</label><input type="number" name="amount" min="0" step="0.01" value="{{ old('amount', $transaction->amount ?? 0) }}" required class="field"></div>
            <div><label class="label">Metode pembayaran</label><input name="payment_method" value="{{ old('payment_method', $transaction->payment_method) }}" class="field" placeholder="Tunai, transfer, QRIS"></div>
            <div><label class="label">Pihak terkait</label><input name="counterparty" value="{{ old('counterparty', $transaction->counterparty) }}" class="field" placeholder="Pembeli, pemasok, penerima"></div>
            <div class="md:col-span-2"><label class="label">Catatan</label><textarea name="notes" rows="3" class="field">{{ old('notes', $transaction->notes) }}</textarea></div>
        </div>
        <div class="mt-7 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end"><a href="{{ route('finance.index') }}" class="btn-secondary">Batal</a><button class="btn-primary"><x-heroicon-o-check class="size-5" /> Simpan transaksi</button></div>
    </form>
</x-layouts.app>
