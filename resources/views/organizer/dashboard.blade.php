<x-app-layout>
    <x-slot name="title">Partner Dashboard</x-slot>

    <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Welcome Hero -->
        <div class="relative overflow-hidden bg-purple-600 rounded-lg p-8 sm:p-12 text-white shadow-md">
            <div class="relative z-10 max-w-2xl">
                <h2 class="text-3xl sm:text-4xl font-black font-outfit mb-4 leading-tight">
                    Welcome to the GenTix Family, <br>
                    <span class="text-purple-200">{{ Auth::user()->tenant->name ?? 'Partner' }}</span>!
                </h2>
                <p class="text-lg text-white/90 font-medium mb-8 leading-relaxed">
                    We're excited to help you bring your events to life. Your dashboard is ready for you to start creating memorable experiences.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#" class="px-6 py-2.5 bg-white text-purple-700 rounded-lg font-bold hover:bg-gray-50 transition shadow-sm">
                        Create Your First Event
                    </a>
                    <a href="#" class="px-6 py-2.5 bg-purple-700/50 text-white border border-white/30 rounded-lg font-bold hover:bg-purple-700 transition backdrop-blur-sm">
                        View Documentation
                    </a>
                </div>
            </div>
            
            <!-- Decorative Element -->
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 -mb-20 -mr-20 w-64 h-64 bg-black/10 rounded-full blur-2xl"></div>
        </div>

        <!-- Quick Start Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg border-t-4 border-blue-500 shadow-md hover:shadow-lg transition group">
                <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                </div>
                <h3 class="text-xl font-bold font-outfit mb-2">Create Event</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-light leading-relaxed">Start selling tickets by setting up your first event details and categories.</p>
            </div>

            <div class="bg-white p-6 rounded-lg border-t-4 border-amber-500 shadow-md hover:shadow-lg transition group">
                <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <h3 class="text-xl font-bold font-outfit mb-2">Manage Staff</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-light leading-relaxed">Add Loket and Gate officers to help you manage your event operations.</p>
            </div>

            <div class="bg-white p-6 rounded-lg border-t-4 border-emerald-500 shadow-md hover:shadow-lg transition group">
                <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                </div>
                <h3 class="text-xl font-bold font-outfit mb-2">Analytics</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-light leading-relaxed">Track your ticket sales and gate check-ins in real-time as they happen.</p>
            </div>
        </div>
    </div>
</x-app-layout>
