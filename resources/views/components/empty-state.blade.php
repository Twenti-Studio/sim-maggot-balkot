@props(['title' => 'Belum ada data', 'description' => 'Data akan tampil di sini setelah tersedia.'])
<div class="card flex flex-col items-center px-6 py-12 text-center">
    <div class="grid size-12 place-items-center rounded-2xl bg-stone-100 text-slate-500">
        <x-heroicon-o-inbox class="size-6" />
    </div>
    <h3 class="mt-4 font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mt-1 max-w-md text-sm text-slate-500">{{ $description }}</p>
    {{ $slot }}
</div>
