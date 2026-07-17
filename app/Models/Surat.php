<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Surat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_datang',
        'perihal',
        'pengirim',
        'isi_ringkasan',
        'file_path',
        'file_name',
        'status',
        'status_pengambilan',
        'tanggal_diambil',
        'metode_pengambilan',
        'kurir_pengiriman',
        'resi_pengiriman',
        'created_by',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_datang' => 'date',
        'tanggal_diambil' => 'date',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Admin yang membuat surat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Users yang menerima surat (many-to-many)
     */
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'surat_user')
                    ->withPivot('is_read', 'read_at', 'notified_at')
                    ->withTimestamps();
    }

    /**
     * Recipients yang sudah baca
     */
    public function readRecipients()
    {
        return $this->belongsToMany(User::class, 'surat_user')
                    ->withPivot('is_read', 'read_at', 'notified_at')
                    ->wherePivot('is_read', true)
                    ->withTimestamps();
    }

    /**
     * Recipients yang belum baca
     */
    public function unreadRecipients()
    {
        return $this->belongsToMany(User::class, 'surat_user')
                    ->withPivot('is_read', 'read_at', 'notified_at')
                    ->wherePivot('is_read', false)
                    ->withTimestamps();
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nomor_surat', 'like', "%{$term}%")
              ->orWhere('perihal', 'like', "%{$term}%")
              ->orWhere('pengirim', 'like', "%{$term}%");
        });
    }

    // ==========================================
    // METHODS
    // ==========================================

    /**
     * Cek apakah surat sudah dibaca oleh user tertentu
     */
    public function isReadByUser($userId): bool
    {
        return $this->recipients()
                    ->where('user_id', $userId)
                    ->wherePivot('is_read', true)
                    ->exists();
    }

    /**
     * Mark surat sebagai sudah dibaca oleh user
     */
    public function markAsReadByUser($userId): void
    {
        $this->recipients()->updateExistingPivot($userId, [
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get persentase pembacaan
     */
    public function getReadPercentage(): int
    {
        $total = $this->recipients()->count();
        if ($total === 0) return 0;

        $readCount = $this->recipients()->wherePivot('is_read', true)->count();
        return (int) round(($readCount / $total) * 100);
    }

    /**
     * Cek apakah surat punya file lampiran
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }
}
