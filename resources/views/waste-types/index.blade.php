<x-layouts.app title="Jenis Sampah" subtitle="Master sampah yang diterima dan harga tabungan">
    <div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
        <section>
            <div class="table-wrap"><table class="data-table"><thead><tr><th>Kategori</th><th>Jenis</th><th>Harga</th><th>Status</th><th>Catatan</th><th class="text-right">Aksi</th></tr></thead><tbody>
                @foreach($types as $type)
                    <tr><td>{{ $type->category }}</td><td class="font-semibold text-slate-900">{{ $type->name }}</td><td>Rp{{ number_format($type->price_per_unit, 0, ',', '.') }} / kg</td><td><x-status-badge :status="$type->is_active ? 'active' : 'inactive'" /></td><td class="text-sm text-slate-500">{{ $type->description ?: '—' }}</td><td><div class="flex justify-end"><button type="button" data-type-edit data-id="{{ $type->id }}" data-category="{{ $type->category }}" data-name="{{ $type->name }}" data-price="{{ $type->price_per_unit }}" data-description="{{ $type->description }}" data-active="{{ $type->is_active ? 1 : 0 }}" class="grid size-10 place-items-center rounded-xl text-slate-500 hover:bg-stone-100" title="Edit"><x-heroicon-o-pencil-square class="size-5" /></button></div></td></tr>
                @endforeach
            </tbody></table></div><div class="mt-5">{{ $types->links() }}</div>
        </section>
        <aside class="card h-fit p-5 xl:sticky xl:top-24">
            <div class="flex items-center gap-3"><div class="grid size-10 place-items-center rounded-xl bg-brand-50 text-brand-700"><x-heroicon-o-tag class="size-5" /></div><div><h2 id="type-form-title" class="font-bold text-slate-900">Tambah jenis sampah</h2><p class="text-xs text-slate-500">Harga dipakai saat petugas menimbang</p></div></div>
            <form id="type-form" method="POST" action="{{ route('waste-types.store') }}" class="mt-5 space-y-4">@csrf <input id="type-method" type="hidden" name="_method" value="POST"><input type="hidden" name="unit" value="kg">
                <div><label class="label">Kategori sampah</label><select id="type-category" name="category" required class="field"><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category }}">{{ $category }}</option>@endforeach</select></div>
                <div><label class="label">Nama jenis sampah</label><input id="type-name" name="name" required class="field" placeholder="Botol plastik"></div>
                <div><label class="label">Harga per kilogram</label><input id="type-price" name="price_per_unit" type="number" min="0" step="100" required class="field" placeholder="2500"><p class="mt-1 text-xs text-slate-500">Harga ini otomatis dipakai petugas saat menimbang, termasuk berat kurang dari 1 kg.</p></div>
                <div><label class="label">Catatan</label><textarea id="type-description" name="description" rows="3" class="field"></textarea></div>
                <div><label class="label">Status</label><select id="type-active" name="is_active" class="field"><option value="1">Aktif diterima</option><option value="0">Nonaktif</option></select></div>
                <div class="flex gap-2"><button class="btn-primary flex-1">Simpan</button><button id="type-cancel" type="button" class="btn-secondary hidden">Batal</button></div>
            </form>
        </aside>
    </div>
    <script>
        document.querySelectorAll('[data-type-edit]').forEach(button => button.addEventListener('click', () => {
            const form = document.getElementById('type-form');
            form.action = `/jenis-sampah/${button.dataset.id}`; document.getElementById('type-method').value = 'PUT'; document.getElementById('type-form-title').textContent = 'Edit jenis sampah';
            ['category','name','price','description'].forEach(k => document.getElementById(`type-${k}`).value = button.dataset[k] ?? '');
            document.getElementById('type-active').value = button.dataset.active; document.getElementById('type-cancel').classList.remove('hidden');
        }));
        document.getElementById('type-cancel')?.addEventListener('click', () => window.location.reload());
    </script>
</x-layouts.app>
