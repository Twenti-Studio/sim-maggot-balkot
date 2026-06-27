<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#166534">
    <meta name="vapid-public-key" content="{{ config('services.webpush.public_key') }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/icons/app-icon.png">
    <link rel="apple-touch-icon" href="/icons/app-icon.png">
    <title>{{ isset($title) ? $title.' · ' : '' }}SIM Maggot Balkot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50">
    <div data-overlay class="fixed inset-0 z-30 hidden bg-slate-950/40 lg:hidden"></div>
    <aside data-sidebar class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-stone-200 bg-white transition-transform lg:translate-x-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 border-b border-stone-100 px-5 py-5">
            <img src="/icons/app-icon.png" alt="Logo SIM Maggot Balkot" class="size-11 rounded-xl object-contain">
            <div>
                <p class="font-bold leading-tight text-slate-900">SIM Maggot Balkot</p>
                <p class="mt-0.5 text-xs text-slate-500">Smart Integration & Management</p>
            </div>
        </a>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="Navigasi utama">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                <x-heroicon-o-squares-2x2 class="size-5" /> Dashboard
            </a>
            @if(auth()->user()->hasPermission('production.view'))
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-clipboard-document-list class="size-5" /> Laporan Harian
                </a>
            @endif
            @if(auth()->user()->hasPermission('attendance.view'))
                <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance.*') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-clock class="size-5" /> Absensi
                </a>
            @endif
            @if(auth()->user()->hasPermission('staff.manage'))
                <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-user-group class="size-5" /> Petugas
                </a>
            @endif
            @if(auth()->user()->hasPermission('payroll.manage'))
                <a href="{{ route('payroll.index') }}" class="nav-link {{ request()->routeIs('payroll.*') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-banknotes class="size-5" /> Gaji & Bonus
                </a>
            @endif
            @if(auth()->user()->hasPermission('finance.view'))
                <a href="{{ route('finance.index') }}" class="nav-link {{ request()->routeIs('finance.*') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-wallet class="size-5" /> Keuangan
                </a>
            @endif
            @if(auth()->user()->hasPermission('waste.type.manage'))
                <a href="{{ route('waste-types.index') }}" class="nav-link {{ request()->routeIs('waste-types.*') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-tag class="size-5" /> Jenis Sampah
                </a>
            @endif
            @if(auth()->user()->hasPermission('waste.deposit.view'))
                <a href="{{ route('waste-deposits.index') }}" class="nav-link {{ request()->routeIs('waste-deposits.*') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-scale class="size-5" /> Setor Sampah
                </a>
            @endif
            @if(auth()->user()->role === 'user')
                <a href="{{ route('savings.index') }}" class="nav-link {{ request()->routeIs('savings.index') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-wallet class="size-5" /> Tabungan Saya
                </a>
                <a href="{{ route('savings.history') }}" class="nav-link {{ request()->routeIs('savings.history') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-clock class="size-5" /> Riwayat Tabungan
                </a>
                <a href="{{ route('savings.withdrawals') }}" class="nav-link {{ request()->routeIs('savings.withdrawals') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-banknotes class="size-5" /> Penarikan
                </a>
            @endif
            @if(auth()->user()->hasPermission('asset.view'))
                <a href="{{ route('assets.index') }}" class="nav-link {{ request()->routeIs('assets.*', 'maintenance.*') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-wrench-screwdriver class="size-5" /> Aset & Perawatan
                </a>
            @endif
            @if(auth()->user()->hasPermission('report.export') && in_array(auth()->user()->role, ['super_admin', 'admin']))
                <a href="{{ route('exports.index') }}" class="nav-link {{ request()->routeIs('exports.*') ? 'nav-link-active' : '' }}">
                    <x-heroicon-o-arrow-down-tray class="size-5" /> Unduh Laporan
                </a>
            @endif

            @if(auth()->user()->hasPermission('user.view') || auth()->user()->hasPermission('location.manage'))
                <p class="px-3 pb-1 pt-5 text-[11px] font-bold uppercase tracking-widest text-slate-400">Administrasi</p>
                @if(auth()->user()->hasPermission('user.view'))
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'nav-link-active' : '' }}">
                        <x-heroicon-o-users class="size-5" /> Pengguna
                    </a>
                @endif
                @if(auth()->user()->hasPermission('location.manage'))
                    <a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'nav-link-active' : '' }}">
                        <x-heroicon-o-map-pin class="size-5" /> Lokasi
                    </a>
                @endif
            @endif
        </nav>

        <div class="border-t border-stone-100 p-3">
            <div class="rounded-xl bg-stone-50 p-3">
                <p class="truncate text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-500">{{ auth()->user()->roleLabel() }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button class="flex items-center gap-2 text-xs font-semibold text-red-600 hover:text-red-700">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="size-4" /> Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex min-h-screen flex-col lg:pl-72">
        <header class="sticky top-0 z-20 border-b border-stone-200 bg-white/95 backdrop-blur">
            <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button data-menu-toggle type="button" class="grid size-11 place-items-center rounded-xl text-slate-600 hover:bg-stone-100 lg:hidden" aria-label="Buka navigasi">
                        <x-heroicon-o-bars-3 class="size-6" />
                    </button>
                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-bold text-slate-900">{{ $title ?? 'SIM Maggot Balkot' }}</h1>
                        @isset($subtitle)<p class="hidden truncate text-xs text-slate-500 sm:block">{{ $subtitle }}</p>@endisset
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button data-install-pwa class="hidden min-h-10 items-center gap-2 rounded-xl border border-stone-200 px-3 text-sm font-semibold text-slate-700 hover:bg-stone-50">
                        <x-heroicon-o-arrow-down-tray class="size-4" /> <span class="hidden md:inline">Pasang aplikasi</span>
                    </button>
                    <button data-enable-push class="grid size-11 place-items-center rounded-xl text-slate-600 hover:bg-stone-100" aria-label="Aktifkan notifikasi">
                        <x-heroicon-o-bell-alert class="size-5" />
                    </button>
                    <a href="{{ route('notifications.index') }}" class="relative grid size-11 place-items-center rounded-xl text-slate-600 hover:bg-stone-100" aria-label="Notifikasi">
                        <x-heroicon-o-bell class="size-5" />
                        @php($unread = auth()->user()->notifications()->whereNull('read_at')->count())
                        @if($unread)<span class="absolute right-1.5 top-1.5 min-w-4 rounded-full bg-red-500 px-1 text-center text-[10px] font-bold text-white">{{ min($unread, 99) }}</span>@endif
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <x-flash />
            {{ $slot }}
        </main>

        <footer class="border-t border-stone-200 bg-white px-4 py-5 text-center text-xs text-slate-500 sm:px-6 lg:px-8">
            SIM Maggot Balkot · Dibangun dan dikelola oleh <a href="https://twenti.studio" target="_blank" rel="noopener" class="font-semibold text-brand-700 hover:text-brand-800">twenti.studio</a>
        </footer>
    </div>
</body>
</html>
