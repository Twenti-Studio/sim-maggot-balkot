<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sesi berakhir · SIM Maggot Balkot</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="grid min-h-screen place-items-center bg-stone-50 px-5 text-center">
    <main class="max-w-md">
        <img src="/icons/app-icon.png" alt="SIM Maggot Balkot" class="mx-auto size-16 rounded-2xl">
        <h1 class="mt-6 text-2xl font-bold text-slate-900">Sesi Anda berakhir</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Silakan masuk kembali untuk melanjutkan pekerjaan.</p>
        <a href="{{ route('login') }}" class="btn-primary mt-6">Masuk kembali</a>
    </main>
</body>
</html>
