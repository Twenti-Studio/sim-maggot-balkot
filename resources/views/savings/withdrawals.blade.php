<x-layouts.app title="Penarikan" subtitle="Ajukan dan pantau penarikan saldo tabungan">
    <div class="grid gap-6 xl:grid-cols-[24rem_1fr]">
        <aside class="card h-fit p-5">
            <div class="flex items-center gap-3"><div class="grid size-10 place-items-center rounded-xl bg-brand-50 text-brand-700"><x-heroicon-o-banknotes class="size-5" /></div><div><h2 class="font-bold text-slate-900">Tarik saldo</h2><p class="text-xs text-slate-500">Saldo tersedia Rp{{ number_format($totalSavings, 0, ',', '.') }}</p></div></div>
            <p class="mt-4 text-sm leading-6 text-slate-600">Penarikan belum terhubung payment gateway. Setelah permintaan dikirim, datang ke {{ $location?->name ?? 'lokasi maggot terdekat' }} untuk verifikasi dan pencairan manual.</p>
            <form method="POST" action="{{ route('savings.withdraw') }}" class="mt-5 space-y-3">
                @csrf
                <div><label class="label">Nominal penarikan</label><input name="amount" type="number" min="1000" max="{{ floor($totalSavings) }}" step="1000" value="{{ old('amount') }}" required class="field" placeholder="Contoh: 25000" @disabled($totalSavings < 1000)></div>
                <div><label class="label">Catatan</label><textarea name="notes" rows="3" class="field" placeholder="Opsional" @disabled($totalSavings < 1000)>{{ old('notes') }}</textarea></div>
                <button class="btn-primary w-full" @disabled($totalSavings < 1000)><x-heroicon-o-paper-airplane class="size-4" /> Ajukan penarikan</button>
                @if($totalSavings < 1000)<p class="text-xs text-slate-500">Saldo belum cukup untuk penarikan minimal Rp1.000.</p>@endif
            </form>
        </aside>

        <section>
            <div class="mb-3 flex items-center justify-between gap-3"><h2 class="font-bold text-slate-900">Riwayat penarikan</h2><p class="text-sm text-slate-500">Total penarikan Rp{{ number_format($totalWithdrawals, 0, ',', '.') }}</p></div>
            @if($withdrawals->count())
                <div class="table-wrap"><table class="data-table"><thead><tr><th>Tanggal</th><th>Nominal</th><th>Status</th><th>Lokasi pencairan</th><th>Catatan</th></tr></thead><tbody>
                    @foreach($withdrawals as $withdrawal)
                        <tr><td>{{ $withdrawal->withdrawal_date->format('d/m/Y') }}</td><td class="font-semibold text-violet-700">Rp{{ number_format($withdrawal->amount, 0, ',', '.') }}</td><td><x-status-badge :status="$withdrawal->status" /></td><td>{{ $withdrawal->location->name }}</td><td class="text-sm text-slate-500">{{ $withdrawal->notes ?: 'Silakan datang ke lokasi untuk pencairan manual.' }}</td></tr>
                    @endforeach
                </tbody></table></div><div class="mt-5">{{ $withdrawals->links() }}</div>
            @else
                <x-empty-state title="Belum ada penarikan" description="Permintaan tarik saldo akan muncul di sini setelah Anda mengajukan penarikan." />
            @endif
        </section>
    </div>
</x-layouts.app>
