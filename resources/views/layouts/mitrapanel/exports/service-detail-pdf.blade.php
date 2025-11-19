<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi - {{ $room_type }}</title>

    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th { background: #f97316; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header-line { border-bottom: 3px solid #f97316; margin-bottom: 20px; padding-bottom: 10px; }
        .stat-box { background: #f3f4f6; padding: 10px; border-radius: 6px; text-align: center; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="header-line">
        <h2 style="margin:0; font-size:22px;">URBAN OFFICE</h2>
        <p style="color:#f97316; font-size:14px;">Detail Transaksi Layanan</p>
        <p>{{ $location->name }} — {{ $period_label }}</p>
    </div>

    {{-- Info --}}
    <table style="margin-bottom: 20px;">
        <tr>
            <td width="25%"><b>Layanan</b></td>
            <td>{{ $room_type }}</td>
        </tr>
        <tr>
            <td><b>Lokasi</b></td>
            <td>{{ $location->name }}</td>
        </tr>
        <tr>
            <td><b>Periode</b></td>
            <td>{{ $period_label }}</td>
        </tr>
        <tr>
            <td><b>Tanggal Cetak</b></td>
            <td>{{ $generated_at }}</td>
        </tr>
    </table>

    {{-- Stats --}}
    <table style="margin-bottom: 25px;">
        <tr>
            <td class="stat-box">
                <div>Total Transaksi</div>
                <strong>{{ number_format($stats['total_transactions']) }}</strong>
            </td>
            <td class="stat-box">
                <div>Total Nominal</div>
                <strong>Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</strong>
            </td>
            <td class="stat-box">
                <div>Rata-rata</div>
                <strong>Rp {{ number_format($stats['average_amount'], 0, ',', '.') }}</strong>
            </td>
            <td class="stat-box">
                <div>Tertinggi</div>
                <strong>Rp {{ number_format($stats['highest_amount'], 0, ',', '.') }}</strong>
            </td>
            <td class="stat-box">
                <div>Terendah</div>
                <strong>Rp {{ number_format($stats['lowest_amount'], 0, ',', '.') }}</strong>
            </td>
        </tr>
    </table>

    {{-- Table --}}
    @if($transactions->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th class="text-right">Nominal</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $i => $t)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $t->booking_date }}</td>
                        <td>{{ $t->order_id }}</td>
                        <td>{{ $t->nama_lengkap }}</td>
                        <td class="text-right">Rp {{ number_format($t->gross_amount, 0, ',', '.') }}</td>
                        <td class="text-center">{{ ucfirst($t->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align:center; padding: 30px; color:#777;">Tidak ada data transaksi.</p>
    @endif

    <div class="footer">
        Dicetak otomatis oleh sistem Urban Office • {{ $generated_at }}
    </div>

</body>
</html>
