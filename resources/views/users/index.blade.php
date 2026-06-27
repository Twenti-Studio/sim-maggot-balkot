<x-layouts.app title="Manajemen Pengguna" subtitle="Akun, jenis pengguna, lokasi, dan status akses">
    <div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
        <section>
            <div class="table-wrap"><table class="data-table"><thead><tr><th>Pengguna</th><th>Jenis pengguna</th><th>Lokasi</th><th>Status</th><th class="text-right">Aksi</th></tr></thead><tbody>
                @foreach($users as $user)
                    <tr><td><p class="font-semibold text-slate-900">{{ $user->name }}</p><p class="text-xs text-slate-500">{{ $user->email }}{{ $user->phone ? ' · '.$user->phone : '' }}</p></td><td>{{ $user->roleLabel() }}</td><td>{{ $user->location?->name ?? 'Semua lokasi' }}</td><td><x-status-badge :status="$user->is_active ? 'active' : 'inactive'" /></td><td><div class="flex justify-end gap-1"><button type="button" data-user-edit data-id="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-phone="{{ $user->phone }}" data-role="{{ $user->role }}" data-location-id="{{ $user->location_id }}" data-active="{{ $user->is_active ? 1 : 0 }}" class="grid size-10 place-items-center rounded-xl text-slate-500 hover:bg-stone-100" title="Edit"><x-heroicon-o-pencil-square class="size-5" /></button>@if($user->id!==auth()->id() && $user->is_active)<form method="POST" action="{{ route('users.destroy',$user) }}" onsubmit="return confirm('Nonaktifkan akun ini?')">@csrf @method('DELETE')<button class="grid size-10 place-items-center rounded-xl text-red-500 hover:bg-red-50" title="Nonaktifkan"><x-heroicon-o-user-minus class="size-5" /></button></form>@endif</div></td></tr>
                @endforeach
            </tbody></table></div><div class="mt-5">{{ $users->links() }}</div>
        </section>

        <aside class="card h-fit p-5 xl:sticky xl:top-24">
            <div class="flex items-center gap-3"><div class="grid size-10 place-items-center rounded-xl bg-brand-50 text-brand-700"><x-heroicon-o-user-plus class="size-5" /></div><div><h2 id="user-form-title" class="font-bold text-slate-900">Tambah pengguna</h2><p class="text-xs text-slate-500">Tetapkan akses awal pengguna</p></div></div>
            <form id="user-form" method="POST" action="{{ route('users.store') }}" class="mt-5 space-y-4">@csrf <input id="user-method" type="hidden" name="_method" value="POST">
                <div><label class="label">Nama</label><input id="user-name" name="name" required class="field"></div>
                <div><label class="label">Email</label><input id="user-email" type="email" name="email" required class="field"></div>
                <div><label class="label">Nomor kontak</label><input id="user-phone" name="phone" class="field"></div>
                <div><label class="label">Kata sandi</label><div class="relative"><input id="user-password" type="password" name="password" class="field pr-11" minlength="8"><button type="button" data-password-toggle="user-password" class="absolute right-2 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-lg text-slate-500 hover:bg-stone-100" aria-label="Lihat kata sandi"><x-heroicon-o-eye class="size-5" /></button></div><p class="mt-1 text-xs text-slate-500">Wajib untuk akun baru; kosongkan saat edit. Kata sandi lama tidak dapat ditampilkan.</p></div>
                <div><label class="label">Jenis pengguna</label><select id="user-role" name="role" class="field">@foreach($roleOptions as $key=>$label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select><p class="mt-1 text-xs text-slate-500">Super Admin hanya boleh satu akun.</p></div>
                <div><label class="label">Lokasi</label><select id="user-location" name="location_id" class="field"><option value="">Semua lokasi</option>@foreach($locations as $location)<option value="{{ $location->id }}">{{ $location->name }}</option>@endforeach</select></div>
                <div><label class="label">Status</label><select id="user-active" name="is_active" class="field"><option value="1">Aktif</option><option value="0">Tidak aktif</option></select></div>
                <div class="flex gap-2"><button class="btn-primary flex-1">Simpan</button><button id="user-cancel" type="button" class="btn-secondary hidden">Batal</button></div>
            </form>
        </aside>
    </div>
    <script>
        document.querySelectorAll('[data-user-edit]').forEach(button => button.addEventListener('click', () => {
            const u = { id: button.dataset.id, name: button.dataset.name, email: button.dataset.email, phone: button.dataset.phone, role: button.dataset.role, location_id: button.dataset.locationId, is_active: button.dataset.active === '1' }, form = document.getElementById('user-form');
            form.action = `/pengguna/${u.id}`; document.getElementById('user-method').value = 'PUT'; document.getElementById('user-form-title').textContent = 'Edit pengguna';
            ['name','email','phone','role'].forEach(k => document.getElementById(`user-${k}`).value = u[k] ?? '');
            document.getElementById('user-location').value = u.location_id ?? ''; document.getElementById('user-active').value = u.is_active ? '1' : '0'; document.getElementById('user-password').required = false; document.getElementById('user-cancel').classList.remove('hidden');
        }));
        document.getElementById('user-cancel')?.addEventListener('click', () => window.location.reload());
    </script>
</x-layouts.app>
