<!DOCTYPE html>
<html>
<head>
    <title>Test Midtrans</title>
</head>
<body>
    <button id="pay-button">Bayar Sekarang</button>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            window.snap.pay('{{ $snapToken }}');
        };
    </script>
</body>
</html>
