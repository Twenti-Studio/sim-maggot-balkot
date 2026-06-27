<x-layouts.app title="Riwayat Tabungan" subtitle="Setoran sampah dan nilai saldo yang masuk">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <article class="card min-w-64 p-4"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total masuk</p><p class="mt-1 text-2xl font-bold text-slate-900">Rp{{ number_format($totalDeposits, 0, ',', '.') }}</p></article>
        <a href="{{ route('savings.index') }}" class="btn-secondary"><x-heroicon-o-arrow-left class="size-4" /> Tabungan Saya</a>
    </div>

    @if($deposits->count())
        <div class="table-wrap"><table class="data-table"><thead><tr><th>Tanggal</th><th>Kategori</th><th>Jenis</th><th>Berat</th><th>Harga/kg</th><th>Nilai tabungan</th><th>Petugas</th></tr></thead><tbody>
            @foreach($deposits as $deposit)
                <tr><td>{{ $deposit->deposit_date->format('d/m/Y') }}</td><td>{{ $deposit->wasteType->category }}</td><td>{{ $deposit->wasteType->name }}</td><td>{{ number_format($deposit->weight, 2, ',', '.') }} kg</td><td>Rp{{ number_format($deposit->price_per_unit, 0, ',', '.') }}</td><td class="font-semibold text-emerald-700">Rp{{ number_format($deposit->total_amount, 0, ',', '.') }}</td><td>{{ $deposit->creator->name }}</td></tr>
            @endforeach
        </tbody></table></div><div class="mt-5">{{ $deposits->links() }}</div>
    @else
        <x-empty-state title="Belum ada tabungan" description="Saldo akan muncul setelah petugas menimbang dan mencatat setoran sampah Anda." />
    @endif
</x-layouts.app>
