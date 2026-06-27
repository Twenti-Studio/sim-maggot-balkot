<x-layouts.app title="Dashboard" subtitle="Ringkasan operasional bulan berjalan">
    @if(auth()->user()->role === 'user')
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $cards = [
                    ['Saldo tabungan', 'Rp'.number_format($summary['savings'], 0, ',', '.'), 'wallet', 'bg-emerald-50 text-emerald-700'],
                    ['Total setoran', 'Rp'.number_format($summary['deposits'], 0, ',', '.'), 'arrow-down-tray', 'bg-sky-50 text-sky-700'],
                    ['Total penarikan', 'Rp'.number_format($summary['withdrawals'], 0, ',', '.'), 'banknotes', 'bg-violet-50 text-violet-700'],
                    ['Berat tersetor', number_format($summary['weight'], 2, ',', '.').' kg', 'scale', 'bg-amber-50 text-amber-700'],
                ];
            @endphp
            @foreach($cards as [$label, $value, $icon, $colors])
                <article class="card p-4">
                    <div class="grid size-10 place-items-center rounded-xl {{ $colors }}"><x-dynamic-component :component="'heroicon-o-'.$icon" class="size-5" /></div>
                    <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $value }}</p>
                </article>
            @endforeach
        </div>

        <section class="mt-6 card p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div><h2 class="font-bold text-slate-900">Sumber angka tabungan</h2><p class="mt-1 text-sm text-slate-500">Semua angka di dashboard ini dihitung dari setoran dan penarikan akun Anda, bukan dari data dummy atau laporan operasional.</p></div>
                <a href="{{ route('savings.index') }}" class="btn-secondary">Tabungan Saya <x-heroicon-o-arrow-right class="size-4" /></a>
            </div>
            <div class="mt-4 rounded-xl border border-stone-200 bg-stone-50 p-4 text-sm text-slate-600">
                Lokasi maggot akun: <span class="font-semibold text-slate-900">{{ $location?->name ?? 'Belum diatur' }}</span>. Jumlah transaksi setoran tercatat: <span class="font-semibold text-slate-900">{{ number_format($summary['transactions']) }}</span>.
            </div>
        </section>

        <section class="mt-6 card p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div><h2 class="font-bold text-slate-900">Setoran terbaru</h2><p class="mt-1 text-sm text-slate-500">Riwayat terbaru yang sudah dicatat petugas.</p></div>
                <a href="{{ route('savings.history') }}" class="btn-secondary">Lihat riwayat <x-heroicon-o-clock class="size-4" /></a>
            </div>
            @if($recentDeposits->count())
                <div class="mt-5 table-wrap"><table class="data-table"><thead><tr><th>Tanggal</th><th>Kategori</th><th>Jenis</th><th>Berat</th><th>Nilai</th><th>Petugas</th></tr></thead><tbody>
                    @foreach($recentDeposits as $deposit)
                        <tr><td>{{ $deposit->deposit_date->format('d/m/Y') }}</td><td>{{ $deposit->wasteType->category }}</td><td>{{ $deposit->wasteType->name }}</td><td>{{ number_format($deposit->weight, 2, ',', '.') }} kg</td><td class="font-semibold text-emerald-700">Rp{{ number_format($deposit->total_amount, 0, ',', '.') }}</td><td>{{ $deposit->creator->name }}</td></tr>
                    @endforeach
                </tbody></table></div>
            @else
                <x-empty-state title="Belum ada setoran" description="Dashboard akan terisi setelah petugas mencatat setoran sampah pertama Anda." />
            @endif
        </section>
    @else
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['Sampah organik', number_format($summary['organic'], 1, ',', '.').' kg', 'arrow-path-rounded-square', 'bg-emerald-50 text-emerald-700'],
                ['Non-organik', number_format($summary['non_organic'], 1, ',', '.').' kg', 'trash', 'bg-slate-100 text-slate-700'],
                ['Maggot basah', number_format($summary['wet_maggot'], 1, ',', '.').' kg', 'beaker', 'bg-lime-50 text-lime-700'],
                ['Produksi kasgot', number_format($summary['kasgot'], 1, ',', '.').' kg', 'cube', 'bg-amber-50 text-amber-700'],
                ['Hadir hari ini', number_format($summary['attendance']), 'user-group', 'bg-sky-50 text-sky-700'],
                ['Aset kritis', number_format($summary['critical_assets']), 'exclamation-triangle', 'bg-red-50 text-red-700'],
            ];
            if (auth()->user()->role !== 'operator') {
                $cards[] = ['Total tabungan', 'Rp'.number_format($summary['savings'], 0, ',', '.'), 'wallet', 'bg-violet-50 text-violet-700'];
            }
        @endphp
        @foreach($cards as [$label, $value, $icon, $colors])
            <article class="card p-4">
                <div class="grid size-10 place-items-center rounded-xl {{ $colors }}"><x-dynamic-component :component="'heroicon-o-'.$icon" class="size-5" /></div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $value }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.5fr_1fr]">
        <section class="card p-5 sm:p-6">
            <div class="flex items-center justify-between">
                <div><h2 class="font-bold text-slate-900">Tren 7 hari terakhir</h2><p class="mt-1 text-sm text-slate-500">Sampah organik dan produksi maggot basah</p></div>
                <x-heroicon-o-chart-bar class="size-6 text-brand-700" />
            </div>
            @php($maxValue = max(1, $chart->max(fn($item) => max($item['organic'], $item['production']))))
            <div class="mt-8 flex h-56 items-end justify-between gap-2" role="img" aria-label="Grafik operasional tujuh hari">
                @foreach($chart as $item)
                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <div class="flex h-full w-full items-end justify-center gap-1">
                            <div class="group relative flex h-full items-end">
                                <div class="w-2.5 rounded-t bg-brand-700 transition hover:bg-brand-800 sm:w-4" style="height: {{ max(3, ($item['organic'] / $maxValue) * 100) }}%" tabindex="0" aria-label="Sampah organik {{ number_format($item['organic'], 1, ',', '.') }} kg pada {{ $item['label'] }}"></div>
                                <div class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-2 hidden w-max -translate-x-1/2 rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white shadow-lg group-hover:block group-focus-within:block">Organik {{ number_format($item['organic'], 1, ',', '.') }} kg</div>
                            </div>
                            <div class="group relative flex h-full items-end">
                                <div class="w-2.5 rounded-t bg-lime-400 transition hover:bg-lime-500 sm:w-4" style="height: {{ max(3, ($item['production'] / $maxValue) * 100) }}%" tabindex="0" aria-label="Maggot basah {{ number_format($item['production'], 1, ',', '.') }} kg pada {{ $item['label'] }}"></div>
                                <div class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-2 hidden w-max -translate-x-1/2 rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white shadow-lg group-hover:block group-focus-within:block">Basah {{ number_format($item['production'], 1, ',', '.') }} kg</div>
                            </div>
                        </div>
                        <span class="text-[11px] font-medium text-slate-500">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex gap-5 text-xs text-slate-500"><span class="flex items-center gap-2"><i class="size-2.5 rounded-full bg-brand-700"></i> Sampah organik</span><span class="flex items-center gap-2"><i class="size-2.5 rounded-full bg-lime-400"></i> Maggot basah</span></div>
        </section>

        <section class="card p-5 sm:p-6">
            <div class="flex items-center justify-between"><div><h2 class="font-bold text-slate-900">Perawatan mendatang</h2><p class="mt-1 text-sm text-slate-500">Jatuh tempo dalam tujuh hari</p></div><x-heroicon-o-wrench class="size-5 text-slate-400" /></div>
            <div class="mt-5 divide-y divide-stone-100">
                @forelse($dueMaintenances as $item)
                    <a href="{{ route('assets.show', $item->asset) }}" class="flex items-center gap-3 py-3 first:pt-0 hover:text-brand-700">
                        <div class="grid size-10 shrink-0 place-items-center rounded-xl {{ $item->scheduled_at->isPast() ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700' }}"><x-heroicon-o-calendar-days class="size-5" /></div>
                        <div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold text-slate-900">{{ $item->asset->name }}</p><p class="text-xs text-slate-500">{{ $item->scheduled_at->translatedFormat('d M Y') }} · {{ $item->maintenance_type }}</p></div>
                        <x-heroicon-o-chevron-right class="size-4 text-slate-400" />
                    </a>
                @empty
                    <p class="py-8 text-center text-sm text-slate-500">Tidak ada perawatan mendesak.</p>
                @endforelse
            </div>
        </section>
    </div>

    @if($pendingReports->isNotEmpty())
        <section class="mt-6 card p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3"><div><h2 class="font-bold text-slate-900">Menunggu validasi</h2><p class="mt-1 text-sm text-slate-500">Laporan terbaru yang perlu ditinjau</p></div><a href="{{ route('reports.index', ['status' => 'submitted']) }}" class="btn-secondary">Lihat semua <x-heroicon-o-arrow-right class="size-4" /></a></div>
            <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @foreach($pendingReports as $report)
                    <a href="{{ route('reports.show', $report) }}" class="rounded-2xl border border-stone-200 p-4 transition hover:border-brand-300 hover:bg-brand-50/50">
                        <div class="flex items-center justify-between"><p class="font-semibold text-slate-900">{{ $report->report_date->translatedFormat('d M Y') }}</p><x-status-badge :status="$report->status" /></div>
                        <p class="mt-2 text-sm text-slate-500">{{ $report->creator->name }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
    @endif
</x-layouts.app>
