@if(!$shouldHideNav && !auth()->user()->hasRole('Petugas Gate'))
<div class="px-6 py-6 md:px-8 md:py-8 bg-white border-b border-slate-200 mb-6">
    <div class="max-w-[1400px] mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight font-outfit">{{ $header ?? $title ?? 'Dashboard' }}</h2>
            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Admin / <span class="text-orange-500">{{ $header ?? $title ?? 'Dashboard' }}</span></p>
        </div>
        @if(isset($actions))
            <div class="flex items-center gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
@endif
