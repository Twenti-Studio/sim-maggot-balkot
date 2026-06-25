@php
    $report->loadMissing(['location','creator','validator']);
    $metrics = [
        'Sampah organik' => [$report->organic_waste_kg, 'kg'], 'Sampah non-organik' => [$report->non_organic_waste_kg, 'kg'],
        'Telur maggot' => [$report->eggs_amount, $report->eggs_unit], 'Bayi maggot' => [$report->baby_maggot_kg, 'kg'],
        'Maggot dewasa' => [$report->adult_maggot_kg, 'kg'], 'Pre-pupa' => [$report->prepupa_kg, 'kg'], 'Lalat BSF' => [$report->bsf_flies_count, 'ekor'],
        'Kasgot' => [$report->kasgot_kg, 'kg'], 'Pupuk organik lain' => [$report->other_fertilizer_kg, 'kg'], 'Maggot basah' => [$report->wet_maggot_kg, 'kg'], 'Maggot kering' => [$report->dry_maggot_kg, 'kg'],
    ];
@endphp
<x-layouts.app title="Detail Laporan" :subtitle="$report->report_date->translatedFormat('l, d F Y')">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('reports.index') }}" class="btn-secondary"><x-heroicon-o-arrow-left class="size-4" /> Kembali</a>
        <div class="flex flex-wrap gap-2">
            @if($report->isEditableBy(auth()->user()))<a href="{{ route('reports.edit', $report) }}" class="btn-secondary"><x-heroicon-o-pencil-square class="size-4" /> Edit</a>@endif
            @if(in_array($report->status, ['draft','rejected']) && ($report->created_by===auth()->id() || auth()->user()->hasPermission('production.validate')))<form method="POST" action="{{ route('reports.submit', $report) }}">@csrf<button class="btn-primary"><x-heroicon-o-paper-airplane class="size-4" /> Ajukan validasi</button></form>@endif
            @if(auth()->user()->hasPermission('production.delete'))<form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Arsipkan laporan ini?')">@csrf @method('DELETE')<button class="btn-danger"><x-heroicon-o-archive-box class="size-4" /> Arsipkan</button></form>@endif
        </div>
    </div>

    <section class="card p-5 sm:p-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
            <div><div class="flex items-center gap-2"><x-status-badge :status="$report->status" /><span class="text-xs text-slate-400">#{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</span></div><h2 class="mt-3 text-xl font-bold text-slate-900">{{ $report->location->name }}</h2><p class="mt-1 text-sm text-slate-500">Dicatat oleh {{ $report->creator->name }} · {{ $report->created_at->translatedFormat('d M Y, H:i') }}</p></div>
            @if($report->validator)<div class="rounded-xl bg-stone-50 px-4 py-3 text-sm"><p class="text-xs text-slate-500">Validator</p><p class="mt-1 font-semibold text-slate-800">{{ $report->validator->name }}</p></div>@endif
        </div>
        @if($report->rejection_reason)<div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><p class="font-semibold">Catatan validator</p><p class="mt-1">{{ $report->rejection_reason }}</p></div>@endif
        @if($report->revision_reason)<div class="mt-5 rounded-xl border border-violet-200 bg-violet-50 p-4 text-sm text-violet-800"><p class="font-semibold">Alasan permintaan revisi</p><p class="mt-1">{{ $report->revision_reason }}</p></div>@endif
    </section>

    <section class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach($metrics as $label=>[$value,$unit])<article class="card p-4"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($value, is_float((float)$value) ? 1 : 0, ',', '.') }} <span class="text-sm font-medium text-slate-500">{{ $unit }}</span></p></article>@endforeach
    </section>

    <section class="mt-6 card p-5 sm:p-6">
        <h2 class="font-bold text-slate-900">Keterangan laporan</h2>
        <dl class="mt-4 grid gap-5 md:grid-cols-2">
            <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sumber organik</dt><dd class="mt-1 text-sm text-slate-800">{{ $report->organic_source ?: '—' }}</dd><dd class="mt-1 text-sm text-slate-500">{{ $report->organic_notes ?: 'Tidak ada catatan' }}</dd></div>
            <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis non-organik</dt><dd class="mt-1 text-sm text-slate-800">{{ $report->non_organic_type ?: '—' }}</dd><dd class="mt-1 text-sm text-slate-500">{{ $report->non_organic_notes ?: 'Tidak ada catatan' }}</dd></div>
            <div class="md:col-span-2"><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan umum</dt><dd class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $report->notes ?: 'Tidak ada catatan.' }}</dd></div>
        </dl>
    </section>

    @if(auth()->user()->hasPermission('production.validate') && $report->status==='submitted')
        <section class="mt-6 grid gap-4 lg:grid-cols-2">
            <form method="POST" action="{{ route('reports.validate', $report) }}" class="card border-emerald-200 p-5">@csrf<h3 class="font-bold text-emerald-900">Validasi laporan</h3><p class="mt-1 text-sm text-emerald-700">Pastikan seluruh angka dan sumber telah diperiksa.</p><button class="btn-primary mt-4"><x-heroicon-o-check-badge class="size-5" /> Validasi laporan</button></form>
            <form method="POST" action="{{ route('reports.reject', $report) }}" class="card border-red-200 p-5">@csrf<h3 class="font-bold text-red-900">Kembalikan untuk revisi</h3><textarea name="reason" required rows="2" class="field mt-3" placeholder="Jelaskan data yang perlu diperbaiki"></textarea><button class="btn-danger mt-3"><x-heroicon-o-x-circle class="size-5" /> Tolak laporan</button></form>
        </section>
    @endif
    @if($report->status==='validated' && $report->created_by===auth()->id())<form method="POST" action="{{ route('reports.request-revision', $report) }}" class="mt-6 card p-5">@csrf<h3 class="font-bold text-slate-900">Ada kesalahan setelah validasi?</h3><div class="mt-3 flex flex-col gap-3 sm:flex-row"><input name="reason" required class="field" placeholder="Alasan permintaan revisi"><button class="btn-secondary shrink-0">Ajukan revisi</button></div></form>@endif
    @if($report->status==='revision_requested' && auth()->user()->hasPermission('production.validate'))<form method="POST" action="{{ route('reports.reopen', $report) }}" class="mt-6">@csrf<button class="btn-primary"><x-heroicon-o-lock-open class="size-4" /> Buka akses revisi</button></form>@endif
</x-layouts.app>
