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
                                50: '#f5f3ff',
                                100: '#ede9fe',
                                200: '#ddd6fe',
                                300: '#c4b5fd',
                                400: '#a78bfa',
                                500: '#8b5cf6',
                                600: '#7c3aed',
                                700: '#6d28d9',
                                800: '#5b21b6',
                                900: '#4c1d95',
                                950: '#2e1065',
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
            background-color: #f8fafc;
            color: #0f172a;
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .dark .glass {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .text-gradient {
            background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 50%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-gradient-main {
            background: radial-gradient(circle at top right, rgba(139, 92, 246, 0.15), transparent),
                        radial-gradient(circle at bottom left, rgba(99, 102, 241, 0.15), transparent);
        }
        .hero-overlay {
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.85) 0%, rgba(15, 23, 42, 0.6) 50%, rgba(15, 23, 42, 0.9) 100%);
        }
    </style>
</head>
<body class="antialiased bg-slate-50 dark:bg-[#0f172a] text-slate-900 dark:text-slate-100 min-h-screen">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 glass border-b border-black/5 dark:border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gentix-600 rounded-xl flex items-center justify-center shadow-lg shadow-gentix-600/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight font-outfit uppercase text-white">{{ $settings['app_name'] ?? 'Gen' }}<span class="text-gentix-500">{{ $settings['app_name_suffix'] ?? 'Tix' }}</span></span>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#events" class="text-sm font-medium text-white/80 hover:text-white transition">Events</a>
                    <a href="#how-it-works" class="text-sm font-medium text-white/80 hover:text-white transition">How it Works</a>
                    <a href="#about" class="text-sm font-medium text-white/80 hover:text-white transition">About</a>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-full bg-gentix-600 hover:bg-gentix-700 text-white text-sm font-semibold transition shadow-lg shadow-gentix-600/20">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-white/80 hover:text-white transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-white text-slate-900 hover:bg-slate-100 text-sm font-semibold transition">Partner with Us</a>
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
                <span class="w-2 h-2 bg-gentix-500 rounded-full mr-2"></span>
                <span class="text-xs font-semibold tracking-wider uppercase text-gentix-300">Live Your Best Moments</span>
            </div>
            <h1 class="text-5xl lg:text-8xl font-extrabold font-outfit mb-8 leading-tight text-white">
                {{ $settings['hero_title'] ?? 'GenTix: Connecting Generations Through Every Gate.' }}
            </h1>
            <p class="text-xl lg:text-2xl text-slate-200 max-w-2xl mx-auto mb-10 leading-relaxed font-light">
                {{ $settings['hero_subtitle'] ?? 'Bridging the gap between Generation and Tickets.' }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#events" class="w-full sm:w-auto px-10 py-4 rounded-full bg-gentix-600 hover:bg-gentix-700 text-white font-bold text-lg transition-all shadow-xl shadow-gentix-600/25">
                    Explore Events
                </a>
                <a href="#how-it-works" class="w-full sm:w-auto px-10 py-4 rounded-full glass bg-white/10 hover:bg-white/20 text-white font-bold text-lg transition-all border border-white/20">
                    How it Works
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Events -->
    <section id="events" class="py-24 bg-white dark:bg-[#0F172A]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                <div>
                    <h2 class="text-4xl font-bold font-outfit mb-4 text-slate-900 dark:text-white">Featured <span class="text-gentix-600">Events</span></h2>
                    <p class="text-slate-600 dark:text-slate-300 font-light text-lg">Handpicked experiences you shouldn't miss this month.</p>
                </div>
                <a href="#" class="inline-flex items-center text-gentix-400 font-semibold hover:text-gentix-300 transition group">
                    View all events
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($events as $event)
                <div class="glass glass-card rounded-3xl overflow-hidden group transition-all hover:shadow-2xl hover:shadow-gentix-600/10">
                    <div class="relative h-64 overflow-hidden">
                        <img src="/images/{{ $event->background_image ?? 'concert.png' }}" alt="{{ $event->name }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                        <div class="absolute top-4 left-4 px-3 py-1 bg-gentix-600 rounded-full text-xs font-bold uppercase tracking-widest text-white shadow-lg">
                            {{ $event->city ?? 'Event' }}
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-4 h-4 text-gentix-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium tracking-wide">
                                {{ $event->event_start_date->format('M d, Y • H:i A') }}
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold mb-4 font-outfit text-slate-900 dark:text-white group-hover:text-gentix-600 transition">{{ $event->name }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 font-light text-sm mb-6 line-clamp-2">{{ $event->description }}</p>
                        <div class="flex items-center justify-between pt-6 border-t border-black/5 dark:border-white/5">
                            <span class="text-xl font-bold text-gentix-600 dark:text-gentix-300">
                                @if($event->ticketCategories->count() > 0)
                                    From ${{ number_format($event->ticketCategories->min('price'), 2) }}
                                @else
                                    Coming Soon
                                @endif
                            </span>
                            <a href="{{ route('events.show', $event->slug) }}" class="px-6 py-2 rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-gentix-600 hover:text-white transition-all text-sm font-bold">Details</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section id="how-it-works" class="py-24 relative overflow-hidden bg-slate-50 dark:bg-[#0B0F1A]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-bold font-outfit mb-6 text-slate-900 dark:text-white">Simple <span class="text-gentix-600">Steps</span> to Attend</h2>
                <p class="text-slate-600 dark:text-slate-200 font-light text-lg max-w-xl mx-auto">Getting your tickets has never been easier. Follow our seamless process to join the action.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 relative">
                <!-- Connective line (Desktop) -->
                <div class="hidden md:block absolute top-1/4 left-0 w-full h-px bg-white/10 z-0"></div>

                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center mb-6 group-hover:bg-gentix-600 group-hover:text-white transition-all shadow-xl text-slate-900 dark:text-white">
                        <span class="text-2xl font-bold font-outfit">01</span>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Browse Events</h4>
                    <p class="text-slate-600 dark:text-slate-300 text-sm font-light">Explore our curated list of events across various categories.</p>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center mb-6 group-hover:bg-gentix-600 transition-colors shadow-xl">
                        <span class="text-2xl font-bold font-outfit">02</span>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-white">Choose Seats</h4>
                    <p class="text-slate-300 text-sm font-light">Select your preferred viewing area and number of tickets.</p>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center mb-6 group-hover:bg-gentix-600 transition-colors shadow-xl">
                        <span class="text-2xl font-bold font-outfit">03</span>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-white">Secure Payment</h4>
                    <p class="text-slate-300 text-sm font-light">Pay safely using our encrypted payment gateway.</p>
                </div>

                <!-- Step 4 -->
                <div class="relative z-10 flex flex-col items-center text-center group">
                    <div class="w-16 h-16 rounded-2xl glass flex items-center justify-center mb-6 group-hover:bg-gentix-600 transition-colors shadow-xl">
                        <span class="text-2xl font-bold font-outfit">04</span>
                    </div>
                    <h4 class="text-xl font-bold mb-3 text-white">Get E-Ticket</h4>
                    <p class="text-slate-300 text-sm font-light">Your ticket will be sent to your email and Gentix wallet.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Philosophy Section -->
    <section class="py-24 relative overflow-hidden bg-white dark:bg-[#0F172A]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div>
                    <div class="inline-flex items-center px-4 py-1.5 rounded-full glass mb-6 border border-black/5 dark:border-white/5">
                        <span class="text-xs font-bold uppercase tracking-widest text-gentix-600 dark:text-gentix-400">Our Philosophy</span>
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-bold font-outfit mb-8 leading-tight text-slate-900 dark:text-white">
                        Bridging the <span class="text-gentix-600">Generation</span> Gap
                    </h2>
                    <p class="text-slate-600 dark:text-slate-200 text-xl font-light leading-relaxed mb-8">
                        GenTix is a fusion of <span class="text-slate-900 dark:text-white font-semibold">Generation</span> and <span class="text-slate-900 dark:text-white font-semibold">Tickets</span>. We believe that every event is an opportunity to bring people of all ages together through the power of seamless technology.
                    </p>
                    
                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-gentix-500 to-indigo-600 flex items-center justify-center shrink-0 shadow-lg shadow-gentix-600/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Technology Inclusivity</h4>
                                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                                    Built on high-end cloud infrastructure and PostgreSQL UUID systems, yet designed with a UI so simple that everyone—from Gen Z to Baby Boomers—can scan their E-vouchers or RFID bands without a single worry.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center shrink-0 shadow-lg shadow-blue-600/20">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Universal Accessibility</h4>
                                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                                    Whether it's a massive rock concert, a prestigious corporate seminar, or a local cultural festival, GenTix adapts to any event scale and audience, ensuring every gate is a gateway to a new memory.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute inset-0 bg-gentix-600/10 blur-[100px] rounded-full"></div>
                    <div class="glass p-2 rounded-[2.5rem] relative overflow-hidden border border-white/10">
                        <img src="/images/hero.png" alt="GenTix Vision" class="rounded-[2.2rem] w-full h-full object-cover opacity-80 mix-blend-lighten">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <div class="absolute bottom-10 left-10 right-10">
                            <div class="text-4xl font-bold font-outfit mb-2">GenTix</div>
                            <div class="text-gentix-400 font-medium italic">"Connecting Generations Through Every Gate"</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-24 bg-slate-50 dark:bg-[#0B0F1A]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="absolute -top-10 -left-10 w-40 h-40 bg-gentix-600/20 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl"></div>
                    <div class="glass p-8 rounded-[40px] relative overflow-hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <div class="h-48 bg-slate-800 rounded-3xl flex items-center justify-center">
                                    <span class="text-4xl">🎟️</span>
                                </div>
                                <div class="h-32 bg-gentix-600 rounded-3xl flex items-center justify-center">
                                    <span class="text-4xl">🚀</span>
                                </div>
                            </div>
                            <div class="space-y-4 pt-8">
                                <div class="h-32 bg-slate-700 rounded-3xl flex items-center justify-center">
                                    <span class="text-4xl">💎</span>
                                </div>
                                <div class="h-48 bg-slate-800 rounded-3xl flex items-center justify-center">
                                    <span class="text-4xl">✨</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-4xl font-bold font-outfit mb-8 leading-tight text-slate-900 dark:text-white">About <span class="text-gentix-600">GenTix</span></h2>
                    <p class="text-lg text-slate-600 dark:text-slate-200 font-light mb-8 leading-relaxed">
                        GenTix is more than just a ticketing platform. We are a bridge between passionate event-goers and the most extraordinary experiences. Founded in 2024, our mission is to make event access seamless, secure, and purely delightful.
                    </p>
                    <ul class="space-y-6 mb-10">
                        <li class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-gentix-600/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gentix-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-white">Verified Organizers</h5>
                                <p class="text-sm text-slate-500">Every event on our platform is vetted for security.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-gentix-600/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gentix-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-white">Fast-Pass Entry</h5>
                                <p class="text-sm text-slate-500">Scan your QR code and get in within seconds.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-gentix-600/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-gentix-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <footer class="py-20 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-8">
                        <div class="w-10 h-10 bg-gentix-600 rounded-xl flex items-center justify-center shadow-lg shadow-gentix-600/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold tracking-tight font-outfit uppercase text-slate-900 dark:text-white">{{ $settings['app_name'] ?? 'Gen' }}<span class="text-gentix-600">{{ $settings['app_name_suffix'] ?? 'Tix' }}</span></span>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 font-light max-w-sm mb-8">
                        {{ $settings['meta_description'] ?? 'The ultimate destination for discovery and access to the world\'s most exciting live events.' }}
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full glass flex items-center justify-center hover:text-gentix-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full glass flex items-center justify-center hover:text-gentix-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full glass flex items-center justify-center hover:text-gentix-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h5 class="text-slate-900 dark:text-white font-bold mb-8">Platform</h5>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-slate-600 dark:text-slate-400 hover:text-gentix-600 transition text-sm">About Us</a></li>
                        <li><a href="#" class="text-slate-600 dark:text-slate-400 hover:text-gentix-600 transition text-sm">Events</a></li>
                        <li><a href="#" class="text-slate-600 dark:text-slate-400 hover:text-gentix-600 transition text-sm">Pricing</a></li>
                        <li><a href="#" class="text-slate-600 dark:text-slate-400 hover:text-gentix-600 transition text-sm">Organizers</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-slate-900 dark:text-white font-bold mb-8">Support</h5>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-slate-600 dark:text-slate-400 hover:text-gentix-600 transition text-sm">Help Center</a></li>
                        <li><a href="#" class="text-slate-600 dark:text-slate-400 hover:text-gentix-600 transition text-sm">Privacy Policy</a></li>
                        <li><a href="#" class="text-slate-600 dark:text-slate-400 hover:text-gentix-600 transition text-sm">Terms of Service</a></li>
                        <li><a href="#" class="text-slate-600 dark:text-slate-400 hover:text-gentix-600 transition text-sm">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-black/5 dark:border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-slate-600 dark:text-slate-400 text-xs">
                    &copy; {{ date('Y') }} {{ $settings['app_name'] ?? 'GenTix' }} Inc. All rights reserved.
                </p>
                <div class="flex items-center gap-6">
                    <span class="text-xs text-slate-600">Secure Payments via</span>
                    <div class="flex gap-4 grayscale opacity-50">
                        <span class="text-xs font-bold">VISA</span>
                        <span class="text-xs font-bold">MASTERCARD</span>
                        <span class="text-xs font-bold">STRIPE</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
