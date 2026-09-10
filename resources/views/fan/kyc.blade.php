<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('fan.dashboard') }}" class="p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-black text-slate-900 font-outfit uppercase tracking-tight">
                    Verifikasi Identitas Suporter (KYC & NIK)
                </h2>
                <p class="text-sm text-slate-500 font-medium">Kepatuhan regulasi Single Fan Identity PSSI untuk kenyamanan & keamanan tiket Anda.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('fan.kyc.submit') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm space-y-6">
                @csrf

                @if($member->kyc_status === 'verified')
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 font-semibold text-sm">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Identitas Anda telah terverifikasi resmi oleh klub pada {{ $member->kyc_verified_at?->format('d M Y') }}.
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span></label>
                        <input type="text" name="full_name_ktp" value="{{ old('full_name_ktp', $member->full_name_ktp ?: Auth::user()->name) }}" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Nomor Induk Kependudukan (NIK 16 Digit) <span class="text-rose-500">*</span></label>
                        <input type="text" name="nik" value="{{ old('nik', $member->nik) }}" maxlength="16" minlength="16" placeholder="3201xxxxxxxxxxxx" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $member->phone ?: Auth::user()->phone) }}" placeholder="08xxxxxxxx" required class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Jenis Kelamin</label>
                        <select name="gender" class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm font-semibold">
                            <option value="">-- Pilih --</option>
                            <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Alamat Domisili Lengkap</label>
                    <textarea name="address" rows="2" placeholder="Nama jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten..." class="w-full rounded-xl border-slate-200 focus:border-orange-500 focus:ring-orange-500 text-sm">{{ old('address', $member->address) }}</textarea>
                </div>

                <!-- Dokumen Foto KTP & Wajah -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Foto e-KTP Asli <span class="text-rose-500">*</span></label>
                        <input type="file" name="ktp_photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100">
                        <p class="text-[11px] text-slate-400 mt-1">Pastikan NIK dan nama terbaca jelas, maksimal ukuran 3 MB.</p>
                        @if($member->ktp_photo)
                            <div class="mt-2 text-xs text-emerald-600 font-semibold">✓ Foto KTP sudah terunggah</div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Foto Wajah Asli (Selfie) <span class="text-rose-500">*</span></label>
                        <input type="file" name="face_photo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100">
                        <p class="text-[11px] text-slate-400 mt-1">Foto wajah tampak depan tanpa kacamata hitam/masker, maksimal 3 MB.</p>
                        @if($member->face_photo)
                            <div class="mt-2 text-xs text-emerald-600 font-semibold">✓ Foto wajah sudah terunggah</div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('fan.dashboard') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-500/20 transition">
                        Kirimkan Dokumen KYC
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
