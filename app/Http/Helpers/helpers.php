<?php

// =========================================================
// ROMANIZE — Konversi angka ke angka romawi
// Digunakan untuk nomor bulan di nomor kontrak
// Contoh: romanize(4) → 'IV', romanize(12) → 'XII'
// =========================================================

if (!function_exists('romanize')) {
    function romanize(int $number): string
    {
        if ($number <= 0) return '';

        $map = [
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
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
}

// =========================================================
// TERBILANG — Konversi angka ke teks bahasa Indonesia
// Digunakan untuk nominal uang di dokumen kontrak PDF
// Contoh: terbilang(150000) → 'seratus lima puluh ribu'
// =========================================================

if (!function_exists('terbilang')) {
    function terbilang(int|float $number): string
    {
        $number = (int) abs($number);

        $satuan = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh',
            'sebelas', 'dua belas', 'tiga belas', 'empat belas',
            'lima belas', 'enam belas', 'tujuh belas', 'delapan belas',
            'sembilan belas',
        ];

        if ($number === 0)  return 'nol';
        if ($number < 20)   return $satuan[$number];
        if ($number < 100) {
            $tens    = intdiv($number, 10);
            $ones    = $number % 10;
            $tensStr = $tens . ' puluh';
            return $ones > 0
                ? $tensStr . ' ' . $satuan[$ones]
                : $tensStr;
        }
        if ($number < 200)  return 'seratus'  . ($number > 100 ? ' ' . terbilang($number - 100) : '');
        if ($number < 1000) {
            $hundreds = intdiv($number, 100);
            $rest     = $number % 100;
            return $satuan[$hundreds] . ' ratus' . ($rest > 0 ? ' ' . terbilang($rest) : '');
        }
        if ($number < 2000)  return 'seribu'   . ($number > 1000 ? ' ' . terbilang($number - 1000) : '');
        if ($number < 1000000) {
            $thousands = intdiv($number, 1000);
            $rest      = $number % 1000;
            return terbilang($thousands) . ' ribu' . ($rest > 0 ? ' ' . terbilang($rest) : '');
        }
        if ($number < 1000000000) {
            $millions = intdiv($number, 1000000);
            $rest     = $number % 1000000;
            return terbilang($millions) . ' juta' . ($rest > 0 ? ' ' . terbilang($rest) : '');
        }
        if ($number < 1000000000000) {
            $billions = intdiv($number, 1000000000);
            $rest     = $number % 1000000000;
            return terbilang($billions) . ' miliar' . ($rest > 0 ? ' ' . terbilang($rest) : '');
        }

        return 'angka terlalu besar';
    }
}

// =========================================================
// FORMAT RUPIAH — Format angka ke string Rupiah
// Contoh: formatRupiah(150000) → 'Rp 150.000'
// =========================================================

if (!function_exists('formatRupiah')) {
    function formatRupiah(int|float $amount, bool $withPrefix = true): string
    {
        $formatted = number_format((float) $amount, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}