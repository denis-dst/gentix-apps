<x-app-layout>
    <x-slot name="title">Tambah Crew Baru</x-slot>
    <x-slot name="header">Tambah Crew</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50 text-center">
                <h3 class="text-xl font-bold text-slate-800 font-outfit">Akun Crew Baru</h3>
                <p class="text-sm text-slate-500">Buat akun untuk petugas operasional Anda.</p>
            </div>

            <form action="{{ route('organizer.crews.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Lengkap</label>
                        <input type="text" name="name" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Email Staff</label>
                        <input type="email" name="email" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Peran (Role)</label>
                        <select name="role" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3">
                            <option value="loket">Crew Redeem (Petugas Loket)</option>
                            <option value="gate">Crew Gate (Petugas Pintu Masuk)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Password</label>
                            <input type="password" name="password" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Konfirmasi</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 transition px-4 py-3" required>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <a href="{{ route('organizer.crews.index') }}" class="flex-1 py-3 px-6 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition text-center">
                        Batal
                    </a>
                    <button type="submit" class="flex-1 py-3 px-6 bg-purple-600 text-white rounded-xl font-bold hover:bg-purple-700 transition shadow-lg shadow-purple-200">
                        Simpan Crew
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
