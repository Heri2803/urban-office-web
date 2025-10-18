<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emails Message</title>
</head>
<body>
    <h2>Halo {{ $transaction->nama_lengkap }}</h2>

    <p>Transaksi dengan Order ID <strong>{{ $transaction->order_id }}</strong> pada tanggal <strong>{{ $transaction->booking_date->format('d M Y') }}</strong> sudah <strong>{{ $transaction->status }}</strong>.</p>

    <p>Nominal yang dibayarkan: <strong>RP. {{ number_format($transaction->gross_amount,0,",",".") }}</strong></p>

    <p>Invoice Anda terlampir dalam email ini.</p>

    <p>Terima kasih telah melakukan transaksi bersama kami.</p>
</body>
</html>