<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Test Xendit Checkout</title>
</head>
<body style="font-family: Arial; text-align: center; margin-top: 100px;">

  <h2>Testing Xendit Checkout Popup</h2>

  <p>Masukkan nominal pembayaran:</p>
  <input type="number" id="amount" placeholder="Contoh: 50000" style="padding: 10px; width: 200px;">
  <br><br>
  <button id="payButton" style="padding: 10px 20px;">Bayar Sekarang</button>

  <script>
    document.getElementById("payButton").addEventListener("click", async () => {
      const amount = document.getElementById("amount").value;
      if (!amount || amount <= 0) {
        alert("Masukkan nominal pembayaran!");
        return;
      }

      try {
        const response = await fetch("{{ route('xendit.createInvoice') }}", {
          method: "POST", // <--- PENTING: ini harus POST
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
          },
          body: JSON.stringify({
            amount: amount,
            email: "testuser@example.com"
          })
        });

        // pastikan responsenya JSON
        const data = await response.json();

        if (data.invoice_url) {
          window.open(data.invoice_url, "_blank", "width=600,height=800");
        } else {
          console.error("Gagal membuat invoice:", data);
          alert("Gagal membuat invoice, cek console log");
        }
      } catch (error) {
        console.error("Error:", error);
        alert("Terjadi kesalahan koneksi ke server");
      }
    });
  </script>
</body>
</html>
