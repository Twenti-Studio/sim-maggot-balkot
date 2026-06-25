<x-layouts.app title="Notifikasi" subtitle="Pembaruan aktivitas dan tugas Anda">
    <div class="mb-5 flex justify-end"><form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="btn-secondary"><x-heroicon-o-check class="size-4" /> Tandai semua dibaca</button></form></div>
    <section class="card divide-y divide-stone-100 overflow-hidden">
        @forelse($notifications as $notification)
            <a href="{{ route('notifications.read',$notification) }}" class="flex gap-4 p-4 transition hover:bg-stone-50 sm:p-5 {{ $notification->read_at ? '' : 'bg-brand-50/60' }}"><div class="grid size-10 shrink-0 place-items-center rounded-xl {{ $notification->read_at ? 'bg-stone-100 text-slate-500' : 'bg-brand-100 text-brand-800' }}"><x-heroicon-o-bell class="size-5" /></div><div class="min-w-0 flex-1"><div class="flex items-start justify-between gap-3"><p class="font-semibold text-slate-900">{{ $notification->title }}</p><time class="shrink-0 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</time></div><p class="mt-1 text-sm leading-6 text-slate-600">{{ $notification->message }}</p></div>@if(!$notification->read_at)<i class="mt-2 size-2 shrink-0 rounded-full bg-brand-600"></i>@endif</a>
        @empty<div class="py-14 text-center"><x-heroicon-o-bell-slash class="mx-auto size-8 text-slate-300" /><p class="mt-3 text-sm text-slate-500">Belum ada notifikasi.</p></div>@endforelse
    </section><div class="mt-5">{{ $notifications->links() }}</div>
</x-layouts.app>
