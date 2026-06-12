<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $html = <<<'HTML'
<div><!--block-->&nbsp;<strong>Syarat &amp; Ketentuan</strong></div>
<ol><li><!--block-->Peserta hanya terbatas untuk Alumni Umrah atau Haji Batik Travel</li><li><!--block-->Tidak diperkenankan mengajak rekan/kerabat yang bukan Alumni Batik Travel</li><li><!--block-->Mohon mengisi form pendaftaran dengan benar dan tepat (Jumlah Peserta, Dsb)</li><li><!--block-->Apabila setelah terdaftar, ternyata tidak jadi bisa hadir <strong>Mohon untuk tetap konfirmasi ke Admin (+62 812-1579-7593)</strong>.</li><li><!--block-->Barcode E-Voucher mohon disimpan dan ditunjukkan ke petugas registrasi saat kedatangan di Hari-H.</li><li><!--block-->Apabila jumlah yang hadir di hari-H tidak sesuai dengan yang didaftarkan, maka peserta yang tidak didaftarkan tidak diperkenankan masuk Ruangan Acara.</li></ol>
HTML;

        DB::table('events')
            ->where('name', 'Kajian dan Reuni Akbar Alumni Batik Travel 2026')
            ->orWhere('name', 'like', '%Alumni Batik Travel 2026%')
            ->update([
                'terms_conditions' => $html,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('events')
            ->where('name', 'Kajian dan Reuni Akbar Alumni Batik Travel 2026')
            ->orWhere('name', 'like', '%Alumni Batik Travel 2026%')
            ->update([
                'terms_conditions' => null,
                'updated_at' => now(),
            ]);
    }
};
