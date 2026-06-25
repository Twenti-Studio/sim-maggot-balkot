@php($editing = $staff->exists)
<x-layouts.app :title="$editing ? 'Edit Petugas' : 'Tambah Petugas'" subtitle="Identitas dan penempatan petugas">
    <form method="POST" action="{{ $editing ? route('staff.update', $staff) : route('staff.store') }}" class="card mx-auto max-w-4xl p-5 sm:p-7">
        @csrf @if($editing) @method('PUT') @endif
        <div class="grid gap-5 md:grid-cols-2">
            <div><label class="label">Kode petugas</label><input name="employee_code" value="{{ old('employee_code', $staff->employee_code) }}" required class="field" placeholder="PTG-001"></div>
            <div><label class="label">Nama lengkap</label><input name="name" value="{{ old('name', $staff->name) }}" required class="field"></div>
            <div><label class="label">Jabatan</label><input name="position" value="{{ old('position', $staff->position) }}" required class="field" placeholder="Petugas Budidaya"></div>
            <div><label class="label">Nomor kontak</label><input name="phone" value="{{ old('phone', $staff->phone) }}" class="field" placeholder="08xxxxxxxxxx"></div>
            <div><label class="label">Lokasi kerja</label><select name="location_id" required class="field">@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string)old('location_id',$staff->location_id)===(string)$location->id)>{{ $location->name }}</option>@endforeach</select></div>
            <div><label class="label">Tanggal mulai</label><input type="date" name="joined_at" value="{{ old('joined_at', $staff->joined_at?->format('Y-m-d')) }}" class="field"></div>
            <div><label class="label">Akun pengguna terkait</label><select name="user_id" class="field"><option value="">Tidak ditautkan</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)old('user_id',$staff->user_id)===(string)$user->id)>{{ $user->name }} · {{ $user->email }}</option>@endforeach</select><p class="mt-1.5 text-xs text-slate-500">Diperlukan agar petugas dapat melakukan absensi sendiri.</p></div>
            <div><label class="label">Status</label><select name="status" class="field"><option value="active" @selected(old('status',$staff->status ?? 'active')==='active')>Aktif</option><option value="inactive" @selected(old('status',$staff->status)==='inactive')>Tidak aktif</option></select></div>
            <div class="md:col-span-2"><label class="label">Catatan</label><textarea name="notes" rows="3" class="field">{{ old('notes', $staff->notes) }}</textarea></div>
        </div>
        <div class="mt-7 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end"><a href="{{ route('staff.index') }}" class="btn-secondary">Batal</a><button class="btn-primary"><x-heroicon-o-check class="size-5" /> Simpan petugas</button></div>
    </form>
</x-layouts.app>
