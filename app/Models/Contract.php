<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'invoice_id',
        'created_by',
        'contract_number',
        'type',
        'status',
        'contract_date',
        'start_date',
        'end_date',
        'payment_scheme',
        'next_payment_date',
        'last_paid_date',
        'file_path',
        'public_token',
        'token_expires_at',
        'termination_reason',
        'terminated_at',
        'updated_by',
    ];

    protected $casts = [
        'contract_date'      => 'date',
        'start_date'         => 'date',
        'end_date'           => 'date',
        'next_payment_date'  => 'date',
        'last_paid_date'     => 'date',
        'terminated_at'      => 'datetime',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'token_expires_at' => 'datetime',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    /**
     * Relasi ke transaksi
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /**
     * Relasi ke invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Relasi ke user yang membuat kontrak (admin)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke addendums
     */
    public function addendums()
    {
        return $this->hasMany(Addendum::class, 'contract_id');
    }

    public function latestAddendum()
    {
        return $this->hasOne(Addendum::class, 'contract_id')->latestOfMany('addendum_order');
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeTerminated($query)
    {
        return $query->where('status', 'terminated');
    }

    public function scopeRenewed($query)
    {
        return $query->where('status', 'renewed');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeVirtualOffice($query)
    {
        return $query->where('type', 'Virtual Office');
    }

    public function scopePrivateOffice($query)
    {
        return $query->where('type', 'Private Office');
    }

    // =========================================================
    // METHODS
    // =========================================================

    /**
     * Cek apakah kontrak masih draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Cek apakah kontrak aktif
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Cek apakah kontrak sudah expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Cek apakah kontrak diputus paksa
     */
    public function isTerminated(): bool
    {
        return $this->status === 'terminated';
    }

    /**
     * Cek apakah kontrak diperpanjang
     */
    public function isRenewed(): bool
    {
        return $this->status === 'renewed';
    }

    /**
     * Cek apakah PDF sudah digenerate
     */
    public function hasPdf(): bool
    {
        return !is_null($this->file_path);
    }

    /**
     * Cek apakah kontrak sudah melewati end_date
     * Digunakan oleh scheduler untuk auto-expire
     */
    public function isPastEndDate(): bool
    {
        if (!$this->end_date) return false;
        return $this->end_date->isPast();
    }

    // =========================================================
    // ACCESSORS
    // =========================================================

    /**
     * Status badge HTML untuk ditampilkan di UI
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'draft'      => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">Draft</span>',
            'active'     => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Aktif</span>',
            'expired'    => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Expired</span>',
            'terminated' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">Terminated</span>',
            'renewed'    => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Diperpanjang</span>',
        ];

        return $badges[$this->status] 
            ?? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">' . e(ucfirst($this->status)) . '</span>';
    }

    /**
     * Format contract_date untuk display
     */
    public function getFormattedContractDateAttribute(): string
    {
        return $this->contract_date
            ? $this->contract_date->format('d M Y')
            : 'Belum Diatur';
    }

    /**
     * Format start_date untuk display
     */
    public function getFormattedStartDateAttribute(): string
    {
        return $this->start_date
            ? $this->start_date->format('d M Y')
            : '-';
    }

    /**
     * Format end_date untuk display
     */
    public function getFormattedEndDateAttribute(): string
    {
        return $this->end_date
            ? $this->end_date->format('d M Y')
            : '-';
    }

    /**
     * Sisa hari kontrak
     */
    public function getRemainingDaysAttribute(): ?int
    {
        if (!$this->end_date || !in_array($this->status, ['active', 'renewed'])) return null;
        $remaining = now()->diffInDays($this->end_date, false);
        return $remaining >= 0 ? (int) $remaining : 0;
    }

    /**
     * URL download PDF kontrak
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;
        return asset('storage/' . $this->file_path);
    }

    /**
     * Nama creator untuk display
     */
    public function getCreatorNameAttribute(): string
    {
        return $this->creator ? $this->creator->name : 'System';
    }

    public function hasPublicToken(): bool
    {
        return !is_null($this->public_token);
    }

    public function isTokenValid(): bool
    {
        if (!$this->public_token) return false;
        if (is_null($this->token_expires_at)) return true; // permanent
        return $this->token_expires_at->isFuture();
    }

    public function getPublicVerifyUrlAttribute(): ?string
    {
        if (!$this->public_token) return null;
        return route('contract.public.verify', ['token' => $this->public_token]);
    }
}