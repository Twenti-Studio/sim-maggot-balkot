<x-layouts.app title="Setor Sampah" subtitle="Input dan riwayat setoran sampah user">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div><h2 class="font-bold text-slate-900">Riwayat setoran</h2><p class="mt-1 text-sm text-slate-500">Setoran terbaru yang dicatat petugas.</p></div>
        @if(auth()->user()->hasPermission('waste.deposit.create'))<a href="{{ route('waste-deposits.create') }}" class="btn-primary"><x-heroicon-o-plus class="size-4" /> Input setoran</a>@endif
    </div>
    @if($deposits->count())
        <div class="table-wrap"><table class="data-table"><thead><tr><th>Tanggal</th><th>User</th><th>Kategori</th><th>Jenis</th><th>Berat</th><th>Harga</th><th>Total</th><th>Petugas</th></tr></thead><tbody>
            @foreach($deposits as $deposit)
                <tr><td>{{ $deposit->deposit_date->format('d/m/Y') }}</td><td><p class="font-semibold text-slate-900">{{ $deposit->user->name }}</p><p class="text-xs text-slate-500">{{ $deposit->location->name }}</p></td><td>{{ $deposit->wasteType->category }}</td><td>{{ $deposit->wasteType->name }}</td><td>{{ number_format($deposit->weight, 2, ',', '.') }} {{ $deposit->wasteType->unit }}</td><td>Rp{{ number_format($deposit->price_per_unit, 0, ',', '.') }}</td><td class="font-semibold text-emerald-700">Rp{{ number_format($deposit->total_amount, 0, ',', '.') }}</td><td>{{ $deposit->creator->name }}</td></tr>
            @endforeach
        </tbody></table></div><div class="mt-5">{{ $deposits->links() }}</div>
    @else
        <x-empty-state title="Belum ada setoran" description="Setoran sampah user akan muncul setelah petugas menimbang dan menyimpan transaksi.">@if(auth()->user()->hasPermission('waste.deposit.create'))<a href="{{ route('waste-deposits.create') }}" class="btn-primary mt-5">Input setoran</a>@endif</x-empty-state>
    @endif
</x-layouts.app>
