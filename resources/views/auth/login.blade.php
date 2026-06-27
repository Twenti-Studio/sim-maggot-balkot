<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#166534">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/icons/app-icon.png">
    <title>Masuk · SIM Maggot Balkot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f4f6f1]">
    <main class="grid min-h-screen lg:grid-cols-[1.1fr_0.9fr]">
        <section class="relative hidden overflow-hidden bg-brand-800 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-28 -top-28 size-96 rounded-full border-[60px] border-white/5"></div>
            <div class="absolute -bottom-36 -left-24 size-[28rem] rounded-full bg-lime-400/10"></div>
            <div class="relative flex items-center gap-3">
                <img src="/icons/app-icon.png" class="size-14 rounded-2xl bg-white/95 p-1" alt="Logo">
                <div><p class="text-xl font-bold">SIM Maggot Balkot</p><p class="text-sm text-emerald-100">Smart Integration & Management</p></div>
            </div>
            <div class="relative max-w-xl">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-lime-300">Smart Integration & Management of Maggot Balikpapan Kota</p>
                <h1 class="mt-4 text-5xl font-bold leading-tight">Operasional maggot, petugas, aset, dan tabungan sampah dalam satu alur.</h1>
                <p class="mt-5 text-lg leading-8 text-emerald-100">Data lapangan lebih rapi, setoran warga langsung terhitung, dan keputusan pengelola bisa dibuat dari informasi yang sama.</p>
            </div>
            <p class="relative text-sm text-emerald-100">Produk digital oleh <a href="https://twenti.studio" class="font-semibold text-white">twenti.studio</a></p>
        </section>

        <section class="flex items-center justify-center px-5 py-10 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <img src="/icons/app-icon.png" class="size-12 rounded-xl" alt="Logo">
                    <div><p class="font-bold text-slate-900">SIM Maggot Balkot</p><p class="text-xs text-slate-500">Smart Integration & Management</p></div>
                </div>
                <p class="text-sm font-semibold text-brand-700">Selamat datang kembali</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">Masuk ke akun Anda</h2>
                <p class="mt-2 text-sm text-slate-500">Gunakan akun petugas yang diberikan pengelola, atau daftar sebagai user tabungan sampah.</p>

                <div class="mt-8"><x-flash /></div>
                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="label">Alamat email</label>
                        <div class="relative"><x-heroicon-o-envelope class="pointer-events-none absolute left-3.5 top-3 size-5 text-slate-400" /><input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="field pl-11" placeholder="nama@rumahmaggot.id"></div>
                    </div>
                    <div>
                        <label for="password" class="label">Kata sandi</label>
                        <div class="relative"><x-heroicon-o-lock-closed class="pointer-events-none absolute left-3.5 top-3 size-5 text-slate-400" /><input id="password" name="password" type="password" required autocomplete="current-password" class="field px-11" placeholder="Minimal 8 karakter"><button type="button" data-password-toggle="password" class="absolute right-2 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-lg text-slate-500 hover:bg-stone-100" aria-label="Lihat kata sandi"><x-heroicon-o-eye class="size-5" /></button></div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" value="1" class="size-4 rounded border-stone-300 text-brand-700 focus:ring-brand-600"> Ingat saya di perangkat ini</label>
                    <button class="btn-primary w-full"><x-heroicon-o-arrow-right-end-on-rectangle class="size-5" /> Masuk</button>
                </form>
                <p class="mt-5 text-center text-sm text-slate-500">Belum punya akun tabungan sampah? <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:text-brand-800">Daftar di sini</a></p>
                <p class="mt-8 text-center text-xs text-slate-500 lg:hidden">Dibangun oleh <a href="https://twenti.studio" class="font-semibold text-brand-700">twenti.studio</a></p>
            </div>
        </section>
    </main>
</body>
</html>
