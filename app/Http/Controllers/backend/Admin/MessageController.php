<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Transaction; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;        

class MessageController extends Controller
{
    /**
     * Get list of customers by admin location
     */
        public function getAllMessageUsers()
{
    try {
        $admin = auth()->user();
        
        \Log::info('=== DEBUG: getAllMessageUsers with transaction-based location ===');
        
        // Ambil users dengan location berdasarkan transactions
        $users = User::where('users.id', '!=', $admin->id)
            ->whereIn('users.role', ['customer', 'mitra'])
            ->leftJoin('transactions', function($join) {
                $join->on('transactions.user_id', '=', 'users.id')
                     ->whereNotNull('transactions.location_id');
            })
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.profile_photo',
                // Ambil location_id dari transactions, jika tidak ada gunakan NULL
                \DB::raw('MAX(transactions.location_id) as transaction_location_id')
            )
            ->groupBy('users.id', 'users.name', 'users.email', 'users.role', 'users.profile_photo')
            ->orderBy('users.name')
            ->get()
            ->map(function($user) use ($admin) {
                $locationId = $user->transaction_location_id;
                
                // Filter: hanya tampilkan jika location NULL atau sama dengan admin
                // Atau hapus filter ini untuk testing
                if ($locationId && $locationId != $admin->location_id) {
                    return null; // Skip users dengan location berbeda
                }
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'location_id' => $locationId,
                    'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null,
                    'role_badge' => $this->getRoleBadge($user->role),
                    'has_transactions' => !is_null($locationId)
                ];
            })
            ->filter() // Hapus null values
            ->values(); // Reset array keys
            
        \Log::info('Users found (transaction-based):', [
            'count' => $users->count(),
            'admin_location' => $admin->location_id,
            'users' => $users->map(fn($u) => "{$u['id']}: {$u['name']} (loc: {$u['location_id']})")->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'users' => $users,
            'debug' => [
                'admin_location' => $admin->location_id,
                'location_source' => 'transactions table',
                'total_found' => $users->count()
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error in getAllMessageUsers: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch users'
        ], 500);
    }
}

        private function getRoleBadge($role)
    {
        $badges = [
            'customer' => ['label' => 'Customer', 'color' => 'blue'],
            'mitra' => ['label' => 'Mitra', 'color' => 'green'], 
            'admin' => ['label' => 'Admin', 'color' => 'orange'],
            'superadmin' => ['label' => 'Super Admin', 'color' => 'red'],
        ];
        
        return $badges[$role] ?? ['label' => ucfirst($role), 'color' => 'gray'];
    }

    /**
     * Send message to specific customer
     */
    public function sendToUser(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $admin = auth()->user();
            
            // Check if user exists and has transaction in admin location
            $hasTransaction = Transaction::where('user_id', $userId)
                ->where('location_id', $admin->location_id)
                ->exists();
            
            if (!$hasTransaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or has no booking in your location'
                ], 404);
            }

            $user = User::where('id', $userId)
                ->where('id', '!=', $admin->id)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $messageData = [
                'sender_id' => $admin->id,
                'receiver_id' => $userId,
                'message' => $request->message,
            ];

            // Handle file attachment
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = 'message_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('message_attachments', $filename, 'public');

                $messageData['attachment'] = $path;
                $messageData['attachment_name'] = $file->getClientOriginalName();
                $messageData['attachment_size'] = $file->getSize();
                $messageData['attachment_mime'] = $file->getMimeType();
            }

            $message = Message::create($messageData);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully to ' . $user->name,
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'attachment' => $message->attachment ? asset('storage/' . $message->attachment) : null,
                    'attachment_name' => $message->attachment_name,
                    'recipient' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'role' => $user->role
                    ],
                    'created_at' => $message->created_at->toDateTimeString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Broadcast message to all customers in admin location
     */
    public function broadcastToAll(Request $request)
{
    $validator = Validator::make($request->all(), [
        'message' => 'required|string|max:5000',
        'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $admin = auth()->user();
        
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        Log::info('=== 📢 BROADCAST START ===', [
            'admin_id' => $admin->id,
            'admin_location' => $admin->location_id
        ]);
    
        // ✅ GANTI: Ambil user berdasarkan TRANSAKSI, bukan location_id user
        $userIds = Transaction::where('location_id', $admin->location_id)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id')
            ->toArray();
        
        Log::info('User IDs from transactions:', [
            'user_ids' => $userIds,
            'count' => count($userIds)
        ]);
        
        // Get user details
        $users = User::whereIn('id', $userIds)
            ->where('id', '!=', $admin->id)
            ->get();
        
        Log::info('Users found for broadcast:', [
            'count' => $users->count(),
            'user_details' => $users->map(function($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'role' => $u->role,
                    'location_id' => $u->location_id
                ];
            })->toArray()
        ]);
        
        if ($users->isEmpty()) {
            Log::warning('No users found for broadcast');
            return response()->json([
                'success' => false,
                'message' => 'No users found with bookings in your location'
            ], 404);
        }

        $sentMessages = [];
        $attachmentData = [];

        // Prepare attachment data if exists
        if ($request->hasFile('attachment')) {
            try {
                $file = $request->file('attachment');
                $filename = 'broadcast_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('message_attachments', $filename, 'public');

                $attachmentData = [
                    'attachment' => $path,
                    'attachment_name' => $file->getClientOriginalName(),
                    'attachment_size' => $file->getSize(),
                    'attachment_mime' => $file->getMimeType(),
                ];
                
                Log::info('Attachment uploaded:', ['path' => $path]);
            } catch (\Exception $e) {
                Log::error('Attachment upload failed:', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload attachment: ' . $e->getMessage()
                ], 500);
            }
        }

        // Send to each user
        foreach ($users as $user) {
            try {
                $messageData = [
                    'sender_id' => $admin->id,
                    'receiver_id' => $user->id,
                    'message' => $request->message,
                ];

                if (!empty($attachmentData)) {
                    $messageData = array_merge($messageData, $attachmentData);
                }

                $message = Message::create($messageData);
                $sentMessages[] = [
                    'id' => $message->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name
                ];
                
                Log::info("✅ Message sent to user {$user->id} ({$user->name})");
                
            } catch (\Exception $e) {
                Log::error("❌ Failed to send to user {$user->id}:", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        Log::info('=== 📢 BROADCAST END ===', [
            'success_count' => count($sentMessages),
            'total_users' => $users->count()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message broadcasted to ' . count($sentMessages) . ' users',
            'data' => [
                'user_count' => count($sentMessages),
                'sent_to' => $sentMessages
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('❌ BROADCAST CRITICAL ERROR:', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to broadcast message',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Get conversation with specific customer
     */
    public function getConversation($customerId)
{
    try {
        $admin = auth()->user();
        
        \Log::info('=== DEBUG: getConversation for user ID: ' . $customerId . ' ===');
        \Log::info('Admin:', [
            'id' => $admin->id,
            'location_id' => $admin->location_id,
            'role' => $admin->role
        ]);
        
        // Cari user (customer ATAU mitra) dengan location match
        $customer = User::where('id', $customerId)
            ->whereIn('role', ['customer', 'mitra']) // UBAH INI: include 'mitra'
            ->first();
            
        \Log::info('User query result:', [
            'found' => $customer ? 'YES' : 'NO',
            'user_details' => $customer ? [
                'id' => $customer->id,
                'name' => $customer->name,
                'role' => $customer->role,
                'location_id' => $customer->location_id
            ] : null
        ]);
        
        if (!$customer) {
            \Log::warning('User not found or wrong role', [
                'customer_id' => $customerId,
                'allowed_roles' => ['customer', 'mitra']
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'User not found or not accessible',
                'debug' => [
                    'customer_id' => $customerId,
                    'allowed_roles' => ['customer', 'mitra'],
                    'query_result' => 'not_found'
                ]
            ], 404);
        }
        
        // Untuk sekarang, IGNORE location check atau log saja
        if ($customer->location_id && $customer->location_id != $admin->location_id) {
            \Log::warning('Location mismatch but allowing anyway', [
                'admin_location' => $admin->location_id,
                'customer_location' => $customer->location_id
            ]);
            // Comment return untuk allow semua
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Customer not found or not in your location'
            // ], 404);
        }
        
        \Log::info('✅ User found, proceeding with messages...');
        
        // Ambil pesan antara admin dan user ini
        $messages = Message::betweenUsers($admin->id, $customerId)
            ->with(['sender:id,name,profile_photo', 'receiver:id,name,profile_photo'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) use ($admin) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message,
                    'attachment' => $message->attachment ? asset('storage/' . $message->attachment) : null,
                    'attachment_name' => $message->attachment_name,
                    'attachment_size' => $message->getFormattedFileSize(),
                    'is_image' => $message->isImageAttachment(),
                    'is_read' => $message->is_read,
                    'is_sender' => $message->sender_id == $admin->id,
                    'sender_name' => $message->sender->name,
                    'sender_photo' => $message->sender->profile_photo ? asset('storage/' . $message->sender->profile_photo) : null,
                    'time_ago' => $message->getTimeAgo(),
                    'created_at' => $message->created_at->toDateTimeString(),
                ];
            });
            
        \Log::info('Messages found:', ['count' => $messages->count()]);
        
        // Mark messages as read
        Message::where('sender_id', $customerId)
            ->where('receiver_id', $admin->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
            
        \Log::info('=== DEBUG: getConversation END ===');
        
        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'role' => $customer->role,
                'location_id' => $customer->location_id,
                'profile_photo' => $customer->profile_photo ? asset('storage/' . $customer->profile_photo) : null,
            ],
            'messages' => $messages,
            'debug' => [
                'location_check' => 'disabled for now',
                'admin_location' => $admin->location_id,
                'customer_location' => $customer->location_id,
                'message_count' => $messages->count()
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error in getConversation: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch conversation',
            'error' => config('app.debug') ? $e->getMessage() : 'Server error'
        ], 500);
    }
}

    /**
     * Get admin's message history (list of conversations)
     */
    public function getMessageHistory()
    {
        try {
            $adminId = auth()->id();

            // Get unique customers that admin has conversed with
            $conversations = Message::where(function($query) use ($adminId) {
                    $query->where('sender_id', $adminId)
                          ->orWhere('receiver_id', $adminId);
                })
                ->with(['sender:id,name,profile_photo', 'receiver:id,name,profile_photo'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function($message) use ($adminId) {
                    return $message->sender_id == $adminId ? $message->receiver_id : $message->sender_id;
                })
                ->map(function($messages) use ($adminId) {
                    $latestMessage = $messages->first();
                    $otherUser = $latestMessage->sender_id == $adminId ? $latestMessage->receiver : $latestMessage->sender;
                    
                    $unreadCount = $messages->where('receiver_id', $adminId)
                                          ->where('is_read', false)
                                          ->count();

                    return [
                        'user_id' => $otherUser->id,
                        'user_name' => $otherUser->name,
                        'user_email' => $otherUser->email,
                        'profile_photo' => $otherUser->profile_photo ? asset('storage/' . $otherUser->profile_photo) : null,
                        'last_message' => $latestMessage->message,
                        'last_message_time' => $latestMessage->getTimeAgo(),
                        'unread_count' => $unreadCount,
                        'is_customer' => $otherUser->role === 'customer',
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'conversations' => $conversations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch message history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark messages as read
     */
    public function markAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message_ids' => 'required|array',
            'message_ids.*' => 'integer|exists:messages,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Message::whereIn('id', $request->message_ids)
                ->where('receiver_id', auth()->id())
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Messages marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // MessageController.php - update constructor
    public function __construct()
    {
        $this->middleware('auth');
        
        // Middleware admin check di dalam controller
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role !== 'admin') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access. Admin only.'
                    ], 403);
                }
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        });
    }

    /**
     * Update message - tanpa kolom is_edited
     */
    public function updateMessage(Request $request, $messageId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $message = Message::findOrFail($messageId);
            $user = auth()->user();

            // Cek permission
            if (!$message->canBeEditedBy($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesan tidak dapat diedit. Hanya bisa diedit dalam 15 menit setelah dikirim.'
                ], 403);
            }

            // Update message dengan tanda edit
            $originalMessage = $message->message;
            $updatedMessage = $request->message;
            
            // Tambahkan tanda "(edited)" jika beda
            if ($originalMessage != $updatedMessage) {
                $message->update([
                    'message' => $updatedMessage . " (edited)",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil diperbarui',
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'updated_at' => $message->updated_at->toDateTimeString(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating message: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate pesan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete message - HARD DELETE
     */
    public function deleteMessage($messageId)
    {
        try {
            $message = Message::findOrFail($messageId);
            $user = auth()->user();

            // Cek permission
            if (!$message->canBeDeletedBy($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus pesan ini'
                ], 403);
            }

            // Hapus attachment file jika ada
            if ($message->attachment && Storage::exists('public/' . $message->attachment)) {
                Storage::delete('public/' . $message->attachment);
            }

            // Hapus pesan dari database
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting message: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pesan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Soft delete untuk satu user saja (simulasi)
     */
    public function deleteMessageForMe($messageId)
    {
        try {
            $message = Message::findOrFail($messageId);
            $user = auth()->user();

            // Cek apakah user bagian dari percakapan
            if ($message->sender_id != $user->id && $message->receiver_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak terlibat dalam percakapan ini'
                ], 403);
            }

            // Mark as deleted dengan update message
            $message->update([
                'message' => "[PESAN DIHAPUS]" . ($user->id == $message->sender_id ? " (oleh pengirim)" : " (oleh penerima)")
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan dihapus untuk Anda'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting message for me: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pesan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}