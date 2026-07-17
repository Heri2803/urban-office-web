<?php

namespace App\Traits;

use App\Models\Contract;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait DocumentHelperTrait
{
    /**
     * Konversi nominal Rupiah ke terbilang Indonesia
     * Contoh: 5005000 → "lima juta lima ribu"
     */
    private function amountToWords(int $amount): string
    {
        if ($amount === 0) return 'nol';

        $satuan = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam',
            'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas',
            'dua belas', 'tiga belas', 'empat belas', 'lima belas',
            'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'
        ];

        $puluhan = [
            '', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh',
            'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'
        ];

        $convert = function (int $n) use (&$convert, $satuan, $puluhan): string {
            if ($n === 0)        return '';
            if ($n < 20)         return $satuan[$n];
            if ($n < 100)        return trim($puluhan[(int)($n / 10)] . ' ' . $satuan[$n % 10]);
            if ($n < 200)        return 'seratus ' . $convert($n - 100);
            if ($n < 1000)       return $satuan[(int)($n / 100)] . ' ratus ' . $convert($n % 100);
            if ($n < 2000)       return 'seribu ' . $convert($n - 1000);
            if ($n < 1000000)    return $convert((int)($n / 1000)) . ' ribu ' . $convert($n % 1000);
            if ($n < 1000000000) return $convert((int)($n / 1000000)) . ' juta ' . $convert($n % 1000000);
            return $convert((int)($n / 1000000000)) . ' miliar ' . $convert($n % 1000000000);
        };

        return trim(preg_replace('/\s+/', ' ', $convert($amount)));
    }

    /**
     * Konversi angka kecil ke teks Indonesia untuk durasi
     * Contoh: 1 → 'satu', 14 → 'empat belas'
     */
    private function numberToWords(int $number): string
    {
        $words = [
            1  => 'satu',        2  => 'dua',          3  => 'tiga',
            4  => 'empat',       5  => 'lima',          6  => 'enam',
            7  => 'tujuh',       8  => 'delapan',       9  => 'sembilan',
            10 => 'sepuluh',     11 => 'sebelas',       12 => 'dua belas',
            13 => 'tiga belas',  14 => 'empat belas',   15 => 'lima belas',
            16 => 'enam belas',  17 => 'tujuh belas',   18 => 'delapan belas',
            19 => 'sembilan belas', 20 => 'dua puluh',
            24 => 'dua puluh empat', 36 => 'tiga puluh enam',
            48 => 'empat puluh delapan', 60 => 'enam puluh',
        ];

        return $words[$number] ?? (string) $number;
    }

    /**
     * Konversi number ke huruf romawi
     * Contoh: 4 → 'IV', 9 → 'IX'
     */
    private function romanize(int $number): string
    {
        $map = [
            'M'  => 1000, 'CM' => 900, 'D'  => 500, 'CD' => 400,
            'C'  => 100,  'XC' => 90,  'L'  => 50,  'XL' => 40,
            'X'  => 10,   'IX' => 9,   'V'  => 5,   'IV' => 4,
            'I'  => 1,
        ];

        $result = '';
        foreach ($map as $roman => $value) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }
        return $result;
    }

    /**
     * Generate public token untuk QR code verifikasi kontrak / addendum
     */
    private function generatePublicToken($entity): string
    {
        $token = hash('sha256', $entity->id . Str::random(40) . now()->timestamp);
        
        $entity->update([
            'public_token'     => $token,
            'token_expires_at' => null, // permanent, sesuaikan jika perlu
        ]);

        return $token;
    }

    /**
     * Generate QR code sebagai base64 string untuk embed di PDF
     */
    private function generateQrCodeBase64(string $token, string $routeName = 'contract.public.verify'): string
    {
        $url = route($routeName, ['token' => $token]);

        // Ganti png → svg, tidak butuh imagick
        $qrSvg = QrCode::format('svg')
                    ->size(180)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($url);

        return 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
    }
}