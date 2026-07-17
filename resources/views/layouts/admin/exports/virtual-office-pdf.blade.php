<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Virtual Office</title>
    <style>
        @page {
            margin: 1.2cm 1cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            line-height: 1.4;
            color: #334155;
        }
        /* Header styling using table for horizontal layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
        }
        .logo-container {
            width: 180px;
        }
        .logo-img {
            max-width: 100%;
            height: auto;
            max-height: 55px;
        }
        .title-container {
            text-align: right;
        }
        .report-title {
            font-size: 15pt;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 5px 0;
            letter-spacing: -0.5px;
        }
        .report-subtitle {
            font-size: 9pt;
            color: #64748b;
            margin: 0;
        }
        /* Filter Metadata Section */
        .metadata-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .metadata-table td {
            padding: 8px 12px;
            font-size: 8pt;
            border: none;
        }
        .metadata-label {
            font-weight: bold;
            color: #475569;
            width: 12%;
        }
        .metadata-value {
            color: #0f172a;
        }
        /* Table styling */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 6px;
            font-size: 8pt;
            border: 1px solid #1e293b;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 7px 6px;
            border: 1px solid #e2e8f0;
            font-size: 8pt;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-semibold {
            font-weight: 600;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-settlement {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-expire {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-default {
            background-color: #e2e8f0;
            color: #475569;
        }
        /* Footer/Page number */
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 20px;
            text-align: center;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="logo-container">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo Urban Office">
                @else
                    <span style="font-size: 14pt; font-weight: bold; color: #f97316;">URBAN OFFICE</span>
                @endif
            </td>
            <td class="title-container">
                <h1 class="report-title">Laporan Transaksi Virtual Office</h1>
                <p class="report-subtitle">Sistem Manajemen Office Space & Coworking</p>
            </td>
        </tr>
    </table>

    <!-- Metadata / Filter Section -->
    <table class="metadata-table">
        <tr>
            <td class="metadata-label">Rentang Tanggal</td>
            <td class="metadata-value">: 
                @if(!empty($filters['date_from']) && !empty($filters['date_to']))
                    {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}
                @else
                    Semua Tanggal
                @endif
            </td>
            <td class="metadata-label">Filter Status</td>
            <td class="metadata-value">: 
                {{ !empty($filters['status']) ? ucfirst($filters['status']) : 'Settlement (Default)' }}
            </td>
        </tr>
        <tr>
            <td class="metadata-label">Pencarian</td>
            <td class="metadata-value">: {{ !empty($filters['search']) ? $filters['search'] : '-' }}</td>
            <td class="metadata-label">Batas Ekspor</td>
            <td class="metadata-value">: {{ !empty($filters['limit']) ? $filters['limit'] . ' Data Terbaru' : 'Semua Data' }}</td>
        </tr>
        <tr>
            <td class="metadata-label">Tanggal Cetak</td>
            <td class="metadata-value" colspan="3">: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%" class="text-center">No</th>
                <th width="10%">Order ID</th>
                <th width="15%">Nama Penyewa</th>
                <th width="17%">Kontak (Email / Phone)</th>
                <th width="10%" class="text-center">Tgl Booking</th>
                <th width="8%" class="text-center">Durasi</th>
                <th width="10%" class="text-right">Harga Sewa</th>
                <th width="9%" class="text-right">Deposit</th>
                <th width="10%" class="text-right">Total Bayar</th>
                <th width="8%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $t)
                @php
                    $deposit = $t->deposit ?? 0;
                    $grossAmount = $t->gross_amount ?? 0;
                    $sewa = $grossAmount - $deposit;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-semibold">{{ $t->order_id ?? '-' }}</td>
                    <td>{{ $t->nama_lengkap ?? '-' }}</td>
                    <td>
                        <span style="font-size: 7.5pt; color: #64748b;">{{ $t->email ?? '-' }}</span><br>
                        <span>{{ $t->phone ?? '-' }}</span>
                    </td>
                    <td class="text-center">
                        {{ $t->booking_date ? $t->booking_date->format('d/m/Y') : '-' }}
                    </td>
                    <td class="text-center">
                        @if($t->jam) {{ $t->jam }} Jam
                        @elseif($t->hari) {{ $t->hari }} Hari
                        @elseif($t->minggu) {{ $t->minggu }} Minggu
                        @elseif($t->bulan) {{ $t->bulan }} Bulan
                        @elseif($t->tahun) {{ $t->tahun }} Tahun
                        @else -
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($sewa, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($deposit, 0, ',', '.') }}</td>
                    <td class="text-right font-semibold">Rp {{ number_format($grossAmount, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @php
                            $statusClass = 'badge-default';
                            if ($t->status == 'settlement') $statusClass = 'badge-settlement';
                            elseif ($t->status == 'pending') $statusClass = 'badge-pending';
                            elseif ($t->status == 'expire') $statusClass = 'badge-expire';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $t->status ?? '-' }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada data transaksi Virtual Office ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Page Counter -->
    <div class="footer">
        Halaman <span class="page-number"></span> | Laporan Transaksi Virtual Office - Urban Office
    </div>

</body>
</html>
