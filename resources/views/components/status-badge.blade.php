@props(['status'])
@php
    $map = [
        'draft' => ['Draft', 'bg-slate-100 text-slate-700'],
        'submitted' => ['Diajukan', 'bg-amber-100 text-amber-800'],
        'validated' => ['Tervalidasi', 'bg-emerald-100 text-emerald-800'],
        'rejected' => ['Perlu revisi', 'bg-red-100 text-red-800'],
        'revision_requested' => ['Revisi diminta', 'bg-violet-100 text-violet-800'],
        'active' => ['Aktif', 'bg-emerald-100 text-emerald-800'],
        'inactive' => ['Tidak aktif', 'bg-slate-100 text-slate-700'],
        'archived' => ['Diarsipkan', 'bg-slate-100 text-slate-600'],
        'good' => ['Baik', 'bg-emerald-100 text-emerald-800'],
        'needs_maintenance' => ['Perlu perawatan', 'bg-amber-100 text-amber-800'],
        'minor_damage' => ['Rusak ringan', 'bg-orange-100 text-orange-800'],
        'major_damage' => ['Rusak berat', 'bg-red-100 text-red-800'],
        'scheduled' => ['Terjadwal', 'bg-sky-100 text-sky-800'],
        'in_progress' => ['Berlangsung', 'bg-amber-100 text-amber-800'],
        'completed' => ['Selesai', 'bg-emerald-100 text-emerald-800'],
        'cancelled' => ['Dibatalkan', 'bg-slate-100 text-slate-700'],
        'present' => ['Hadir', 'bg-emerald-100 text-emerald-800'],
    ];
    [$label, $class] = $map[$status] ?? [str($status)->headline(), 'bg-slate-100 text-slate-700'];
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold $class"]) }}>{{ $label }}</span>
