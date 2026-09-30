@props([
    'totalQuota' => 0,
    'totalSold' => 0,
    'totalUnsold' => 0,
    'totalRevenue' => 0,
    'categoryStats' => collect(),
    'throughputChart' => []
])

<div class="relative bg-[#18181b] border border-orange-500/30 rounded-[2rem] md:rounded-[2.5rem] p-5 sm:p-7 lg:p-8 shadow-2xl text-white overflow-hidden mb-6">
    <!-- Ambient subtle background glow -->
    <div class="absolute -top-24 -right-24 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Top Row: 4 Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4 relative z-10">
        <!-- Card 1: KUOTA -->
        <div class="bg-[#23242c]/90 border border-orange-500/40 rounded-2xl p-5 shadow-lg transition-all duration-300 hover:border-orange-500 hover:shadow-orange-500/10 hover:shadow-xl group">
            <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-1.5 group-hover:text-slate-300 transition">KUOTA</p>
            <div class="text-2xl sm:text-3xl lg:text-[1.85rem] font-black text-white font-outfit tracking-tight">
                {{ number_format($totalQuota, 0, ',', '.') }}
            </div>
        </div>

        <!-- Card 2: TERJUAL -->
        <div class="bg-[#23242c]/90 border border-orange-500/40 rounded-2xl p-5 shadow-lg transition-all duration-300 hover:border-orange-500 hover:shadow-orange-500/10 hover:shadow-xl group">
            <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-1.5 group-hover:text-slate-300 transition">TERJUAL</p>
            <div class="text-2xl sm:text-3xl lg:text-[1.85rem] font-black text-white font-outfit tracking-tight">
                {{ number_format($totalSold, 0, ',', '.') }}
            </div>
        </div>

        <!-- Card 3: BELUM TERJUAL -->
        <div class="bg-[#23242c]/90 border border-orange-500/40 rounded-2xl p-5 shadow-lg transition-all duration-300 hover:border-orange-500 hover:shadow-orange-500/10 hover:shadow-xl group">
            <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-1.5 group-hover:text-slate-300 transition">BELUM TERJUAL</p>
            <div class="text-2xl sm:text-3xl lg:text-[1.85rem] font-black text-white font-outfit tracking-tight">
                {{ number_format($totalUnsold, 0, ',', '.') }}
            </div>
        </div>

        <!-- Card 4: TOTAL PENDAPATAN -->
        <div class="bg-[#23242c]/90 border border-orange-500/40 rounded-2xl p-5 shadow-lg transition-all duration-300 hover:border-orange-500 hover:shadow-orange-500/10 hover:shadow-xl group">
            <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-1.5 group-hover:text-slate-300 transition">TOTAL PENDAPATAN</p>
            <div class="text-2xl sm:text-3xl lg:text-[1.85rem] font-black text-white font-outfit tracking-tight truncate" title="Rp {{ number_format($totalRevenue, 0, ',', '.') }}">
                Rp,{{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Bottom Section: Sales Throughput Chart (Left) + Kategori Tiket Breakdown (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 mt-6 pt-5 border-t border-white/10 relative z-10 items-start">
        
        <!-- Left: Sales Throughput Chart -->
        <div class="lg:col-span-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm sm:text-base font-bold text-slate-200 tracking-wide font-outfit">Sales throughput</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Aktivitas Penjualan</span>
            </div>

            <!-- Chart Container -->
            <div class="w-full bg-[#1b1c23]/60 rounded-2xl p-4 border border-white/5">
                <div class="h-44 sm:h-48 flex items-end justify-between gap-2 sm:gap-3 pt-6 pb-2 px-1">
                    @forelse($throughputChart as $bar)
                        <div class="flex-1 flex flex-col items-center h-full justify-end group/bar relative">
                            <!-- Floating Tooltip -->
                            <div class="absolute -top-12 z-20 opacity-0 group-hover/bar:opacity-100 transition-all duration-200 pointer-events-none transform -translate-y-1 group-hover/bar:translate-y-0 whitespace-nowrap bg-slate-900 border border-slate-700 text-white text-[10px] font-bold py-1 px-2.5 rounded-lg shadow-xl">
                                <div class="font-extrabold text-cyan-400">{{ number_format($bar['count'], 0, ',', '.') }} Tiket</div>
                                <div class="text-[9px] text-slate-400">{{ $bar['label'] }}</div>
                            </div>

                            <!-- Bar Column with vibrant neon cyan-blue gradient -->
                            <div class="w-full max-w-[36px] bg-gradient-to-t from-[#0066ff] via-[#0099ff] to-[#00d2ff] rounded-t-lg sm:rounded-t-xl transition-all duration-300 group-hover/bar:brightness-125 group-hover/bar:shadow-[0_0_15px_rgba(0,210,255,0.6)] cursor-pointer"
                                 style="height: {{ $bar['height_pct'] }}%; min-height: 8px;">
                            </div>

                            <!-- Label underneath bar -->
                            <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 mt-2 truncate w-full text-center group-hover/bar:text-cyan-300 transition">
                                {{ $bar['label'] }}
                            </span>
                        </div>
                    @empty
                        <div class="w-full h-full flex items-center justify-center text-xs font-bold text-slate-500">
                            Belum ada aktivitas penjualan
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Kategori Tiket Breakdown Table -->
        <div class="lg:col-span-6 flex flex-col">
            <!-- Header Table -->
            <div class="grid grid-cols-12 gap-2 text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest pb-3 border-b border-white/10 px-2">
                <div class="col-span-6 sm:col-span-6">KATEGORI TIKET</div>
                <div class="col-span-2 text-right">KUOTA</div>
                <div class="col-span-2 text-right">TERJUAL</div>
                <div class="col-span-2 text-right">BELUM TERJUAL</div>
            </div>

            <!-- List Rows -->
            <div class="divide-y divide-white/5 max-h-[190px] overflow-y-auto custom-dark-scrollbar mt-1 pr-1">
                @forelse($categoryStats as $cat)
                    <div class="grid grid-cols-12 gap-2 items-center py-2.5 px-2 hover:bg-white/5 rounded-xl transition group">
                        <!-- Category Name + Colored Indicator Dot -->
                        <div class="col-span-6 sm:col-span-6 flex items-center gap-2.5 min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0 shadow-[0_0_8px_rgba(14,165,233,0.8)]" 
                                  style="background-color: {{ $cat->hex_color ?: '#0284c7' }};"></span>
                            <span class="text-xs sm:text-sm font-black text-white uppercase tracking-wider truncate group-hover:text-cyan-300 transition">
                                {{ $cat->name }}
                            </span>
                        </div>

                        <!-- Kuota -->
                        <div class="col-span-2 text-right text-xs sm:text-sm font-bold text-sky-400 font-mono">
                            {{ number_format($cat->quota, 0, ',', '.') }}
                        </div>

                        <!-- Terjual -->
                        <div class="col-span-2 text-right text-xs sm:text-sm font-bold text-sky-400 font-mono">
                            {{ number_format($cat->sold, 0, ',', '.') }}
                        </div>

                        <!-- Belum Terjual -->
                        <div class="col-span-2 text-right text-xs sm:text-sm font-bold text-sky-400 font-mono">
                            {{ number_format($cat->unsold, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs font-bold text-slate-500">
                        Tidak ada kategori tiket yang sesuai filter.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<style>
.custom-dark-scrollbar::-webkit-scrollbar {
    width: 5px;
}
.custom-dark-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 8px;
}
.custom-dark-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
}
.custom-dark-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}
</style>
