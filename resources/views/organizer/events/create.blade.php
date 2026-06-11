<x-app-layout>
    <x-slot name="title">Buat Event Baru</x-slot>
    <x-slot name="header">Buat Event</x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex items-center gap-6">
                <div class="w-16 h-16 bg-orange-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-orange-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-800 font-outfit">Detail Event Baru</h3>
                    <p class="text-sm text-slate-500 font-medium">Lengkapi informasi dasar event Anda.</p>
                </div>
            </div>

            <form action="{{ route('organizer.events.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                @csrf

                @if ($errors->any())
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-2xl mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">Terdapat beberapa kesalahan pengisian form:</h3>
                                <ul class="mt-2 list-disc list-inside text-xs text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Nama Event</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Konser Musik Bhayangkara" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-4 px-6 text-lg font-bold" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Deskripsi Event</label>
                        <textarea name="description" rows="5" placeholder="Ceritakan tentang event ini..." class="w-full rounded-2xl border-slate-200 focus-within:border-purple-500 focus:ring-purple-500 transition py-4 px-6 text-sm">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Venue / Lokasi</label>
                            <input type="text" name="venue" placeholder="Contoh: Stadion Utama" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Kota</label>
                            <input type="text" name="city" placeholder="Contoh: Bandar Lampung" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Link Google Maps</label>
                        <input type="url" name="google_maps_url" value="{{ old('google_maps_url') }}" placeholder="https://maps.google.com/..." class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Waktu Mulai</label>
                            <input type="datetime-local" name="event_start_date" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Waktu Selesai</label>
                            <input type="datetime-local" name="event_end_date" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Security Code (6 Digit PIN)</label>
                        <div class="flex gap-2">
                            <input type="text" name="security_code" id="security_code" placeholder="Akan di-generate otomatis jika kosong" class="flex-1 rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5 font-mono font-bold text-center tracking-[0.3em]" maxlength="6">
                            <button type="button" onclick="generatePIN()" class="px-4 bg-orange-50 text-orange-600 rounded-2xl border border-orange-200 hover:bg-orange-100 transition text-xs font-black uppercase">Generate</button>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2 italic px-1">Kode ini diperlukan kru untuk masuk ke mode scan. Biarkan kosong untuk generate otomatis.</p>
                    </div>

                    <script>
                        function generatePIN() {
                            const pin = Math.floor(100000 + Math.random() * 900000);
                            document.getElementById('security_code').value = pin;
                        }
                    </script>

                    <div class="pt-6 border-t border-slate-50 space-y-4">
                        <h4 class="text-sm font-black text-slate-700 uppercase tracking-[0.18em]">Wristband Layout</h4>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">League Name</label>
                            <input type="text" name="wristband_league_name" value="{{ old('wristband_league_name', 'BRI Super League 2025-26') }}" class="w-full rounded-2xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition py-3 px-5">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">League Logo</label>
                                <input type="file" name="wristband_league_logo" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Home Club Logo</label>
                                <input type="file" name="wristband_home_club_logo" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Away Club Logo</label>
                                <input type="file" name="wristband_away_club_logo" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Sponsor Logos</label>
                            <input type="file" name="wristband_sponsor_logos[]" multiple class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Informasi E-Voucher Tambahan (Dinamis)</label>
                        <textarea name="evoucher_info" rows="3" placeholder="Contoh: Gabung ke Group Whatsapp Peserta melalui link berikut: https://chat.whatsapp.com/..." class="w-full rounded-2xl border-slate-200 focus-within:border-purple-500 focus:ring-purple-500 transition py-3 px-5 text-sm">{{ old('evoucher_info') }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1 italic px-1">Informasi ini akan ditampilkan di bagian paling atas halaman E-Voucher peserta. Link URL akan otomatis menjadi link klik.</p>
                    </div>

                    {{-- ============================================================
                         FREE EVENT OPTIONS
                         ============================================================ --}}
                    <div class="pt-6 border-t-2 border-dashed border-emerald-100 space-y-4" x-data="{ isFree: false }">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-black text-slate-700 uppercase tracking-[0.18em]">⚡ Mode Event</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Pilih apakah event ini berbayar atau gratis</p>
                            </div>
                        </div>

                        <!-- Free Event Toggle -->
                        <label class="flex items-center gap-4 p-5 bg-emerald-50 border-2 border-emerald-200 rounded-2xl cursor-pointer transition hover:border-emerald-400"
                               :class="isFree ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 bg-slate-50'">
                            <div class="relative">
                                <input type="checkbox" name="is_free" id="is_free" value="1" 
                                       x-model="isFree"
                                       class="sr-only">
                                <div :class="isFree ? 'bg-emerald-500' : 'bg-slate-300'" class="w-12 h-6 rounded-full transition-colors duration-200">
                                    <div :class="isFree ? 'translate-x-6' : 'translate-x-1'" class="w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 mt-1"></div>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-800">Event Gratis (Tanpa Pembayaran)</p>
                                <p class="text-xs text-slate-500">Peserta langsung mendapatkan E-Voucher setelah isi form registrasi</p>
                            </div>
                        </label>

                        <!-- Max tickets per transaction (shown if is_free) -->
                        <div x-show="isFree" x-cloak class="p-5 bg-emerald-50/50 border border-emerald-200 rounded-2xl space-y-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Jumlah Maksimal Tiket per Transaksi</label>
                            <input type="number" name="max_tickets_per_transaction" value="1" min="1" max="100" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 transition py-2.5 px-4 font-bold">
                            <p class="text-[10px] text-slate-400 mt-1">Batasi berapa tiket yang dapat dipesan dalam satu pendaftaran gratis.</p>
                        </div>

                        <!-- Umroh Question Option (shown if is_free) -->
                        <div x-show="isFree" x-cloak class="p-5 bg-amber-50 border border-amber-200 rounded-2xl space-y-3">
                            <label class="flex items-center gap-4 cursor-pointer">
                                <input type="checkbox" name="umroh_question_enabled" id="umroh_question_enabled" value="1"
                                       class="w-5 h-5 rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                                <div>
                                    <p class="text-sm font-black text-amber-800">🕌 Aktifkan Pertanyaan Umroh</p>
                                    <p class="text-xs text-amber-600">Tambahkan pertanyaan: "Pernah Ikut Umroh Bersama Batik Travel Kapan?"</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-50 space-y-4">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2">Syarat & Ketentuan (S&K)</label>
                        
                        <!-- Quill Editor Wrapper -->
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden focus-within:border-purple-500 transition-all">
                            <div id="terms_editor" style="height: 250px; border: none;" class="text-sm text-slate-600">
                                {!! old('terms_conditions') !!}
                            </div>
                        </div>
                        <!-- Hidden input for Quill -->
                        <input type="hidden" name="terms_conditions" id="terms_conditions_input" value="{{ old('terms_conditions') }}">
                        
                        <p class="text-[10px] text-slate-400 mt-1 italic px-1">Kosongkan jika ingin menggunakan S&K Global Tenant. S&K ini akan muncul di e-voucher.</p>
                        
                        <script>
                            (function() {
                                function loadAsset(url, type) {
                                    return new Promise((resolve, reject) => {
                                        if (type === 'css') {
                                            if (document.querySelector(`link[href="${url}"]`)) {
                                                resolve();
                                                return;
                                            }
                                            const link = document.createElement('link');
                                            link.rel = 'stylesheet';
                                            link.href = url;
                                            link.onload = resolve;
                                            link.onerror = reject;
                                            document.head.appendChild(link);
                                        } else if (type === 'js') {
                                            if (window.Quill) {
                                                resolve();
                                                return;
                                            }
                                            let script = document.querySelector(`script[src="${url}"]`);
                                            if (script) {
                                                if (script.dataset.loaded === 'true') {
                                                    resolve();
                                                } else {
                                                    script.addEventListener('load', resolve);
                                                    script.addEventListener('error', reject);
                                                }
                                                return;
                                            }
                                            script = document.createElement('script');
                                            script.src = url;
                                            script.dataset.loaded = 'false';
                                            script.onload = () => {
                                                script.dataset.loaded = 'true';
                                                resolve();
                                            };
                                            script.onerror = reject;
                                            document.head.appendChild(script);
                                        }
                                    });
                                }

                                function initEditor() {
                                    const editorEl = document.getElementById('terms_editor');
                                    const inputEl = document.getElementById('terms_conditions_input');
                                    if (!editorEl || !inputEl) {
                                        setTimeout(initEditor, 50);
                                        return;
                                    }
                                    if (editorEl.dataset.initialized === 'true') {
                                        return;
                                    }
                                    editorEl.dataset.initialized = 'true';

                                    Promise.all([
                                        loadAsset('https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css', 'css'),
                                        loadAsset('https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js', 'js')
                                    ]).then(() => {
                                        if (typeof Quill === 'undefined') return;
                                        
                                        var quill = new Quill(editorEl, {
                                            theme: 'snow',
                                            modules: {
                                                toolbar: [
                                                    ['bold', 'italic', 'underline'],
                                                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                                    ['link', 'clean']
                                                ]
                                            },
                                            placeholder: 'Tuliskan S&K khusus event ini...'
                                        });

                                        if (quill.root.innerHTML === '<p><br></p>' && inputEl.value) {
                                            quill.root.innerHTML = inputEl.value;
                                        }

                                        quill.on('text-change', function() {
                                            inputEl.value = quill.root.innerHTML;
                                        });

                                        const form = inputEl.closest('form');
                                        if (form) {
                                            form.addEventListener('submit', function() {
                                                inputEl.value = quill.root.innerHTML;
                                            });
                                        }

                                        // Style toolbar
                                        setTimeout(() => {
                                            var toolbar = editorEl.previousElementSibling;
                                            if (toolbar && toolbar.classList.contains('ql-toolbar')) {
                                                toolbar.style.border = 'none';
                                                toolbar.style.borderBottom = '1px solid #f1f5f9';
                                            }
                                        }, 50);
                                    }).catch(err => console.error('Failed to load Quill:', err));
                                }

                                if (document.readyState === 'loading') {
                                    document.addEventListener('DOMContentLoaded', initEditor);
                                } else {
                                    initEditor();
                                }
                            })();
                        </script>
                    </div>

                </div>

                <div class="flex gap-4 pt-4 border-t border-slate-50 mt-8">
                    <a href="{{ route('organizer.events.index') }}" class="flex-1 py-4 px-6 bg-orange-50 border border-orange-200 text-orange-700 rounded-2xl font-bold hover:bg-orange-100 transition text-center shadow-sm">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 py-4 px-6 bg-orange-600 text-black rounded-2xl font-bold hover:bg-orange-700 transition shadow-lg shadow-orange-200 flex items-center justify-center gap-2">
                        Lanjut ke Pengaturan Tiket
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
