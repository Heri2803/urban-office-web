<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Addendum {{ $addendum->roman_order }} - {{ $addendum->addendum_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.5;
        }

        /* =============================================
           COVER PAGE — halaman 1 full image
        ============================================= */
        .cover-page {
            width: 100%;
            height: 100vh;
            page-break-after: always;
            overflow: hidden;
        }

        .cover-page img {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* =============================================
           BACKGROUND TEMPLATE — position:fixed agar
           diulang otomatis di setiap halaman DomPDF
        ============================================= */
        .page-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .page-background img {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* =============================================
           KONTEN UTAMA
           - padding-top  : 22mm → konten halaman 2 lebih turun
                            (naikkan angka ini untuk turun lebih jauh)
           - padding-bottom: 10mm → dikurangi dari 18mm agar
                            kapasitas teks per halaman lebih panjang
           - kiri/kanan   : 18mm (tidak berubah)
        ============================================= */
        .content-wrap {
            padding: 22mm 18mm 10mm 18mm;
        }

        /* =============================================
           HEADER LOGO
        ============================================= */
        .page-header {
            margin-bottom: 6mm;
        }

        .page-header img {
            width: 55px;
        }

        /* =============================================
           JUDUL
        ============================================= */
        .doc-title {
            text-align: center;
            margin-bottom: 6mm;
        }

        .doc-title h2 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .doc-title p {
            font-size: 12pt;
            text-decoration: underline;
        }

        /* =============================================
           TEKS UMUM
        ============================================= */
        .text-justify {
            text-align: justify;
            margin-bottom: 2mm;
            font-size: 12pt;
            line-height: 1.55;
        }

        /* =============================================
           PARA PIHAK
        ============================================= */
        .pihak-table {
            width: 100%;
            margin-bottom: 2mm;
            font-size: 12pt;
        }

        .pihak-table td {
            vertical-align: top;
            padding: 0.5px 0;
        }

        .pihak-table td:first-child { width: 105px; }
        .pihak-table td:nth-child(2) { width: 14px; text-align: center; }

        .pihak-label {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 2mm;
        }

        /* =============================================
           SECTION TITLE
        ============================================= */
        .section-title {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 2mm;
        }

        /* =============================================
           PASAL
        ============================================= */
        .pasal-item {
            text-align: justify;
            margin-bottom: 3mm;
            font-size: 12pt;
            line-height: 1.55;
        }

        .pasal-sebelum {
            margin: 2mm 0;
            padding-left: 10mm;
            text-align: justify;
            font-size: 12pt;
            line-height: 1.55;
        }

        .berubah-label {
            margin: 2mm 0 1mm 0;
            font-size: 12pt;
        }

        .berubah-isi {
            margin: 1mm 0 3mm 0;
            padding-left: 10mm;
            text-align: justify;
            font-size: 12pt;
            line-height: 1.55;
        }

        /* =============================================
           PENUTUP
        ============================================= */
        .penutup {
            text-align: justify;
            margin-top: 5mm;
            margin-bottom: 8mm;
            font-size: 12pt;
            line-height: 1.55;
        }

        /* =============================================
           TANDA TANGAN
        ============================================= */
        .ttd-table {
            width: 100%;
            font-size: 12pt;
        }

        .ttd-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .ttd-label {
            font-weight: bold;
            margin-bottom: 1mm;
        }

        .ttd-company {
            margin-bottom: 18mm;
        }

        /* =============================================
           PAGE BREAK — pindah ke halaman baru
           - page-break-before : memaksa ganti halaman
           - padding-top       : 22mm → konten Pasal 4
                                 turun dari atas halaman baru
                                 (naikkan angka ini untuk turun
                                 lebih jauh lagi)
        ============================================= */
        .page-break {
            page-break-before: always;
            padding-top: 22mm;
        }
    </style>
</head>
<body>

{{-- =============================================
     HALAMAN 1 — COVER FULL IMAGE
============================================= --}}
<div class="cover-page">
    <img src="{{ public_path('assets/cover_addendum.jpg') }}" alt="Cover Addendum">
</div>

{{-- =============================================
     BACKGROUND — diulang otomatis setiap halaman
     (DomPDF: position:fixed = repeat on every page)
============================================= --}}
<div class="page-background">
    <img src="{{ public_path('assets/template bg page perjanjian.jpeg') }}" alt="">
</div>

{{-- =============================================
     KONTEN UTAMA — satu aliran, DomPDF yang atur
     pemisahan halaman secara alami
============================================= --}}
<div class="content-wrap">

    {{-- Judul --}}
    <div class="doc-title">
        <h2>Addendum {{ $addendum->roman_order }}</h2>
        <p>Nomor: {{ $addendum->addendum_number }}</p>
    </div>

    {{-- Subtitle --}}
    <div class="text-justify">
        Perpanjangan Perjanjian Sewa Menyewa Kantor Virtual (Virtual Office) antara
        {{ config('company.name') }} (Urban Office) dengan
        {{ $transaction->company_name ?? $transaction->nama_lengkap }}
    </div>

    {{-- Pembukaan --}}
    <div class="text-justify">
        Pada hari ini {{ $addendum->addendum_date->translatedFormat('l') }},
        {{ $addendum->addendum_date->translatedFormat('j F Y') }}
        yang bertanda tangan di bawah ini:
    </div>

    {{-- PIHAK PERTAMA --}}
    <table class="pihak-table">
        <tr><td>Nama</td><td>:</td><td>{{ config('company.director') }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>{{ config('company.representative_title') }}</td></tr>
        <tr><td>Perusahaan</td><td>:</td><td>{{ config('company.name') }}</td></tr>
        <tr>
            <td>Alamat</td><td>:</td>
            <td>{{ config('company.address') }}, {{ config('company.city') }}</td>
        </tr>
    </table>
    <div class="pihak-label">Selanjutnya disebut "PIHAK PERTAMA"</div>

    {{-- PIHAK KEDUA --}}
    <table class="pihak-table">
        <tr><td>Nama</td><td>:</td><td>{{ $transaction->nama_lengkap }}</td></tr>
        <tr><td>Jabatan</td><td>:</td><td>Penyewa Urban</td></tr>
        <tr><td>Perusahaan</td><td>:</td><td>{{ $transaction->company_name ?? '-' }}</td></tr>
        <tr>
            <td>Alamat</td><td>:</td>
            <td>{{ $transaction->location->address ?? config('company.address') }},
                {{ $transaction->location->city->name ?? config('company.city') }}</td>
        </tr>
    </table>
    <div class="pihak-label">Selanjutnya disebut "PIHAK KEDUA"</div>

    {{-- Referensi Kelanjutan Dokumen --}}
    <div class="text-justify">
        @if($parentAddendum)
            PARA PIHAK sebelumnya telah menandatangani Addendum Perjanjian Sewa Menyewa kantor virtual antara
            {{ config('company.name') }} dengan {{ $transaction->company_name ?? $transaction->nama_lengkap }}
            tertanggal {{ $parentAddendum->addendum_date?->translatedFormat('j F Y') ?? '-' }}
            nomor: {{ $parentAddendum->addendum_number ?? '-' }};
        @else
            PARA PIHAK sebelumnya telah menandatangani Perjanjian Sewa Menyewa kantor virtual antara
            {{ config('company.name') }} dengan {{ $transaction->company_name ?? $transaction->nama_lengkap }}
            tertanggal {{ $originalContract->contract_date?->translatedFormat('j F Y') ?? '-' }}
            nomor: {{ $originalContract->contract_number ?? '-' }};
        @endif
    </div>

    {{-- Kesepakatan --}}
    <div class="text-justify">
        Atas hal-hal tersebut diatas, maka PARA PIHAK telah sepakat untuk menandatangani addendum
        dari Perpanjangan Perjanjian Sewa Menyewa Kantor Virtual (selanjutnya disebut
        "Addendum {{ $addendum->roman_order }}")
        dengan ini menerangkan hal-hal lebih lanjut sebagai berikut:
    </div>

    {{-- Section Title --}}
    <div class="section-title">Perubahan dan Penambahan Ketentuan</div>

    {{-- PASAL 2 — DURASI --}}
    <div class="pasal-item">
        1.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pasal 2 ayat (1)/2.1 sebelumnya berbunyi:

        <div class="pasal-sebelum">
            @if($parentAddendum)
                "Perjanjian ini dilangsungkan untuk jangka waktu
                {{ $parentAddendum->duration_text }}
                kecuali diakhiri lebih awal oleh salah satu pihak berdasarkan ketentuan dalam
                Perjanjian ini, terhitung sejak tanggal
                {{ $parentAddendum->formatted_start_date }}
                hingga {{ $parentAddendum->formatted_end_date }}."
            @else
                "Perjanjian ini dilangsungkan untuk jangka waktu
                {{ $originalContract->duration_text ?? ($originalContract->transaction->bulan . ' (' . $addendum->numberToWordsPublic($originalContract->transaction->bulan ?? 12) . ') bulan') }}
                kecuali diakhiri lebih awal oleh salah satu pihak berdasarkan ketentuan dalam
                Perjanjian ini, terhitung sejak tanggal
                {{ $originalContract->formatted_start_date ?? '-' }}
                hingga {{ $originalContract->formatted_end_date ?? '-' }}."
            @endif
        </div>

        <div class="berubah-label">Berubah Menjadi :</div>

        <div class="berubah-isi">
            "Perjanjian ini dilangsungkan untuk jangka waktu
            {{ $addendum->duration_text }}
            kecuali diakhiri lebih awal oleh salah satu pihak berdasarkan ketentuan dalam
            Perjanjian ini, terhitung sejak tanggal
            {{ $addendum->formatted_start_date }}
            hingga {{ $addendum->formatted_end_date }}."
        </div>
    </div>

    {{-- =============================================
         PASAL 4 — PEMBAYARAN
         page-break : pindah ke halaman baru
         padding-top: konten turun dari atas halaman
    ============================================= --}}
    <div class="pasal-item page-break">
        2. Pasal 4 ayat (2)/4.2 sebelumnya berbunyi:<br>

        <div class="pasal-sebelum">
            @if($parentAddendum)
                Cara Pembayaran Uang Sewa, Uang Sewa untuk
                {{ $parentAddendum->duration_text }}
                total sejumlah Rp&nbsp;{{ number_format($parentAddendum->gross_amount, 0, ',', '.') }},-
                ({{ $terbilang_sebelum }})
                yang akan dibayarkan dalam 1 termin yaitu:<br><br>
                1.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rp&nbsp;{{ number_format($parentAddendum->gross_amount, 0, ',', '.') }},-
                ({{ $terbilang_sebelum }})
                pada saat kontrak ditandatangani atau tanggal {{ $parentAddendum->formatted_addendum_date }}
            @else
                Cara Pembayaran Uang Sewa, Uang Sewa untuk jangka waktu
                {{ $originalContract->transaction->bulan ?? 12 }}
                ({{ $addendum->numberToWordsPublic($originalContract->transaction->bulan ?? 12) }})
                bulan total sejumlah
                Rp&nbsp;{{ number_format($originalContract->transaction->gross_amount, 0, ',', '.') }},-
                ({{ $terbilang_original }})
                yang akan dibayarkan dalam 1 termin yaitu:<br><br>
                1.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rp&nbsp;{{ number_format($originalContract->transaction->gross_amount, 0, ',', '.') }},-
                ({{ $terbilang_original }})
                pada saat kontrak ditandatangani atau tanggal
                {{ $originalContract->formatted_contract_date }}
            @endif
        </div>

        <div class="berubah-label">Berubah menjadi :</div>

        <div class="berubah-isi">
            Cara Pembayaran Uang Sewa, Uang Sewa untuk
            {{ $addendum->duration_text }}
            total sejumlah Rp&nbsp;{{ number_format($addendum->gross_amount, 0, ',', '.') }},-
            ({{ $terbilang_baru }})
            yang akan dibayarkan dalam 1 termin yaitu:<br><br>
            1.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rp&nbsp;{{ number_format($addendum->gross_amount, 0, ',', '.') }},-
            ({{ $terbilang_baru }})
            pada saat kontrak ditandatangani atau tanggal {{ $addendum->formatted_addendum_date }}.
        </div>
    </div>

    {{-- Ketentuan Denda --}}
    <div class="text-justify">
        Dengan ketentuan bilamana Pihak Kedua tidak memenuhi kewajibannya tepat pada waktunya atau
        setelah tenggang waktu 7 (tujuh) hari kerja, maka Pihak Kedua diwajibkan untuk membayar denda
        sebesar 10% untuk tiap-tiap hari terlambat membayar angsuran Uang Sewa tersebut, yang harus
        dibayar tiap-tiap hari dengan seketika dan sekali lunas dengan cara ditransfer ke rekening Pihak
        Pertama. Jumlah Uang Sewa tersebut adalah untuk Kantor Virtual (Virtual Office) paket office.
    </div>

    {{-- Info Rekening --}}
    <div class="text-justify">
        Pembayaran dapat dilakukan transfer ke Rekening {{ config('company.bank_name') }}
        : {{ config('company.bank_account') }}
        Atas Nama {{ config('company.bank_holder') }} atau my.urbanoffice.id
    </div>

    <div style="page-break-inside: avoid;">
        {{-- Penutup --}}
        <div class="penutup">
            Demikian addendum ini dibuat dan berlaku efektif sejak ditandatangani oleh PARA PIHAK, dalam
            rangkap dua dan bermaterai cukup. Addendum ini bersifat mengikat dan ketentuan lain yang
            tercantum pada perjanjian sebelumnya tetap berlaku.
        </div>

        {{-- Tanda Tangan --}}
        <table class="ttd-table" style="page-break-inside: avoid;">
            <tr>
                <td>
                    <div class="ttd-label">PIHAK PERTAMA</div>
                    <div class="ttd-company">{{ config('company.name') }}</div>
                </td>
                <td>
                    <div class="ttd-label">PIHAK KEDUA</div>
                    <div class="ttd-company">{{ $transaction->company_name ?? '-' }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div>{{ config('company.director') }}</div>
                    <div>{{ config('company.representative_title') }}</div>
                </td>
                <td>
                    <div>{{ $transaction->nama_lengkap }}</div>
                    <div>Penyewa</div>
                </td>
            </tr>
        </table>

        <div style="margin-top: 2rem; border-top: 1px solid #ccc; padding-top: 1rem; page-break-inside: avoid;">
            <table width="100%">
                <tr>
                    <td width="75%" style="vertical-align: middle;">
                        <p style="font-size: 9pt; color: #555; margin: 0;">
                            <strong>Verifikasi Keaslian Dokumen</strong><br>
                            Scan QR Code ini untuk melihat data Addendum secara online.<br>
                            Dokumen ini diterbitkan secara digital oleh PT. Urban Kreasi Bersama.
                        </p>
                        <p style="font-size: 8pt; color: #888; margin-top: 4px; word-break: break-all;">
                            {{ route('addendum.public.verify', ['token' => $addendum->public_token ?? '-']) }}
                        </p>
                    </td>
                    <td width="25%" style="text-align: right; vertical-align: middle;">
                        @if(!empty($qrCodeBase64))
                            <img src="{{ $qrCodeBase64 }}" width="100" height="100" style="display: block; margin-left: auto;" />
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>{{-- /content-wrap --}}

</body>
</html>