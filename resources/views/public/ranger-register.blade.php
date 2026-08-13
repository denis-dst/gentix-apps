<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pendaftaran Ranger Bhayangkara FC 2026/2027</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19;
            color: #f8fafc;
        }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .form-input {
            background-color: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            transition: all 0.2s ease-in-out;
        }
        .form-input:focus {
            outline: none;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.25);
            background-color: rgba(30, 41, 59, 1);
        }
        .glow-bg {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.15) 0%, rgba(249, 115, 22, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
    </style>
</head>
<body class="antialiased min-h-screen relative flex flex-col justify-between py-10 px-4 sm:px-6 lg:px-8 selection:bg-orange-500 selection:text-white">

    <!-- Background Decoration Glows -->
    <div class="glow-bg -top-20 -left-20"></div>
    <div class="glow-bg bottom-0 right-0"></div>

    <div class="max-w-2xl mx-auto w-full relative z-10 my-auto">
        <!-- Header / Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-3 mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-xl shadow-orange-500/20 ring-4 ring-orange-500/10">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <span class="inline-block px-3 py-1 bg-orange-500/10 border border-orange-500/30 rounded-full text-orange-400 text-xs font-bold tracking-widest uppercase mb-2">Form Registrasi Official</span>
            <h1 class="text-3xl sm:text-4xl font-black font-outfit uppercase tracking-tight text-white leading-tight">
                Ranger Bhayangkara FC <br class="hidden sm:inline"><span class="text-orange-500">2026/2027</span>
            </h1>
            <p class="mt-2 text-slate-400 text-sm sm:text-base max-w-md mx-auto">
                Isi formulir pendaftaran crew & ranger dengan benar untuk keperluan plotting operasional & payment fee.
            </p>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="glass-card rounded-3xl p-8 mb-8 text-center border-emerald-500/30 shadow-2xl animate-fade-in">
                <div class="w-16 h-16 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-500/30">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black font-outfit text-white mb-2">Registrasi Berhasil!</h3>
                <p class="text-slate-300 text-sm leading-relaxed mb-6">
                    {{ session('success') }}
                </p>
                <a href="{{ route('ranger.register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-slate-950 font-extrabold text-sm uppercase tracking-wider rounded-xl transition shadow-lg shadow-orange-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Daftar Ranger Lainnya</span>
                </a>
            </div>
        @else

        <!-- Main Registration Form Card -->
        <div class="glass-card p-6 sm:p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
            <!-- Form Header Badge -->
            <div class="flex items-center gap-3 pb-6 mb-6 border-b border-white/10">
                <div class="w-3 h-3 rounded-full bg-orange-500 animate-pulse"></div>
                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Lengkapi Formulir Pendaftaran</span>
            </div>

            <form action="{{ route('ranger.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- 1. Nama -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        1. Nama Lengkap <span class="text-orange-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}" 
                           placeholder="Contoh: Didi Zizie" 
                           required 
                           class="w-full form-input px-4 py-3.5 rounded-xl text-sm font-medium placeholder-slate-500">
                    @error('name')
                        <p class="mt-1 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Nomor Whatsapp -->
                <div>
                    <label for="whatsapp" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        2. Nomor WhatsApp <span class="text-orange-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="whatsapp" 
                               id="whatsapp" 
                               value="{{ old('whatsapp') }}" 
                               placeholder="Contoh: 081234567890" 
                               required 
                               class="w-full form-input px-4 py-3.5 rounded-xl text-sm font-medium placeholder-slate-500">
                    </div>
                    <p class="mt-1 text-[11px] text-slate-400">Pastikan nomor WhatsApp aktif untuk korespondensi jadwal & pos gate.</p>
                    @error('whatsapp')
                        <p class="mt-1 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 3. Nama Bank/E-Wallet -->
                <div>
                    <label for="bank_name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        3. Nama Bank / E-Wallet <span class="text-orange-500">*</span> <span class="text-[10px] text-orange-400 font-normal lowercase">(untuk payment fee)</span>
                    </label>
                    <input type="text" 
                           name="bank_name" 
                           id="bank_name" 
                           value="{{ old('bank_name') }}" 
                           placeholder="Contoh: Bank BCA / Mandiri / DANA / GoPay / OVO" 
                           required 
                           class="w-full form-input px-4 py-3.5 rounded-xl text-sm font-medium placeholder-slate-500">
                    @error('bank_name')
                        <p class="mt-1 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 4. Nomor Rekening/E-Wallet -->
                <div>
                    <label for="account_number" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        4. Nomor Rekening / Nomor E-Wallet <span class="text-orange-500">*</span> <span class="text-[10px] text-orange-400 font-normal lowercase">(untuk payment fee)</span>
                    </label>
                    <input type="text" 
                           name="account_number" 
                           id="account_number" 
                           value="{{ old('account_number') }}" 
                           placeholder="Contoh: 1234567890 a.n Didi Zizie" 
                           required 
                           class="w-full form-input px-4 py-3.5 rounded-xl text-sm font-medium placeholder-slate-500">
                    @error('account_number')
                        <p class="mt-1 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 5. Gender -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">
                        5. Gender (Jenis Kelamin) <span class="text-orange-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 rounded-xl border border-white/10 bg-slate-800/50 hover:bg-slate-800 cursor-pointer transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-500/10">
                            <input type="radio" 
                                   name="gender" 
                                   value="male" 
                                   {{ old('gender') === 'male' ? 'checked' : '' }}
                                   required 
                                   class="w-4 h-4 text-orange-500 bg-slate-900 border-slate-700 focus:ring-orange-500">
                            <div class="ml-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="text-sm font-bold text-white">Laki-laki (Male)</span>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 rounded-xl border border-white/10 bg-slate-800/50 hover:bg-slate-800 cursor-pointer transition has-[:checked]:border-orange-500 has-[:checked]:bg-orange-500/10">
                            <input type="radio" 
                                   name="gender" 
                                   value="female" 
                                   {{ old('gender') === 'female' ? 'checked' : '' }}
                                   required 
                                   class="w-4 h-4 text-orange-500 bg-slate-900 border-slate-700 focus:ring-orange-500">
                            <div class="ml-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="text-sm font-bold text-white">Perempuan (Female)</span>
                            </div>
                        </label>
                    </div>
                    @error('gender')
                        <p class="mt-1 text-xs text-red-400 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-slate-950 font-black font-outfit text-base uppercase tracking-wider rounded-xl transition-all shadow-xl shadow-orange-500/25 hover:shadow-orange-500/40 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                        <span>Kirim Pendaftaran Ranger</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        @endif

        <!-- Footer -->
        <p class="mt-8 text-center text-slate-500 text-xs">
            &copy; {{ date('Y') }} Ranger Bhayangkara FC 2026/2027 Management System.
        </p>
    </div>

</body>
</html>
