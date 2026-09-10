<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gabung Member Resmi {{ $tenant->name }} - GenTix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0f19; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex items-center justify-center p-4 md:p-8 relative overflow-x-hidden">

    <!-- Ambient Glow Background -->
    <div class="fixed -top-40 -left-40 w-96 h-96 bg-orange-600/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="fixed -bottom-40 -right-40 w-96 h-96 bg-amber-600/20 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-xl w-full relative z-10 space-y-6">

        <!-- Header / Club Branding -->
        <div class="text-center space-y-3">
            <div class="inline-flex p-3 rounded-2xl bg-white/5 border border-white/10 shadow-2xl backdrop-blur-md mb-2">
                @if($tenant->logo)
                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->name }}" class="h-16 w-auto object-contain">
                @else
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center font-black text-2xl text-white font-outfit shadow-lg shadow-orange-500/30">
                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h1 class="text-3xl font-black text-white font-outfit uppercase tracking-tight">
                {{ $tenant->name }}
            </h1>
            <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                Pendaftaran Resmi Keanggotaan Suporter (Official Membership & Fan Card). Dapatkan prioritas tiket match kandang, diskon khusus, dan kumpulkan poin loyalitas.
            </p>
        </div>

        <!-- Registration Card -->
        <div class="bg-slate-900/90 border border-white/10 rounded-[2.5rem] p-6 md:p-8 shadow-2xl backdrop-blur-xl space-y-6">

            @if ($errors->any())
                <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-400 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            <form action="{{ url()->current() }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-300 mb-1.5">Nama Lengkap (Sesuai KTP) <span class="text-orange-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap Anda" required class="w-full bg-slate-950/80 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 font-medium">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-300 mb-1.5">Nomor NIK KTP (16 Digit) <span class="text-orange-400">*</span></label>
                        <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" minlength="16" placeholder="3201xxxxxxxxxxxx" required class="w-full bg-slate-950/80 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 font-mono font-bold">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-300 mb-1.5">No. WhatsApp Aktif <span class="text-orange-400">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required class="w-full bg-slate-950/80 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-slate-300 mb-1.5">Alamat Email <span class="text-orange-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@domain.com" required class="w-full bg-slate-950/80 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 font-medium">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-300 mb-1.5">Password <span class="text-orange-400">*</span></label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter" required class="w-full bg-slate-950/80 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 font-medium">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-300 mb-1.5">Konfirmasi Password <span class="text-orange-400">*</span></label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password" required class="w-full bg-slate-950/80 border border-white/10 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 font-medium">
                    </div>
                </div>

                @if($tiers->isNotEmpty())
                    <div class="pt-2">
                        <label class="block text-[11px] font-black uppercase tracking-wider text-slate-300 mb-2">Pilih Kategori Keanggotaan</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                            @foreach($tiers as $tier)
                                <label class="flex items-center gap-3 p-3 bg-slate-950/50 border border-white/5 rounded-xl cursor-pointer hover:border-orange-500/50 transition">
                                    <input type="radio" name="membership_tier_id" value="{{ $tier->id }}" class="text-orange-600 focus:ring-orange-500 bg-slate-900 border-white/20">
                                    <div class="text-xs">
                                        <p class="font-bold text-white">{{ $tier->name }}</p>
                                        <p class="text-orange-400 font-semibold text-[11px]">{{ $tier->price > 0 ? 'Rp ' . number_format($tier->price, 0, ',', '.') : 'Gratis' }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white font-black text-sm uppercase tracking-wider rounded-2xl shadow-xl shadow-orange-500/25 transition duration-150 transform hover:-translate-y-0.5">
                        Daftar Sebagai Member Resmi
                    </button>
                </div>
            </form>

            <div class="text-center pt-2 border-t border-white/5">
                <p class="text-xs text-slate-400">
                    Sudah memiliki akun suporter? 
                    <a href="{{ route('login') }}" class="text-orange-400 font-bold hover:underline">Masuk di sini</a>
                </p>
            </div>
        </div>

        <!-- Footer Notice -->
        <p class="text-[11px] text-slate-500 text-center font-medium">
            Powered by GenTix Apps • Platform Manajemen Tiket & Komunitas Klub Sepakbola
        </p>
    </div>

</body>
</html>
