<x-layouts.app title="Unduh Laporan" subtitle="Unduh laporan operasional PDF dan Excel">
    <div class="mb-6"><h2 class="text-2xl font-bold text-slate-900">Laporan operasional</h2><p class="mt-1 text-sm text-slate-500">Data terbaru sesuai pencatatan sistem.</p></div>
    <div class="grid gap-5 sm:grid-cols-2">@foreach([
        'production'=>['Produksi & sampah','Sampah, siklus maggot, dan hasil produksi.','clipboard-document-list'],
        'attendance'=>['Absensi petugas','Jam masuk, jam pulang, dan status kehadiran.','clock'],
        'assets'=>['Inventaris aset','Lokasi, nilai, kondisi, dan status aset.','archive-box'],
        'maintenance'=>['Perawatan aset','Jadwal, tindakan, komponen, dan biaya.','wrench-screwdriver'],
    ] as $type=>$item)<article class="card p-5 sm:p-6"><div class="flex items-start gap-4"><div class="grid size-12 shrink-0 place-items-center rounded-2xl bg-brand-50 text-brand-700"><x-dynamic-component :component="'heroicon-o-'.$item[2]" class="size-6" /></div><div><h3 class="font-bold text-slate-900">{{ $item[0] }}</h3><p class="mt-1 text-sm leading-6 text-slate-500">{{ $item[1] }}</p><p class="mt-2 text-xs font-semibold text-brand-700">{{ number_format($counts[$type]) }} data</p></div></div><div class="mt-5 flex gap-2 border-t border-stone-100 pt-4"><a href="{{ route('exports.download',[$type,'pdf']) }}" class="btn-secondary flex-1"><x-heroicon-o-document-text class="size-4" /> PDF</a><a href="{{ route('exports.download',[$type,'xlsx']) }}" class="btn-primary flex-1"><x-heroicon-o-table-cells class="size-4" /> Excel</a></div></article>@endforeach</div>
</x-layouts.app>
