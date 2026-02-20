<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'alamat',
        'paket',
        'profile_photo',
        'google_id',
        'mitra_id',
        'location_id', // ← TAMBAHKAN INI
        'role', // ← JANGAN LUPA INI JIKA BELUM ADA
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ========== RELATIONSHIPS ==========
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Pesan yang dikirim oleh user ini
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Pesan yang diterima oleh user ini
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // ========== SCOPES ==========
    
    /**
     * Scope untuk mendapatkan customer berdasarkan location admin
     */
        public function scopeCustomersByAdminLocation($query, $adminId)
    {
        $admin = User::find($adminId);
        
        // Get customer IDs yang pernah booking di location admin
        $customerIds = Transaction::where('location_id', $admin->location_id)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');
        
        return $query->where('role', 'customer')
                    ->whereIn('id', $customerIds);
    }

    /**
     * Scope untuk mendapatkan user berdasarkan location
     */
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Scope untuk mendapatkan admin di location tertentu
     */
    public function scopeAdminsByLocation($query, $locationId)
    {
        return $query->where('role', 'admin')
                    ->where('location_id', $locationId);
    }

    /**
     * Scope untuk mendapatkan semua customer
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    /**
     * Scope untuk mendapatkan semua admin
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // ========== METHODS ==========
    
    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah customer
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Cek apakah user adalah mitra
     */
    public function isMitra()
    {
        return $this->mitra_id !== null && 
               $this->mitra && 
               in_array($this->mitra->status, ['approved', 'active']);
    }

    /**
     * Cek apakah punya pendaftaran mitra pending
     */
    public function hasPendingMitra()
    {
        return $this->mitra_id !== null && 
               $this->mitra && 
               $this->mitra->status === 'pending';
    }

    /**
     * Cek apakah user bisa mengirim pesan ke target user
     */
    public function canMessage($targetUser)
    {
        // Admin bisa kirim pesan ke customer di location yang sama
        if ($this->isAdmin() && $targetUser->isCustomer()) {
            return $this->location_id === $targetUser->location_id;
        }

        // Customer bisa kirim pesan ke admin di location yang sama
        if ($this->isCustomer() && $targetUser->isAdmin()) {
            return $this->location_id === $targetUser->location_id;
        }

        return false;
    }

    /**
     * Get conversation dengan user tertentu
     */
    public function conversationWith($otherUserId)
    {
        return Message::where(function($query) use ($otherUserId) {
                $query->where('sender_id', $this->id)
                      ->where('receiver_id', $otherUserId);
            })
            ->orWhere(function($query) use ($otherUserId) {
                $query->where('sender_id', $otherUserId)
                      ->where('receiver_id', $this->id);
            })
            ->orderBy('created_at', 'asc')
            ->with(['sender', 'receiver']);
    }

    /**
     * Get unread messages count
     */
    public function unreadMessagesCount()
    {
        return $this->receivedMessages()
                    ->where('is_read', false)
                    ->count();
    }

    /**
     * ✅ PASTIKAN RELATIONSHIP INI ADA
     */
    public function userBonuses()
    {
        return $this->hasMany(UserBonus::class, 'user_id', 'id');
    }
    
    /**
     * ✅ Optional: Relationship untuk active bonus saja
     */
    public function activeBonuses()
    {
        return $this->hasMany(UserBonus::class)
            ->where('status', 'active')
            ->where('valid_until', '>=', now())
            ->whereRaw('bonus_hours_total > bonus_hours_used');
    }
}