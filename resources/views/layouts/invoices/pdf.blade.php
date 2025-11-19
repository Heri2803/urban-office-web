<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaction->order_id }}</title>
    <style>
        @media print {
            body { 
                margin: 0;
                padding: 0;
            }
            .print-container {
                page-break-after: avoid;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 3px solid #ff6b35;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo {
            width: 45px;
            height: 45px;
        }
        
        .company-info {
            font-size: 10px;
            color: #666;
            line-height: 1.5;
        }
        
        .company-name {
            font-weight: bold;
            color: #333;
            font-size: 13px;
            margin-bottom: 2px;
        }
        
        .invoice-title-section {
            text-align: right;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: 300;
            color: #ff6b35;
            margin-bottom: 3px;
        }
        
        .invoice-meta {
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }
        
        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }
        
        .info-table thead th {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            color: white;
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            border-right: 1px solid rgba(255,255,255,0.3);
        }
        
        .info-table thead th:last-child {
            border-right: none;
        }
        
        .info-table tbody td {
            padding: 10px;
            vertical-align: top;
            border-right: 1px solid #e5e5e5;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .info-table tbody td:last-child {
            border-right: none;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-size: 9px;
            color: #888;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .info-value {
            font-size: 11px;
            color: #333;
            font-weight: 600;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-paid {
            background: #10b981;
            color: white;
        }
        
        .status-pending {
            background: #fbbf24;
            color: #78350f;
        }
        
        .status-failed {
            background: #ef4444;
            color: white;
        }
        
        .pkp-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .pkp-yes {
            background: #d1fae5;
            color: #065f46;
        }
        
        .pkp-no {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Summary Table */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #ddd;
        }
        
        .summary-table thead th {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 8px 12px;
            font-weight: 600;
            font-size: 11px;
        }
        
        .summary-table thead th:first-child {
            text-align: left;
        }
        
        .summary-table thead th:last-child {
            text-align: right;
        }
        
        .summary-table tbody td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .summary-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .service-name {
            font-weight: 600;
            color: #333;
            font-size: 11px;
        }
        
        .service-detail {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }
        
        .total-row {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        }
        
        .total-label {
            font-weight: bold;
            color: #333;
            font-size: 12px;
        }
        
        .total-amount {
            font-weight: bold;
            color: #ff6b35;
            font-size: 14px;
            text-align: right;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding-top: 12px;
            border-top: 1px solid #e5e5e5;
            font-size: 9px;
            color: #888;
            line-height: 1.6;
        }
        
        .footer-contact {
            margin-top: 8px;
            color: #666;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <img src="{{ public_path('assets/LOGO_URBAN_OFFICE.png') }}" alt="Urban Office" class="logo">
                <div class="company-info">
                    <div class="company-name">Urban Office</div>
                    {{-- PERUBAHAN: Menggunakan relasi city dari city_id --}}
                    <div>{{ $transaction->city->name ?? 'Jakarta' }}</div>
                </div>
            </div>
            <div class="invoice-title-section">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <div><strong>#{{ $transaction->order_id }}</strong></div>
                    <div>{{ date('d F Y', strtotime($transaction->created_at ?? now())) }}</div>
                </div>
            </div>
        </div>

        <!-- Info Table -->
        <table class="info-table">
            <thead>
                <tr>
                    <th style="width: 25%;">INFORMASI PELANGGAN</th>
                    <th style="width: 25%;">DETAIL BOOKING</th>
                    <th style="width: 25%;">LAYANAN</th>
                    <th style="width: 25%;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <!-- Customer Info -->
                    <td>
                        <div class="info-item">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value">{{ $transaction->nama_lengkap }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">No. Telepon</div>
                            <div class="info-value">{{ $transaction->phone ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kota</div>
                            {{-- PERUBAHAN: Menggunakan relasi city dari city_id --}}
                            <div class="info-value">{{ $transaction->city->name ?? '-' }}</div>
                        </div>
                    </td>

                    <!-- Booking Info -->
                    <td>
                        <div class="info-item">
                            <div class="info-label">Tipe Ruangan</div>
                            <div class="info-value">{{ $transaction->room_type ?? '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jumlah Orang</div>
                            <div class="info-value">{{ $transaction->jumlah_orang ?? '-' }} orang</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Booking</div>
                            <div class="info-value">{{ $transaction->booking_date ? date('d M Y', strtotime($transaction->booking_date)) : '-' }}</div>
                        </div>
                        @if(isset($transaction->jam))
                        <div class="info-item">
                            <div class="info-label">Jam</div>
                            <div class="info-value">{{ $transaction->jam }}</div>
                        </div>
                        @endif
                    </td>

                    <!-- Service Info -->
                    <td>
                        @if($transaction->room_type == 'Virtual Office')
                            @if(isset($transaction->paket))
                            <div class="info-item">
                                <div class="info-label">Paket</div>
                                <div class="info-value">{{ $transaction->paket }}</div>
                            </div>
                            @endif
                            
                            @if(isset($transaction->bulan))
                            <div class="info-item">
                                <div class="info-label">Durasi</div>
                                <div class="info-value">{{ $transaction->bulan }} Bulan</div>
                            </div>
                            @elseif(isset($transaction->tahun))
                            <div class="info-item">
                                <div class="info-label">Durasi</div>
                                <div class="info-value">{{ $transaction->tahun }} Tahun</div>
                            </div>
                            @endif
                            
                            @if(isset($transaction->status_pkp))
                            <div class="info-item">
                                <div class="info-label">Status PKP</div>
                                <div class="info-value">
                                    <span class="pkp-badge {{ $transaction->status_pkp == 'Ya' ? 'pkp-yes' : 'pkp-no' }}">
                                        {{ $transaction->status_pkp == 'Ya' ? 'PKP' : 'Non-PKP' }}
                                    </span>
                                </div>
                            </div>
                            @endif
                        @else
                            <div style="color: #ccc; font-style: italic;">-</div>
                        @endif
                    </td>

                    <!-- Status -->
                    <td>
                        <div class="info-item">
                            <div class="info-label">Pembayaran</div>
                            <div class="info-value">
                                @if($transaction->status == 'paid' || $transaction->status == 'settlement')
                                    <span class="status-badge status-paid">Lunas</span>
                                @elseif($transaction->status == 'pending')
                                    <span class="status-badge status-pending">Pending</span>
                                @else
                                    <span class="status-badge status-failed">Gagal</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Order ID</div>
                            <div class="info-value" style="font-family: monospace; font-size: 10px;">{{ $transaction->order_id }}</div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Summary Table -->
        <table class="summary-table">
            <thead>
                <tr>
                    <th>KETERANGAN</th>
                    <th style="width: 30%;">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="service-name">{{ $transaction->room_type ?? 'Layanan' }}</div>
                        @if($transaction->room_type == 'Virtual Office' && isset($transaction->paket))
                            <div class="service-detail">Paket: {{ $transaction->paket }}</div>
                        @endif
                        @if(isset($transaction->booking_date))
                            <div class="service-detail">{{ date('d F Y', strtotime($transaction->booking_date)) }}</div>
                        @endif
                    </td>
                    <td style="text-align: right; color: #555;">Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="total-label">TOTAL PEMBAYARAN</td>
                    <td class="total-amount">Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div>Terima kasih telah menggunakan layanan Urban Office</div>
            <div style="color: #aaa; margin-top: 3px;">Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan</div>
            <div class="footer-contact">
                Urban Office • info@urbanoffice.com • www.urbanoffice.com
            </div>
        </div>
    </div>
</body>
</html>