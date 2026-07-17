<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VirtualOfficeImportTemplate implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    /**
     * Define the Excel headings.
     */
    public function headings(): array
    {
        return [
            'Nama Lengkap',
            'Email',
            'No Telepon',
            'Nama Perusahaan',
            'Alamat Perusahaan',
            'NIK',
            'NPWP',
            'Status PKP',
            'Nama Lokasi',
            'Tanggal Mulai',
            'Paket',
            'Durasi (Bulan)',
            'Harga Sewa (IDR)',
            'Deposit (IDR)',
            'Tanggal Kontrak',
            'Status Transaksi',
            'Catatan'
        ];
    }

    /**
     * Provide dummy example data for row 2.
     */
    public function array(): array
    {
        return [
            [
                'Budi Santoso',
                'budi@company.com',
                '081234567890',
                'PT Sukses Bersama',
                'Jl. Sudirman No. 12, Jakarta Selatan',
                '3171234567890001',
                '01.234.567.8-012.000',
                'Non PKP', // PKP / Non PKP
                'Urban Office - Merr', // Must match name in locations table
                '2026-07-01', // YYYY-MM-DD
                'monthly', // monthly / yearly
                '12', // integer duration
                '5000000', // rent price
                '1000000', // deposit
                '2026-06-26', // YYYY-MM-DD
                'settlement', // settlement / pending / expire
                'Contoh catatan tambahan penyewaan'
            ]
        ];
    }

    /**
     * Define the sheet title.
     */
    public function title(): string
    {
        return 'Template Import VO';
    }
}
