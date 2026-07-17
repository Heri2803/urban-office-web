<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'transaction_id',
        'status',
        'created_by',
        'settlement_request_status',
        'settlement_requested_by',
        'settlement_processed_by',
        'settlement_request_notes',
        'settlement_payment_proof',
        'settlement_rejection_reason'
    ];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'status_badge',
        'formatted_total',
        'formatted_date',
        'creator_name',
    ];
    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Relasi ke transaksi (invoice milik satu transaksi)
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /**
     * Relasi ke user yang membuat invoice (admin)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke user yang mengajukan settlement (admin)
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'settlement_requested_by');
    }

    /**
     * Relasi ke user yang menyetujui/menolak (finance)
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'settlement_processed_by');
    }

    /**
     * Cek apakah invoice manual
     */
    public function isManual(): bool
    {
        return str_starts_with($this->invoice_number, 'MANUAL');
    }

    /**
     * Relasi ke contract
     */
    public function contract()
    {
        return $this->hasOne(Contract::class, 'invoice_id');
    }

    // =========================================================
    // BOOT METHOD
    // =========================================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if ($invoice->transaction && !$invoice->status) {
                $invoice->status = $invoice->transaction->status;
            }
        });
    }

    // =========================================================
    // METHODS - FORMAT INVOICE NUMBER (DENGAN ORDER_ID)
    // =========================================================

    /**
     * Generate nomor invoice baru
     * Format: {order_id}/{room_code}/{YYYYMMDD}/{URUTAN}/{YYYY}
     * Contoh: ORDER-0YHM2I39WH-1773037446/VO/20240315/660/2026
     * 
     * MODIFIKASI:
     * - URUTAN dimulai dari 660 (bukan 001)
     * - Menambahkan tahun di akhir format
     */
    public static function generateNumber($transaction)
    {
        // 1. Ambil order_id dari transaksi
        $orderId = $transaction->order_id;
        
        // 2. Dapatkan kode room type
        $roomCode = self::getRoomTypeCode($transaction->room_type);
        
        // 3. Format tanggal: YYYYMMDD
        $date = date('Ymd');
        
        // 4. Tahun sekarang
        $year = date('Y');
        
        // 5. 🔴 MODIFIKASI: Hitung nomor urut mulai dari 660
        $sequence = self::getNextSequence($orderId, $roomCode, $date, $year);
        
        // 6. 🔴 MODIFIKASI: Tambahkan tahun di akhir
        return $orderId . '/' . $roomCode . '/' . $date . '/' . str_pad($sequence, 3, '0', STR_PAD_LEFT) . '/' . $year;
    }

    /**
     * Dapatkan kode untuk room type
     */
    private static function getRoomTypeCode($roomType)
    {
        $codes = [
            'Virtual Office' => 'VO',
            'Private Office' => 'PO',
            'Meeting Room' => 'MR',
            'Coworking Space' => 'CS',
            'Sharing Room' => 'SR',
            'Event Space' => 'ES',
            'Daily Office' => 'DO',
            'Training Room' => 'TR',
        ];

        return $codes[$roomType] ?? 'INV';
    }

    /**
     * 🔴 MODIFIKASI: Dapatkan nomor urut berikutnya dengan START 660
     * Tetap mempertahankan logika per hari (seperti function Anda)
     * Tapi sequence dimulai dari 660
     */
    private static function getNextSequence($orderId, $roomCode, $date, $year)
    {
        // Pattern pencarian: {orderId}/{roomCode}/{date}/ + apapun + /{year}
        // Contoh: ORDER-XXX/VO/20240315/660/2026
        $pattern = $orderId . '/' . $roomCode . '/' . $date . '/';
        
        // Cari invoice dengan pola yang sama di tahun yang sama
        $lastInvoice = self::where('invoice_number', 'like', $pattern . '%/' . $year)
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract nomor urut dari invoice terakhir
            // Format: ORDER-XXX/VO/20240315/660/2026 -> ambil "660"
            $parts = explode('/', $lastInvoice->invoice_number);
            $lastNumber = (int) $parts[3]; // Index ke-3 adalah nomor urut
            
            // 🔴 MODIFIKASI: sequence selanjutnya (661, 662, dst)
            return $lastNumber + 1;
        }

        // 🔴 MODIFIKASI: Jika belum ada, mulai dari 660
        return 660;
    }

    /**
     * Method alternatif yang lebih sederhana - langsung pakai ID transaksi sebagai urutan
     * Karena 1 transaksi hanya akan punya 1 invoice, sebenarnya urutan selalu 001
     */
    public static function generateNumberSimple($transaction)
    {
        $orderId = $transaction->order_id;
        $roomCode = self::getRoomTypeCode($transaction->room_type);
        $date = date('Ymd');
        
        // Cek apakah sudah ada invoice untuk transaksi ini
        $existingInvoice = self::whereHas('transaction', function($q) use ($orderId) {
            $q->where('order_id', $orderId);
        })->first();
        
        if ($existingInvoice) {
            throw new \Exception('Transaksi ini sudah memiliki invoice');
        }
        
        // Karena 1 transaksi hanya 1 invoice, urutan selalu 001
        return $orderId . '/' . $roomCode . '/' . $date . '/001';
    }

    // =========================================================
    // METHODS
    // =========================================================

    /**
     * Cek apakah invoice bisa di-download
     */
    public function isDownloadable(): bool
    {
        return in_array($this->status, ['settlement', 'pending']);
    }

    /**
     * Cek apakah invoice sudah dibayar
     */
    public function isPaid(): bool
    {
        return $this->status === 'settlement';
    }

    /**
     * Cek apakah invoice masih pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Cek apakah invoice expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Mendapatkan order_id dari nomor invoice
     */
    public function getOrderIdFromNumberAttribute()
    {
        $parts = explode('/', $this->invoice_number);
        return $parts[0]; // Ambil bagian pertama (order_id)
    }

    /**
     * Mendapatkan room code dari nomor invoice
     */
    public function getRoomCodeFromNumberAttribute()
    {
        $parts = explode('/', $this->invoice_number);
        return $parts[1] ?? null;
    }

    // =========================================================
    // ACCESSORS
    // =========================================================

     /**
     * Dapatkan nomor urut dari invoice (accessor)
     */
    public function getSequenceNumberAttribute()
    {
        $parts = explode('/', $this->invoice_number);
        return (int) ($parts[3] ?? 0);
    }
    
    /**
     * Dapatkan tahun dari invoice
     */
    public function getYearFromNumberAttribute()
    {
        $parts = explode('/', $this->invoice_number);
        return (int) ($parts[4] ?? date('Y'));
    }

    /**
     * Format total invoice dari transaksi
     */
    public function getFormattedTotalAttribute()
    {
        if (!$this->transaction) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($this->transaction->gross_amount, 0, ',', '.');
    }

    /**
     * Format tanggal invoice
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d F Y');
    }

    /**
     * Mendapatkan status dengan badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'settlement' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Settlement</span>',
            'expired' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Expired</span>',
            'expire' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Expired</span>', // untuk yg pakai 'expire'
        ];

        return $badges[$this->status] ?? '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">' . ucfirst($this->status) . '</span>';
    }

    /**
     * Nama creator untuk display
     */
    public function getCreatorNameAttribute()
    {
        return $this->creator ? $this->creator->name : 'System';
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSettlement($query)
    {
        return $query->where('status', 'settlement');
    }

    public function scopeExpired($query)
    {
        return $query->whereIn('status', ['expired', 'expire']);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();
            return $query->whereBetween('created_at', [$start, $end]);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('transaction', function($q) use ($search) {
                      $q->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('order_id', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }
        return $query;
    }

    /**
     * Scope untuk mencari berdasarkan order_id
     */
    public function scopeByOrderId($query, $orderId)
    {
        return $query->where('invoice_number', 'like', $orderId . '/%');
    }
}