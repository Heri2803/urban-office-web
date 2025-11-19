{{-- resources/views/mitrapanel/exports/faktur-pajak-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Faktur Pajak - {{ $faktur->invoice_number }}</title>
    <style>
        /* DJP-like Styling */
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 12px; 
            line-height: 1.4;
            color: #000;
        }
        .header { 
            border-bottom: 3px double #000; 
            padding-bottom: 10px; 
            margin-bottom: 15px;
        }
        .company-info { 
            float: left; 
            width: 60%; 
        }
        .faktur-info { 
            float: right; 
            width: 35%; 
            text-align: right;
        }
        .clear { clear: both; }
        .section { 
            margin: 15px 0; 
            padding: 10px;
            border: 1px solid #000;
        }
        .section-title { 
            background: #f0f0f0; 
            padding: 5px; 
            font-weight: bold;
            margin: -10px -10px 10px -10px;
            border-bottom: 1px solid #000;
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0;
        }
        .table th, .table td { 
            border: 1px solid #000; 
            padding: 6px 8px; 
            text-align: left;
        }
        .table th { 
            background: #f0f0f0; 
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .total-section { 
            margin-top: 20px; 
            border-top: 2px solid #000;
            padding-top: 10px;
        }
        .footer { 
            margin-top: 30px; 
            border-top: 1px solid #000;
            padding-top: 10px;
            font-size: 10px;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header Section (tetap sama) -->
    <div class="header">
        <div class="company-info">
            <div style="font-size: 16px; font-weight: bold;">URBAN OFFICE INDONESIA</div>
            <div>Jl. Contoh Alamat No. 123</div>
            <div>Jakarta Selatan, DKI Jakarta 12345</div>
            <div>NPWP: 12.345.678.9-012.345</div>
            <div>Telp: (021) 123-4567 | Email: admin@urbanoffice.com</div>
        </div>
        
        <div class="faktur-info">
            <div style="font-size: 18px; font-weight: bold; text-transform: uppercase;">Faktur Pajak</div>
            <div style="font-size: 14px; margin-top: 5px;">{{ $faktur->invoice_number }}</div>
            <div style="margin-top: 10px;">
                <div>Tanggal: {{ \Carbon\Carbon::parse($faktur->period)->lastOfMonth()->format('d/m/Y') }}</div>
                <div>Masa Pajak: {{ $formatted_period }}</div>
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <!-- Identitas Section -->
    <div class="section">
        <div class="section-title">IDENTITAS</div>
        
        <table width="100%" style="margin-bottom: 15px;">
            <tr>
                <td width="50%" style="vertical-align: top;">
                    <div class="text-bold">PENJUAL</div>
                    <div>URBAN OFFICE INDONESIA</div>
                    <div>Jl. Contoh Alamat No. 123</div>
                    <div>Jakarta Selatan, DKI Jakarta 12345</div>
                    <div>NPWP: 12.345.678.9-012.345</div>
                </td>
                <td width="50%" style="vertical-align: top;">
                    <div class="text-bold">PEMBELI</div>
                    <div>UMUM</div>
                    <div>-</div>
                    <div>NPWP: 00.000.000.0-000.000</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Detail Transaksi Section -->
    <div class="section">
        <div class="section-title">DETAIL TRANSAKSI</div>
        
        <table class="table">
            <thead>
                <tr>
                    <th width="60%">Uraian Jasa</th>
                    <th width="15%" class="text-right">DPP</th>
                    <th width="10%" class="text-right">PPN</th>
                    <th width="15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @if($service_details->count() > 0)
                    @foreach($service_details as $service)
                    <tr>
                        <td>
                            <div class="service-item">
                                <div class="service-name">{{ $service['room_type'] }}</div>
                                <div class="service-details">
                                    Lokasi: {{ $faktur->location->name }} | 
                                    Periode: {{ $formatted_period }} |
                                    {{ $service['transaction_count'] }} transaksi
                                </div>
                            </div>
                        </td>
                        <td class="text-right">{{ number_format($service['total_amount'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($service['total_amount'] * 0.11, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($service['total_amount'] * 1.11, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @else
                    <!-- Fallback jika tidak ada detail transaksi -->
                    <tr>
                        <td>
                            Jasa Penyediaan Ruang Kerja<br>
                            <small>Lokasi: {{ $faktur->location->name }}</small><br>
                            <small>Periode: {{ $formatted_period }}</small>
                        </td>
                        <td class="text-right">{{ number_format($faktur->total_revenue, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($faktur->tax_amount, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($faktur->total_revenue + $faktur->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <table width="100%">
                <tr>
                    <td width="70%">
                        @if($service_details->count() > 0)
                            <div style="font-size: 11px; color: #666;">
                                Total Transaksi: {{ $total_transactions }} transaksi<br>
                                Detail per layanan seperti tertera di atas
                            </div>
                        @endif
                    </td>
                    <td width="30%">
                        <table width="100%">
                            <tr>
                                <td><strong>DPP:</strong></td>
                                <td class="text-right">Rp {{ number_format($faktur->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>PPN (11%):</strong></td>
                                <td class="text-right">Rp {{ number_format($faktur->tax_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr style="border-top: 1px solid #000;">
                                <td><strong>TOTAL:</strong></td>
                                <td class="text-right"><strong>Rp {{ number_format($faktur->total_revenue + $faktur->tax_amount, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Keterangan & Signature Section (tetap sama) -->
    <div class="section">
        <div class="section-title">KETERANGAN</div>
        <div>
            <strong>Metode Pembayaran:</strong> Transfer Bank<br>
            <strong>Nama Bank:</strong> Bank Central Asia (BCA)<br>
            <strong>No. Rekening:</strong> 123-456-7890<br>
            <strong>Atas Nama:</strong> URBAN OFFICE INDONESIA
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div>Jakarta, {{ \Carbon\Carbon::parse($faktur->period)->lastOfMonth()->format('d F Y') }}</div>
            <div>Penanggung Jawab,</div>
            <div style="margin-top: 60px;">_________________________</div>
            <div><strong>Admin Urban Office</strong></div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="footer">
        <div class="text-center">
            <strong>URBAN OFFICE INDONESIA</strong><br>
            Jl. Contoh Alamat No. 123, Jakarta Selatan - Telp: (021) 123-4567 | Email: admin@urbanoffice.com<br>
            <em>Faktur ini merupakan bukti pemungutan PPN sesuai dengan peraturan perundang-undangan yang berlaku</em>
        </div>
    </div>
</body>
</html>