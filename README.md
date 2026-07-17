# Urban Office — Web App

Aplikasi booking & manajemen coworking space / virtual office Urban Office. Dipakai oleh 4 tipe user: **Customer** (booking & pembayaran), **Admin/Finance** (kelola booking, promo, invoice, kontrak), **Superadmin** (approval pricing lintas cabang), dan **Mitra** (partner/vendor, akses terbatas ke data mereka sendiri).

## Tech Stack

- **Backend:** Laravel 10 (PHP), MySQL
- **Frontend:** Blade + Alpine.js + Tailwind CSS, build via Vite
- **Payment Gateway:** Midtrans (utama), Xendit
- **Integrasi lain:** Google OAuth (login)

> Ada file `app/Services/BalesOtomatisService.php` dan `Booking/MidtransWebHookController.php` di codebase (integrasi WhatsApp), tapi **tidak terdaftar di route manapun** — dead code, bukan bagian dari flow yang live. Webhook Midtrans yang benar-benar aktif adalah `TransactionController::notificationHandler` (lihat flow #1 di bawah).

## User Roles & Panel

Role disimpan di kolom `users.role`. Middleware yang menjaga tiap panel:

| Role | Middleware | Route group | Keterangan |
|---|---|---|---|
| `customer` (default) | `auth` | `routes/web.php` | Booking, pembayaran, klaim voucher, lihat invoice/kontrak sendiri |
| `admin`, `finance` | `EnsureAdmin` | `routes/admin.php` | Kelola booking, room, promo, invoice, kontrak, laporan |
| `superadmin` | `EnsureSuperadmin` | `routes/superadmin.php` | Approval pricing override lintas cabang |
| `mitra` (harus `mitra.status = approved`) | `CheckMitraAccess` | `routes/mitrapanel.php` | Dashboard mitra/partner — riwayat promo, faktur pajak, settings |

Session expired → redirect ke halaman `beforelogin` (bukan langsung form login), diatur di [`app/Http/Middleware/Authenticate.php`](app/Http/Middleware/Authenticate.php).

## Struktur Folder & Modul

Controller/Model/View dikelompokkan per domain berikut. Kalau mau ubah satu fitur, ini titik masuknya:

| Modul | Model | Controller | Service pendukung | View utama |
|---|---|---|---|---|
| **Booking & Transaksi (core)** | `Transaction`, `Room`, `RoomType`, `ServicePrice`, `LunchOption` | `Booking/BookingApiController`, `Booking/TransactionController`, `Booking/PaymentController`, `Booking/MidtransWebHookController`, `XenditController`, `backend/Admin/BookingController`, `RoomAssignController`, `RoomStatusController` | `RoomStatusService` | `dashboard/bookingform.blade.php`, `admin/booking-all.blade.php`, `admin/room-*.blade.php` |
| **Promo & Voucher** | `Promo`, `PromoUsage`, `PromoType`, `PromoCategory`, `PromoMetric` | `Promo/CustomerPromoController`, `backend/Admin/BannerController`, `backend/Admin/PromoUsageController` | `PromoService` (validasi & pencatatan pemakaian promo) | `admin/banners-promo.blade.php`, `admin/promo-usage.blade.php`, `dashboard/deals.blade.php`, `dashboard/my-vouchers.blade.php` |
| **Invoice** | `Invoice` | `Booking/InvoiceController`, `backend/Admin/InvoiceController` | `InvoicePdfService` | `dashboard/invoice.blade.php`, `admin/management-invoice.blade.php`, `invoices/pdf.blade.php` |
| **Kontrak & Addendum** | `Contract`, `Addendum` | `Booking/ContractController`, `backend/Admin/ContractController`, `backend/Admin/PublicContractController` | `Listeners/CreateContractOnSettlement` (auto-generate saat settlement) | `admin/management-contract.blade.php`, `admin/management-addendum.blade.php`, `dashboard/contract*.blade.php` |
| **Surat Masuk/Keluar** | `Surat` | `Booking/SuratController`, `backend/Admin/SuratController` | — | `admin/surats/`, `customer/surats/` |
| **Bonus/Reward** | `UserBonus`, `BonusRule`, `BonusClaim` | `Booking/BonusController`, `backend/Admin/BonusClaimController`, `BonusManagementController` | `BonusClaimService` | `dashboard/reward.blade.php` |
| **Virtual Office** | — | `backend/Admin/VirtualOfficeController` | `Exports/VirtualOfficeImportTemplate`, `app/Imports/` | `admin/virtual-office-*.blade.php` |
| **Notifikasi Browser** | — | `Notification/*` | `BrowserNotificationService` | `components/notification-manager.blade.php`, `public/service-worker.js` |
| **Mitra Panel** | `Mitra` | `Mitra/MitraController`, `backend/MitraPanel/*` | — | `layouts/mitrapanel/` |
| **Superadmin** | — | `backend/Superadmin/*` (Pricing Approval/Override) | — | `layouts/superadmin/` |
| **Auth & Session** | `User` | `AuthController`, `Auth/GoogleController`, `Auth/ForgotPasswordController` | — | `layouts/auth/` |

## Alur Kerja Utama

### 1. Booking → Pembayaran → Invoice → Kontrak
1. Customer isi form booking (`bookingform.blade.php`) → `BookingApiController` ambil data room/harga.
2. Submit → `TransactionController::store`/`storeV2` bikin record `Transaction`. Kalau ada `promo_code`, divalidasi via `PromoService::validate()` (cek klaim, tanggal, kuota, lokasi, min. transaksi).
3. **Saat `Transaction` dibuat**, `TransactionObserver::created()` langsung auto-generate `Invoice` (pakai `firstOrCreate`, idempotent).
4. Pembayaran lewat Midtrans Snap (`PaymentController`) atau Xendit (`XenditController`).
5. Callback/webhook settlement masuk ke `TransactionController::notificationHandler` / `MidtransWebHookController`:
   - `PromoService::recordUsage()` mencatat pemakaian promo ke `promo_usages` + increment `promos.usage_count` (dalam satu DB transaction, row-locked).
   - Event `TransactionSettled` di-fire → listener `CreateContractOnSettlement` auto-generate `Contract`.
6. Customer bisa lihat invoice & kontraknya dari dashboard; Admin memantau semuanya dari panel admin.

### 2. Promo & Voucher (Banner / Discount / Voucher)
1. Admin bikin promo di halaman **Generate Promo** (`banners-promo.blade.php` → `BannerController`). Tiga tipe: **Banner** (murni display, tidak perlu klaim), **Discount** (publik, tampil di `/deals`), **Voucher** (targeted ke user tertentu, cuma tampil di `/my-vouchers`).
2. Customer klaim Discount/Voucher → dicatat di pivot table `promo_user` (`is_claimed`, `claimed_at`) — ini baru masuk wallet, **belum** terhitung "usage".
3. Customer pakai kode promo saat checkout & transaksinya settlement → baru masuk `promo_usages` (lihat flow #1 langkah 5).
4. Admin pantau keduanya (klaim wallet + pemakaian settlement) sekaligus di halaman **Promo Usage Report** (`PromoUsageController` — query UNION dari `promo_user` dan `promo_usages`).

### 3. Login & Role Routing
- Login manual (`AuthController`) atau Google OAuth (`Auth/GoogleController`).
- Guard session (`config/auth.php`, guard `web`).
- Setelah login, redirect berdasarkan `users.role` ke panel masing-masing (lihat tabel Role di atas).

## Validasi Penting (Business Rules)

Aturan-aturan ini gampang salah kalau disentuh tanpa tahu detailnya — beberapa pernah jadi bug nyata di production.

**Validasi promo** (`PromoService::validate()`, dipanggil sebelum transaksi dibuat):
1. Promo harus `is_approved = true` dan status bukan `draft`/`inactive`/`ended`.
2. Rentang tanggal dicek pakai `start_date->startOfDay()` s.d. `end_date->endOfDay()` — **bukan** perbandingan jam mentah. Promo yang `end_date`-nya "hari ini" tetap valid sampai jam 23:59:59 hari itu, bukan langsung dianggap expired.
3. **Discount & Voucher wajib sudah diklaim dulu** (`promo_user.is_claimed = true` dan `is_used = false`) sebelum bisa dipakai di checkout. **Banner tidak butuh klaim sama sekali.**
4. `usage_limit` (kuota global) dan `usage_per_user` (kuota per user, dihitung dari jumlah baris `promo_usages` milik user itu) dicek terpisah.
5. Lokasi & `service_types` booking harus cocok dengan target promo (atau promo tidak dibatasi — `all` / `all-services`).
6. Subtotal booking harus ≥ `min_transaction`.

**Race condition saat mencatat pemakaian** — `PromoService::recordUsage()` (dipanggil saat transaksi settlement) membungkus insert `promo_usages` + increment `usage_count` dalam `DB::transaction()` dengan `Promo::lockForUpdate()`, dan **mengecek ulang** `usage_limit`/`usage_per_user` setelah lock diambil (bukan cuma andalkan hasil `validate()` di awal). Ini mencegah dua transaksi settlement bersamaan sama-sama lolos untuk kuota slot terakhir yang sama.

**Validasi role** — jangan asumsikan 1 middleware = 1 role:
- `EnsureAdmin` meloloskan role `admin` **atau** `finance` (bukan admin-only, sering disangka begitu).
- `EnsureSuperadmin` ketat cuma role `superadmin`.
- `CheckMitraAccess` butuh **dua syarat sekaligus**: role `mitra` **dan** `mitra.status === 'approved'` — mitra yang belum di-approve tetap ditolak walau rolenya sudah benar.

## Environment Variables Penting

Selain variabel standar Laravel (`DB_*`, `MAIL_*`, `APP_*`), aplikasi ini butuh (cek `config/services.php`):

```
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=

XENDIT_SECRET_KEY=
XENDIT_PUBLIC_KEY=
XENDIT_MODE=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=
```

> `.env.example` di repo ini masih template default Laravel dan belum mencantumkan variabel di atas — perlu diisi manual berdasarkan kredensial masing-masing environment (dev/staging/production).

## Setup Lokal

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# isi .env: DB_* dan variabel di atas
php artisan migrate
php artisan db:seed --class=FinanceUserSeeder   # akun awal untuk role finance
```

## Catatan Deployment

- Deploy ke production **bukan** lewat `git pull` di server — masih manual copy/FTP file. Jadi setelah `git push`, file yang berubah tetap perlu di-copy manual ke server (lihat riwayat masalah document root & Cloudflare cache yang pernah terjadi karena hal ini).
- Jangan jalankan `php artisan db:seed --class=PromoMetricsSeeder` di production — seeder ini generate data dummy untuk testing lokal dan pernah menyebabkan data `promo_usages` asli ter-hapus.
- `public/build.zip` adalah hasil `npm run build` yang di-zip untuk upload manual — pastikan selalu re-generate sebelum deploy kalau ada perubahan asset frontend.
