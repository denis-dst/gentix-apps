<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - GenTix</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gentix-50: #fff7ed;
            --gentix-600: #ea580c;
            --gentix-700: #c2410c;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .terms-content ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; }
        .terms-content ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; }
        .terms-content li { margin-bottom: 0.5rem; }
        .terms-content b, .terms-content strong { font-weight: 700; color: white; }
        .terms-content p { margin-bottom: 1rem; }
    </style>
</head>
<body class="bg-[#111118] text-[#e8e4df] antialiased">
    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 py-4 glass border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-12">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight font-outfit uppercase text-white">Gen<span class="text-orange-400">Tix</span></span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm font-medium text-stone-300 hover:text-white transition">Back to Home</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <section class="pt-40 pb-20 relative overflow-hidden bg-[#13131b]">
        <div class="absolute inset-0 bg-orange-500/5 blur-[100px]"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-bold font-outfit mb-6 text-white">{{ $page->title }}</h1>
            <div class="w-20 h-1 bg-orange-500 mx-auto rounded-full"></div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass p-8 md:p-12 rounded-[2rem] terms-content">
                {!! $page->content !!}
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-10 border-t border-white/5 bg-[#0d0d14] mt-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-stone-600 text-sm">
                &copy; {{ date('Y') }} GenTix Inc. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>
