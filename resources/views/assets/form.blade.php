@php($editing = $asset->exists)
<x-layouts.app :title="$editing ? 'Edit Aset' : 'Tambah Aset'" subtitle="Identitas, lokasi, dan kondisi aset">
    <form method="POST" action="{{ $editing ? route('assets.update', $asset) : route('assets.store') }}" class="card mx-auto max-w-5xl p-5 sm:p-7">
        @csrf @if($editing) @method('PUT') @endif
        <div class="grid gap-5 md:grid-cols-2">
            <div><label class="label">Kode aset</label><input name="asset_code" value="{{ old('asset_code',$asset->asset_code) }}" required class="field" placeholder="AST-001"></div>
            <div><label class="label">Nama aset</label><input name="name" value="{{ old('name',$asset->name) }}" required class="field"></div>
            <div><label class="label">Kategori</label><input name="category" value="{{ old('category',$asset->category) }}" required class="field" placeholder="Mesin, Peralatan, Bangunan"></div>
            <div><label class="label">Lokasi</label><select name="location_id" required class="field">@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string)old('location_id',$asset->location_id)===(string)$location->id)>{{ $location->name }}</option>@endforeach</select></div>
            <div><label class="label">Detail penempatan</label><input name="location_detail" value="{{ old('location_detail',$asset->location_detail) }}" class="field" placeholder="Area pengolahan"></div>
            <div><label class="label">Tanggal pembelian</label><input type="date" name="purchased_at" value="{{ old('purchased_at',$asset->purchased_at?->format('Y-m-d')) }}" class="field"></div>
            <div><label class="label">Nilai perolehan (Rp)</label><input type="number" min="0" step="1" name="purchase_value" value="{{ old('purchase_value',$asset->purchase_value ?? 0) }}" required class="field"></div>
            <div><label class="label">Kondisi</label><select name="condition" class="field">@foreach(['good'=>'Baik','needs_maintenance'=>'Perlu perawatan','minor_damage'=>'Rusak ringan','major_damage'=>'Rusak berat'] as $v=>$l)<option value="{{ $v }}" @selected(old('condition',$asset->condition ?? 'good')===$v)>{{ $l }}</option>@endforeach</select></div>
            <div><label class="label">Status penggunaan</label><select name="status" class="field"><option value="active" @selected(old('status',$asset->status ?? 'active')==='active')>Aktif</option><option value="inactive" @selected(old('status',$asset->status)==='inactive')>Nonaktif</option><option value="archived" @selected(old('status',$asset->status)==='archived')>Diarsipkan</option></select></div>
            <div class="md:col-span-2"><label class="label">Catatan</label><textarea name="notes" rows="3" class="field">{{ old('notes',$asset->notes) }}</textarea></div>
        </div>
        <div class="mt-7 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end"><a href="{{ $editing ? route('assets.show',$asset) : route('assets.index') }}" class="btn-secondary">Batal</a><button class="btn-primary"><x-heroicon-o-check class="size-5" /> Simpan aset</button></div>
    </form>
</x-layouts.app>
