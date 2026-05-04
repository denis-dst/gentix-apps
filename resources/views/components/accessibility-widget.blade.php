<div x-data="{ 
        open: false,
        highContrast: false,
        largeText: false,
        toggleContrast() {
            this.highContrast = !this.highContrast;
            if(this.highContrast) {
                document.documentElement.classList.add('contrast-125', 'saturate-150');
            } else {
                document.documentElement.classList.remove('contrast-125', 'saturate-150');
            }
        },
        toggleText() {
            this.largeText = !this.largeText;
            if(this.largeText) {
                document.documentElement.classList.add('text-lg');
            } else {
                document.documentElement.classList.remove('text-lg');
            }
        }
    }" 
    class="fixed bottom-4 left-4 md:bottom-6 md:left-6 z-[99]">
    
    <div x-show="open" @click.away="open = false" x-transition class="absolute bottom-full left-0 mb-4 bg-[#111118] border border-white/10 rounded-2xl p-4 shadow-2xl w-48 md:w-56 text-white">
        <div class="text-xs md:text-sm font-bold mb-3 pb-2 border-b border-white/10">{{ app()->getLocale() == 'id' ? 'Aksesibilitas' : 'Accessibility' }}</div>
        
        <button @click="toggleContrast()" class="w-full text-left px-2 py-2 md:px-3 text-xs md:text-sm rounded-lg hover:bg-white/5 transition flex justify-between items-center mb-1">
            <span>{{ app()->getLocale() == 'id' ? 'Kontras Tinggi' : 'High Contrast' }}</span>
            <div class="w-7 h-4 md:w-8 rounded-full relative transition-colors shrink-0" :class="highContrast ? 'bg-orange-500' : 'bg-white/20'">
                <div class="w-4 h-4 bg-white rounded-full absolute top-0 transition-transform" :class="highContrast ? 'translate-x-3 md:translate-x-4' : 'translate-x-0'"></div>
            </div>
        </button>
        
        <button @click="toggleText()" class="w-full text-left px-2 py-2 md:px-3 text-xs md:text-sm rounded-lg hover:bg-white/5 transition flex justify-between items-center">
            <span>{{ app()->getLocale() == 'id' ? 'Teks Besar' : 'Large Text' }}</span>
            <div class="w-7 h-4 md:w-8 rounded-full relative transition-colors shrink-0" :class="largeText ? 'bg-orange-500' : 'bg-white/20'">
                <div class="w-4 h-4 bg-white rounded-full absolute top-0 transition-transform" :class="largeText ? 'translate-x-3 md:translate-x-4' : 'translate-x-0'"></div>
            </div>
        </button>
    </div>

    <button @click="open = !open" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-br from-orange-500 to-amber-600 rounded-full flex items-center justify-center text-white shadow-lg shadow-orange-500/30 hover:scale-110 active:scale-95 transition-all">
        <svg class="w-6 h-6 md:w-7 md:h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm9 7h-6v13h-2v-6h-2v6H9V9H3V7h18v2z"/></svg>
    </button>
</div>
