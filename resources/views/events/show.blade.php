<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->name }} - GenTix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">
    <div class="min-h-screen">
        <!-- Hero Section -->
        <div class="relative h-[400px] lg:h-[500px] overflow-hidden">
            <img src="{{ $event->background_image ? Storage::url($event->background_image) : 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&q=80' }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-8 lg:p-20">
                <div class="max-w-7xl mx-auto">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-purple-600 text-white text-[10px] font-bold uppercase tracking-widest mb-4">
                        {{ $event->city }}
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-black text-white font-outfit mb-4">{{ $event->name }}</h1>
                    <div class="flex flex-wrap gap-6 text-white/80 text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            {{ $event->event_start_date->format('l, d F Y • H:i') }}
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            {{ $event->venue }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-6 py-12 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Left: Description -->
                <div class="lg:col-span-2 space-y-12">
                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-6 font-outfit">About this Event</h2>
                        <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </section>

                    <section>
                        <h2 class="text-2xl font-bold text-slate-900 mb-6 font-outfit">Location</h2>
                        <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100">
                            <div class="aspect-video bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" /></svg>
                            </div>
                            <div class="mt-4 p-2">
                                <h4 class="font-bold text-slate-900">{{ $event->venue }}</h4>
                                <p class="text-sm text-slate-500">{{ $event->city }}</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Right: Checkout -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-purple-900/5 border border-slate-100 overflow-hidden sticky top-8">
                        <div class="p-8 border-b border-slate-50">
                            <h3 class="text-xl font-bold text-slate-900 font-outfit">Get Tickets</h3>
                            <p class="text-sm text-slate-500">Max 2 tickets per transaction.</p>
                        </div>

                        <form action="{{ route('checkout.process', $event->slug) }}" method="POST" class="p-8 space-y-6">
                            @csrf
                            @if(session('error'))
                                <div class="p-4 bg-rose-50 text-rose-600 text-sm font-bold rounded-2xl">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Select Tier</label>
                                <div class="space-y-3">
                                    @foreach($event->ticketCategories as $category)
                                        @php
                                            $isAvailable = $category->isAvailable();
                                            $soldOut = $category->sold_count >= $category->quota;
                                        @endphp
                                        <label class="relative block group">
                                            <input type="radio" name="ticket_category_id" value="{{ $category->id }}" class="peer sr-only" {{ $soldOut ? 'disabled' : '' }} required>
                                            <div class="p-4 rounded-2xl border-2 border-slate-50 peer-checked:border-purple-600 peer-checked:bg-purple-50/50 cursor-pointer transition hover:border-slate-200 group-disabled:opacity-50 group-disabled:cursor-not-allowed">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <div class="font-bold text-slate-900">{{ $category->name }}</div>
                                                        <div class="text-xs text-slate-500">Rp {{ number_format($category->price, 0, ',', '.') }}</div>
                                                    </div>
                                                    @if($soldOut)
                                                        <span class="text-[10px] font-black text-rose-600 uppercase">Sold Out</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Quantity</label>
                                    <select name="quantity" class="w-full rounded-2xl border-slate-100 focus:ring-purple-500 focus:border-purple-500 transition px-4 py-3 bg-slate-50/50">
                                        <option value="1">1 Ticket</option>
                                        <option value="2">2 Tickets</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest">Purchaser Details</label>
                                <input type="text" name="nik" placeholder="NIK (16 Digits)" required maxlength="16" class="w-full rounded-2xl border-slate-100 focus:ring-purple-500 focus:border-purple-500 transition px-4 py-3 bg-slate-50/50">
                                <input type="text" name="name" placeholder="Full Name" required class="w-full rounded-2xl border-slate-100 focus:ring-purple-500 focus:border-purple-500 transition px-4 py-3 bg-slate-50/50">
                                <input type="email" name="email" placeholder="Email Address" required class="w-full rounded-2xl border-slate-100 focus:ring-purple-500 focus:border-purple-500 transition px-4 py-3 bg-slate-50/50">
                                <input type="text" name="phone" placeholder="Phone / WhatsApp" required class="w-full rounded-2xl border-slate-100 focus:ring-purple-500 focus:border-purple-500 transition px-4 py-3 bg-slate-50/50">
                            </div>

                            <button type="submit" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                                Buy Tickets Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
