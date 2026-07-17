<?php

// =========================================================
// config/company.php
// Data statis perusahaan untuk dokumen legal (kontrak, invoice)
// Ubah nilai di sini tanpa perlu menyentuh template PDF
// =========================================================

return [

    // =============================================
    // IDENTITAS PERUSAHAAN
    // =============================================
    'name'          => 'PT. URBAN KREASI BERSAMA',
    'name_short'    => 'Urban Office',

    // =============================================
    // PEJABAT PENANDATANGAN
    // =============================================
    'director'      => 'Georgius Mario Miracel',
    'representative_title' => 'Direktur',
    'representative_city'  => 'Surabaya',

    // =============================================
    // ALAMAT
    // =============================================
    'address'       => 'Jl. Dr. Ir. H. Soekarno No. 470 RT 002 RW 009 Kel. Kedung Baruk Kec. Rungkut',
    'city'          => 'Surabaya',
    'province'      => 'Jawa Timur',
    'postal_code'   => '60298',
    'full_address'  => 'Jl. Dr. Ir. H. Soekarno No. 470 RT 002 RW 009 Kel. Kedung Baruk Kec. Rungkut, Surabaya Jawa Timur 60298',

    // =============================================
    // KONTAK
    // =============================================
    'phone'         => '031-87855578',
    'email'         => '',

    // =============================================
    // DATA PAJAK & REKENING
    // =============================================
    'npwp'          => '92.347.130.4.615.000',
    'bank_name'     => 'Bank Mandiri',
    'bank_branch'   => 'Cabang Surabaya Merr',
    'bank_account'  => '140-00-8785555-8',
    'bank_holder'   => 'PT. Urban Kreasi Bersama',

    // =============================================
    // OPERASIONAL
    // =============================================
    'operational_hours' => 'Senin-Sabtu, 08.00 - 16.00 WIB',
    'operational_days'  => 'Senin sampai Sabtu',
    'operational_time'  => '08.00 WIB s/d 16.00 WIB',

    // =============================================
    // DOMISILI HUKUM
    // =============================================
    'legal_domicile' => 'Pengadilan Negeri Surabaya',

];