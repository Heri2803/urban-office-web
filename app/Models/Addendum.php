<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Addendum extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'transaction_id',
        'invoice_id',
        'parent_addendum_id',
        'addendum_number',
        'addendum_order',
        'sequence_number',
        'addendum_date',
        'start_date',
        'end_date',
        'duration',
        'duration_type',
        'gross_amount',
        'file_path',
        'public_token',
        'token_expires_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'addendum_date'    => 'date',
        'start_date'       => 'date',
        'end_date'         => 'date',
        'token_expires_at' => 'datetime',
        'gross_amount'     => 'decimal:2',
        'addendum_order'   => 'integer',
        'sequence_number'  => 'integer',
        'duration'         => 'integer',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parentAddendum()
    {
        return $this->belongsTo(Addendum::class, 'parent_addendum_id');
    }

    public function childAddendums()
    {
        return $this->hasMany(Addendum::class, 'parent_addendum_id');
    }

    // =========================================================
    // BOOT
    // =========================================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Auto-set sequence_number global
            if (!$model->sequence_number) {
                $model->sequence_number = static::count() + 1;
            }

            // Auto-set addendum_order per contract_id
            if (!$model->addendum_order) {
                $model->addendum_order = static::where('contract_id', $model->contract_id)->count() + 1;
            }

            // Auto-set start_date = addendum_date
            if ($model->addendum_date && !$model->start_date) {
                $model->start_date = $model->addendum_date;
            }

            // Auto-set end_date dari start_date + duration
            if ($model->start_date && $model->duration && !$model->end_date) {
                $start = Carbon::parse($model->start_date);
                $model->end_date = $model->duration_type === 'year'
                    ? $start->addYears($model->duration)
                    : $start->addMonths($model->duration);
            }

            // Auto-set created_by jika admin
            if (auth()->check() && !$model->created_by) {
                $model->created_by = auth()->id();
            }
        });

        // Sync start_date saat addendum_date diupdate
        static::updating(function ($model) {
            if ($model->isDirty('addendum_date')) {
                $model->start_date = $model->addendum_date;

                // Recalculate end_date
                if ($model->duration) {
                    $start = Carbon::parse($model->start_date);
                    $model->end_date = $model->duration_type === 'year'
                        ? $start->addYears($model->duration)
                        : $start->addMonths($model->duration);
                }
            }
        });
    }

    // =========================================================
    // ACCESSORS
    // =========================================================

    /**
     * Label romawi berdasarkan addendum_order
     * 1 → I, 2 → II, 3 → III, dst
     */
    public function getRomanOrderAttribute(): string
    {
        return $this->romanize($this->addendum_order);
    }

    /**
     * Format tanggal addendum untuk display
     */
    public function getFormattedAddendumDateAttribute(): string
    {
        return $this->addendum_date
            ? $this->addendum_date->translatedFormat('d F Y')
            : '-';
    }

    /**
     * Format start_date untuk display
     */
    public function getFormattedStartDateAttribute(): string
    {
        return $this->start_date
            ? $this->start_date->translatedFormat('d F Y')
            : '-';
    }

    /**
     * Format end_date untuk display
     */
    public function getFormattedEndDateAttribute(): string
    {
        return $this->end_date
            ? $this->end_date->translatedFormat('d F Y')
            : '-';
    }

    /**
     * Teks durasi untuk PDF
     * Contoh: 14 (empat belas) bulan
     */
    public function getDurationTextAttribute(): string
    {
        $satuan = $this->duration_type === 'year' ? 'tahun' : 'bulan';
        return $this->duration . ' (' . $this->numberToWords($this->duration) . ') ' . $satuan;
    }

    /**
     * Cek apakah PDF sudah ada
     */
    public function hasPdf(): bool
    {
        return !empty($this->file_path) && Storage::disk('public')->exists($this->file_path);
    }

    /**
     * Cek apakah sudah punya public token
     */
    public function hasPublicToken(): bool
    {
        return !empty($this->public_token);
    }

    /**
     * Sisa hari aktif
     */
    public function getRemainingDaysAttribute(): ?int
    {
        if (!in_array($this->status, ['active', 'renewed']) || !$this->end_date) {
            return null;
        }
        return max(0, now()->diffInDays($this->end_date, false));
    }

    /**
     * Status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $map = [
            'draft'      => 'bg-gray-100 text-gray-700',
            'active'     => 'bg-green-100 text-green-800',
            'expired'    => 'bg-orange-100 text-orange-800',
            'terminated' => 'bg-red-100 text-red-800',
        ];

        $class = $map[$this->status] ?? 'bg-gray-100 text-gray-700';
        $label = ucfirst($this->status);

        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$class}\">{$label}</span>";
    }


    // Di Model Addendum — untuk akses public dari Blade
    public function numberToWordsPublic(int $number): string
    {
        return $this->numberToWords($number);
    }
    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeForContract($query, $contractId)
    {
        return $query->where('contract_id', $contractId);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    private function romanize(int $number): string
    {
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100,  'XC' => 90,  'L' => 50,  'XL' => 40,
            'X' => 10,   'IX' => 9,   'V' => 5,   'IV' => 4,
            'I' => 1
        ];

        $result = '';
        foreach ($map as $roman => $value) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }
        return $result;
    }

    private function numberToWords(int $number): string
    {
        $words = [
            1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat',
            5 => 'lima', 6 => 'enam', 7 => 'tujuh', 8 => 'delapan',
            9 => 'sembilan', 10 => 'sepuluh', 11 => 'sebelas',
            12 => 'dua belas', 13 => 'tiga belas', 14 => 'empat belas',
            15 => 'lima belas', 24 => 'dua puluh empat',
            36 => 'tiga puluh enam', 48 => 'empat puluh delapan',
            60 => 'enam puluh',
        ];

        return $words[$number] ?? (string) $number;
    }
}