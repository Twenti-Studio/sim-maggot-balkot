<x-layouts.app title="Manajemen Petugas" subtitle="Data petugas operasional Rumah Maggot">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-1 gap-2"><div class="relative max-w-sm flex-1"><x-heroicon-o-magnifying-glass class="absolute left-3.5 top-3 size-5 text-slate-400" /><input name="q" value="{{ request('q') }}" class="field pl-11" placeholder="Cari nama atau kode"></div><select name="status" class="field max-w-40"><option value="">Semua status</option><option value="active" @selected(request('status')==='active')>Aktif</option><option value="inactive" @selected(request('status')==='inactive')>Tidak aktif</option></select><button class="btn-secondary">Cari</button></form>
        <a href="{{ route('staff.create') }}" class="btn-primary"><x-heroicon-o-user-plus class="size-4" /> Tambah petugas</a>
    </div>
    @if($staffMembers->count())
        <div class="table-wrap"><table class="data-table"><thead><tr><th>Petugas</th><th>Jabatan</th><th>Lokasi</th><th>Kontak</th><th>Status</th><th class="text-right">Aksi</th></tr></thead><tbody>
            @foreach($staffMembers as $staff)
                <tr><td><div class="flex items-center gap-3"><div class="grid size-10 shrink-0 place-items-center rounded-full bg-brand-100 font-bold text-brand-800">{{ str($staff->name)->substr(0,1)->upper() }}</div><div><p class="font-semibold text-slate-900">{{ $staff->name }}</p><p class="text-xs text-slate-500">{{ $staff->employee_code }}{{ $staff->user ? ' · Terhubung akun' : '' }}</p></div></div></td><td>{{ $staff->position }}</td><td>{{ $staff->location->name }}</td><td>{{ $staff->phone ?: '—' }}</td><td><x-status-badge :status="$staff->status" /></td><td><div class="flex justify-end gap-1"><a href="{{ route('staff.edit', $staff) }}" class="grid size-10 place-items-center rounded-xl text-slate-500 hover:bg-stone-100" title="Edit"><x-heroicon-o-pencil-square class="size-5" /></a><form method="POST" action="{{ route('staff.destroy', $staff) }}" onsubmit="return confirm('Nonaktifkan petugas ini?')">@csrf @method('DELETE')<button class="grid size-10 place-items-center rounded-xl text-red-500 hover:bg-red-50" title="Arsipkan"><x-heroicon-o-archive-box class="size-5" /></button></form></div></td></tr>
            @endforeach
        </tbody></table></div><div class="mt-5">{{ $staffMembers->links() }}</div>
    @else
        <x-empty-state title="Belum ada petugas" description="Tambahkan petugas untuk mengelola absensi dan penugasan perawatan."><a href="{{ route('staff.create') }}" class="btn-primary mt-5">Tambah petugas</a></x-empty-state>
    @endif
</x-layouts.app>
