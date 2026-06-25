@php($editing = $maintenance->exists)
<x-layouts.app :title="$editing ? 'Update Perawatan' : 'Jadwalkan Perawatan'" :subtitle="$asset->asset_code.' · '.$asset->name">
    <form method="POST" action="{{ $editing ? route('maintenance.update',[$asset,$maintenance]) : route('maintenance.store',$asset) }}" class="card mx-auto max-w-4xl p-5 sm:p-7">
        @csrf @if($editing) @method('PUT') @endif
        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2"><label class="label">Jenis perawatan</label><input name="maintenance_type" value="{{ old('maintenance_type',$maintenance->maintenance_type) }}" required class="field" placeholder="Pemeriksaan mesin berkala"></div>
            <div><label class="label">Tanggal rencana</label><input type="date" name="scheduled_at" value="{{ old('scheduled_at',$maintenance->scheduled_at?->format('Y-m-d') ?? today()->format('Y-m-d')) }}" required class="field"></div>
            <div><label class="label">Petugas</label><select name="assigned_staff_id" class="field" @disabled(auth()->user()->role==='operator')><option value="">Belum ditugaskan</option>@foreach($staffMembers as $staff)<option value="{{ $staff->id }}" @selected((string)old('assigned_staff_id',$maintenance->assigned_staff_id)===(string)$staff->id)>{{ $staff->name }} · {{ $staff->employee_code }}</option>@endforeach</select>@if(auth()->user()->role==='operator')<input type="hidden" name="assigned_staff_id" value="{{ auth()->user()->staff?->id }}">@endif</div>
            <div><label class="label">Status</label><select name="status" class="field">@foreach(['scheduled'=>'Terjadwal','in_progress'=>'Berlangsung','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $v=>$l)<option value="{{ $v }}" @selected(old('status',$maintenance->status ?? 'scheduled')===$v)>{{ $l }}</option>@endforeach</select></div>
            <div><label class="label">Tanggal selesai</label><input type="date" name="completed_at" value="{{ old('completed_at',$maintenance->completed_at?->format('Y-m-d')) }}" class="field"></div>
            <div><label class="label">Estimasi biaya (Rp)</label><input type="number" min="0" name="estimated_cost" value="{{ old('estimated_cost',$maintenance->estimated_cost ?? 0) }}" required class="field"></div>
            <div><label class="label">Biaya aktual (Rp)</label><input type="number" min="0" name="actual_cost" value="{{ old('actual_cost',$maintenance->actual_cost ?? 0) }}" required class="field"></div>
            <div class="md:col-span-2"><label class="label">Tindakan yang dilakukan</label><textarea name="actions" rows="3" class="field">{{ old('actions',$maintenance->actions) }}</textarea></div>
            <div><label class="label">Komponen diganti</label><textarea name="components" rows="3" class="field">{{ old('components',$maintenance->components) }}</textarea></div>
            <div><label class="label">Catatan</label><textarea name="notes" rows="3" class="field">{{ old('notes',$maintenance->notes) }}</textarea></div>
        </div>
        <div class="mt-7 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end"><a href="{{ route('assets.show',$asset) }}" class="btn-secondary">Batal</a><button class="btn-primary"><x-heroicon-o-check class="size-5" /> Simpan perawatan</button></div>
    </form>
</x-layouts.app>
