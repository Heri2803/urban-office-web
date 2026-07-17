<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kontrak Manajemen Virtual Office - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin-top: 2.3cm;
            margin-bottom: 2.8cm;
            margin-left: 3cm;
            margin-right: 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.7;
            color: #000;
            text-align: justify;
        }
        p {
            margin-top: 0.4rem;
            margin-bottom: 0.4rem;
            text-align: justify;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .underline { text-decoration: underline; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .title {
            font-size: 13pt;
            margin-top: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        h3 {
            font-size: 12pt;
            text-align: center;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        ul, ol {
            margin-top: 0.3rem;
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
        }
        li {
            margin-bottom: 0.25rem;
            text-align: justify;
        }
        .signature-box {
            width: 100%;
            margin-top: 50px;
        }
        .signature-col {
            width: 50%;
            float: left;
            text-align: center;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        /* Custom list style para paragraph */
        .pasal-content p {
            margin-top: 0.3rem;
            margin-bottom: 0.3rem;
        }
    </style>
</head>
<body>

    {{-- Cover Page Layer (Absolute) - offset negatif harus = margin agar menutup tepi kertas --}}
    <img src="{{ public_path('assets/cover perjanjian sewa.jpeg') }}" style="position: absolute; top: -2.3cm; left: -3cm; width: 21cm; height: 29.7cm; z-index: 50;" />
    
    {{-- Memaksa ganti halaman setelah cover --}}
    <div style="page-break-after: always; height: 1px;"></div>

    {{-- Watermark Layer (Fixed - Repeated on all subsequent pages) --}}
    <div style="position: fixed; top: -2.3cm; left: -3cm; width: 21cm; height: 29.7cm; z-index: -10;">
        <img src="{{ public_path('assets/template bg page perjanjian.jpeg') }}" style="width: 100%; height: 100%;" />
    </div>

    @php
        // Helper date
        $contractDate = $transaction->contract_date ? \Carbon\Carbon::parse($transaction->contract_date) : now();
        
        $hariArr = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulanArr = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $hari = $hariArr[$contractDate->dayOfWeek];
        $tanggal = $contractDate->day;
        $bulan = $bulanArr[$contractDate->month];
        $tahun = $contractDate->year;
        
        $gross_amount = $transaction->gross_amount;
        $formatted_amount = number_format($gross_amount, 0, ',', '.');
        
        // Cek nama perusahaan
        $company_name = $transaction->company_name ? strtoupper($transaction->company_name) : strtoupper($transaction->nama_lengkap);
        $nama_lengkap = strtoupper($transaction->nama_lengkap);
    @endphp

    <div class="text-center mb-4">
        <div class="title font-bold underline">PERJANJIAN SEWA MENYEWA KANTOR VIRTUAL (VIRTUAL OFFICE)</div>
        <div>Nomor : {{ $contractNumber }}</div>
    </div>

    <p>
        Perjanjian Sewa Menyewa ini (untuk selanjutnya disebut sebagai "Perjanjian") dibuat dan ditanda tangani di {{ $transaction->location->city->name ?? 'Surabaya' }} pada hari <strong>{{ $hari }}</strong> Tanggal <strong>{{ $tanggal }}</strong> Bulan <strong>{{ $bulan }}</strong> Tahun <strong>{{ $tahun }}</strong> ({{ $contractDate->format('d - m - Y') }}) oleh dan antara:
    </p>

    <ol>
        <li>
            <strong>PT. URBAN KREASI BERSAMA</strong> suatu perseroan terbatas yang didirikan berdasarkan ketentuan Undang-undang Republik Indonesia, berkedudukan di Surabaya untuk selanjutnya disebut sebagai "Pemilik Gedung" yang dalam hal ini diwakili oleh Georgius Mario Miracel yang beralamat di Surabaya dengan NIK 3275031706020019 yang dalam hal ini bertindak dalam kedudukannya selaku Direktur, oleh dan karenanya bertindak untuk dan atas nama Pemilik Gedung (untuk selanjutnya disebut sebagai <strong>"Pihak Pertama"</strong>);
        </li>
        <li class="mt-4">
            <strong>{{ $company_name }}</strong> suatu perseroan terbatas badan/perseroan komanditer yang didirikan berdasarkan ketentuan Undang-undang Republik Indonesia, berkedudukan di {{ $transaction->location->city->name ?? 'Surabaya' }} yang dalam hal ini diwakili oleh dengan NIK <strong>{{ $transaction->nik ?? '................' }}</strong> (KTP) dan oleh <strong>{{ $nama_lengkap }}</strong> dan karenanya sah untuk bertindak atas nama {{ $company_name }} (untuk selanjutnya disebut sebagai <strong>"Pihak Kedua"</strong>);
        </li>
    </ol>

    <p>Pihak Pertama dan Pihak Kedua untuk selanjutnya secara bersama-sama disebut sebagai <strong>"Para Pihak"</strong>.</p>
    <p>Para Pihak terlebih dahulu menerangkan hal-hal sebagai berikut :</p>
    <ul>
        <li>Bahwa, Pihak Pertama adalah pemilik/pengelola dan/atau yang berhak atas Gedung {{ $transaction->location->name ?? 'Urban Office' }} di {{ $transaction->location->address ?? '-' }} (untuk selanjutnya disebut sebagai "Kantor Virtual (Virtual Office)");</li>
        <li>Bahwa, Pihak Kedua bermaksud untuk menyewa Kantor Virtual (Virtual Office) milik Pihak Pertama tersebut yang akan digunakan sebagai Kantor Virtual (Virtual Office) Pihak Kedua.</li>
        <li>Bahwa, Diatas Kantor Virtual (Virtual Office) tersebut akan dipergunakan antara lain untuk Kantor Virtual (Virtual Office) oleh Pihak kedua.</li>
    </ul>

    <p>Berdasarkan hal-hal tersebut di atas, Para Pihak setuju untuk membuat Perjanjian ini dengan syarat dan ketentuan sebagai berikut :</p>

    <h3>PASAL 1<br>D E F I N I S I</h3>
    <p>Dalam perjanjian ini istilah-istilah berikut ini kecuali dikehendaki lain oleh hubungan kalimat, mempunyai arti sebagai berikut :</p>
    <div class="pasal-content">
        <p>1.1 "Gedung" adalah bangunan yang berdiri di atas bidang tanah tersebut di atas, berikut dengan segala fasilitasnya;</p>
        <p>1.2 "Kantor Virtual (Virtual Office)" adalah Sebuah "ruang kerja" yang memiliki sejumlah karyawan namun tidak memilliki ruangan secara fisik dan secara fisik tidak bekerja di alamat tersebut, hanya menggunakan fasilitas alamat dan nomor telepon ;</p>
        <p>1.3 "Jangka Waktu Sewa" adalah jangka waktu sebagaimana ditentukan dalam Pasal 2 Perjanjian ini, jangka waktu mana dapat diperpanjang berdasarkan ketentuan-ketentuan dalam Perjanjian ini;</p>
        <p>1.4 "Uang Sewa" adalah jumlah uang sewa yang dibayarkan oleh Pihak kedua kepada Pihak Pertama sesuai dengan Pasal 4 Perjanjian ini;</p>
        <p>1.5 "Uang Jaminan" adalah jumlah uang yang harus dibayarkan oleh Pihak Kedua kepada Pihak Pertama sebagai jaminan sebagaimana dimaksud pada Pasal 4.4 Perjanjian ini;</p>
        <p>1.6 "Fasilitas Umum" adalah bagian-bagian dari Gedung yang disediakan oleh Pihak Pertama dipergunakan secara umum bersama Pihak Pertama, Pihak Kedua, penyewa lainnya dan orang-orang lain yang diperkenankan untuk menggunakannya berdasarkan Perjanjian ini, termasuk ruang masuk, tangga, selasar, lorong-lorong, toilet dan sarana parkir;</p>
        <p>1.7 "Tanggal Mulai Sewa" adalah tanggal dimana Pihak Kedua mulai menyewa.</p>
        <p>1.8 "Perjanjian" adalah Perjanjian Sewa Menyewa ini berikut dengan lampiran-lampiran, perubahan-perubahan dan tambahan-tambahan terhadapnya (bila ada).</p>
    </div>

    <h3>PASAL 2<br>JANGKA WAKTU SEWA</h3>
    <div class="pasal-content">
        <p>2.1 Jangka Waktu Sewa</p>
        <p>Perjanjian ini dilangsungkan untuk jangka waktu {{ $durasiTeks }} kecuali diakhiri 
        lebih awal oleh salah satu pihak berdasarkan ketentuan dalam Perjanjian ini, terhitung 
        sejak tanggal {{ $startDate->format('d/m/Y') }} &ndash; {{ $endDate->format('d/m/Y') }}.</p>
        <div style="padding-left: 20px;">
            <p>2.1.1 Syarat-syarat dan ketentuan untuk perpanjangan sewa menyewa akan disetujui 
            bersama oleh Para Pihak dimana uang sewa untuk perpanjangan tersebut akan di tentukan 
            dalam Rupiah berdasarkan kesepakatan Para Pihak yang mana dilakukan dengan prinsip 
            musyawarah untuk mufakat, dengan memperhatikan status Pihak Kedua sebagai penyewa utama.</p>
        </div>
    </div>

    <h3>PASAL 3<br>PENGGUNAAN KANTOR VIRTUAL (VIRTUAL OFFICE)</h3>
    <div class="pasal-content">
        <p>3.1 Pihak Kedua diperkenankan untuk memakai/menggunakan alamat Kantor Virtual (Virtual Office) hanya untuk kegiatan surat menyurat saja. Surat menyurat hanya boleh untuk 1 bisnis saja dan tidak untuk beberapa bisnis dalam satu nama Kantor Virtual.</p>
        <p>3.2 Pihak Kedua dapat mempergunakan telepon Kantor Virtual (Virtual Office) hanya menerima telepon saja.</p>
        <p>3.3 Pihak Kedua tidak akan menggunakan Kantor Virtual (Virtual Office) untuk menjalankan kegiatan dan/atau Tindakan melanggar hukum yang dapat merugikan Para Pihak.</p>
        <p>3.4 Pihak Kedua hanya boleh menggunakan Kantor Virtual (Virtual Office) hanya untuk satu bisnis per Kantor Virtual (Virtual Office) yang diizinkan.</p>
    </div>

    <h3>PASAL 4<br>UANG SEWA DAN CARA PEMBAYARAN</h3>
    <div class="pasal-content">

        <p>4.1 Uang Sewa<br>
        Besarnya Uang Sewa Kantor Virtual (Virtual Office) ditetapkan sebesar 
        Rp {{ number_format($sewa_amount, 0, ',', '.') }},- 
        ({{ $terbilang_sewa }} rupiah) selama masa perjanjian sewa kantor virtual.</p>

        <p>4.2 Cara Pembayaran Uang Sewa<br>
        Uang Sewa sejumlah Rp {{ number_format($sewa_amount, 0, ',', '.') }},- 
        ({{ $terbilang_sewa }} rupiah) akan dibayarkan dalam 1 termin yaitu: pada saat kontrak 
        ditandatangani atau tanggal {{ $contractDate->format('d/m/Y') }}. Dengan ketentuan bilamana 
        Pihak Kedua tidak memenuhi kewajibannya tepat pada waktunya atau setelah tenggang waktu 
        7 (tujuh) hari kerja, maka Pihak Kedua diwajibkan untuk membayar denda sebesar 10% untuk 
        tiap-tiap hari terlambat membayar angsuran Uang Sewa tersebut, yang harus dibayar tiap-tiap 
        hari dengan seketika dan sekali lunas dengan cara ditransfer ke rekening Pihak Pertama.</p>

        <p>4.3 Pembayaran Uang Sewa dapat dilakukan dengan cara transfer melalui rekening Bank Mandiri 
        dengan nomor rekening <strong>140-00-8785555-8</strong> atas nama 
        <strong>PT. Urban Kreasi Bersama</strong> dan untuk setiap pembayaran Pihak Kedua akan diberi 
        Invoice/Tagihan tersendiri oleh Pihak Pertama.</p>

        <p>4.4 Wanprestasi Pembayaran Sewa</p>
        <div style="padding-left: 20px;">
            <p>4.4.1 Jika Pihak Kedua tidak melakukan pembayaran biaya sewa virtual office sebagaimana 
            dimaksud dalam Pasal 4.2 dalam waktu lebih dari 7 (tujuh) hari setelah invoice diterbitkan 
            dan diterima pihak kedua, maka Pihak Pertama berhak untuk:<br>
            a. Mengeluarkan dan atau memutus kontrak atau perjanjian sewa ini tanpa perlu konfirmasi 
            dan persetujuan dari Pihak Kedua;<br>
            b. Menghentikan perjanjian sewa ini tanpa perlu pemberitahuan lebih lanjut kepada Pihak Kedua.</p>
            <p>4.4.2 Dengan berakhirnya Perjanjian sewa ini, Pihak Kedua wajib meninggalkan dan melepas 
            penggunaan alamat Urban Office di semua platform yang terdaftar oleh Pihak Kedua dan Pihak 
            Kedua tidak berhak atas pengembalian biaya yang telah dibayarkan sebelumnya.</p>
        </div>

        <p>4.5 Uang Jaminan</p>
        <div style="padding-left: 20px;">
            <p>4.5.1 Untuk menjamin pembayaran kewajiban Pihak Kedua kepada Pihak Pertama berdasarkan 
            Perjanjian ini, maka Pihak Kedua wajib membayar kepada Pihak Pertama Uang Jaminan (deposit) 
            sebesar Rp {{ number_format($transaction->deposit ?? 0, 0, ',', '.') }},- 
            ({{ $terbilang_deposit }} rupiah) yang dibayar bersama pembayaran sewa.</p>
            <p>4.5.2 Uang Jaminan atau deposit merupakan dana untuk menjamin bilamana terjadi kelalaian 
            atau menyalahgunakan Kantor Virtual (Virtual Office) yang melanggar hukum.</p>
            <p>4.5.3 Uang Jaminan tersebut akan dikembalikan oleh Pihak Pertama kepada Pihak Kedua 
            paling lambat 30 (tiga puluh) hari setelah Pihak Kedua mematuhi ketentuan Perjanjian ini, 
            dengan ketentuan Pihak Kedua telah melunasi/memenuhi seluruh kewajibannya.</p>
            <p>4.5.4 Bilamana Pihak Kedua lalai membayar kewajibannya kepada Pihak Pertama berdasarkan 
            Perjanjian ini, maka Pihak Kedua dengan ini memberikan kuasa kepada Pihak Pertama untuk 
            menggunakan Uang Jaminan untuk membayar kewajibannya.</p>
            <p>4.5.5 Uang Jaminan akan hangus/tidak dikembalikan ketika kontrak diputus karena 
            kesalahan Pihak Kedua.</p>
            <p>4.5.6 Perpanjangan atas sewa hanya perlu membayar biaya sewa tanpa membayar uang 
            jaminan kembali.</p>
        </div>

    </div>

    <h3>PASAL 5<br>FASILITAS-FASILITAS</h3>
    <div class="pasal-content">
        <p>5.1 Fasilitas-fasilitas Kantor Virtual (Virtual Office)<br>
        Atas biaya Pihak Pertama Kantor Virtual (Virtual Office) akan dilengkapi dengan fasilitas-fasilitas yang dipakai oleh Pihak Kedua sebagai berikut :</p>
        <div style="padding-left: 20px;">
            <p>a. Alamat resmi untuk Kantor Virtual (Virtual Office)</p>
            <p>b. Call handling/pemberitahuan telepon masuk</p>
            <p>c. Mail handling/pemberitahuan surat/dokumen masuk</p>
            <p>d. Surat keterangan domisili dari gedung Urban office</p>
            <p>e. Peluang akses konektivitas ke komunitas bisnis yang ada di Urban office</p>
            <p>f. Resepsionis Profesional</p>
        </div>
        <p>5.2 Fasilitas-fasilitas tersedia dan berfungsi bagi Pihak Kedua setiap hari, mulai dari hari Senin sampai dengan hari Sabtu, tidak termasuk hari libur atau hari besar lainnya; 8 (delapan) jam sehari; Kecuali dalam keadaan darurat yang diluar kemampuan Pihak Pertama.</p>
        <p>5.3 Fasilitas-fasilitas tersedia dan berfungsi selama jam operasional Gedung.</p>
        <p>5.4 Seluruh biaya-biaya untuk fasilitas telah termasuk dalam Uang Sewa.</p>
        <p>5.5 Bilamana Pihak Kedua menghendaki perubahan atau penambahan fasilitas, maka biaya perubahan menjadi tanggungan Pihak Kedua.</p>
    </div>

    <h3>PASAL 6<br>PAJAK-PAJAK</h3>
    <div class="pasal-content">
        <p>6.1 Pajak-pajak yang menurut ketentuan perundang-undangan menjadi kewajiban Pihak Pertama, akan ditanggung oleh Pihak Pertama sepenuhnya; demikian pula yang menjadi kewajiban Pihak Kedua akan ditanggung oleh Pihak Kedua sepenuhnya.</p>
        <p>6.2 Pajak Bumi dan Bangunan (PBB) atas gedung dan/atau Ruangan Sewa, adalah tanggungan oleh Pihak Pertama.</p>
        <p>6.3 Pajak Sewa-menyewa (PPh pasal 23) sebesar 2% akan dipotong oleh Pihak Kedua (jika Pihak Kedua PKP), dan bukti potong dikirimkan kepada Pihak Pertama.</p>
    </div>

    <h3>PASAL 7<br>KEWAJIBAN-KEWAJIBAN DAN TANGGUNG JAWAB PIHAK PERTAMA</h3>
    <div class="pasal-content">
        <p>7.1 Sehubungan dengan Sewa Kantor Virtual (Virtual Office), maka Pihak Pertama 
        berkewajiban dan bertanggung jawab:</p>
        <div style="padding-left: 20px;">
            <p>7.1.1 Memberikan pelayanan/service secara teratur pada Fasilitas yang telah 
            diberikan.</p>
        </div>
    </div>

    <h3>PASAL 8<br>KEWAJIBAN-KEWAJIBAN DAN TANGGUNG JAWAB PIHAK KEDUA</h3>
    <div class="pasal-content">
        <p>Sehubungan dengan Sewa Kantor Virtual (Virtual Office), maka Pihak Kedua berkewajiban dan bertanggung jawab:<br>
        8.1 Membayar Uang Sewa dan biaya lainnya kepada Pihak Pertama, seperti yang telah ditetapkan pada Perjanjian ini.</p>
    </div>

    <h3>PASAL 9<br>A S U R A N S I</h3>
    <div class="pasal-content">
        <p>9.1 Pihak Pertama wajib mengasuransikan Gedung beserta Ruangan Sewa dan perlengkapannya dari bahaya kebakaran atau kerusuhan yang biaya preminya menjadi tanggung jawab Pihak Pertama.</p>
        <p>9.2 Pihak Kedua wajib mengasuransikan surat menyurat atau dokumen miliknya terhadap bahaya/resiko yang dipandang perlu; biaya menjadi tanggung jawab Pihak Kedua.</p>
        <p>9.3 Bilamana terjadi musibah kebakaran atas Gedung, Pihak Pertama akan memperbaiki Gedung agar fasilitas dapat beroperasi kembali.</p>
    </div>

    <h3>PASAL 10<br>ANTI PENYALAHGUNAAN ALAMAT & KEGIATAN USAHA MELANGGAR HUKUM</h3>
    <div class="pasal-content">
        <p>Pihak Kedua dengan ini menyatakan bahwa alamat Virtual Office tidak akan digunakan 
        untuk kegiatan yang bertentangan dengan hukum, termasuk namun tidak terbatas pada:</p>
        <div style="padding-left: 20px;">
            <p>a) Penipuan, money laundering, atau tindak pidana ekonomi lainnya.</p>
            <p>b) Pendaftaran entitas fiktif atau penggunaan alamat tanpa legalitas dokumen usaha.</p>
            <p>c) Kegiatan ilegal berbasis online, termasuk pinjaman ilegal atau platform yang 
            dilarang OJK.</p>
        </div>
        <p>Jika terbukti, Pihak Pertama berhak secara sepihak mengakhiri perjanjian tanpa 
        kewajiban pengembalian dana, dan Pihak Kedua wajib segera mencabut semua penggunaan 
        alamat dari media apa pun.</p>
    </div>
    <h3>PASAL 11<br>FORCE MAJEURE</h3>
    <p>Bilamana Gedung mengalami kerusakan yang diakibatkan Force Majeure misalnya kebakaran, banjir, gempa bumi, angin ribut atau bencana alam lainnya atau huru-hara, maka Para Pihak dibebaskan dari tuntutan satu kepada yang lain, kecuali untuk yang sudah ada sebelum terjadinya kejadian Force Majeure.</p>

    <h3>PASAL 12<br>JAMINAN PIHAK PERTAMA</h3>
    <div class="pasal-content">
        <p>12.1 Pihak Pertama menjamin kepada Pihak Kedua, bahwa:</p>
        <div style="padding-left: 20px;">
            <p>12.1.1 Pajak-pajak Gedung telah dibayar lunas.</p>
            <p>12.1.2 Tata letak Gedung diperuntukkan untuk daerah komersil.</p>
            <p>12.1.3 Pihak Pertama memelihara semua ijin-ijin dan persetujuan.</p>
        </div>
        <p>12.2 Apabila di kemudian hari jaminan dari Pihak Pertama tidak benar, Pihak Pertama 
        wajib mengembalikan uang sewa yang belum terpakai kepada Pihak Kedua.</p>
    </div>

    <h3>PASAL 13<br>P R O M O S I</h3>
    <div class="pasal-content">
        <p>13.1 Pihak Pertama bertanggung jawab atas pelaksanaan promosi sehubungan dengan Gedung.</p>
        <p>13.2 Pihak Kedua dapat melakukan promosi keberadaannya dalam Gedung, dengan terlebih dahulu memberitahukan bentuk promosi kepada Pihak Pertama.</p>
    </div>

    <h3>PASAL 14<br>PENGALIHAN PENGGUNAAN KANTOR VIRTUAL SECARA BERSAMA-SAMA</h3>
    <p>14.1 Pihak Kedua tidak boleh meminjamkan kepada pihak-pihak lain menggunakan sebagian atau keseluruhan fasilitas Kantor Virtual (Virtual Office) ini.</p>

    <h3>PASAL 15<br>BERAKHIRNYA PERJANJIAN</h3>
    <div class="pasal-content">

        <p>15.1 Perjanjian ini dapat berakhir bilamana terjadi:</p>
        <div style="padding-left: 20px;">
            <p>15.1.1 Pihak Kedua tidak membayar Uang Sewa, dan pembayaran lainnya sebagaimana 
            ditentukan dalam Perjanjian ini dan Pihak Kedua telah diberikan 3 (tiga) kali peringatan 
            tertulis oleh Pihak Pertama dengan tenggang waktu untuk tiap teguran 10 (sepuluh) hari 
            dengan tetap dikenakan denda sebagaimana tersebut dalam Pasal 8 ayat (8.1), 
            Pasal 4 ayat (4.2).</p>
            <p>15.1.2 Pihak Kedua melakukan praktik usaha melanggar hukum.</p>
            <p>15.1.3 Pihak Kedua dengan sengaja merusak/membakar Gedung.</p>
            <p>15.1.4 Pihak Pertama tidak memenuhi kewajibannya.</p>
        </div>

        <p>15.2 Tindakan Setelah Perjanjian Berakhir</p>
        <div style="padding-left: 20px;">
            <p>15.2.1 Kewajiban menghentikan penggunaan alamat & nomor telepon. Jika perjanjian 
            berakhir, Pihak Kedua wajib berhenti menggunakan alamat sebagai domisili Virtual Office 
            untuk online maupun offline.</p>
            <p>15.2.2 Sanksi atas Kelalaian. Jika 1 minggu setelah perjanjian berakhir alamat masih 
            digunakan, dikenakan denda Rp 100.000 per hari keterlambatan.</p>
            <p>15.2.3 Pemberian Kuasa Otomatis. Pihak Kedua otomatis memberi kuasa kepada Pihak 
            Pertama untuk menghentikan penggunaan alamat/nomor di instansi terkait. Kuasa ini tidak 
            dapat dicabut dan tetap berlaku meskipun ada alasan dalam Pasal 1813 KUHPerdata (yang 
            biasanya membatalkan kuasa karena alasan tertentu). Pelaksanaan dilakukan atas nama, 
            biaya, dan risiko Pihak Kedua.</p>
        </div>

    </div>

    <h3>PASAL 16<br>KETENTUAN-KETENTUAN LAIN</h3>
    <div class="pasal-content">

        <p>16.1 Semua surat-menyurat dikirim pada alamat berikut:</p>
        <div style="padding-left: 20px;">
            <p><strong>Pihak Pertama:</strong><br>
            Nama : PT. URBAN KREASI BERSAMA<br>
            Alamat : {{ $transaction->location->address ?? '-' }}.<br>
            Telepon : 031-87855578<br>
            NPWP : 92.347.130.4.615.000<br>
            Rekening : 140-00-8785555-8 Bank Mandiri</p>

            <p><strong>Pihak Kedua:</strong><br>
            Nama : {{ $company_name }}<br>
            Alamat : {{ $transaction->company_address ?? 'Sesuai Domisili KTP/Perusahaan Tersimpan' }}<br>
            Telepon : {{ $transaction->phone ?? '-' }}<br>
            NPWP : {{ $transaction->npwp ?? '-' }}</p>
        </div>

        <p>16.2 Apabila Pihak Kedua dibubarkan/dilikuidasi, kewajiban beralih ke pengganti 
        haknya.</p>

        <p>16.3 Pihak Pertama berhak mengatur penggunaan dan penempatan ruang-ruang lain dalam 
        Gedung tanpa harus merugikan hak-hak dan kepentingan Pihak Kedua berdasarkan 
        Perjanjian ini.</p>

        <p>16.4 Jam Operasional Office adalah Senin-Sabtu jam 08.00 WIB s/d 16.00 WIB.</p>

        <p>16.5 Masing-masing Pihak tidak bertanggung jawab dan saling membebaskan terhadap 
        segala kerugian yang diderita atau biaya-biaya yang dikeluarkan oleh Pihak lainnya 
        yang timbul sebagai akibat dari kelalaian pihak yang bersangkutan.</p>

        <p>16.6 Dalam hal terjadi perselisihan antara Para Pihak sehubungan dengan Perjanjian 
        ini, Para Pihak akan berupaya menyelesaikan perselisihan tersebut secara musyawarah. 
        Apabila musyawarah tidak berhasil dalam kurun waktu 30 (tiga puluh) hari kalender, 
        Para Pihak sepakat untuk menyelesaikan perselisihan tersebut melalui mediasi. Apabila 
        mediasi tidak berhasil, maka penyelesaian akan dilakukan melalui Pengadilan sesuai 
        dengan peraturan yang berlaku.</p>

        <p>16.7 Perjanjian ini diatur berdasarkan peraturan hukum Negara Republik Indonesia.</p>

        <p>16.8 Para Pihak dengan ini sepakat untuk saling melepaskan Pasal 1266 dan Pasal 1267 
        Kitab Undang-undang Hukum Perdata, sejauh Pasal-pasal tersebut mensyaratkan adanya 
        suatu keputusan atau penetapan pengadilan untuk membatalkan atau memutuskan suatu 
        perjanjian.</p>

        <p>16.9 Tidak ada ketentuan dalam Perjanjian ini yang dapat ditafsirkan sebagai suatu 
        pembentukan suatu hubungan kemitraan (partnership), petugas (principal/agent), atau 
        hubungan hukum lainnya antara Para Pihak, selain dari hubungan antara pemilik/pengelola 
        gedung dan penyewa sebagaimana yang diatur dalam Perjanjian ini.</p>

        <p>16.10 Pihak Pertama berhak secara otomatis melakukan pemutusan kontrak apabila 
        dikemudian hari Pihak Kedua terbukti melakukan kegiatan usaha yang bertentangan dengan 
        ketentuan hukum yang berlaku di Negara Republik Indonesia.</p>

        <p>16.11 Pihak Kedua bertanggung jawab penuh atas semua kegiatan usahanya dan segala 
        akibat yang ditimbulkannya sehubungan dengan penggunaan Kantor Virtual (Virtual Office).</p>

        <p>16.12 Pihak Pertama tidak bertanggung jawab atas segala tindakan atau kelalaian atau 
        kerusakan yang terjadi baik di dalam maupun di luar lingkungan ruang sewa Kantor Virtual 
        (Virtual Office) sehubungan dengan kegiatan usaha Pihak Kedua.</p>

        <p>16.13 Pihak Pertama tidak bertanggung jawab atas segala tindakan atau kegiataan 
        surat-menyurat yang dilakukan Pihak Kedua maupun pihak-pihak yang berkaitan dengan 
        usaha Pihak Kedua.</p>

        <p>16.14 Pihak Pertama mempunyai hak untuk memutus perjanjian ini sewaktu-waktu apabila 
        Pihak Kedua melanggar hukum dalam menjalankan kegiatan usahanya ataupun hal lain yang 
        dianggap oleh Pihak Pertama merugikan nama baik Pihak Pertama.</p>

        <p>16.15 Pihak Kedua berkewajiban untuk mengasuransikan sendiri barang-barang dan 
        dokumen-dokumen miliknya yang tersimpan dan/atau disampaikan melalui alamat Kantor 
        Virtual (Virtual Office) terhadap bahaya/risiko yang dipandang perlu oleh Pihak Kedua, 
        dengan biaya menjadi tanggung jawab sepenuhnya Pihak Kedua.</p>

    </div>

    <h3>PASAL 17<br>PRIVASI DAN PERLINDUNGAN DATA PRIBADI</h3>
    <p>Pihak Pertama akan mengumpulkan, menyimpan, dan memproses data pribadi Pihak Kedua untuk keperluan administratif sesuai Undang-Undang No. 27 Tahun 2022. Pihak Kedua setuju bahwa data yang diberikan benar dan Pihak Pertama menjaga kerahasiaannya.</p>

    <h3>PASAL 18<br>ADDENDUM</h3>
    <p>Hal-hal yang belum atau cukup diatur dalam perjanjian ini, apabila dipandang perlu perubahan, akan ditetapkan tersendiri secara musyawarah dalam suatu addendum.</p>

    <h3>PASAL 19<br>D O M I S I L I</h3>
    <p>Tentang Perjanjian ini dan segala akibat hukumnya, para pihak memilih domisili hukum yang sah dan tetap di Kantor Kepaniteraan Pengadilan Negeri di {{ $transaction->location->city->name ?? 'Surabaya' }}.</p>

    <div style="page-break-inside: avoid;">
        <p style="text-align: center; margin-top: 2rem;">
            Demikianlah Perjanjian Sewa Menyewa ini dibuat dan ditandatangani oleh para pihak secara sah pada tanggal sebagaimana tersebut pada permulaan Perjanjian ini.
        </p>

        <table width="100%" style="margin-top: 30px; border-collapse: collapse; page-break-inside: avoid;">
            <tr>
                <td width="50%" style="text-align: center; vertical-align: top; padding: 0;">
                    <p><strong>Pihak Pertama,</strong><br>PT. URBAN KREASI BERSAMA</p>
                    <br><br><br><br><br>
                    <p class="underline font-bold" style="margin-bottom: 0;">Georgius Mario Miracel</p>
                    <p style="margin-top: 0;">Direktur / Pemberi Sewa</p>
                </td>
                <td width="50%" style="text-align: center; vertical-align: top; padding: 0;">
                    <p><strong>Pihak Kedua,</strong><br>{{ $company_name }}</p>
                    <br><br><br><br><br>
                    <p class="underline font-bold" style="margin-bottom: 0;">{{ $nama_lengkap }}</p>
                    <p style="margin-top: 0;">Direktur / Penyewa</p>
                </td>
            </tr>
        </table>

        <div style="margin-top: 2rem; border-top: 1px solid #ccc; padding-top: 1rem; page-break-inside: avoid;">
            <table width="100%">
                <tr>
                    <td width="75%" style="vertical-align: middle;">
                        <p style="font-size: 9pt; color: #555; margin: 0;">
                            <strong>Verifikasi Keaslian Dokumen</strong><br>
                            Scan QR Code ini untuk melihat data kontrak secara online.<br>
                            Dokumen ini diterbitkan secara digital oleh PT. Urban Kreasi Bersama.
                        </p>
                        <p style="font-size: 8pt; color: #888; margin-top: 4px; word-break: break-all;">
                            {{ $contract->public_verify_url }}
                        </p>
                    </td>
                    <td width="25%" style="text-align: right; vertical-align: middle;">
                        <img src="{{ $qrCodeBase64 }}" width="100" height="100" 
                            style="display: block; margin-left: auto;" />
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
