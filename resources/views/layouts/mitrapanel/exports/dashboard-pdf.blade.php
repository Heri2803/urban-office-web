<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Performance Report - Urban Office</title>
    <style>
        /* === VARIABLES ORANGE THEME === */
        :root {
            --orange-primary: #EA580C;
            --orange-dark: #C2410C;
            --orange-light: #FDBA74;
            --orange-bg: #FFF7ED;
            --orange-border: #FED7AA;
            --text-dark: #1F2937;
            --text-light: #6B7280;
            --bg-light: #F8FAFC;
            --white: #FFFFFF;
        }

        body { 
            font-family: DejaVu Sans, Arial, sans-serif; 
            font-size: 11px; 
            line-height: 1.3;
            color: var(--text-dark);
            margin: 0;
            padding: 20px;
            background: var(--white);
        }

        /* === HEADER === */
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            padding-bottom: 15px;
            border-bottom: 2px solid var(--orange-primary);
        }

        .header p { 
            margin: 5px 0 0; 
            color: var(--text-light);
            font-size: 12px;
        }

        /* === PERIOD INFO === */
        .period-info {
            background: var(--orange-bg);
            padding: 10px 15px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: center;
            border: 1px solid var(--orange-border);
        }

        .period-info strong {
            color: var(--orange-dark);
            font-weight: 600;
        }

        /* === PERFORMANCE METRICS - SINGLE COLUMN === */
        .metrics-section {
            margin: 20px 0;
        }

        .metrics-section h3 {
            color: var(--orange-dark);
            border-bottom: 1px solid var(--orange-light);
            padding-bottom: 8px;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .metric-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid var(--orange-border);
        }

        .metric-label {
            font-weight: 600;
            color: var(--orange-dark);
            font-size: 11px;
        }

        .metric-value {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 11px;
        }

        /* === TABLE STYLING - SIMPLIFIED === */
        .table-section {
            margin: 20px 0;
        }

        .table-section h3 {
            color: var(--orange-dark);
            border-bottom: 1px solid var(--orange-light);
            padding-bottom: 8px;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }

        /* FIXED TABLE HEAD STYLING */
        .data-table thead tr {
            background-color: #EA580C;
            color: white;
        }

        .data-table th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #C2410C;
            font-size: 9px;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 6px 6px;
            border: 1px solid #FED7AA;
            font-size: 9px;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #FFF7ED;
        }

        .data-table tbody tr:nth-child(odd) {
            background-color: white;
        }

        /* === STATUS BADGES === */
        .status-success {
            color: #059669;
            font-weight: 600;
            font-size: 9px;
            padding: 2px 6px;
            background: #D1FAE5;
            border-radius: 3px;
        }

        .status-pending {
            color: #D97706;
            font-weight: 600;
            font-size: 9px;
            padding: 2px 6px;
            background: #FEF3C7;
            border-radius: 3px;
        }

        .status-failed {
            color: #DC2626;
            font-weight: 600;
            font-size: 9px;
            padding: 2px 6px;
            background: #FEE2E2;
            border-radius: 3px;
        }

        .location-highlight {
            background: var(--orange-primary);
            color: var(--white);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
        }

        /* === FOOTER === */
        .footer { 
            margin-top: 25px; 
            text-align: center; 
            font-size: 9px; 
            color: var(--text-light);
            border-top: 1px solid var(--orange-light);
            padding-top: 12px;
        }

        .footer p {
            margin: 3px 0;
        }

        .footer strong {
            color: var(--orange-dark);
        }

        /* === NO DATA STATE === */
        .no-data {
            text-align: center;
            color: var(--text-light);
            font-style: italic;
            padding: 25px 15px;
            background: var(--orange-bg);
            border-radius: 6px;
            border: 1px dashed var(--orange-border);
            font-size: 11px;
        }

        /* === ANALYSIS SECTION === */
        .analysis-content {
            background: var(--orange-bg);
            padding: 12px;
            border-radius: 6px;
            border: 1px solid var(--orange-border);
            margin: 15px 0;
            font-size: 10px;
        }

        .analysis-content ul {
            margin: 5px 0;
            padding-left: 15px;
        }

        .analysis-content li {
            margin-bottom: 4px;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h2>URBAN OFFICE PERFORMANCE REPORT</h2>
        <p>Laporan Analisis Performa dan Transaksi</p>
    </div>

    <!-- PERIOD & LOCATION INFO -->
    <div class="period-info">
        <strong>Periode:</strong> {{ $period ?? 'Not Specified' }} | 
        <strong>Dibuat pada:</strong> {{ $generatedAt ?? now()->format('d/m/Y H:i') }}
        @if(isset($locationId) && $locationId > 0)
            | <strong>Lokasi:</strong> 
            <span class="location-highlight">{{ $selectedLocationName ?? 'Lokasi Terpilih' }}</span>
        @else
            | <strong>Lokasi:</strong> Semua Lokasi
        @endif
    </div>

    <!-- PERFORMANCE METRICS - SINGLE COLUMN -->
    <div class="metrics-section">
        <h3>PERFORMANCE METRICS</h3>
        <div class="metrics-grid">
            <div class="metric-row">
                <div class="metric-label">Total Transaksi</div>
                <div class="metric-value">{{ $stats['totalTransactions'] ?? 0 }}</div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Total Revenue</div>
                <div class="metric-value">Rp {{ number_format($stats['totalRevenue'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Lokasi Aktif</div>
                <div class="metric-value">{{ $stats['activeLocations'] ?? 0 }}</div>
            </div>
            <div class="metric-row">
                <div class="metric-label">Booking Pending</div>
                <div class="metric-value">{{ $stats['pendingBookings'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- RECENT TRANSACTIONS -->
    @if(!empty($recentTransactions) && count($recentTransactions) > 0)
    <div class="table-section">
        <h3>RECENT TRANSACTIONS</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Layanan</th>
                    <th>Customer</th>
                    <th>Lokasi</th>
                    <th>Nominal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTransactions as $index => $transaction)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $transaction['date'] ?? 'N/A' }}</strong></td>
                    <td>{{ $transaction['service'] ?? 'N/A' }}</td>
                    <td>{{ $transaction['customer'] ?? 'N/A' }}</td>
                    <td>{{ $transaction['location_name'] ?? 'N/A' }}</td>
                    <td style="text-align: right;"><strong>Rp {{ number_format($transaction['amount'] ?? 0, 0, ',', '.') }}</strong></td>
                    <td style="text-align: center;">
                        @if(isset($transaction['status']) && in_array($transaction['status'], ['settlement', 'capture', 'paid']))
                            <span class="status-success">Sukses</span>
                        @elseif(isset($transaction['status']) && $transaction['status'] === 'pending')
                            <span class="status-pending">Pending</span>
                        @else
                            <span class="status-failed">Gagal</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="no-data">
        <p>Tidak ada data transaksi tersedia untuk periode ini</p>
    </div>
    @endif

    <!-- PERFORMANCE ANALYSIS -->
    <div class="metrics-section">
        <h3>PERFORMANCE ANALYSIS</h3>
        <div class="analysis-content">
            <p><strong>Ringkasan Kinerja:</strong></p>
            <ul>
                <li>Total <strong>{{ $stats['totalTransactions'] ?? 0 }} transaksi</strong> berhasil diproses</li>
                <li>Revenue yang dihasilkan: <strong>Rp {{ number_format($stats['totalRevenue'] ?? 0, 0, ',', '.') }}</strong></li>
                <li><strong>{{ $stats['activeLocations'] ?? 0 }} lokasi</strong> aktif berkontribusi</li>
                <li><strong>{{ $stats['pendingBookings'] ?? 0 }} booking</strong> menunggu konfirmasi</li>
            </ul>
            
            <p><strong>Rekomendasi:</strong></p>
            <ul>
                @if(($stats['pendingBookings'] ?? 0) > 5)
                <li>Prioritas: Follow up {{ $stats['pendingBookings'] ?? 0 }} pending bookings</li>
                @endif
                @if(($stats['activeLocations'] ?? 0) <= 1)
                <li>Pertimbangkan ekspansi ke lebih banyak lokasi</li>
                @endif
                @if(($stats['totalRevenue'] ?? 0) < 1000000)
                <li>Fokus pada strategi peningkatan revenue</li>
                @endif
                @if(($stats['totalTransactions'] ?? 0) == 0)
                <li>Perlu strategi marketing untuk meningkatkan transaksi</li>
                @endif
            </ul>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p><strong>© {{ date('Y') }} Urban Office Management System</strong></p>
        <p>Laporan ini dihasilkan secara otomatis • Halaman 1 dari 1</p>
        <p>Confidential Business Document</p>
    </div>
</body>
</html>