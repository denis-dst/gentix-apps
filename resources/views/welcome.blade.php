<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings['app_name'] ?? 'GenTix' }} - {{ $settings['app_tagline'] ?? 'Connecting Generations' }}</title>
    <meta name="description" content="{{ $settings['meta_description'] ?? '' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'sans-serif'],
                            outfit: ['Outfit', 'sans-serif'],
                        },
                        colors: {
                            gentix: {
                                50: '#fff7ed',
                                100: '#ffedd5',
                                200: '#fed7aa',
                                300: '#fdba74',
                                400: '#fb923c',
                                500: '#f97316',
                                600: '#ea580c',
                                700: '#c2410c',
                                800: '#9a3412',
                                900: '#7c2d12',
                                950: '#431407',
                            },
                        },
                    }
                }
            }
        </script>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #111118;
            color: #e8e4df;
        }
        .glass {
            background: rgba(30, 28, 35, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .glass-card {
            background: rgba(30, 28, 35, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            background: rgba(40, 38, 48, 0.8);
            border: 1px solid rgba(249, 115, 22, 0.2);
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(249, 115, 22, 0.08);
        }
        .text-gradient {
            background: linear-gradient(135deg, #fdba74 0%, #f97316 50%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-gradient-main {
            background: radial-gradient(circle at top right, rgba(249, 115, 22, 0.08), transparent),
                        radial-gradient(circle at bottom left, rgba(251, 146, 60, 0.06), transparent);
        }
        .hero-overlay {
            background: linear-gradient(to bottom, rgba(17, 17, 24, 0.88) 0%, rgba(17, 17, 24, 0.55) 50%, rgba(17, 17, 24, 0.95) 100%);
        }
        ::selection { background: rgba(249, 115, 22, 0.3); }
    </style>
</head>
<body class="antialiased bg-[#111118] text-[#e8e4df] min-h-screen">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 glass border-b border-black/5 dark:border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-2">
                    @if(isset($settings['app_logo']) && $settings['app_logo'])
                        <img src="{{ asset('storage/' . $settings['app_logo']) }}" alt="Logo" class="h-10 w-auto">
                    @else
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                    @endif
                    <span class="text-2xl font-bold tracking-tight font-outfit uppercase text-white">
                        @if(isset($settings['app_name']))
                            @php
                                $nameParts = explode(' ', $settings['app_name']);
                                $firstPart = $nameParts[0] ?? '';
                                $secondPart = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';
                            @endphp
                            {{ $firstPart }}<span class="text-orange-400">{{ $secondPart }}</span>
                        @else
                            Gen<span class="text-orange-400">Tix</span>
                        @endif
                    </span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#events" class="text-sm font-medium text-white/80 hover:text-white transition">{{ __('Events') }}</a>
                    <a href="#how-it-works" class="text-sm font-medium text-white/80 hover:text-white transition">{{ __('How it Works') }}</a>
                    <a href="#about" class="text-sm font-medium text-white/80 hover:text-white transition">{{ __('About') }}</a>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-sm font-bold text-white/80 hover:text-white transition uppercase flex items-center gap-1">
                            {{ app()->getLocale() }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute top-full mt-4 right-0 glass rounded-xl py-2 w-24">
                            <a href="{{ route('lang.switch', 'id') }}" class="block px-4 py-2 text-sm text-stone-300 hover:text-white hover:bg-white/5 {{ app()->getLocale() == 'id' ? 'font-bold text-white' : '' }}">ID</a>
                            <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-stone-300 hover:text-white hover:bg-white/5 {{ app()->getLocale() == 'en' ? 'font-bold text-white' : '' }}">EN</a>
                        </div>
                    </div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-full bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-sm font-semibold transition shadow-lg shadow-orange-500/20">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-white/80 hover:text-white transition">{{ __('Log in') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-orange-600 text-white hover:bg-orange-700 text-sm font-semibold transition shadow-lg shadow-orange-500/20">{{ __('Partner with Us') }}</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="/images/hero.png" alt="Hero Background" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 hero-overlay"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center px-4 py-2 rounded-full glass mb-8 animate-bounce border border-white/10">
                <span class="w-2 h-2 bg-orange-400 rounded-full mr-2 animate-pulse"></span>
                <span class="text-xs font-semibold tracking-wider uppercase text-orange-300/80">{{ __('Live your best moments') }}</span>
            </div>
            <h1 class="text-5xl lg:text-8xl font-extrabold font-outfit mb-8 leading-tight text-white">
                {{ __($settings['hero_title'] ?? 'GenTix: Connecting Generations Through Every Gate.') }}
            </h1>
            <p class="text-xl lg:text-2xl text-stone-300 max-w-2xl mx-auto mb-10 leading-relaxed font-light">
                {{ __($settings['hero_subtitle'] ?? 'Bridging the gap between Generation and Tickets.') }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#events" class="w-full sm:w-auto px-10 py-4 rounded-full bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-lg transition-all shadow-xl shadow-orange-500/25">
                    {{ __('Explore Events') }}
                </a>
                <a href="#how-it-works" class="w-full sm:w-auto px-10 py-4 rounded-full glass hover:bg-white/10 text-white font-bold text-lg transition-all border border-white/10">
                    {{ __('How it Works') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Events -->
    <section id="events" class="py-24 bg-[#13131b]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div>
                    <h2 class="text-4xl font-bold font-outfit mb-4 text-white">{{ __('Featured Events') }}</h2>
                    <p class="text-stone-400 font-light text-lg">{{ __('Handpicked experiences you shouldn\'t miss this month.') }}</p>
                </div>
                <a href="#" class="inline-flex items-center text-orange-400 font-semibold hover:text-orange-300 transition group">
                    {{ __('View all events') }}
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($events as $event)
                <div class="glass-card rounded-3xl overflow-hidden group transition-all">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ $event->background_image ? asset('storage/' . $event->background_image) : asset('images/concert.png') }}" alt="{{ $event->name }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#13131b] via-transparent to-transparent"></div>
                        <div class="absolute top-4 left-4 px-3 py-1 bg-gradient-to-r from-orange-500 to-amber-500 rounded-full text-xs font-bold uppercase tracking-widest text-white shadow-lg">
                            {{ $event->city ?? 'Event' }}
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs text-stone-400 font-medium tracking-wide">
                                {{ $event->event_start_date->format('M d, Y • H:i A') }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 font-outfit text-white group-hover:text-orange-400 transition">{{ $event->name }}</h3>
                        <p class="text-stone-400 font-light text-sm mb-6 line-clamp-2">{{ $event->description }}</p>
                        <div class="flex items-center justify-between pt-6 border-t border-white/5">
                            <span class="text-xl font-bold text-orange-400">
                                @if($event->ticketCategories->count() > 0)
                                    Mulai Rp. {{ number_format($event->ticketCategories->min('price'), 0, ',', '.') }}
                                @else
                                    Coming Soon
                                @endif
                            </span>
                            <a href="{{ route('events.show', $event->slug) }}" class="px-6 py-2 rounded-xl bg-white/5 hover:bg-orange-500 hover:text-white text-stone-300 transition-all text-sm font-bold">Details</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section id="how-it-works" class="py-24 relative overflow-hidden bg-[#111118]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-bold font-outfit mb-6 text-white">{{ __('Simple Steps to Attend') }}</h2>
                <p class="text-stone-400 font-light text-lg max-w-xl mx-auto">{{ __('Getting your tickets has never been easier. Follow our seamless process to join the action.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 relative">
                <!-- Connective line (Desktop) -->
                <div class="hidden md:block absolute top-1/4 left-0 w-full h-px bg-white/10 z-0"></div>

                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center mb-6 group-hover:bg-orange-500 group-hover:text-white transition-all shadow-xl text-white">
                        <span class="text-2xl font-bold font-outfit">01</span>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-white">{{ __('Browse Events') }}</h4>
                    <p class="text-stone-400 text-sm font-light">{{ __('Explore our curated list of events across various categories.') }}</p>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center mb-6 group-hover:bg-orange-500 group-hover:text-white transition-colors shadow-xl text-white">
                        <span class="text-2xl font-bold font-outfit">02</span>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-white">{{ __('Choose Seats') }}</h4>
                    <p class="text-stone-400 text-sm font-light">{{ __('Select your preferred viewing area and number of tickets.') }}</p>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center mb-6 group-hover:bg-orange-500 group-hover:text-white transition-colors shadow-xl text-white">
                        <span class="text-2xl font-bold font-outfit">03</span>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-white">{{ __('Secure Payment') }}</h4>
                    <p class="text-stone-400 text-sm font-light">{{ __('Pay safely using our encrypted payment gateway.') }}</p>
                </div>

                <!-- Step 4 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center mb-6 group-hover:bg-orange-500 group-hover:text-white transition-colors shadow-xl text-white">
                        <span class="text-2xl font-bold font-outfit">04</span>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-white">{{ __('Get E-Ticket') }}</h4>
                    <p class="text-stone-400 text-sm font-light">{{ __('Your ticket will be sent to your email and Gentix wallet.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Philosophy Section -->
    <section class="py-24 relative overflow-hidden bg-[#13131b]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div>
                    <div class="inline-flex items-center px-4 py-1.5 rounded-full glass mb-6 border border-white/5">
                        <span class="text-xs font-bold uppercase tracking-widest text-orange-400">{{ __('Our Philosophy') }}</span>
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-bold font-outfit mb-8 leading-tight text-white">
                        {{ __('Bridging the Generation Gap') }}
                    </h2>
                    <p class="text-stone-400 text-xl font-light leading-relaxed mb-8">
                        GenTix is a fusion of <span class="text-white font-semibold">Generation</span> and <span class="text-white font-semibold">Tickets</span>. We believe that every event is an opportunity to bring people of all ages together through the power of seamless technology.
                    </p>
                    
                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center shrink-0 shadow-lg shadow-orange-500/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2 text-white">{{ __('Technology Inclusivity') }}</h4>
                                <p class="text-stone-400 text-sm leading-relaxed">
                                    Built on high-end cloud infrastructure, yet designed with a UI so simple that everyone—from Gen Z to Baby Boomers—can scan their E-vouchers or RFID bands without a single worry.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-yellow-600 flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2 text-white">{{ __('Universal Accessibility') }}</h4>
                                <p class="text-stone-400 text-sm leading-relaxed">
                                    Whether it's a massive rock concert, a prestigious corporate seminar, or a local cultural festival, GenTix adapts to any event scale and audience, ensuring every gate is a gateway to a new memory.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 bg-orange-500/10 blur-[100px] rounded-full"></div>
                    <div class="glass p-2 rounded-[2.5rem] relative overflow-hidden border border-white/10">
                        <img src="/images/hero.png" alt="GenTix Vision" class="rounded-[2.2rem] w-full h-full object-cover opacity-80 mix-blend-lighten">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#13131b] via-transparent to-transparent"></div>
                        <div class="absolute bottom-10 left-10 right-10">
                            <div class="text-4xl font-bold font-outfit mb-2">GenTix</div>
                            <div class="text-orange-400 font-medium italic">"Connecting Generations Through Every Gate"</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-24 bg-[#111118]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-orange-500/20 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-amber-500/20 rounded-full blur-3xl"></div>
                    <div class="glass p-8 rounded-[40px] relative overflow-hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <div class="h-48 bg-[#1e1e2a] rounded-3xl flex items-center justify-center">
                                    <span class="text-4xl">🎟️</span>
                                </div>
                                <div class="h-32 bg-gradient-to-br from-orange-500 to-amber-600 rounded-3xl flex items-center justify-center">
                                    <span class="text-4xl">🚀</span>
                                </div>
                            </div>
                            <div class="space-y-4 pt-8">
                                <div class="h-32 bg-[#252530] rounded-3xl flex items-center justify-center">
                                    <span class="text-4xl">💎</span>
                                </div>
                                <div class="h-48 bg-[#1e1e2a] rounded-3xl flex items-center justify-center">
                                    <span class="text-4xl">✨</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-4xl font-bold font-outfit mb-8 leading-tight text-white">About <span class="text-orange-400">GenTix</span></h2>
                    <p class="text-lg text-stone-400 font-light mb-8 leading-relaxed">
                        GenTix is more than just a ticketing platform. We are a bridge between passionate event-goers and the most extraordinary experiences. Founded in 2024, our mission is to make event access seamless, secure, and purely delightful.
                    </p>
                    <ul class="space-y-6 mb-10">
                        <li class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-white">Verified Organizers</h5>
                                <p class="text-sm text-stone-500">Every event on our platform is vetted for security.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-white">Fast-Pass Entry</h5>
                                <p class="text-sm text-stone-500">Scan your QR code and get in within seconds.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-white">24/7 Priority Support</h5>
                                <p class="text-sm text-slate-500">Our team is always here to help with your bookings.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 border-t border-white/5 bg-[#0d0d14]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-8">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold tracking-tight font-outfit uppercase text-white">{{ $settings['app_name'] ?? 'Gen' }}<span class="text-orange-400">{{ $settings['app_name_suffix'] ?? 'Tix' }}</span></span>
                    </div>
                    <p class="text-stone-500 font-light max-w-sm mb-8">
                        {{ $settings['meta_description'] ?? 'The ultimate destination for discovery and access to the world\'s most exciting live events.' }}
                    </p>
                    <div class="flex space-x-4">
                        @if(isset($settings['social_facebook']))
                        <a href="{{ $settings['social_facebook'] }}" target="_blank" class="w-10 h-10 rounded-full glass flex items-center justify-center hover:text-orange-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if(isset($settings['social_twitter']))
                        <a href="{{ $settings['social_twitter'] }}" target="_blank" class="w-10 h-10 rounded-full glass flex items-center justify-center hover:text-orange-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        @endif
                        @if(isset($settings['social_instagram']))
                        <a href="{{ $settings['social_instagram'] }}" target="_blank" class="w-10 h-10 rounded-full glass flex items-center justify-center hover:text-orange-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        @endif
                        @if(isset($settings['social_youtube']))
                        <a href="{{ $settings['social_youtube'] }}" target="_blank" class="w-10 h-10 rounded-full glass flex items-center justify-center hover:text-orange-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>

                <div>
                    <h5 class="text-white font-bold mb-8">Platform</h5>
                    <ul class="space-y-4">
                        <li><a href="{{ route('pages.show', 'about') }}" class="text-stone-500 hover:text-orange-400 transition text-sm">About Us</a></li>
                        <li><a href="#events" class="text-stone-500 hover:text-orange-400 transition text-sm">Events</a></li>
                        <li><a href="{{ route('pages.show', 'pricing') }}" class="text-stone-500 hover:text-orange-400 transition text-sm">Pricing</a></li>
                        <li><a href="#organizers" class="text-stone-500 hover:text-orange-400 transition text-sm">Organizers</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-white font-bold mb-8">Support</h5>
                    <ul class="space-y-4">
                        <li><a href="{{ route('pages.show', 'help-center') }}" class="text-stone-500 hover:text-orange-400 transition text-sm">Help Center</a></li>
                        <li><a href="{{ route('pages.show', 'privacy-policy') }}" class="text-stone-500 hover:text-orange-400 transition text-sm">Privacy Policy</a></li>
                        <li><a href="{{ route('pages.show', 'terms-of-service') }}" class="text-stone-500 hover:text-orange-400 transition text-sm">Terms of Service</a></li>
                        <li><a href="{{ route('pages.show', 'contact-us') }}" class="text-stone-500 hover:text-orange-400 transition text-sm">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-stone-600 text-xs">
                    {!! $settings['footer_text'] ?? '&copy; ' . date('Y') . ' ' . ($settings['app_name'] ?? 'GenTix') . ' Inc. All rights reserved.' !!}
                </p>
                <div class="flex items-center gap-6">
                    <span class="text-xs text-stone-600">Secure Payments via</span>
                    <div class="flex gap-4 grayscale opacity-50">
                        <span class="text-xs font-bold">VISA</span>
                        <span class="text-xs font-bold">MASTERCARD</span>
                        <span class="text-xs font-bold">STRIPE</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <x-accessibility-widget />
</body>
</html>
