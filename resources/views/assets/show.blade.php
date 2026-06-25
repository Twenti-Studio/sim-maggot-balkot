<x-layouts.app :title="$asset->name" :subtitle="$asset->asset_code.' · '.$asset->category">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3"><a href="{{ route('assets.index') }}" class="btn-secondary"><x-heroicon-o-arrow-left class="size-4" /> Kembali</a><div class="flex gap-2">@if(in_array(auth()->user()->role,['super_admin','admin']))<a href="{{ route('assets.edit',$asset) }}" class="btn-secondary"><x-heroicon-o-pencil-square class="size-4" /> Edit</a>@endif @if(auth()->user()->hasPermission('maintenance.create'))<a href="{{ route('maintenance.create',$asset) }}" class="btn-primary"><x-heroicon-o-calendar-days class="size-4" /> Jadwalkan perawatan</a>@endif</div></div>
    <div class="grid gap-6 lg:grid-cols-[1fr_0.7fr]">
        <section class="card p-5 sm:p-6">
            <div class="flex items-start justify-between"><div class="grid size-14 place-items-center rounded-2xl bg-stone-100 text-slate-700"><x-heroicon-o-wrench-screwdriver class="size-7" /></div><x-status-badge :status="$asset->condition" /></div>
            <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Lokasi</dt><dd class="mt-1 font-medium text-slate-900">{{ $asset->location->name }}</dd><dd class="text-sm text-slate-500">{{ $asset->location_detail ?: 'Tanpa detail lokasi' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nilai aset</dt><dd class="mt-1 font-medium text-slate-900">Rp {{ number_format($asset->purchase_value,0,',','.') }}</dd><dd class="text-sm text-slate-500">Dibeli {{ $asset->purchased_at?->translatedFormat('d M Y') ?? '—' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</dt><dd class="mt-1"><x-status-badge :status="$asset->status" /></dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan</dt><dd class="mt-1 text-sm text-slate-700">{{ $asset->notes ?: 'Tidak ada catatan.' }}</dd></div>
            </dl>
        </section>
        @if(auth()->user()->hasPermission('asset.update'))
            <form method="POST" action="{{ route('assets.condition',$asset) }}" class="card p-5 sm:p-6">@csrf @method('PATCH')<h2 class="font-bold text-slate-900">Laporkan kondisi</h2><p class="mt-1 text-sm text-slate-500">Perbarui hasil inspeksi terakhir.</p><div class="mt-5"><label class="label">Kondisi terbaru</label><select name="condition" class="field">@foreach(['good'=>'Baik','needs_maintenance'=>'Perlu perawatan','minor_damage'=>'Rusak ringan','major_damage'=>'Rusak berat'] as $v=>$l)<option value="{{ $v }}" @selected($asset->condition===$v)>{{ $l }}</option>@endforeach</select></div><div class="mt-4"><label class="label">Catatan kondisi</label><textarea name="notes" rows="3" class="field">{{ $asset->notes }}</textarea></div><button class="btn-primary mt-4 w-full"><x-heroicon-o-arrow-path class="size-4" /> Update kondisi</button></form>
        @endif
    </div>

    <section class="mt-6 card p-5 sm:p-6">
        <div class="flex items-center justify-between"><div><h2 class="font-bold text-slate-900">Riwayat perawatan</h2><p class="mt-1 text-sm text-slate-500">Jadwal, tindakan, dan penggantian komponen</p></div><x-heroicon-o-clipboard-document-check class="size-6 text-brand-700" /></div>
        <div class="mt-5 divide-y divide-stone-100">
            @forelse($asset->maintenances->sortByDesc('scheduled_at') as $maintenance)
                <div class="flex flex-col gap-3 py-4 first:pt-0 sm:flex-row sm:items-center"><div class="grid size-11 shrink-0 place-items-center rounded-xl {{ $maintenance->status==='completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}"><x-heroicon-o-calendar class="size-5" /></div><div class="min-w-0 flex-1"><div class="flex flex-wrap items-center gap-2"><p class="font-semibold text-slate-900">{{ $maintenance->maintenance_type }}</p><x-status-badge :status="$maintenance->status" /></div><p class="mt-1 text-sm text-slate-500">{{ $maintenance->scheduled_at->translatedFormat('d M Y') }} · {{ $maintenance->assignedStaff?->name ?? 'Belum ditugaskan' }}</p>@if($maintenance->actions)<p class="mt-2 text-sm text-slate-700">{{ $maintenance->actions }}</p>@endif</div>@if(auth()->user()->hasPermission('maintenance.create'))<a href="{{ route('maintenance.edit',[$asset,$maintenance]) }}" class="btn-secondary shrink-0"><x-heroicon-o-pencil-square class="size-4" /> Update</a>@endif</div>
            @empty<p class="py-8 text-center text-sm text-slate-500">Belum ada riwayat perawatan.</p>@endforelse
        </div>
    </section>
</x-layouts.app>
