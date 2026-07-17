<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaction->order_id }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            background: transparent;
            margin: 0;
            padding: 0;
        }

        /* ===== BACKGROUND FIXED (muncul di semua halaman) ===== */
        .background-fixed {
            position: fixed;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            z-index: -1;
            pointer-events: none;
        }

        .background-fixed img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 1;
        }

        .print-container {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: transparent;
            position: relative;
            padding: 0;
        }

        /* ===== CONTENT WRAPPER ===== */
        .content-wrapper {
            position: relative;
            z-index: 1;
            padding: 180px 50px 150px 50px;
        }

        /* ===== INFO SECTION ===== */
        .info-section-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-section-table td {
            vertical-align: top;
            padding: 0;
        }

        .buyer-info {
            width: 55%;
        }

        .invoice-details {
            width: 40%;
            text-align: right;
        }

        .info-row {
            margin-bottom: 8px;
            font-size: 13px;
        }

        .info-label {
            min-width: 120px;
            font-weight: bold;
            color: #333;
            flex-shrink: 0;
        }

        .info-value {
            font-weight: normal;
            color: #333;
        }

        /* ===== SERVICE TITLE ===== */
        .service-title {
            background: #ff6b35;
            color: white;
            padding: 12px 20px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 0;
            border-radius: 8px 8px 0 0;
        }

        /* ===== DETAILS TABLE ===== */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            margin-bottom: 25px;
            page-break-inside: auto;
        }

        .details-table tr {
            border-bottom: 1px solid #ddd;
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table td {
            padding: 12px 20px;
            font-size: 13px;
        }

        .details-table td:first-child {
            width: 45%;
            color: #333;
            font-weight: normal;
            background: #f9f9f9;
        }

        .details-table td:last-child {
            width: 55%;
            color: #333;
            font-weight: normal;
        }

        /* Lunch rows */
        .details-table tr.lunch-header td {
            background: #e8f5e9 !important;
            color: #2e7d32 !important;
            font-weight: bold;
            font-size: 13px;
            text-align: center;
            padding: 10px 20px;
        }

        .details-table tr.lunch-detail td {
            background: #f9f9f9;
            padding: 8px 20px;
            font-size: 12.5px;
        }

        .details-table tr.lunch-detail td:first-child {
            background: #f5f5f5;
            color: #555;
        }

        .details-table tr.lunch-total td {
            background: #fff8e1;
            font-weight: 600;
            border-top: 2px dashed #ffd54f;
        }

        .details-table tr.lunch-total td:first-child {
            background: #fff3cd;
        }

        .details-table tr.subtotal-row td {
            background: #e3f2fd;
            font-weight: 600;
            border-top: 2px solid #bbdefb;
        }

        .details-table tr.subtotal-row td:first-child {
            background: #bbdefb;
        }

        .details-table tr.total-row td {
            font-weight: bold;
            background: #fff !important;
            border-top: 2px solid #ff6b35;
            font-size: 14px;
            position: relative;
            z-index: 11;
        }

        .details-table tr.total-row td:last-child {
            color: #ff6b35;
            font-size: 15px;
            position: relative;
        }

        .details-table tr.total-row {
            position: relative;
            z-index: 10;
            page-break-inside: avoid;
            page-break-before: avoid;
            page-break-after: avoid;
        }

        /* ===== STATUS BADGE ===== */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 10px;
            text-transform: uppercase;
            vertical-align: middle;
        }

        .status-badge.status-settlement {
            background: #10b981;
            color: white;
        }

        .status-badge.status-pending {
            background: #f59e0b;
            color: white;
        }

        .status-badge.status-expire {
            background: #ef4444;
            color: white;
        }

        /* ===== TERMS SECTION ===== */
        .terms-section {
            margin-top: 120px;
            clear: both;
            position: relative;
            z-index: 1;
            page-break-before: always;
        }

        .terms-title {
            color: #ff6b35;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .terms-content {
            font-size: 12px;
            color: #333;
            line-height: 1.8;
        }

        .terms-subtitle {
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        .terms-content ul {
            margin-left: 20px;
            margin-top: 5px;
        }

        .terms-content li {
            margin-bottom: 5px;
        }

        /* ===== SIGNATURE SECTION ===== */
        .signature-section {
            margin-top: 50px;
            text-align: right;
            position: relative;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            padding: 20px;
            min-width: 250px;
        }

        .signature-image {
            margin: 15px 0;
            padding: 10px;
            display: inline-block;
        }

        /* ===== PAGE 2 ===== */
        .page-break-avoid {
            page-break-inside: avoid;
        }

        .page-continuation {
            page-break-before: always;
            position: relative;
        }

        .page-2 .content-wrapper {
            padding: 180px 50px 150px 50px !important;
        }

        .page-2 .invoice-details {
            margin-top: 0 !important;
        }
    </style>
</head>
<body>

    {{-- BACKGROUND IMAGE (base64 dari controller) --}}
    @if($hasBackground)
    <div class="background-fixed">
        <img src="{{ $backgroundImage }}" alt="Background">
    </div>
    @endif

    <div class="print-container">
        <div class="content-wrapper">

            {{-- ===== INFO SECTION ===== --}}
            <table class="info-section-table">
                <tr>
                    {{-- KIRI: Info Pembeli --}}
                    <td class="buyer-info">
                        <div class="info-row">
                            <span class="info-label">Nama Pembeli</span>
                            <span class="colon-separator"> : </span>
                            <span class="info-value">{{ $transaction->nama_lengkap }}</span>
                        </div>

                        @if($transaction->company_name)
                        <div class="info-row">
                            <span class="info-label">Perusahaan</span>
                            <span class="colon-separator"> : </span>
                            <span class="info-value">{{ $transaction->company_name }}</span>
                        </div>
                        @endif

                        <div class="info-row">
                            <span class="info-label">Alamat</span>
                            <span class="colon-separator"> : </span>
                            <span class="info-value" style="word-wrap: break-word; line-height: 1.4;">
                                {{ $transaction->location->address ?? '-' }}
                            </span>
                        </div>
                    </td>

                    {{-- SPACER --}}
                    <td style="width: 5%;"></td>

                    {{-- KANAN: Detail Invoice --}}
                    <td class="invoice-details">
                        <div class="info-row">
                            <span class="info-label">No</span>
                            <span class="colon-separator"> : </span>
                            <span class="info-value">
                                {{ $invoice->invoice_number ?? $transaction->invoice->invoice_number ?? $transaction->order_id }}
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Metode Pembayaran</span>
                            <span class="colon-separator"> : </span>
                            <span class="info-value">{{ $transaction->payment_type ?? 'Belum Dibayar' }}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Tanggal</span>
                            <span class="colon-separator"> : </span>
                            <span class="info-value">
                                {{ date('d F Y', strtotime($transaction->created_at ?? now())) }}
                            </span>
                        </div>
                    </td>
                </tr>
            </table>

            {{-- ===== SERVICE TITLE ===== --}}
            <div class="service-title" style="text-align: center;">
                @php
                    $roomType = $transaction->room_type ?? '';
                    $serviceCategoryName = '';

                    if ($transaction->serviceCategory && !empty($transaction->serviceCategory->name)) {
                        $name = trim($transaction->serviceCategory->name);
                        if (!in_array(strtolower($name), ['tanpa kategori', '(tanpa kategori)', '-', ''])) {
                            $serviceCategoryName = $name;
                        }
                    }

                    $finalText = $roomType;
                    if (!empty($serviceCategoryName) && strtolower($roomType) !== strtolower($serviceCategoryName)) {
                        $finalText .= " ({$serviceCategoryName})";
                    }

                    $locationName = $transaction->location->name ?? $transaction->city->name ?? 'MERR';
                @endphp

                Pembayaran Sewa <strong>{{ $finalText }}</strong>
                <span>di {{ $locationName }}</span>
            </div>

            {{-- ===== DETAILS TABLE ===== --}}
            <table class="details-table">

                <tr>
                    <td>Perjanjian Sewa</td>
                    <td>{{ $transaction->order_id }}</td>
                </tr>

                @if($transaction->room_id && $transaction->room)
                <tr>
                    <td>Nomor Ruangan</td>
                    <td>
                        {{ $transaction->room->room_number ?? '-' }}
                        @if($transaction->room->room_name)
                            ({{ $transaction->room->room_name }})
                        @endif
                    </td>
                </tr>
                @endif

                {{-- Tanggal & Waktu Mulai --}}
                <tr>
                    <td>Tanggal dan Waktu Mulai</td>
                    <td>
                        @php
                            $startDate = $transaction->booking_date ?? now();
                            $startTime = $transaction->start_time ?? null;
                            $startFormatted = date('d F Y', strtotime($startDate));
                            if ($startTime) {
                                $startFormatted .= ' ' . date('H:i', strtotime($startTime));
                            }
                        @endphp
                        {{ $startFormatted }}
                    </td>
                </tr>

                {{-- Durasi Sewa --}}
                <tr>
                    <td>Durasi Sewa</td>
                    <td>
                        @php
                            $duration     = 0;
                            $durationUnit = '';
                            $durationText = '';

                            if ($transaction->tahun && $transaction->tahun > 0) {
                                $duration     = $transaction->tahun;
                                $durationUnit = 'Tahun';
                            } elseif ($transaction->bulan && $transaction->bulan > 0) {
                                $duration     = $transaction->bulan;
                                $durationUnit = 'Bulan';
                            } elseif ($transaction->minggu && $transaction->minggu > 0) {
                                $duration     = $transaction->minggu;
                                $durationUnit = 'Minggu';
                            } elseif ($transaction->hari && $transaction->hari > 0) {
                                $duration     = $transaction->hari;
                                $durationUnit = 'Hari';
                            } elseif ($transaction->jam && $transaction->jam > 0) {
                                $duration     = $transaction->jam;
                                $durationUnit = 'Jam';
                            }

                            $endDate           = $startDate;
                            $endTimeFormatted  = '';

                            if ($duration > 0 && $durationUnit) {
                                switch ($durationUnit) {
                                    case 'Tahun':
                                        $endDate = date('d F Y', strtotime($startDate . " + {$duration} years"));
                                        break;
                                    case 'Bulan':
                                        $endDate = date('d F Y', strtotime($startDate . " + {$duration} months"));
                                        break;
                                    case 'Minggu':
                                        $endDate = date('d F Y', strtotime($startDate . " + {$duration} weeks"));
                                        break;
                                    case 'Hari':
                                        $endDate = date('d F Y', strtotime($startDate . " + {$duration} days"));
                                        break;
                                    case 'Jam':
                                        if ($startTime) {
                                            $endTime          = date('H:i', strtotime($startTime . " + {$duration} hours"));
                                            $endTimeFormatted = ' ' . $endTime;
                                        }
                                        $endDate = date('d F Y', strtotime($startDate)) . $endTimeFormatted;
                                        break;
                                }

                                $durationText = "{$duration} {$durationUnit}";
                            }
                        @endphp

                        @if($duration > 0)
                            {{ $durationText }}
                            @if($durationUnit != 'Jam')
                                <br>
                                <small style="color: #666; font-size: 11px;">
                                    (Berakhir: {{ $endDate }})
                                </small>
                            @endif
                        @else
                            Tidak ditentukan
                        @endif
                    </td>
                </tr>

                {{-- Catatan & Coffee Break --}}
                @if($transaction->coffee_break || $transaction->notes)
                <tr>
                    <td>Keterangan Tambahan</td>
                    <td>
                        @if($transaction->coffee_break)
                            <strong>Coffee Break:</strong> {{ $transaction->coffee_break }}<br>
                        @endif
                        @if($transaction->notes)
                            <strong>Catatan:</strong> {{ $transaction->notes }}
                        @endif
                    </td>
                </tr>
                @endif

                {{-- ===== LUNCH ITEMS ===== --}}
                @if($transaction->lunches && $transaction->lunches->count() > 0)
                    @foreach($transaction->lunches as $lunch)
                        @php
                            $lunchName       = $lunch->lunchOption->name ?? 'Paket Lunch';
                            $quantity        = $lunch->quantity ?? 1;
                            $unitPrice       = $lunch->unit_price ?? 0;
                            $subtotal        = $lunch->subtotal ?? ($quantity * $unitPrice);
                            $formattedUnit   = 'Rp. ' . number_format($unitPrice, 0, ',', '.');
                            $formattedSub    = 'Rp. ' . number_format($subtotal, 0, ',', '.');
                        @endphp

                        <tr>
                            <td>Lunch Item</td>
                            <td>{{ $lunchName }} - {{ $formattedUnit }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="padding-left: 20px; color: #666; font-size: 12px;">
                                {{ $quantity }} porsi x {{ $formattedUnit }} = {{ $formattedSub }}
                            </td>
                        </tr>
                    @endforeach

                    @php $totalLunch = $transaction->lunches->sum('subtotal'); @endphp

                    @if($transaction->lunches->count() > 1)
                    <tr>
                        <td style="font-weight: 600;">Total Lunch</td>
                        <td style="font-weight: 600;">Rp. {{ number_format($totalLunch, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                @endif

                {{-- Biaya Sewa --}}
                <tr>
                    <td>Total Biaya Sewa</td>
                    <td>
                        @php
                            $sewaAmount = $transaction->gross_amount - ($transaction->deposit ?? 0);
                            if (($transaction->lunch_total ?? 0) > 0) {
                                $sewaAmount = $transaction->gross_amount - ($transaction->deposit ?? 0) - $transaction->lunch_total;
                            }
                            $sewaAmount = max(0, $sewaAmount);
                        @endphp
                        Rp. {{ number_format($sewaAmount, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- Deposit (hanya Virtual Office / Private Office) --}}
                @php
                    $showDeposit  = false;
                    $roomTypeLower = strtolower($transaction->room_type ?? '');

                    if (in_array($roomTypeLower, ['virtual office', 'private office', 'virtual', 'private'])) {
                        $showDeposit = true;
                    }

                    if ($transaction->serviceCategory) {
                        $categoryName = strtolower($transaction->serviceCategory->name ?? '');
                        if (strpos($categoryName, 'virtual') !== false || strpos($categoryName, 'private') !== false) {
                            $showDeposit = true;
                        }
                    }
                @endphp

                @if($showDeposit && ($transaction->deposit ?? 0) > 0)
                <tr>
                    <td>Uang Jaminan (Deposit)</td>
                    <td>Rp. {{ number_format($transaction->deposit, 0, ',', '.') }}</td>
                </tr>
                @endif

                {{-- Subtotal --}}
                <tr class="subtotal-row">
                    <td>Subtotal</td>
                    <td>
                        @php
                            $subtotalAmount = $sewaAmount + ($transaction->lunch_total ?? 0);
                        @endphp
                        Rp. {{ number_format($subtotalAmount, 0, ',', '.') }}
                    </td>
                </tr>

                {{-- Total Final --}}
                <tr class="total-row">
                    <td>Total Yang Perlu Dibayar</td>
                    <td>
                        @php
                            $totalFinal = $subtotalAmount;
                            if ($showDeposit) {
                                $totalFinal += ($transaction->deposit ?? 0);
                            }
                        @endphp

                        Rp. {{ number_format($totalFinal, 0, ',', '.') }}

                        <span class="status-badge status-{{ strtolower($transaction->status) }}">
                            {{ ucfirst(str_replace('_', ' ', $transaction->status)) }}
                        </span>
                    </td>
                </tr>

            </table>

            {{-- ===== TERMS & CONDITIONS ===== --}}
            <div class="terms-section">
                <div class="terms-title">Syarat dan Ketentuan</div>
                <div class="terms-content">
                    <div class="terms-subtitle">Masa Berlaku & Penggunaan</div>
                    <ul>
                        <li>Berlaku sesuai periode invoice dan hanya untuk keperluan bisnis yang sah.</li>
                        @if($duration > 0)
                        <li>Durasi sewa: {{ $durationText }} mulai dari {{ $startFormatted }}.</li>
                        @endif
                    </ul>

                    <div class="terms-subtitle">Pembayaran & Jaminan</div>
                    <ul>
                        <li>Biaya dinyatakan lunas, jaminan dikembalikan setelah masa sewa berakhir sesuai ketentuan.</li>
                        @if($showDeposit && ($transaction->deposit ?? 0) > 0)
                        <li>Deposit sebesar Rp. {{ number_format($transaction->deposit, 0, ',', '.') }} akan dikembalikan setelah masa sewa berakhir dengan kondisi ruangan sesuai perjanjian.</li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- ===== SIGNATURE SECTION ===== --}}
            @if($hasTTD && $ttdImage)
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-image">
                        <img src="{{ $ttdImage }}" alt="Tanda Tangan" style="max-width: 150px; height: auto;">
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</body>
</html>