<x-layouts.app title="Input Setor Sampah" subtitle="Timbang sampah user dan sistem menghitung nilai tabungan otomatis">
    <form method="POST" action="{{ route('waste-deposits.store') }}" class="card max-w-5xl p-5 sm:p-6">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">
            <div><label class="label">User penyetor</label><select name="user_id" required class="field"><option value="">Pilih user yang terdaftar</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) old('user_id') === (string) $user->id)>{{ $user->name }} · {{ $user->email }} · {{ $user->location?->name ?? 'Lokasi belum diatur' }}</option>@endforeach</select></div>
            <div><label class="label">Tanggal setor</label><input type="date" name="deposit_date" value="{{ old('deposit_date', today()->format('Y-m-d')) }}" max="{{ today()->format('Y-m-d') }}" required class="field"></div>
        </div>
        <section class="mt-5">
            <div class="mb-3 flex items-center justify-between gap-3"><h2 class="font-bold text-slate-900">Pilih jenis sampah</h2><p class="text-sm text-slate-500">Bisa lebih dari satu jenis dalam sekali setoran</p></div>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach($types as $type)
                    @php($oldSelected = old("items.{$type->id}.selected"))
                    <label class="flex gap-3 rounded-xl border border-stone-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/40">
                        <input type="checkbox" name="items[{{ $type->id }}][selected]" value="1" class="mt-1 size-4 rounded border-stone-300 text-brand-700 focus:ring-brand-600" data-waste-check @checked($oldSelected)>
                        <span class="min-w-0 flex-1">
                            <span class="block text-xs font-semibold uppercase tracking-wide text-brand-700">{{ $type->category }}</span>
                            <span class="mt-1 block font-semibold text-slate-900">{{ $type->name }}</span>
                            <span class="mt-1 block text-sm text-slate-500">Harga Rp{{ number_format($type->price_per_unit, 0, ',', '.') }} / {{ $type->unit }}</span>
                            @if($type->description)<span class="mt-1 block text-xs text-slate-400">{{ $type->description }}</span>@endif
                            <span class="mt-3 block">
                                <span class="label">Berat {{ $type->unit }}</span>
                                <input name="items[{{ $type->id }}][weight]" type="number" min="0.01" step="0.01" value="{{ old("items.{$type->id}.weight") }}" class="field" placeholder="0.00" data-waste-weight data-price="{{ $type->price_per_unit }}">
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
        </section>
        <div class="mt-4 rounded-xl border border-brand-100 bg-brand-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Estimasi nilai tabungan</p>
            <p id="deposit-total" class="mt-1 text-2xl font-bold text-slate-900">Rp0</p>
        </div>
        <div class="mt-4"><label class="label">Catatan</label><textarea name="notes" rows="3" class="field">{{ old('notes') }}</textarea></div>
        <div class="mt-5 flex gap-2"><button class="btn-primary"><x-heroicon-o-scale class="size-4" /> Simpan setoran</button><a href="{{ route('waste-deposits.index') }}" class="btn-secondary">Batal</a></div>
    </form>
    <script>
        const formatRupiah = value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value || 0);
        const updateTotal = () => {
            let total = 0;
            document.querySelectorAll('[data-waste-check]').forEach(check => {
                const input = check.closest('label').querySelector('[data-waste-weight]');
                if (check.checked) total += Number(input.value || 0) * Number(input.dataset.price || 0);
            });
            document.getElementById('deposit-total').textContent = formatRupiah(total);
        };
        document.querySelectorAll('[data-waste-check], [data-waste-weight]').forEach(element => element.addEventListener('input', updateTotal));
        document.querySelectorAll('[data-waste-check]').forEach(element => element.addEventListener('change', updateTotal));
        updateTotal();
    </script>
</x-layouts.app>
