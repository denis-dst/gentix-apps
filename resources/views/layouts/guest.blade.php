<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GenTix') }} - Authentication</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --gentix-50: #f5f3ff;
                --gentix-600: #7c3aed;
                --gentix-700: #6d28d9;
            }
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #0f172a;
            }
            .font-outfit { font-family: 'Outfit', sans-serif; }
            .glass {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .auth-bg {
                background-image: url('/images/hero.png');
                background-size: cover;
                background-position: center;
            }
            .auth-overlay {
                background: radial-gradient(circle at center, rgba(15, 23, 42, 0.7) 0%, rgba(15, 23, 42, 0.95) 100%);
            }
        </style>
    </head>
    <body class="antialiased text-white">
        <div class="min-h-screen relative flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 auth-bg">
            <div class="absolute inset-0 auth-overlay z-0"></div>
            
            <div class="relative z-10 w-full sm:max-w-md">
                <div class="text-center mb-10">
                    <a href="/" class="inline-flex items-center gap-3 group">
                        <div class="w-12 h-12 bg-gentix-600 rounded-2xl flex items-center justify-center shadow-2xl shadow-gentix-600/40 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold tracking-tight font-outfit uppercase">Gen<span class="text-gentix-600">Tix</span></span>
                    </a>
                </div>

                <div class="glass p-8 sm:p-10 rounded-[2.5rem] shadow-2xl">
                    {{ $slot }}
                </div>
                
                <p class="mt-8 text-center text-slate-400 text-sm">
                    &copy; {{ date('Y') }} GenTix Inc. Connecting Generations.
                </p>
            </div>
        </div>
    </body>
</html>
