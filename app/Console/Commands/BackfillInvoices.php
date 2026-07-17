<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillInvoices extends Command
{
    /**
     * Nama dan signature command.
     * Jalankan: php artisan invoices:backfill
     * Dry run:  php artisan invoices:backfill --dry-run
     */
    protected $signature = 'invoices:backfill
                            {--dry-run : Simulasi tanpa benar-benar membuat invoice}
                            {--limit=0 : Batasi jumlah transaksi yang diproses (0 = semua)}';

    protected $description = 'Generate invoice untuk semua transaksi lama yang belum memiliki invoice.';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $limit    = (int) $this->option('limit');

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE — tidak ada data yang akan dibuat.');
        }

        // Ambil semua transaksi yang belum punya invoice
        $query = Transaction::whereDoesntHave('invoice')->oldest();

        if ($limit > 0) {
            $query->limit($limit);
        }

        $transactions = $query->get();
        $total        = $transactions->count();

        if ($total === 0) {
            $this->info('✅ Semua transaksi sudah memiliki invoice. Tidak ada yang perlu di-backfill.');
            return self::SUCCESS;
        }

        $this->info("🔍 Ditemukan {$total} transaksi tanpa invoice.");
        $this->newLine();

        // Tampilkan tabel preview
        $this->table(
            ['ID', 'Order ID', 'Nama', 'Status', 'Tanggal'],
            $transactions->map(fn($t) => [
                $t->id,
                $t->order_id,
                $t->nama_lengkap,
                $t->status,
                $t->created_at->format('d/m/Y'),
            ])
        );

        $this->newLine();

        // Konfirmasi sebelum eksekusi (kecuali dry-run)
        if (!$isDryRun && !$this->confirm("Lanjutkan generate {$total} invoice?", true)) {
            $this->warn('Dibatalkan oleh user.');
            return self::SUCCESS;
        }

        // Proses dengan progress bar
        $bar     = $this->output->createProgressBar($total);
        $success = 0;
        $failed  = 0;
        $errors  = [];

        $bar->start();

        foreach ($transactions as $transaction) {
            if ($isDryRun) {
                // Dry run: hanya simulasi
                $this->newLine();
                $this->line("  [DRY RUN] Akan buat invoice untuk: {$transaction->order_id} (status: {$transaction->status})");
                $success++;
                $bar->advance();
                continue;
            }

            try {
                DB::beginTransaction();

                // Double-check: hindari race condition jika command dijalankan paralel
                $alreadyExists = Invoice::where('transaction_id', $transaction->id)->exists();

                if ($alreadyExists) {
                    $this->newLine();
                    $this->warn("  ⏭️  Skip #{$transaction->order_id} — invoice sudah ada.");
                    $bar->advance();
                    continue;
                }

                $invoiceNumber = Invoice::generateNumber($transaction);

                Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'transaction_id' => $transaction->id,
                    'status'         => $transaction->status, // ikuti status transaksi
                    'created_by'     => null,                 // null = system (backfill)
                ]);

                DB::commit();

                Log::info("✅ Backfill invoice: {$invoiceNumber} for transaction #{$transaction->order_id}");

                $success++;

            } catch (\Exception $e) {
                DB::rollBack();

                $errorMsg = "Transaction #{$transaction->order_id}: " . $e->getMessage();
                $errors[] = $errorMsg;

                Log::error("❌ Backfill failed for transaction #{$transaction->order_id}: " . $e->getMessage());

                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Ringkasan hasil
        $this->info("📊 Hasil Backfill:");
        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Total transaksi diproses', $total],
                ['Berhasil dibuat', $success],
                ['Gagal', $failed],
            ]
        );

        if (!empty($errors)) {
            $this->newLine();
            $this->error('❌ Error yang terjadi:');
            foreach ($errors as $err) {
                $this->line("  - {$err}");
            }
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn('ℹ️  Ini adalah dry run. Jalankan tanpa --dry-run untuk eksekusi sesungguhnya.');
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}