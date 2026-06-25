@if (session('success'))
    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
        <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0" />
        <span>{{ session('success') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
        <div class="flex items-center gap-2 font-semibold">
            <x-heroicon-o-exclamation-triangle class="size-5" />
            Periksa kembali data yang dimasukkan.
        </div>
        <ul class="mt-2 list-disc space-y-1 pl-7">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
