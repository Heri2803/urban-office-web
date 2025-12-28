<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id', 
        'message',
        'attachment',
        'attachment_name',
        'attachment_size', 
        'attachment_mime',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * User yang mengirim pesan
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * User yang menerima pesan
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope untuk pesan antara dua user
     */
    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function($q) use ($user1Id, $user2Id) {
                $q->where('sender_id', $user1Id)
                  ->where('receiver_id', $user2Id);
            })
            ->orWhere(function($q) use ($user1Id, $user2Id) {
                $q->where('sender_id', $user2Id)
                  ->where('receiver_id', $user1Id);
            });
    }

    /**
     * Scope untuk pesan yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope untuk pesan yang dikirim oleh user tertentu
     */
    public function scopeSentBy($query, $userId)
    {
        return $query->where('sender_id', $userId);
    }

    /**
     * Scope untuk pesan yang diterima oleh user tertentu
     */
    public function scopeReceivedBy($query, $userId)
    {
        return $query->where('receiver_id', $userId);
    }

    /**
     * Scope untuk pesan dalam location tertentu
     */
    public function scopeInLocation($query, $locationId)
    {
        return $query->whereHas('sender', function($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })
            ->orWhereHas('receiver', function($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
    }

    // ========== METHODS ==========

    /**
     * Tandai pesan sebagai sudah dibaca
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    /**
     * Cek apakah pesan memiliki attachment
     */
    public function hasAttachment()
    {
        return !is_null($this->attachment);
    }

    /**
     * Get URL attachment
     */
    public function getAttachmentUrl()
    {
        if ($this->hasAttachment()) {
            return asset('storage/' . $this->attachment);
        }
        return null;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSize()
    {
        if (!$this->attachment_size) return null;

        $size = (int) $this->attachment_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Cek apakah file attachment adalah gambar
     */
    public function isImageAttachment()
    {
        if (!$this->attachment_mime) return false;
        
        return str_starts_with($this->attachment_mime, 'image/');
    }

    /**
     * Format waktu pesan untuk display
     */
    public function getTimeAgo()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Cek apakah pesan bisa diakses oleh user
     */
    public function canAccess($userId)
    {
        return $this->sender_id == $userId || $this->receiver_id == $userId;
    }

    // Tambahkan method untuk cek edit/delete permission
    public function canBeEditedBy($userId)
    {
        // Hanya sender yang bisa edit, dan maksimal 15 menit setelah dikirim
        return $this->sender_id == $userId && 
            $this->created_at->addMinutes(15)->isFuture();
    }

    public function canBeDeletedBy($userId)
    {
        $user = User::find($userId);
        
        // Sender selalu bisa hapus pesannya sendiri
        if ($this->sender_id == $userId) {
            return true;
        }
        
        // Admin bisa hapus pesan di location-nya
        if ($user->role == 'admin') {
            $senderLocation = User::find($this->sender_id)->location_id;
            return $senderLocation == $user->location_id;
        }
        
        return false;
    }

    // Method untuk soft delete (simpan di database tanpa kolom baru)
    public function markAsDeletedForUser($userId)
    {
        // Simpan di field message sebagai metadata atau di kolom lain yang ada
        // Contoh: tambahkan prefix "[DELETED]" di message
        if (!$this->isDeletedForUser($userId)) {
            $this->message = "[PESAN DIHAPUS] " . $this->message;
            $this->save();
        }
    }

    public function isDeletedForUser($userId)
    {
        return strpos($this->message, "[PESAN DIHAPUS]") !== false;
    }
}