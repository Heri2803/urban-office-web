<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLocationScope;

class Transaction extends Model
{
    use HasFactory, HasLocationScope;

    protected $fillable = [
        'user_id',
        'city_id',
        'location_id',
        'room_id',
        'city',
        'room_type',
        'jumlah_orang',
        'booking_date',
        'start_time',
        'paket',
        'bulan',
        'tahun',
        'minggu',
        'jam',
        'hari',
        'service_category_id',
        'status_pkp',
        'phone',
        'nama_lengkap',
        'email',
        'order_id',
        'gross_amount',
        'deposit',
        'lunch_total',
        'coffee_break',
        'company_name',
        'company_address',
        'notes',
        'nik',
        'npwp',
        'payment_type',
        'status',
        'snap_token',
        'transaction_time',
        'is_read',
        // TAMBAHKAN FIELD INI:
        'created_by', // untuk tracking admin yang input manual
        'contract_date', // untuk kebutuhan pembuatan kontrak legal PDF
        'promo_code',
        'discount_amount',
    ];

    protected $casts = [
        'user_id'          => 'integer',
        'city_id'          => 'integer',
        'location_id'      => 'integer',
        'room_id'          => 'integer',
        'jumlah_orang'     => 'integer',
        'booking_date'     => 'date',
        'bulan'            => 'integer',
        'tahun'            => 'integer',
        'minggu'           => 'integer',
        'jam'              => 'integer',
        'hari'             => 'integer',
        'gross_amount'     => 'decimal:2',
        'deposit'          => 'decimal:2',
        'lunch_total'      => 'decimal:2',
        'is_read'          => 'boolean',
        'transaction_time' => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        // TAMBAHKAN CAST UNTUK FIELD BARU:
        'created_by'       => 'integer',
        'contract_date'    => 'date',
        'discount_amount'  => 'decimal:2',
    ];

    protected $appends = [
        'booking_time_formatted',
        'duration_text',
        'participants_text',
        'service_type_slug',
    ];

    // =========================================================
    // RELATIONSHIPS
    // =========================================================

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id')
            ->withDefault([
                'name'        => 'Tanpa Kategori',
                'description' => null,
            ]);
    }

    public function lunches()
    {
        return $this->hasMany(TransactionLunch::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'transaction_id');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class, 'transaction_id');
    }

    public function verifiedDocuments()
    {
        return $this->hasMany(Document::class, 'transaction_id')->where('status', 'verified');
    }

    public function pendingDocuments()
    {
        return $this->hasMany(Document::class, 'transaction_id')->where('status', 'pending');
    }

    // =========================================================
    // RELATIONSHIPS BARU UNTUK INVOICE
    // =========================================================

    /**
     * Relasi ke invoice (satu transaksi punya satu invoice)
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'transaction_id');
    }

    /**
     * Relasi ke kontrak (Satu transaksi bisa memiliki riwayat/arsip kontrak)
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'transaction_id');
    }

    /**
     * Relasi ke user yang membuat transaksi manual (admin)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================
    // BOOT METHOD
    // =========================================================

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $lunchTotal = $model->calculateLunchTotal();
            if ($model->lunch_total != $lunchTotal) {
                $model->updateQuietly(['lunch_total' => $lunchTotal]);
            }
        });

        static::deleting(function ($model) {
            $model->lunches()->delete();
            // Opsional: hapus juga invoice terkait
            $model->invoice()->delete();
        });

        // TAMBAHKAN: auto-set created_by jika dari admin dan belum diisi
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->isAdmin() && !$model->created_by) {
                $model->created_by = auth()->id();
            }
        });
    }

    // =========================================================
    // METHODS
    // =========================================================

    /**
     * Hitung total lunch dari relasi
     */
    public function calculateLunchTotal()
    {
        return $this->lunches()->sum('subtotal');
    }

    /**
     * Get total amount termasuk lunch
     */
    public function getTotalAmountAttribute()
    {
        return $this->gross_amount + $this->lunch_total;
    }

    /**
     * Cek apakah transaksi ini sudah memiliki invoice
     */
    public function hasInvoice(): bool
    {
        return $this->invoice()->exists();
    }

    /**
     * Cek apakah transaksi ini adalah transaksi manual (diinput admin)
     */
    public function isManual(): bool
    {
        // Cek dari order_id (format MANUAL-ORDER-YYYYMMDD-XXX)
        return str_starts_with($this->order_id, 'MANUAL-ORDER');
    }

    /**
     * Cek apakah transaksi ini dibuat oleh admin
     */
    public function isCreatedByAdmin(): bool
    {
        return !is_null($this->created_by);
    }

    public function isSettlement(): bool
    {
        return $this->status === 'settlement';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpire(): bool
    {
        return $this->status === 'expire';
    }

    /**
     * Cek apakah dokumen Virtual Office sudah lengkap
     */
    public function hasCompleteDocuments(): bool
    {
        if ($this->room_type !== 'Virtual Office') {
            return true;
        }

        $requiredTypes = ['ktp', 'npwp', 'akta_perusahaan', 'siup_nib'];
        $uploadedTypes = $this->documents()
            ->where('status', 'verified')
            ->pluck('document_type')
            ->toArray();

        return empty(array_diff($requiredTypes, $uploadedTypes));
    }

    /**
     * Progress upload dokumen Virtual Office
     */
    public function getDocumentProgressAttribute(): array
    {
        if ($this->room_type !== 'Virtual Office') {
            return ['total' => 0, 'uploaded' => 0, 'verified' => 0, 'percentage' => 0];
        }

        $requiredTypes  = ['ktp', 'npwp', 'akta_perusahaan', 'siup_nib'];
        $uploadedDocs   = $this->documents()->get()->groupBy('document_type');
        $uploaded       = $uploadedDocs->count();
        $verified       = $this->documents()->where('status', 'verified')->count();

        return [
            'total'      => count($requiredTypes),
            'uploaded'   => $uploaded,
            'verified'   => $verified,
            'percentage' => $uploaded > 0 ? round(($uploaded / count($requiredTypes)) * 100) : 0,
        ];
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeSettlement($query)
    {
        return $query->where('status', 'settlement');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeExpire($query)
    {
        return $query->where('status', 'expire');
    }

    public function scopeByServiceType($query, $serviceType)
    {
        return $serviceType ? $query->where('room_type', $serviceType) : $query;
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return ($startDate && $endDate)
            ? $query->whereBetween('booking_date', [$startDate, $endDate])
            : $query;
    }

    public function scopeSearch($query, $searchTerm)
    {
        if ($searchTerm) {
            return $query->where(function ($q) use ($searchTerm) {
                $q->where('order_id', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('nama_lengkap', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }
        return $query;
    }

    /**
     * Scope untuk transaksi manual (diinput admin)
     */
    public function scopeManual($query)
    {
        return $query->where('order_id', 'like', 'MANUAL-ORDER%');
    }

    /**
     * Scope untuk transaksi dari customer (bukan manual)
     */
    public function scopeFromCustomer($query)
    {
        return $query->where('order_id', 'not like', 'MANUAL-ORDER%');
    }

    // =========================================================
    // ACCESSORS
    // =========================================================

    /**
     * Format waktu booking
     */
    public function getBookingTimeFormattedAttribute()
    {
        if (!empty($this->start_time)) {
            try {
                return date('H:i', strtotime($this->start_time));
            } catch (\Exception $e) {
                return '-';
            }
        }

        if (!empty($this->booking_time)) {
            try {
                return date('H:i', strtotime($this->booking_time));
            } catch (\Exception $e) {
                return '-';
            }
        }

        return '-';
    }

    /**
     * Teks durasi
     */
    public function getDurationTextAttribute()
    {
        if (!empty($this->jam) && $this->jam > 0)    return $this->jam . ' Jam';
        if (!empty($this->hari) && $this->hari > 0)  return $this->hari . ' Hari';
        if (!empty($this->minggu) && $this->minggu > 0) return $this->minggu . ' Minggu';
        if (!empty($this->bulan) && $this->bulan > 0)   return $this->bulan . ' Bulan';
        if (!empty($this->tahun) && $this->tahun > 0)   return $this->tahun . ' Tahun';
        return '-';
    }

    /**
     * Teks jumlah peserta
     */
    public function getParticipantsTextAttribute()
    {
        return (!empty($this->jumlah_orang) && $this->jumlah_orang > 0)
            ? $this->jumlah_orang . ' orang'
            : '-';
    }

    public function getBookingDateOnlyAttribute()
    {
        return $this->booking_date ? $this->booking_date->format('Y-m-d') : null;
    }

    public function getBookingDateTimeAttribute()
    {
        return $this->booking_date ? $this->booking_date->format('Y-m-d H:i:s') : null;
    }

    /**
     * Nama creator (admin) untuk display
     */
    public function getCreatorNameAttribute()
    {
        return $this->creator ? $this->creator->name : 'System/Customer';
    }

    /**
     * Cek apakah transaksi sudah memiliki kontrak (contract_date sudah diisi)
     */
    public function getHasContractAttribute(): bool
    {
        return !is_null($this->contract_date);
    }

    /**
     * Format tanggal kontrak untuk display
     */
    public function getFormattedContractDateAttribute(): string
    {
        return $this->contract_date
            ? $this->contract_date->format('d M Y')
            : 'Belum Diatur';
    }

    /**
     * Slug tipe layanan
     */
    public function getServiceTypeSlugAttribute()
    {
        $mapping = [
            'Meeting Room'    => 'meeting-room',
            'Private Office'  => 'private-office',
            'Sharing Room'    => 'sharing-room',
            'Coworking Space' => 'coworking-space',
            'Virtual Office'  => 'virtual-office',
            'Event Space'     => 'event-space',
        ];

        return $mapping[$this->room_type] ?? strtolower(str_replace(' ', '-', $this->room_type));
    }
}