@php
    $typeLabels = ['cash_in' => 'Kas masuk', 'cashier_sale' => 'Kasir transaksi', 'cash_out' => 'Kas keluar'];
@endphp
<x-layouts.app title="Manajemen Keuangan" subtitle="Kas masuk, kasir transaksi, kas keluar, dan laba rugi">
    <div class="grid gap-4 md:grid-cols-3">
        <article class="card p-4"><div class="grid size-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><x-heroicon-o-arrow-trending-up class="size-5" /></div><p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Kas masuk tervalidasi</p><p class="mt-1 text-2xl font-bold text-slate-900">Rp{{ number_format($summary['income'], 0, ',', '.') }}</p></article>
        <article class="card p-4"><div class="grid size-10 place-items-center rounded-xl bg-red-50 text-red-700"><x-heroicon-o-arrow-trending-down class="size-5" /></div><p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Kas keluar tervalidasi</p><p class="mt-1 text-2xl font-bold text-slate-900">Rp{{ number_format($summary['expense'], 0, ',', '.') }}</p></article>
        <article class="card p-4"><div class="grid size-10 place-items-center rounded-xl bg-sky-50 text-sky-700"><x-heroicon-o-scale class="size-5" /></div><p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Laba rugi</p><p class="mt-1 text-2xl font-bold {{ $summary['profit'] < 0 ? 'text-red-700' : 'text-slate-900' }}">Rp{{ number_format($summary['profit'], 0, ',', '.') }}</p></article>
    </div>

    <div class="my-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="grid flex-1 gap-2 sm:grid-cols-2 lg:grid-cols-6">
            <select name="type" class="field"><option value="">Semua tipe</option>@foreach($typeLabels as $value => $label)<option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>@endforeach</select>
            <select name="status" class="field"><option value="">Semua status</option><option value="draft" @selected(request('status') === 'draft')>Draft</option><option value="validated" @selected(request('status') === 'validated')>Tervalidasi</option><option value="void" @selected(request('status') === 'void')>Dibatalkan</option></select>
            <select name="location_id" class="field"><option value="">Semua lokasi</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string) request('location_id') === (string) $location->id)>{{ $location->name }}</option>@endforeach</select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="field">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="field">
            <button class="btn-secondary">Filter</button>
        </form>
        @if(auth()->user()->hasPermission('finance.create'))
            <a href="{{ route('finance.create') }}" class="btn-primary"><x-heroicon-o-plus class="size-4" /> Tambah transaksi</a>
        @endif
    </div>

    @if($transactions->count())
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Tanggal</th><th>Tipe</th><th>Deskripsi</th><th>Metode</th><th>Nominal</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date->format('d/m/Y') }}<p class="text-xs text-slate-500">{{ $transaction->reference_no ?: 'Tanpa referensi' }}</p></td>
                            <td><p class="font-semibold text-slate-900">{{ $typeLabels[$transaction->type] ?? $transaction->type }}</p><p class="text-xs text-slate-500">{{ $transaction->category ?: 'Umum' }}</p></td>
                            <td><p class="font-medium text-slate-800">{{ $transaction->description }}</p><p class="text-xs text-slate-500">{{ $transaction->location?->name ?: 'Semua lokasi' }}{{ $transaction->counterparty ? ' · '.$transaction->counterparty : '' }}</p></td>
                            <td>{{ $transaction->payment_method ?: '—' }}</td>
                            <td class="font-semibold {{ $transaction->type === 'cash_out' ? 'text-red-700' : 'text-emerald-700' }}">{{ $transaction->type === 'cash_out' ? '-' : '+' }}Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td><x-status-badge :status="$transaction->status" /></td>
                            <td><div class="flex justify-end gap-1">
                                @if(auth()->user()->hasPermission('finance.validate') && $transaction->status !== 'validated')<form method="POST" action="{{ route('finance.validate', $transaction) }}">@csrf<button class="grid size-10 place-items-center rounded-xl text-emerald-600 hover:bg-emerald-50" title="Validasi"><x-heroicon-o-check-badge class="size-5" /></button></form>@endif
                                @if(auth()->user()->hasPermission('finance.update'))<a href="{{ route('finance.edit', $transaction) }}" class="grid size-10 place-items-center rounded-xl text-slate-500 hover:bg-stone-100" title="Edit"><x-heroicon-o-pencil-square class="size-5" /></a><form method="POST" action="{{ route('finance.destroy', $transaction) }}" onsubmit="return confirm('Arsipkan transaksi ini?')">@csrf @method('DELETE')<button class="grid size-10 place-items-center rounded-xl text-red-500 hover:bg-red-50" title="Arsipkan"><x-heroicon-o-archive-box class="size-5" /></button></form>@endif
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $transactions->links() }}</div>
    @else
        <x-empty-state title="Belum ada transaksi" description="Catat kas masuk, penjualan kasir, dan kas keluar untuk menghitung laba rugi.">@if(auth()->user()->hasPermission('finance.create'))<a href="{{ route('finance.create') }}" class="btn-primary mt-5">Tambah transaksi</a>@endif</x-empty-state>
    @endif
</x-layouts.app>
