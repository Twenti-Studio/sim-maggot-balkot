@php($editing = $report->exists)
<x-layouts.app :title="$editing ? 'Edit Laporan Harian' : 'Tambah Laporan Harian'" subtitle="Semua nilai berat menggunakan kilogram kecuali jumlah telur dan lalat">
    <form method="POST" action="{{ $editing ? route('reports.update', $report) : route('reports.store') }}" class="space-y-6">
        @csrf @if($editing) @method('PUT') @endif
        <section class="card p-5 sm:p-6">
            <div class="mb-5 flex items-center gap-3"><div class="grid size-10 place-items-center rounded-xl bg-brand-50 text-brand-700"><x-heroicon-o-calendar-days class="size-5" /></div><div><h2 class="font-bold text-slate-900">Informasi laporan</h2><p class="text-sm text-slate-500">Tanggal dan lokasi kegiatan</p></div></div>
            <div class="grid gap-4 md:grid-cols-2">
                <div><label class="label" for="report_date">Tanggal laporan</label><input id="report_date" name="report_date" type="date" max="{{ today()->format('Y-m-d') }}" value="{{ old('report_date', $report->report_date?->format('Y-m-d') ?? today()->format('Y-m-d')) }}" required class="field"></div>
                <div><label class="label" for="location_id">Lokasi</label><select id="location_id" name="location_id" required class="field" @disabled(!in_array(auth()->user()->role, ['super_admin','admin']))>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string)old('location_id', $report->location_id ?? auth()->user()->location_id)===(string)$location->id)>{{ $location->name }}</option>@endforeach</select>@if(!in_array(auth()->user()->role, ['super_admin','admin']))<input type="hidden" name="location_id" value="{{ auth()->user()->location_id }}">@endif</div>
            </div>
        </section>

        <section class="card p-5 sm:p-6">
            <div class="mb-5 flex items-center gap-3"><div class="grid size-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><x-heroicon-o-arrow-path-rounded-square class="size-5" /></div><div><h2 class="font-bold text-slate-900">Sampah masuk</h2><p class="text-sm text-slate-500">Catat material organik dan non-organik</p></div></div>
            <div class="grid gap-5 lg:grid-cols-2">
                <fieldset class="rounded-2xl border border-stone-200 p-4"><legend class="px-2 text-sm font-bold text-slate-800">Sampah organik</legend><div class="space-y-4"><div><label class="label">Berat (kg)</label><input name="organic_waste_kg" type="number" min="0" step="0.01" value="{{ old('organic_waste_kg', $report->organic_waste_kg ?? 0) }}" required class="field"></div><div><label class="label">Sumber sampah</label><input name="organic_source" value="{{ old('organic_source', $report->organic_source) }}" class="field" placeholder="Contoh: Pasar lokal"></div><div><label class="label">Catatan</label><textarea name="organic_notes" rows="2" class="field">{{ old('organic_notes', $report->organic_notes) }}</textarea></div></div></fieldset>
                <fieldset class="rounded-2xl border border-stone-200 p-4"><legend class="px-2 text-sm font-bold text-slate-800">Sampah non-organik</legend><div class="space-y-4"><div><label class="label">Berat (kg)</label><input name="non_organic_waste_kg" type="number" min="0" step="0.01" value="{{ old('non_organic_waste_kg', $report->non_organic_waste_kg ?? 0) }}" required class="field"></div><div><label class="label">Jenis sampah</label><input name="non_organic_type" value="{{ old('non_organic_type', $report->non_organic_type) }}" class="field" placeholder="Contoh: Plastik campuran"></div><div><label class="label">Catatan</label><textarea name="non_organic_notes" rows="2" class="field">{{ old('non_organic_notes', $report->non_organic_notes) }}</textarea></div></div></fieldset>
            </div>
        </section>

        <section class="card p-5 sm:p-6">
            <div class="mb-5 flex items-center gap-3"><div class="grid size-10 place-items-center rounded-xl bg-lime-50 text-lime-700"><x-heroicon-o-beaker class="size-5" /></div><div><h2 class="font-bold text-slate-900">Siklus maggot</h2><p class="text-sm text-slate-500">Perkembangan telur hingga lalat BSF</p></div></div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <div><label class="label">Jumlah telur</label><input name="eggs_amount" type="number" min="0" step="0.01" value="{{ old('eggs_amount', $report->eggs_amount ?? 0) }}" required class="field"></div>
                <div><label class="label">Satuan telur</label><select name="eggs_unit" class="field">@foreach(['gram'=>'Gram','kilogram'=>'Kilogram','estimasi'=>'Estimasi ekor'] as $v=>$l)<option value="{{ $v }}" @selected(old('eggs_unit', $report->eggs_unit ?? 'gram')===$v)>{{ $l }}</option>@endforeach</select></div>
                <div><label class="label">Bayi maggot (kg)</label><input name="baby_maggot_kg" type="number" min="0" step="0.01" value="{{ old('baby_maggot_kg', $report->baby_maggot_kg ?? 0) }}" required class="field"></div>
                <div><label class="label">Maggot dewasa (kg)</label><input name="adult_maggot_kg" type="number" min="0" step="0.01" value="{{ old('adult_maggot_kg', $report->adult_maggot_kg ?? 0) }}" required class="field"></div>
                <div><label class="label">Pre-pupa (kg)</label><input name="prepupa_kg" type="number" min="0" step="0.01" value="{{ old('prepupa_kg', $report->prepupa_kg ?? 0) }}" required class="field"></div>
                <div><label class="label">Lalat BSF (ekor)</label><input name="bsf_flies_count" type="number" min="0" step="1" value="{{ old('bsf_flies_count', $report->bsf_flies_count ?? 0) }}" required class="field"></div>
            </div>
        </section>

        <section class="card p-5 sm:p-6">
            <div class="mb-5 flex items-center gap-3"><div class="grid size-10 place-items-center rounded-xl bg-amber-50 text-amber-700"><x-heroicon-o-cube class="size-5" /></div><div><h2 class="font-bold text-slate-900">Hasil produksi</h2><p class="text-sm text-slate-500">Output produksi pada tanggal laporan</p></div></div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach(['kasgot_kg'=>'Kasgot','other_fertilizer_kg'=>'Pupuk organik lain','wet_maggot_kg'=>'Maggot basah','dry_maggot_kg'=>'Maggot kering'] as $name=>$label)<div><label class="label">{{ $label }} (kg)</label><input name="{{ $name }}" type="number" min="0" step="0.01" value="{{ old($name, $report->{$name} ?? 0) }}" required class="field"></div>@endforeach
            </div>
            <div class="mt-4"><label class="label">Catatan umum</label><textarea name="notes" rows="3" class="field" placeholder="Kondisi atau kejadian penting hari ini">{{ old('notes', $report->notes) }}</textarea></div>
        </section>

        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end"><a href="{{ $editing ? route('reports.show', $report) : route('reports.index') }}" class="btn-secondary">Batal</a><button class="btn-primary"><x-heroicon-o-check class="size-5" /> Simpan sebagai draft</button></div>
    </form>
</x-layouts.app>
