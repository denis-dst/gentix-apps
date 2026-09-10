<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('fan.dashboard') }}" class="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Fan Game Zone: Tebak Skor & Kuis Klub
                </h2>
                <p class="text-sm text-slate-500 font-medium">Mainkan game tebak skor dan ikuti kuis untuk mendapatkan Poin Suporter.</p>
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

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-3 font-semibold text-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Section 1: Tebak Skor Matchday -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 font-outfit">
                            ⚽ Tebak Skor Pertandingan Mendatang
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tebak skor pertandingan kandang sebelum kick-off dimulai. Dapatkan hingga 100 Poin untuk tebakan yang benar!</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($upcomingMatches as $match)
                        @php $hasPredicted = in_array($match->id, $myPredictions); @endphp
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between">
                            <div>
                                <div class="text-xs text-slate-400 font-semibold mb-2">{{ $match->event_start_date->format('l, d M Y • H:i') }} WIB</div>
                                <h4 class="font-bold text-slate-900 text-sm text-center py-3 bg-white rounded-xl border border-slate-100 mb-4">
                                    {{ $match->home_team_name ?: 'Home Team' }} <span class="text-orange-500 font-mono font-black">VS</span> {{ $match->away_team_name ?: 'Away Team' }}
                                </h4>
                            </div>

                            @if($hasPredicted)
                                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center text-xs font-bold text-emerald-800">
                                    ✓ Tebakan Anda Sudah Tersimpan
                                </div>
                            @else
                                <form action="{{ route('fan.game-zone.predict', $match) }}" method="POST" class="space-y-3">
                                    @csrf
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-black uppercase text-slate-500 mb-1 text-center">{{ $match->home_team_name ?: 'Home' }}</label>
                                            <input type="number" name="home_score" min="0" max="20" required class="w-full rounded-xl border-slate-200 text-sm font-bold text-center">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black uppercase text-slate-500 mb-1 text-center">{{ $match->away_team_name ?: 'Away' }}</label>
                                            <input type="number" name="away_score" min="0" max="20" required class="w-full rounded-xl border-slate-200 text-sm font-bold text-center">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition">
                                        Kirim Tebakan (+100 Pts)
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-slate-400 text-sm">
                            Belum ada jadwal pertandingan aktif untuk tebak skor.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Section 2: Kuis Trivia Klub -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-black uppercase tracking-tight text-slate-900 font-outfit">
                            🧠 Kuis Trivia Klub
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Uji pengetahuan Anda seputar sejarah klub dan pemain kebanggaan.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($quizzes as $quiz)
                        @php $participant = $quiz->participants->first(); @endphp
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/80 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-black uppercase rounded-full">
                                    ★ Hadiah {{ $quiz->points_reward }} Poin
                                </span>
                                <span class="text-xs text-slate-400">Berakhir: {{ $quiz->expires_at->format('d M H:i') }}</span>
                            </div>

                            <h4 class="font-bold text-slate-900 text-base">{{ $quiz->title }}</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">{{ $quiz->description }}</p>

                            @if($participant)
                                <div class="p-3 bg-slate-200/60 rounded-xl text-center text-xs font-bold text-slate-700">
                                    Anda sudah menyelesaikan kuis ini (Skor: {{ $participant->score }}%) • +{{ $participant->points_earned }} Pts Didapatkan
                                </div>
                            @else
                                <form action="{{ route('fan.game-zone.quiz', $quiz) }}" method="POST" class="space-y-4 pt-2 border-t border-slate-200">
                                    @csrf
                                    @foreach($quiz->questions ?? [] as $idx => $q)
                                        <div class="space-y-2">
                                            <p class="text-xs font-bold text-slate-800">{{ $idx + 1 }}. {{ $q['question'] }}</p>
                                            <div class="space-y-1">
                                                @foreach($q['options'] ?? [] as $optIdx => $opt)
                                                    <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-slate-200 text-xs font-medium cursor-pointer hover:bg-orange-50">
                                                        <input type="radio" name="answers[{{ $idx }}]" value="{{ $optIdx }}" required class="text-orange-600">
                                                        <span>{{ $opt }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-black uppercase tracking-wider transition">
                                        Kirim Jawaban Kuis
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-8 text-slate-400 text-sm">
                            Belum ada kuis aktif saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
