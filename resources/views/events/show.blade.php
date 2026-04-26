<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name }} - GenTix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-slate-800" x-data="{ 
    step: 1,
    selectedCategory: null,
    quantity: 0,
    nik: '',
    name: '',
    phone: '',
    email: '',
    paymentMethod: 'qris',
    notifWA: true,
    notifEmail: true,
    lang: 'id',
    showModalSK: false,
    promoCode: '',
    discount: 0,
    appliedPromoId: null,
    promoMessage: '',
    promoStatus: null, // 'success' or 'error'
    isSubmitting: false,
    
    get total() {
        if (!this.selectedCategory || !this.quantity) return 0;
        let subtotal = this.selectedCategory.price * this.quantity;
        return Math.max(0, subtotal - this.discount);
    },
    
    selectTicket(cat, qty) {
        let q = parseInt(qty);
        if (q > 0) {
            this.selectedCategory = cat;
            this.quantity = q;
        } else {
            // Only reset if this was the selected one
            if (this.selectedCategory && this.selectedCategory.id === cat.id) {
                this.selectedCategory = null;
                this.quantity = 0;
            }
        }
        this.discount = 0;
        this.promoMessage = '';
    },

    async applyPromo() {
        if (!this.promoCode || !this.selectedCategory) {
            this.promoMessage = this.lang === 'id' ? 'Pilih tiket dulu.' : 'Select ticket first.';
            this.promoStatus = 'error';
            return;
        }
        try {
            const amount = this.selectedCategory.price * this.quantity;
            const response = await fetch(`/promo/validate?code=${this.promoCode}&event_id={{ $event->id }}&amount=${amount}`);
            const data = await response.json();
            
            if (data.success) {
                this.discount = data.discount;
                this.appliedPromoId = data.promo_id;
                this.promoMessage = data.message;
                this.promoStatus = 'success';
            } else {
                this.discount = 0;
                this.appliedPromoId = null;
                this.promoMessage = data.message;
                this.promoStatus = 'error';
            }
        } catch (e) {
            this.promoMessage = 'Gagal validasi promo.';
            this.promoStatus = 'error';
        }
    },

    goToPayment() {
        if (!this.selectedCategory || this.quantity === 0) {
            alert(this.lang === 'id' ? 'Silakan pilih tiket.' : 'Please select ticket.');
            return;
        }
        if (this.nik.length < 16) {
            alert(this.lang === 'id' ? 'NIK harus 16 digit.' : 'NIK must be 16 digits.');
            return;
        }

        if (this.selectedCategory.nik_restriction) {
            const allowed = this.selectedCategory.nik_restriction.split(',').map(p => p.trim());
            if (!allowed.some(p => this.nik.startsWith(p))) {
                alert(this.selectedCategory.nik_restriction_message || 'NIK tidak diizinkan.');
                return;
            }
        }
        this.step = 2;
    },

    async submitBooking() {
        if (!this.selectedCategory) return;
        this.isSubmitting = true;
        try {
            const response = await fetch('{{ route('checkout.process', $event->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    ticket_category_id: this.selectedCategory.id,
                    quantity: this.quantity,
                    nik: this.nik,
                    name: this.name,
                    phone: this.phone,
                    email: this.email,
                    promo_code_id: this.appliedPromoId,
                    discount_amount: this.discount,
                    notif_wa: this.notifWA,
                    notif_email: this.notifEmail
                })
            });

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server error (Non-JSON response)');
            }

            const data = await response.json();
            if (data.success) {
                window.snap.pay(data.snap_token, {
                    onSuccess: (result) => { window.location.href = `/checkout/success/${data.reference_no}`; },
                    onPending: (result) => { window.location.href = `/checkout/success/${data.reference_no}`; },
                    onError: (result) => { alert('Pembayaran gagal.'); }
                });
            } else {
                alert(data.message || 'Gagal memproses pesanan.');
            }
        } catch (e) {
            console.error(e);
            alert('Kesalahan Sistem: ' + e.message);
        } finally {
            this.isSubmitting = false;
        }
    }
}">
    <div class="min-h-screen pb-20">
        <!-- Header / Banner Section -->
        <div class="bg-white border-b border-slate-200">
            <div class="max-w-6xl mx-auto p-4 lg:p-8">
                <div class="flex flex-col lg:flex-row gap-8 items-start">
                    <div class="w-full lg:w-1/3 shrink-0 relative group">
                        <img src="{{ $event->background_image ? (str_starts_with($event->background_image, 'http') ? $event->background_image : asset('storage/' . $event->background_image)) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&q=80' }}" 
                             onerror="this.src='https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&q=80'"
                             class="w-full aspect-[4/3] object-cover rounded-3xl shadow-2xl shadow-purple-200 transition duration-500 group-hover:scale-[1.02]" alt="{{ $event->name }}">
                        
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-emerald-500 text-white text-[10px] font-black rounded-lg shadow-lg flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M17.707 9.293l-5-5a1 1 0 00-1.414 1.414L14.586 9H3a1 1 0 100 2h11.586l-3.293 3.293a1 1 0 001.414 1.414l5-5a1 1 0 000-1.414z"/></svg>
                                PROMO AVAILABLE
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 space-y-4">
                        <div class="flex justify-between items-start">
                            <h1 class="text-2xl lg:text-3xl font-black text-slate-900 font-outfit">{{ $event->name }}</h1>
                            <div class="flex gap-2">
                                <button @click="lang = 'id'" :class="lang === 'id' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400'" class="px-2 py-1 rounded text-[10px] font-bold border border-slate-200 transition-all">ID</button>
                                <button @click="lang = 'en'" :class="lang === 'en' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400'" class="px-2 py-1 rounded text-[10px] font-bold border border-slate-200 transition-all">EN</button>
                            </div>
                        </div>
                        
                        <div class="space-y-2 text-sm font-medium text-slate-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                {{ $event->event_start_date->format('l, d F Y • H:i') }} WIB
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $event->venue }}, {{ $event->city }}
                            </div>
                        </div>

                        <div class="pt-4" x-data="{ expanded: false }">
                            <h3 class="font-bold text-slate-900 mb-2" x-text="lang === 'id' ? 'Deskripsi' : 'Description'"></h3>
                            <div class="text-sm text-slate-600 leading-relaxed" :class="expanded ? '' : 'line-clamp-2'">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                            <button @click="expanded = !expanded" class="text-blue-600 text-sm font-bold mt-1 hover:underline">
                                <span x-show="!expanded" x-text="lang === 'id' ? 'Selengkapnya...' : 'Read More...'"></span>
                                <span x-show="expanded" x-text="lang === 'id' ? 'Tutup' : 'Close'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-6xl mx-auto px-4 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Ticket List Section -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-center border-b border-blue-500 mb-6">
                        <button class="px-8 py-3 text-blue-600 font-bold border-b-4 border-blue-600">Tiket</button>
                    </div>

                    <div class="space-y-4">
                        @foreach($event->ticketCategories as $category)
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm transition hover:shadow-md">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-lg text-slate-900">{{ $category->name }}</h3>
                                        <div class="text-xs text-orange-400 font-medium flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg>
                                            Tutup pada {{ $category->sale_end_at ? $category->sale_end_at->format('d M Y, H:i') : 'Selesai' }} WIB
                                        </div>
                                    </div>
                                    <button @click="showModalSK = true" class="text-blue-500 text-xs font-bold hover:underline uppercase tracking-wider">S&K</button>
                                </div>
 
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-xl font-black text-slate-900 font-outfit">
                                            Rp {{ number_format($category->price, 0, ',', '.') }}<span class="text-xs text-slate-400 font-normal">/tiket</span>
                                        </div>
                                        <div class="mt-2">
                                            @if($category->badge_text)
                                                <span class="px-3 py-1 bg-rose-50 text-rose-500 text-[10px] font-bold rounded-full">{{ $category->badge_text }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div>
                                        <select 
                                            @change="selectTicket({ id: {{ $category->id }}, name: '{{ $category->name }}', price: {{ $category->price }}, nik_restriction: '{{ $category->nik_restriction }}', nik_restriction_message: '{{ $category->nik_restriction_message }}' }, $event.target.value)"
                                            class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sidebar Summary & Form Section -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8 space-y-4">
                        
                        <!-- Step 1: Summary & NIK -->
                        <div x-show="step === 1" class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-blue-900/5 border border-slate-50 space-y-6 animate-in fade-in slide-in-from-right-4 duration-300">
                            <div class="space-y-4">
                                <div class="text-sm font-medium text-slate-500" x-text="selectedCategory ? selectedCategory.name : 'Pilih Tiket'"></div>
                                <div class="flex justify-between items-center" x-show="selectedCategory">
                                    <div class="text-slate-400 text-sm" x-text="selectedCategory ? quantity + ' x ' + Number(selectedCategory.price).toLocaleString('id-ID') : ''"></div>
                                </div>
                                
                                <div class="flex justify-between items-end pt-4 border-t border-slate-100">
                                    <div class="text-slate-400 text-sm font-bold">Jumlah (<span x-text="quantity">0</span> tiket)</div>
                                    <div class="text-2xl font-black text-slate-900 font-outfit">
                                        Rp <span x-text="selectedCategory ? (selectedCategory.price * quantity).toLocaleString('id-ID') : '0'">0</span>
                                    </div>
                                </div>

                                <!-- Promo Code Section -->
                                <div class="pt-4 border-t border-slate-50 space-y-3" x-show="quantity > 0">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Punya Kode Promo?</label>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="promoCode" placeholder="Masukkan kode" 
                                               class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:border-blue-500 outline-none uppercase">
                                        <button @click="applyPromo" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                                            Gunakan
                                        </button>
                                    </div>
                                    <p x-show="promoMessage" :class="promoStatus === 'success' ? 'text-emerald-600' : 'text-rose-600'" class="text-[10px] font-bold" x-text="promoMessage"></p>
                                    
                                    <div class="flex justify-between items-center text-emerald-600 font-bold text-sm" x-show="discount > 0">
                                        <span>Potongan Promo</span>
                                        <span>- Rp <span x-text="discount.toLocaleString('id-ID')"></span></span>
                                    </div>
                                </div>
                                
                                <div class="flex justify-between items-center pt-4 border-t border-blue-100" x-show="discount > 0">
                                    <div class="text-blue-600 text-sm font-black uppercase">Total Bayar</div>
                                    <div class="text-2xl font-black text-blue-600 font-outfit">
                                        Rp <span x-text="total.toLocaleString('id-ID')">0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3 pt-4 border-t border-slate-50">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Data Pemesan (NIK)</label>
                                <div class="relative">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-4 0a2 2 0 012-2h2a2 2 0 012 2v1m-6 0h6"/></svg>
                                    </div>
                                    <input type="text" x-model="nik" placeholder="Masukkan 16 Digit NIK" maxlength="16"
                                           class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-4 font-bold text-slate-700 focus:border-blue-500 focus:bg-white transition-all outline-none">
                                </div>
                            </div>

                            <button @click="goToPayment" 
                                    :disabled="!selectedCategory || quantity === 0"
                                    class="w-full py-4 bg-blue-500 disabled:bg-blue-200 text-white rounded-2xl font-black shadow-lg shadow-blue-200 hover:bg-blue-600 transition-all transform active:scale-95">
                                Lanjut Pembayaran
                            </button>
                        </div>

                        <!-- Step 2: Customer Details & Payment -->
                        <div x-show="step === 2" x-cloak class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-blue-900/5 border border-slate-50 space-y-6 animate-in fade-in slide-in-from-right-4 duration-300">
                            <div class="flex items-center gap-2 mb-4">
                                <button @click="step = 1" class="p-2 hover:bg-slate-50 rounded-full transition">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <h3 class="font-bold text-slate-900 font-outfit">Detail Pembayaran</h3>
                            </div>

                            <form @submit.prevent="submitBooking" class="space-y-4">
                                <div class="space-y-3">
                                    <input type="text" x-model="name" placeholder="Nama Lengkap" required
                                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                    <input type="text" x-model="phone" placeholder="Nomor HP / WhatsApp" required
                                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                    <input type="email" x-model="email" placeholder="Alamat Email" required
                                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>

                                <div class="space-y-2 pt-2">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" x-model="notifWA" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-xs text-slate-600 font-medium">Kirim notifikasi ke WhatsApp</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" x-model="notifEmail" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-xs text-slate-600 font-medium">Kirim notifikasi ke Email</span>
                                    </label>
                                </div>

                                <div class="pt-4">
                                    <div class="flex justify-between items-center mb-4 p-4 bg-slate-50 rounded-2xl">
                                        <span class="text-xs font-bold text-slate-500">Total Pembayaran</span>
                                        <span class="text-xl font-black text-blue-600 font-outfit">Rp <span x-text="total.toLocaleString('id-ID')"></span></span>
                                    </div>
                                    <button type="submit" 
                                            :disabled="isSubmitting"
                                            class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black shadow-lg shadow-blue-200 hover:bg-blue-700 transition transform active:scale-95 disabled:bg-slate-300 flex items-center justify-center gap-3">
                                        <template x-if="isSubmitting">
                                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="isSubmitting ? 'Memproses...' : 'Bayar Sekarang'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Modal S&K -->
    <div x-show="showModalSK" 
         x-cloak
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-300">
        <div @click.away="showModalSK = false" 
             class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300">
            <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-900 font-outfit">Syarat & Ketentuan</h3>
                <button @click="showModalSK = false" class="p-2 hover:bg-white rounded-full transition shadow-sm">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-8 max-h-[60vh] overflow-y-auto text-slate-600 leading-relaxed space-y-4">
                @if($event->terms_conditions)
                    {!! nl2br(e($event->terms_conditions)) !!}
                @else
                    <div class="text-center py-10 space-y-4">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto text-blue-500">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="font-medium">Belum ada Syarat & Ketentuan khusus untuk event ini.</p>
                    </div>
                @endif
            </div>
            <div class="p-8 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button @click="showModalSK = false" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>
</body>
</html>

