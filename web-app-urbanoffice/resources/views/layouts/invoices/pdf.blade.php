<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaction->order_id }}</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 0; 
            padding: 20px; 
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2c5aa0;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
        }
        
        .logo {
            width: 80px;
            height: auto;
            margin-right: 15px;
        }
        
        .company-info {
            font-size: 14px;
            color: #666;
        }
        
        .invoice-title {
            text-align: right;
            color: #2c5aa0;
        }
        
        .invoice-title h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        
        .invoice-number {
            color: #666;
            font-size: 16px;
            margin: 5px 0;
        }
        
        .invoice-content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .customer-info, .booking-info {
            width: 48%;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2c5aa0;
        }
        
        .info-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            width: 40%;
        }
        
        .info-value {
            color: #333;
            width: 60%;
            text-align: right;
        }
        
        .status-paid {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pending {
            background: #ffc107;
            color: #333;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-failed {
            background: #dc3545;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .summary-table th {
            background: #2c5aa0;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .summary-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .summary-table tr:last-child td {
            border-bottom: none;
        }
        
        .total-row {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 1.2em;
        }
        
        .total-amount {
            color: #2c5aa0;
            font-size: 1.3em;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
        
        .virtual-office-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .pkp-status {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .pkp-yes {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .pkp-no {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- Header dengan Logo -->
    <div class="header">
        <div class="logo-section">
            <img src="{{ public_path('assets/LOGO_URBAN_OFFICE.png') }}" alt="Urban Office Logo" class="logo">
            <div class="company-info">
                <strong>Urban Office</strong><br>
                Professional Co-working Space<br>
                {{ $transaction->city ?? 'Jakarta' }}
            </div>
        </div>
        <div class="invoice-title">
            <h1>INVOICE</h1>
            <div class="invoice-number">#{{ $transaction->order_id }}</div>
            <div style="font-size: 14px; color: #666;">
                {{ date('d F Y', strtotime($transaction->created_at ?? now())) }}
            </div>
        </div>
    </div>

    <!-- Informasi Customer dan Booking -->
    <div class="invoice-content">
        <div class="customer-info">
            <div class="info-title">Informasi Pelanggan</div>
            <div class="info-row">
                <span class="info-label">Nama Lengkap:</span>
                <span class="info-value">{{ $transaction->nama_lengkap }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. Telepon:</span>
                <span class="info-value">{{ $transaction->phone ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kota:</span>
                <span class="info-value">{{ $transaction->city ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status Pembayaran:</span>
                <span class="info-value">
                    @if($transaction->status == 'paid' || $transaction->status == 'settlement')
                        <span class="status-paid">LUNAS</span>
                    @elseif($transaction->status == 'pending')
                        <span class="status-pending">PENDING</span>
                    @else
                        <span class="status-failed">GAGAL</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="booking-info">
            <div class="info-title">Detail Booking</div>
            <div class="info-row">
                <span class="info-label">Tipe Ruangan:</span>
                <span class="info-value">{{ $transaction->room_type ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jumlah Orang:</span>
                <span class="info-value">{{ $transaction->jumlah_orang ?? '-' }} orang</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Booking:</span>
                <span class="info-value">
                    {{ $transaction->booking_date ? date('d F Y', strtotime($transaction->booking_date)) : '-' }}
                </span>
            </div>
            @if(isset($transaction->jam))
            <div class="info-row">
                <span class="info-label">Jam:</span>
                <span class="info-value">{{ $transaction->jam }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Informasi Khusus Virtual Office -->
    @if($transaction->room_type == 'Virtual Office')
    <div class="virtual-office-info">
        <h4 style="margin: 0 0 15px 0; color: #1976d2;">Informasi Virtual Office</h4>
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
            @if(isset($transaction->paket))
            <div style="margin-bottom: 10px;">
                <strong>Paket:</strong> {{ $transaction->paket }}
                @if(isset($transaction->bulan))
                    - {{ $transaction->bulan }} Bulan
                @elseif(isset($transaction->tahun))
                    - {{ $transaction->tahun }} Tahun
                @endif
            </div>
            @endif
            
            @if(isset($transaction->status_pkp))
            <div style="margin-bottom: 10px;">
                <strong>Status PKP:</strong>
                <span class="pkp-status {{ $transaction->status_pkp == 'Ya' ? 'pkp-yes' : 'pkp-no' }}">
                    {{ $transaction->status_pkp == 'Ya' ? 'PKP' : 'Non-PKP' }}
                </span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Tabel Ringkasan -->
    <table class="summary-table">
        <thead>
            <tr>
                <th style="width: 60%;">Keterangan</th>
                <th style="width: 40%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $transaction->room_type ?? 'Layanan' }}</strong>
                    @if($transaction->room_type == 'Virtual Office' && isset($transaction->paket))
                        <br><small style="color: #666;">Paket: {{ $transaction->paket }}</small>
                    @endif
                    @if(isset($transaction->booking_date))
                        <br><small style="color: #666;">{{ date('d F Y', strtotime($transaction->booking_date)) }}</small>
                    @endif
                </td>
                <td style="text-align: right;">Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>TOTAL PEMBAYARAN</strong></td>
                <td style="text-align: right;" class="total-amount">
                    <strong>Rp {{ number_format($transaction->gross_amount, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Terima kasih telah menggunakan layanan Urban Office</p>
        <p>Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan</p>
        <p style="margin-top: 15px;">
            <strong>Urban Office</strong> | 
            Email: info@urbanoffice.com | 
            Website: www.urbanoffice.com
        </p>
    </div>
</body>
</html>