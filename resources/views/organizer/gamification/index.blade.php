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
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Section 1: Update Match Score (Tebak Skor) -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 font-outfit">
                            Pertandingan & Hasil Tebak Skor
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Input skor akhir saat pertandingan selesai. Sistem otomatis menghitung tebakan benar dan mendistribusikan poin ke saldo suporter.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($events as $event)
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-3 text-xs text-slate-500 font-semibold">
                                    <span>{{ $event->season_name ?: 'Liga Match' }}</span>
                                    <span>{{ $event->event_start_date->format('d M Y, H:i') }}</span>
                                </div>

                                <div class="text-center py-4 bg-white rounded-xl border border-slate-200 mb-4">
                                    <p class="text-sm font-black text-slate-900">
                                        {{ $event->home_team_name ?: 'Home Team' }} 
                                        <span class="text-orange-600 px-2 font-mono text-base">{{ is_null($event->home_score) ? 'VS' : "{$event->home_score} - {$event->away_score}" }}</span>
                                        {{ $event->away_team_name ?: 'Away Team' }}
                                    </p>
                                </div>
                            </div>

                            <form action="{{ route('organizer.gamification.update-score', $event) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Skor Home</label>
                                        <input type="number" name="home_score" value="{{ $event->home_score ?? 0 }}" min="0" required class="w-full rounded-xl border-slate-300 text-xs font-bold text-center py-2 focus:ring-2 focus:ring-orange-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Skor Away</label>
                                        <input type="number" name="away_score" value="{{ $event->away_score ?? 0 }}" min="0" required class="w-full rounded-xl border-slate-300 text-xs font-bold text-center py-2 focus:ring-2 focus:ring-orange-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Hadiah Poin Pemenang</label>
                                    <input type="number" name="reward_points" value="100" min="10" required class="w-full rounded-xl border-slate-300 text-xs font-bold py-2 focus:ring-2 focus:ring-orange-500">
                                </div>

                                <button type="submit" class="w-full min-h-[44px] py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition focus:ring-2 focus:ring-orange-500">
                                    Simpan & Bagikan Poin
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-slate-500 text-sm">
                            Belum ada jadwal pertandingan match sepakbola yang terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Section 2: Kuis Trivia Klub -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6" x-data="{ showQuizModal: false }">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 font-outfit">
                            Kuis Trivia & Mini Game Suporter
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kuis seputar sejarah klub dan pemain untuk meningkatkan engagement suporter.</p>
                    </div>
                    <button type="button" @click="showQuizModal = true" class="min-h-[44px] px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition focus:ring-2 focus:ring-slate-700">
                        + Buat Kuis Baru
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($quizzes as $quiz)
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-950 text-[10px] font-black uppercase rounded-full">
                                        {{ $quiz->points_reward }} Poin
                                    </span>
                                    <span class="text-xs text-slate-500 font-semibold">{{ $quiz->participants_count }} Peserta</span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm mb-1">{{ $quiz->title }}</h4>
                                <p class="text-xs text-slate-600 mb-4 leading-relaxed">{{ $quiz->description }}</p>
                            </div>
                            <div class="text-xs text-slate-500 border-t border-slate-200 pt-3">
                                Berakhir: {{ $quiz->expires_at->format('d M Y H:i') }}
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-slate-500 text-sm">
                            Belum ada kuis yang dibuat.
                        </div>
                    @endforelse
                </div>

                <!-- Modal Buat Kuis -->
                <div x-show="showQuizModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm" x-cloak>
                    <div class="bg-white rounded-2xl p-6 sm:p-8 max-w-xl w-full max-h-[90vh] overflow-y-auto space-y-6 shadow-2xl">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <h3 class="text-base font-black uppercase text-slate-900 font-outfit">Buat Kuis Klub Baru</h3>
                            <button type="button" @click="showQuizModal = false" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center text-slate-400 hover:text-slate-600">✕</button>
                        </div>

                        <form action="{{ route('organizer.gamification.store-quiz') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="quiz_title" class="block text-xs font-bold uppercase text-slate-700 mb-1">Judul Kuis</label>
                                <input type="text" id="quiz_title" name="title" required placeholder="Contoh: Trivia Sejarah Juara Klub" class="w-full rounded-xl border-slate-300 text-xs font-semibold py-2.5">
                            </div>
                            <div>
                                <label for="quiz_description" class="block text-xs font-bold uppercase text-slate-700 mb-1">Deskripsi</label>
                                <textarea id="quiz_description" name="description" rows="2" class="w-full rounded-xl border-slate-300 text-xs py-2"></textarea>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="quiz_starts" class="block text-xs font-bold uppercase text-slate-700 mb-1">Mulai</label>
                                    <input type="datetime-local" id="quiz_starts" name="starts_at" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full rounded-xl border-slate-300 text-xs py-2">
                                </div>
                                <div>
                                    <label for="quiz_expires" class="block text-xs font-bold uppercase text-slate-700 mb-1">Berakhir</label>
                                    <input type="datetime-local" id="quiz_expires" name="expires_at" value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}" required class="w-full rounded-xl border-slate-300 text-xs py-2">
                                </div>
                                <div>
                                    <label for="quiz_reward" class="block text-xs font-bold uppercase text-slate-700 mb-1">Hadiah Poin</label>
                                    <input type="number" id="quiz_reward" name="points_reward" value="50" min="5" required class="w-full rounded-xl border-slate-300 text-xs font-bold py-2">
                                </div>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-slate-100">
                                <h4 class="text-xs font-bold uppercase text-slate-900">Pertanyaan Kuis (Pilihan Ganda)</h4>
                                
                                @for($i = 0; $i < 2; $i++)
                                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Pertanyaan #{{ $i + 1 }}</label>
                                            <input type="text" name="questions[{{ $i }}][question]" required placeholder="Pertanyaan trivia..." class="w-full rounded-xl border-slate-300 text-xs py-2">
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            @for($opt = 0; $opt < 4; $opt++)
                                                <div>
                                                    <input type="text" name="questions[{{ $i }}][options][{{ $opt }}]" required placeholder="Pilihan {{ chr(65 + $opt) }}" class="w-full rounded-lg border-slate-300 text-xs py-1.5">
                                                </div>
                                            @endfor
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-600 mb-1">Jawaban Benar</label>
                                            <select name="questions[{{ $i }}][correct_answer]" required class="w-full rounded-lg border-slate-300 text-xs py-1.5">
                                                <option value="0">Pilihan A</option>
                                                <option value="1">Pilihan B</option>
                                                <option value="2">Pilihan C</option>
                                                <option value="3">Pilihan D</option>
                                            </select>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                                <button type="button" @click="showQuizModal = false" class="min-h-[44px] px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">Batal</button>
                                <button type="submit" class="min-h-[44px] px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold shadow transition">Terbitkan Kuis</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
