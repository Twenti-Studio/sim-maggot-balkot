<x-layouts.app title="Role & Akses" subtitle="Matriks permission empat role MVP">
    <div class="mb-5 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-800"><div class="flex gap-3"><x-heroicon-o-information-circle class="size-5 shrink-0" /><p>Role bersifat tetap pada MVP. Super Admin memiliki seluruh permission; perubahan role pengguna dilakukan melalui menu Pengguna.</p></div></div>
    @php($allPermissions = collect($permissions)->flatten()->reject(fn($p)=>$p==='*')->unique()->sort()->values())
    <div class="table-wrap"><table class="data-table"><thead><tr><th>Permission</th>@foreach($roles as $label)<th class="text-center">{{ $label }}</th>@endforeach</tr></thead><tbody>
        @foreach($allPermissions as $permission)
            <tr><td><code class="rounded-lg bg-stone-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $permission }}</code></td>@foreach($roles as $key=>$label)<td class="text-center">@if(in_array('*',$permissions[$key],true) || in_array($permission,$permissions[$key],true))<x-heroicon-s-check-circle class="mx-auto size-5 text-emerald-600" /><span class="sr-only">Diizinkan</span>@else<span class="text-slate-300">—</span>@endif</td>@endforeach</tr>
        @endforeach
    </tbody></table></div>
</x-layouts.app>
