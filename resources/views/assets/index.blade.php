<x-layouts.app title="Aset & Perawatan" subtitle="Inventaris dan kondisi aset Rumah Maggot">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-1 flex-wrap gap-2"><div class="relative max-w-sm flex-1"><x-heroicon-o-magnifying-glass class="absolute left-3.5 top-3 size-5 text-slate-400" /><input name="q" value="{{ request('q') }}" class="field pl-11" placeholder="Cari aset atau kode"></div><select name="condition" class="field max-w-48"><option value="">Semua kondisi</option>@foreach(['good'=>'Baik','needs_maintenance'=>'Perlu perawatan','minor_damage'=>'Rusak ringan','major_damage'=>'Rusak berat'] as $v=>$l)<option value="{{ $v }}" @selected(request('condition')===$v)>{{ $l }}</option>@endforeach</select><button class="btn-secondary">Cari</button></form>
        @if(auth()->user()->hasPermission('asset.create'))<a href="{{ route('assets.create') }}" class="btn-primary"><x-heroicon-o-plus class="size-4" /> Tambah aset</a>@endif
    </div>
    @if($assets->count())
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($assets as $asset)
                <article class="card p-5 transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3"><div class="grid size-11 place-items-center rounded-xl bg-stone-100 text-slate-600"><x-heroicon-o-wrench-screwdriver class="size-6" /></div><x-status-badge :status="$asset->condition" /></div>
                    <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $asset->asset_code }} · {{ $asset->category }}</p><h2 class="mt-1 text-lg font-bold text-slate-900">{{ $asset->name }}</h2><p class="mt-2 flex items-center gap-1.5 text-sm text-slate-500"><x-heroicon-o-map-pin class="size-4" /> {{ $asset->location->name }}{{ $asset->location_detail ? ' · '.$asset->location_detail : '' }}</p>
                    <div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4"><p class="text-sm font-semibold text-slate-700">Rp {{ number_format($asset->purchase_value, 0, ',', '.') }}</p><a href="{{ route('assets.show', $asset) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-700">Detail <x-heroicon-o-arrow-right class="size-4" /></a></div>
                </article>
            @endforeach
        </div><div class="mt-5">{{ $assets->links() }}</div>
    @else
        <x-empty-state title="Belum ada aset" description="Daftarkan mesin, peralatan, dan fasilitas agar kondisinya dapat dipantau.">@if(auth()->user()->hasPermission('asset.create'))<a href="{{ route('assets.create') }}" class="btn-primary mt-5">Tambah aset</a>@endif</x-empty-state>
    @endif
</x-layouts.app>
