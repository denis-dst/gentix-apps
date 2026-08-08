<?php

namespace App\Http\Controllers;

use App\Models\Ranger;
use Illuminate\Http\Request;

class RangerRegistrationController extends Controller
{
    /**
     * Show the public registration form.
     */
    public function index()
    {
        return view('public.ranger-register');
    }

    /**
     * Store a newly created ranger registration.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:30',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'bank_name.required' => 'Nama Bank / E-Wallet wajib diisi.',
            'account_number.required' => 'Nomor Rekening / E-Wallet wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Pilihan jenis kelamin tidak valid.',
        ]);

        $ranger = Ranger::create($validated);

        return redirect()->back()->with('success', 'Registrasi Ranger Bhayangkara FC 2026/2027 Berhasil! Data Anda telah tersimpan.');
    }
}
