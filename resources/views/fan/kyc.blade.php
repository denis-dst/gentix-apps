<x-app-layout>
    <x-slot name="title">Verifikasi Identitas Suporter (KYC & NIK)</x-slot>
    <x-slot name="header">Verifikasi Identitas Suporter (KYC & NIK)</x-slot>
    <x-slot name="actions">
        <a href="{{ route('fan.dashboard') }}" class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition focus:ring-2 focus:ring-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>
    </x-slot>

    <div class="space-y-6">
        <div class="max-w-4xl mx-auto">
            <form action="{{ route('fan.kyc.submit') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                @csrf

                @if($member->kyc_status === 'verified')
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl flex items-center gap-3 font-semibold text-sm">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Identitas Anda telah terverifikasi resmi oleh klub pada {{ $member->kyc_verified_at?->format('d M Y') }}.
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="full_name_ktp" class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                            Nama Lengkap (Sesuai KTP) <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" id="full_name_ktp" name="full_name_ktp" value="{{ old('full_name_ktp', $member->full_name_ktp ?: Auth::user()->name) }}" required class="w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500 text-sm font-semibold py-2.5">
                    </div>

                    <div>
                        <label for="nik" class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                            Nomor Induk Kependudukan (NIK 16 Digit) <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik', $member->nik) }}" maxlength="16" minlength="16" placeholder="3201xxxxxxxxxxxx" required class="w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500 text-sm font-mono font-bold py-2.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                            Nomor WhatsApp Aktif <span class="text-rose-600">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $member->phone ?: Auth::user()->phone) }}" placeholder="08xxxxxxxx" required class="w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500 text-sm font-semibold py-2.5">
                    </div>

                    <div>
                        <label for="birth_date" class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                            Tanggal Lahir
                        </label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $member->birth_date?->format('Y-m-d')) }}" class="w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500 text-sm font-semibold py-2.5">
                    </div>

                    <div>
                        <label for="gender" class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                            Jenis Kelamin
                        </label>
                        <select id="gender" name="gender" class="w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500 text-sm font-semibold py-2.5">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                        Alamat Lengkap Domisili
                    </label>
                    <textarea id="address" name="address" rows="2" placeholder="Alamat tempat tinggal saat ini..." class="w-full rounded-xl border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500 text-sm">{{ old('address', $member->address) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                            Foto e-KTP / Kartu Identitas
                        </label>
                        @if($member->ktp_photo)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $member->ktp_photo) }}" alt="Foto KTP" class="w-48 h-32 object-cover rounded-xl border border-slate-200 shadow-sm">
                            </div>
                        @endif
                        <input type="file" name="ktp_photo" accept="image/*" class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 cursor-pointer">
                        <p class="text-xs text-slate-500 mt-1">Format JPG, PNG maksimal 2MB. Pastikan tulisan NIK dan nama terbaca jelas.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                            Foto Wajah (Selfie)
                        </label>
                        @if($member->face_photo)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $member->face_photo) }}" alt="Foto Wajah" class="w-48 h-32 object-cover rounded-xl border border-slate-200 shadow-sm">
                            </div>
                        @endif
                        <input type="file" name="face_photo" accept="image/*" class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 cursor-pointer">
                        <p class="text-xs text-slate-500 mt-1">Format JPG, PNG maksimal 2MB. Foto wajah menghadap kamera dengan pencahayaan cukup.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('fan.dashboard') }}" class="min-h-[44px] inline-flex items-center px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                        Batal
                    </a>
                    <button type="submit" class="min-h-[44px] px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow-md transition focus:ring-2 focus:ring-orange-500">
                        Kirim Dokumen KYC
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
