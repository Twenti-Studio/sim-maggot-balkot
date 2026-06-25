<x-layouts.app title="Laporan Harian" subtitle="Sampah, siklus BSF, dan hasil produksi">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-1 flex-wrap gap-2">
            <select name="status" class="field max-w-44"><option value="">Semua status</option>@foreach(['draft'=>'Draft','submitted'=>'Diajukan','validated'=>'Tervalidasi','rejected'=>'Perlu revisi','revision_requested'=>'Revisi diminta'] as $value=>$label)<option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>@endforeach</select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="field max-w-44" aria-label="Tanggal mulai">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="field max-w-44" aria-label="Tanggal akhir">
            <button class="btn-secondary"><x-heroicon-o-funnel class="size-4" /> Filter</button>
        </form>
        <div class="flex gap-2">
            @if(auth()->user()->hasPermission('report.export') && in_array(auth()->user()->role, ['super_admin', 'admin']))<a href="{{ route('exports.index') }}" class="btn-secondary"><x-heroicon-o-arrow-down-tray class="size-4" /> <span class="hidden sm:inline">PDF / Excel</span></a>@endif
            @if(auth()->user()->hasPermission('production.create'))<a href="{{ route('reports.create') }}" class="btn-primary"><x-heroicon-o-plus class="size-4" /> Tambah laporan</a>@endif
        </div>
    </div>

    @if($reports->count())
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>Tanggal</th><th>Lokasi/Petugas</th><th>Sampah masuk</th><th>Produksi</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td><p class="font-semibold text-slate-900">{{ $report->report_date->translatedFormat('d M Y') }}</p><p class="mt-1 text-xs text-slate-500">#{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</p></td>
                            <td><p class="font-medium text-slate-900">{{ $report->location->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $report->creator->name }}</p></td>
                            <td><p>{{ number_format($report->organic_waste_kg, 1, ',', '.') }} kg organik</p><p class="mt-1 text-xs text-slate-500">{{ number_format($report->non_organic_waste_kg, 1, ',', '.') }} kg non-organik</p></td>
                            <td><p>{{ number_format($report->wet_maggot_kg, 1, ',', '.') }} kg maggot basah</p><p class="mt-1 text-xs text-slate-500">{{ number_format($report->kasgot_kg, 1, ',', '.') }} kg kasgot</p></td>
                            <td><x-status-badge :status="$report->status" /></td>
                            <td><div class="flex justify-end gap-1"><a href="{{ route('reports.show', $report) }}" class="grid size-10 place-items-center rounded-xl text-slate-500 hover:bg-stone-100 hover:text-brand-700" title="Lihat"><x-heroicon-o-eye class="size-5" /></a>@if($report->isEditableBy(auth()->user()))<a href="{{ route('reports.edit', $report) }}" class="grid size-10 place-items-center rounded-xl text-slate-500 hover:bg-stone-100 hover:text-brand-700" title="Edit"><x-heroicon-o-pencil-square class="size-5" /></a>@endif</div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $reports->links() }}</div>
    @else
        <x-empty-state title="Belum ada laporan" description="Mulai pencatatan operasional harian Rumah Maggot.">@if(auth()->user()->hasPermission('production.create'))<a href="{{ route('reports.create') }}" class="btn-primary mt-5"><x-heroicon-o-plus class="size-4" /> Tambah laporan</a>@endif</x-empty-state>
    @endif
</x-layouts.app>
