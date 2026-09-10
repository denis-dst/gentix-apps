<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Gamifikasi Suporter: Tebak Skor & Kuis Klub
                </h2>
                <p class="text-sm text-slate-500 font-medium">
                    Input hasil skor pertandingan untuk mendistribusikan poin reward suporter, dan terbitkan kuis trivia klub.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Section 1: Update Match Score (Tebak Skor) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 font-outfit">
                            Pertandingan & Hasil Tebak Skor
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Input skor akhir saat pertandingan selesai. Sistem otomatis menghitung tebakan benar dan mendistribusikan poin ke saldo suporter.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($events as $event)
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3 text-xs text-slate-400 font-semibold">
                                    <span>{{ $event->season_name ?: 'Liga Match' }}</span>
                                    <span>{{ $event->event_start_date->format('d M Y, H:i') }}</span>
                                </div>

                                <div class="text-center py-4 bg-white rounded-xl border border-slate-100 mb-4">
                                    <p class="text-sm font-black text-slate-900">
                                        {{ $event->home_team_name ?: 'Home Team' }} 
                                        <span class="text-orange-500 px-2 font-mono text-base">{{ is_null($event->home_score) ? 'VS' : "{$event->home_score} - {$event->away_score}" }}</span>
                                        {{ $event->away_team_name ?: 'Away Team' }}
                                    </p>
                                </div>
                            </div>

                            <form action="{{ route('organizer.gamification.update-score', $event) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Skor Home</label>
                                        <input type="number" name="home_score" value="{{ $event->home_score ?? 0 }}" min="0" required class="w-full rounded-lg border-slate-200 text-xs font-bold text-center">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Skor Away</label>
                                        <input type="number" name="away_score" value="{{ $event->away_score ?? 0 }}" min="0" required class="w-full rounded-lg border-slate-200 text-xs font-bold text-center">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Hadiah Poin Pemenang</label>
                                    <input type="number" name="reward_points" value="100" min="10" required class="w-full rounded-lg border-slate-200 text-xs font-bold">
                                </div>

                                <button type="submit" class="w-full py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition">
                                    Simpan & Bagikan Poin
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-slate-400 text-sm">
                            Belum ada jadwal pertandingan match sepakbola yang terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Section 2: Kuis Trivia Klub -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6" x-data="{ showQuizModal: false }">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 font-outfit">
                            Kuis Trivia & Mini Game Suporter
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kuis seputar sejarah klub dan pemain untuk meningkatkan engagement suporter.</p>
                    </div>
                    <button type="button" @click="showQuizModal = true" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                        + Buat Kuis Baru
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($quizzes as $quiz)
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-[10px] font-black uppercase rounded-full">
                                        ★ {{ $quiz->points_reward }} Poin
                                    </span>
                                    <span class="text-xs text-slate-400 font-semibold">{{ $quiz->participants_count }} Peserta</span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm mb-1">{{ $quiz->title }}</h4>
                                <p class="text-xs text-slate-500 mb-4 leading-relaxed">{{ $quiz->description }}</p>
                            </div>
                            <div class="text-[11px] text-slate-400 border-t border-slate-200 pt-3">
                                Berakhir: {{ $quiz->expires_at->format('d M Y H:i') }}
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-slate-400 text-sm">
                            Belum ada kuis yang dibuat.
                        </div>
                    @endforelse
                </div>

                <!-- Modal Buat Kuis -->
                <div x-show="showQuizModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
                    <div class="bg-white rounded-3xl p-8 max-w-xl w-full max-h-[90vh] overflow-y-auto space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <h3 class="text-base font-black uppercase text-slate-900 font-outfit">Buat Kuis Klub Baru</h3>
                            <button type="button" @click="showQuizModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                        </div>

                        <form action="{{ route('organizer.gamification.store-quiz') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-black uppercase text-slate-700 mb-1">Judul Kuis</label>
                                <input type="text" name="title" required placeholder="e.g. Trivia Sejarah Juara Klub" class="w-full rounded-xl border-slate-200 text-xs font-semibold">
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase text-slate-700 mb-1">Deskripsi</label>
                                <textarea name="description" rows="2" class="w-full rounded-xl border-slate-200 text-xs"></textarea>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-black uppercase text-slate-700 mb-1">Mulai</label>
                                    <input type="datetime-local" name="starts_at" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full rounded-xl border-slate-200 text-xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase text-slate-700 mb-1">Berakhir</label>
                                    <input type="datetime-local" name="expires_at" value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}" required class="w-full rounded-xl border-slate-200 text-xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase text-slate-700 mb-1">Hadiah Poin</label>
                                    <input type="number" name="points_reward" value="50" min="10" required class="w-full rounded-xl border-slate-200 text-xs font-bold">
                                </div>
                            </div>

                            <!-- Single Sample Question -->
                            <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50 space-y-3">
                                <label class="block text-xs font-black uppercase text-slate-800">Pertanyaan #1</label>
                                <input type="text" name="questions[0][question]" value="Pada tahun berapa klub pertama kali didirikan?" required class="w-full rounded-xl border-slate-200 text-xs font-semibold">
                                
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="questions[0][correct_index]" value="0" checked class="text-orange-600">
                                        <input type="text" name="questions[0][options][0]" value="1933" required class="flex-1 rounded-xl border-slate-200 text-xs">
                                        <span class="text-[10px] text-emerald-600 font-bold">(Kunci Jawaban)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="questions[0][correct_index]" value="1" class="text-orange-600">
                                        <input type="text" name="questions[0][options][1]" value="1950" required class="flex-1 rounded-xl border-slate-200 text-xs">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="questions[0][correct_index]" value="2" class="text-orange-600">
                                        <input type="text" name="questions[0][options][2]" value="1970" required class="flex-1 rounded-xl border-slate-200 text-xs">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                                <button type="button" @click="showQuizModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold">Batal</button>
                                <button type="submit" class="px-5 py-2 bg-orange-600 text-white rounded-xl text-xs font-bold shadow-lg shadow-orange-500/20">Simpan Kuis</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
