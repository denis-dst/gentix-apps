<x-app-layout>
    <x-slot name="title">Kelola Syarat & Ketentuan</x-slot>

    <div class="max-w-4xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-black text-slate-900 font-outfit">Kelola S & K</h2>
                <p class="text-slate-500 mt-1">Syarat & Ketentuan global ini akan berlaku untuk seluruh event yang Anda kelola.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-4 rounded-2xl flex items-center gap-3 animate-in zoom-in duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <form action="{{ route('organizer.settings.terms.update') }}" method="POST" class="p-8 sm:p-12 space-y-8">
                @csrf
                
                <!-- Quill CSS -->
                <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                
                <div class="space-y-4">
                    <label for="terms_conditions" class="block text-sm font-black text-slate-700 uppercase tracking-widest">Konten Syarat & Ketentuan</label>
                    <div class="bg-white rounded-3xl border-2 border-slate-100 overflow-hidden shadow-sm focus-within:border-indigo-500 transition-all">
                        <!-- Quill Editor Container -->
                        <div id="editor" style="height: 400px; border: none;" class="text-slate-600 leading-relaxed">
                            {!! old('terms_conditions', $tenant->terms_conditions) !!}
                        </div>
                    </div>
                    <!-- Hidden input to store Quill HTML content -->
                    <input type="hidden" name="terms_conditions" id="terms_conditions">
                    
                    @error('terms_conditions')
                        <p class="text-rose-500 text-xs font-bold mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quill JS -->
                <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
                <script>
                    var quill = new Quill('#editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                [{ 'header': [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                ['link', 'clean']
                            ]
                        },
                        placeholder: 'Tuliskan syarat & ketentuan di sini...'
                    });

                    // Sync Quill content to hidden input
                    var termsInput = document.getElementById('terms_conditions');
                    
                    quill.on('text-change', function() {
                        termsInput.value = quill.root.innerHTML;
                    });

                    // Also sync on form submit just in case
                    var form = termsInput.closest('form');
                    form.addEventListener('submit', function() {
                        termsInput.value = quill.root.innerHTML;
                    });

                    // Initialize input value
                    termsInput.value = quill.root.innerHTML;

                    // Customize toolbar styling
                    var toolbar = document.querySelector('.ql-toolbar');
                    if (toolbar) {
                        toolbar.style.border = 'none';
                        toolbar.style.borderBottom = '1px solid #f1f5f9';
                        toolbar.style.padding = '1rem';
                    }
                    var container = document.querySelector('.ql-container');
                    if (container) {
                        container.style.border = 'none';
                        container.style.fontSize = '14px';
                        container.style.fontFamily = '"Plus Jakarta Sans", sans-serif';
                    }
                </script>

                <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 flex gap-4 items-start">
                    <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-indigo-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="text-sm text-indigo-900 leading-relaxed">
                        <p class="font-bold mb-1">Informasi Penting:</p>
                        S&K ini akan muncul secara otomatis di e-voucher semua event Anda. Jika sebuah event memiliki S&K khusus yang diatur pada halaman edit event, maka S&K khusus tersebut yang akan ditampilkan.
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition transform active:scale-95">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
