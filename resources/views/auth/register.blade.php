<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#166534">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/icons/app-icon.png">
    <title>Daftar · SIM Maggot Balkot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f4f6f1]">
    <main class="flex min-h-screen items-center justify-center px-5 py-10">
        <section class="w-full max-w-xl">
            <div class="mb-8 flex items-center gap-3">
                <img src="/icons/app-icon.png" class="size-12 rounded-xl" alt="Logo">
                <div><p class="font-bold text-slate-900">SIM Maggot Balkot</p><p class="text-xs text-slate-500">Tabungan sampah terintegrasi</p></div>
            </div>
            <div class="card p-5 sm:p-7">
                <p class="text-sm font-semibold text-brand-700">Pendaftaran user biasa</p>
                <h1 class="mt-2 text-2xl font-bold text-slate-900">Buat akun tabungan sampah</h1>
                <p class="mt-2 text-sm text-slate-500">Pilih lokasi maggot terdekat agar petugas dapat mencatat setoran sampah Anda dengan benar.</p>
                <div class="mt-5"><x-flash /></div>
                <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div><label class="label">Nama lengkap</label><input name="name" value="{{ old('name') }}" required autocomplete="name" class="field"></div>
                    <div><label class="label">Email</label><input name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="field"></div>
                    <div><label class="label">Nomor kontak</label><input name="phone" value="{{ old('phone') }}" autocomplete="tel" class="field"></div>
                    <div><label class="label">Lokasi maggot terdekat</label><select name="location_id" required class="field"><option value="">Pilih lokasi</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected((string) old('location_id') === (string) $location->id)>{{ $location->name }}{{ $location->address ? ' · '.$location->address : '' }}</option>@endforeach</select></div>
                    <div><label class="label">Kata sandi</label><div class="relative"><input id="password" name="password" type="password" required autocomplete="new-password" minlength="8" class="field pr-11"><button type="button" data-password-toggle="password" class="absolute right-2 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-lg text-slate-500 hover:bg-stone-100" aria-label="Lihat kata sandi"><x-heroicon-o-eye class="size-5" /></button></div></div>
                    <div><label class="label">Konfirmasi kata sandi</label><input name="password_confirmation" type="password" required autocomplete="new-password" minlength="8" class="field"></div>
                    <button class="btn-primary w-full"><x-heroicon-o-user-plus class="size-5" /> Daftar</button>
                </form>
                <p class="mt-5 text-center text-sm text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Masuk</a></p>
            </div>
        </section>
    </main>
</body>
</html>
