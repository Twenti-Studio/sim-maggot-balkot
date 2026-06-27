<x-layouts.app title="Tabungan Saya" subtitle="Saldo dari akumulasi setoran sampah">
    <div class="grid gap-4 md:grid-cols-4">
        <article class="card p-4"><div class="grid size-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><x-heroicon-o-wallet class="size-5" /></div><p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Total saldo</p><p class="mt-1 text-2xl font-bold text-slate-900">Rp{{ number_format($totalSavings, 0, ',', '.') }}</p></article>
        <article class="card p-4"><div class="grid size-10 place-items-center rounded-xl bg-violet-50 text-violet-700"><x-heroicon-o-arrow-down-tray class="size-5" /></div><p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Total penarikan</p><p class="mt-1 text-2xl font-bold text-slate-900">Rp{{ number_format($totalWithdrawals, 0, ',', '.') }}</p></article>
        <article class="card p-4"><div class="grid size-10 place-items-center rounded-xl bg-sky-50 text-sky-700"><x-heroicon-o-scale class="size-5" /></div><p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Total sampah</p><p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($totalWeight, 2, ',', '.') }} kg</p></article>
        <article class="card p-4"><div class="grid size-10 place-items-center rounded-xl bg-amber-50 text-amber-700"><x-heroicon-o-map-pin class="size-5" /></div><p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">Lokasi maggot</p><p class="mt-1 text-lg font-bold text-slate-900">{{ $location?->name ?? 'Belum diatur' }}</p></article>
    </div>

    <section class="mt-6 grid gap-3 md:grid-cols-2">
        <a href="{{ route('savings.history') }}" class="card p-5 transition hover:border-brand-300 hover:bg-brand-50/40">
            <x-heroicon-o-clock class="size-7 text-brand-700" />
            <h2 class="mt-3 font-bold text-slate-900">Riwayat Tabungan</h2>
            <p class="mt-1 text-sm text-slate-500">Lihat setoran sampah, berat, harga per kg, dan nilai tabungan yang masuk.</p>
        </a>
        <a href="{{ route('savings.withdrawals') }}" class="card p-5 transition hover:border-brand-300 hover:bg-brand-50/40">
            <x-heroicon-o-banknotes class="size-7 text-brand-700" />
            <h2 class="mt-3 font-bold text-slate-900">Penarikan</h2>
            <p class="mt-1 text-sm text-slate-500">Ajukan penarikan saldo dan lihat riwayat penarikan.</p>
        </a>
    </section>

    <section class="mt-6">
        <div class="mb-3 flex items-center justify-between gap-3"><h2 class="font-bold text-slate-900">Harga sampah diterima</h2><p class="text-sm text-slate-500">Harga per kg dari pengelola</p></div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($wasteTypes as $type)
                <article class="rounded-xl border border-stone-200 bg-white p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">{{ $type->category }}</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $type->name }}</p>
                    <p class="mt-1 text-lg font-bold text-emerald-700">Rp{{ number_format($type->price_per_unit, 0, ',', '.') }} / kg</p>
                    @if($type->description)<p class="mt-1 text-sm text-slate-500">{{ $type->description }}</p>@endif
                </article>
            @empty
                <x-empty-state title="Belum ada harga sampah" description="Harga sampah akan muncul setelah admin menambahkan jenis sampah yang diterima." />
            @endforelse
        </div>
    </section>
</x-layouts.app>
