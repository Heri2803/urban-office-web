<?php

namespace App\Http\Controllers\Mails;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerMessageController extends Controller
{
    /**
     * Display messaging page - conversation with admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $transactionId = $request->input('transaction_id');
        
        // Cari admin berdasarkan location user
        $admin = User::where('role', 'admin')
                    ->where('location_id', $user->location_id)
                    ->first();
        
        if (!$admin) {
            return view('customer.messages.index', [
                'messages' => collect(),
                'admin' => null,
                'error' => 'Admin tidak ditemukan untuk location Anda.'
            ]);
        }
        
        // Query dasar: percakapan dengan admin
        $query = Message::betweenUsers($user->id, $admin->id)
            ->with(['sender', 'receiver']);
        
        // Filter berdasarkan transaction jika ada
        if ($transactionId) {
            $transaction = Transaction::where('id', $transactionId)
                ->where('user_id', $user->id)
                ->first();
            
            if ($transaction) {
                // Jika ingin menyimpan transaction_id di messages, perlu tambah kolom
                // Untuk sekarang, kita hanya akan filter dengan keyword
                $query->where('message', 'like', "%{$transaction->order_id}%");
            }
        }
        
        // Search messages
        if ($search) {
            $query->where('message', 'like', "%{$search}%");
        }
        
        $messages = $query->orderBy('created_at', 'asc')->paginate(20);
        
        // Tandai pesan yang belum dibaca sebagai sudah dibaca
        Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        // Get unread count for notification
        $unreadCount = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return view('customer.messages.index', [
            'messages' => $messages,
            'admin' => $admin,
            'unreadCount' => $unreadCount,
            'search' => $search,
            'transactionId' => $transactionId
        ]);
    }

    /**
     * Send message to admin
     */
    public function store(Request $request, $transaction = null)
{
    // ✅ Get transaction_id from URL parameter or from request body
    $transactionId = $transaction ?? $request->transaction_id;
    
    \Log::info('🚀 CustomerMessageController::store START', [
        'user_id' => Auth::id(),
        'transaction_id' => $transactionId,
        'transaction_from_url' => $transaction,
        'transaction_from_request' => $request->transaction_id,
        'message_preview' => substr($request->message ?? '', 0, 50),
        'has_attachment' => $request->hasFile('attachment')
    ]);

    // ✅ Merge transaction_id untuk validasi
    $validator = Validator::make(
        array_merge($request->all(), ['transaction_id' => $transactionId]), 
        [
            'message' => 'required|string|min:1|max:5000',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip,rar',
            'transaction_id' => 'required|exists:transactions,id', // ✅ REQUIRED
            'reply_to' => 'nullable|exists:messages,id'
        ]
    );
    
    if ($validator->fails()) {
        \Log::warning('⚠️ Validation failed', [
            'errors' => $validator->errors()->toArray()
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        return redirect()->back()->withErrors($validator)->withInput();
    }
    
    try {
        $user = Auth::user();
        
        // ✅ Get transaction
        $transactionModel = Transaction::findOrFail($transactionId);
        
        \Log::info('📦 Transaction found', [
            'transaction_id' => $transactionModel->id,
            'order_id' => $transactionModel->order_id,
            'user_id' => $transactionModel->user_id,
            'location_id' => $transactionModel->location_id
        ]);
        
        // ✅ Authorization check
        if ($user->role !== 'admin' && $transactionModel->user_id != $user->id) {
            \Log::warning('⚠️ Unauthorized access attempt', [
                'user_id' => $user->id,
                'transaction_user_id' => $transactionModel->user_id
            ]);
            
            throw new \Exception('Anda tidak memiliki akses untuk mengirim pesan pada transaksi ini.');
        }
        
        \Log::info('✅ User authorized');
        
        // ✅ Get admin based on TRANSACTION location (bukan user location)
        $admin = User::where('role', 'admin')
                    ->where('location_id', $transactionModel->location_id)
                    ->first();
        
        if (!$admin) {
            \Log::error('❌ Admin not found', [
                'location_id' => $transactionModel->location_id
            ]);
            throw new \Exception('Admin tidak ditemukan untuk location transaksi ini.');
        }
        
        \Log::info('👤 Admin found', [
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_location' => $admin->location_id
        ]);
        
        // ✅ Determine receiver based on sender role
        // If sender is admin -> receiver is customer
        // If sender is customer -> receiver is admin
        $receiverId = ($user->role === 'admin') ? $transactionModel->user_id : $admin->id;
        
        \Log::info('💬 Message participants', [
            'sender_id' => $user->id,
            'sender_name' => $user->name,
            'sender_role' => $user->role,
            'receiver_id' => $receiverId
        ]);
        
        // ✅ Handle attachment
        $attachmentData = null;
        if ($request->hasFile('attachment')) {
            \Log::info('📎 Processing attachment');
            $attachmentData = $this->storeAttachment($request->file('attachment'));
            \Log::info('✅ Attachment stored', [
                'path' => $attachmentData['path'],
                'name' => $attachmentData['name'],
                'size' => $attachmentData['size']
            ]);
        }
        
        // ✅ Create message - tanpa clean() function
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => strip_tags($request->input('message')), // ✅ Simple XSS protection
            'attachment' => $attachmentData['path'] ?? null,
            'attachment_name' => $attachmentData['name'] ?? null,
            'attachment_size' => $attachmentData['size'] ?? null,
            'attachment_mime' => $attachmentData['mime'] ?? null,
            'is_read' => false,
        ]);
        
        \Log::info('✅ Message created', [
            'message_id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id
        ]);
        
        // ✅ Handle reply_to (optional)
        if ($request->filled('reply_to')) {
            $originalMessage = Message::find($request->reply_to);
            if ($originalMessage) {
                \Log::info('💬 Reply to message', [
                    'original_message_id' => $originalMessage->id
                ]);
            }
        }
        
        // ✅ Load relationships
        $message->load(['sender', 'receiver']);
        
        // ✅ Fire event (optional - comment out jika event belum dibuat)
        // event(new \App\Events\NewMessage($message));
        
        // ✅ Format response sesuai yang diharapkan Alpine.js
        $formattedMessage = [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'message' => $message->message,
            'attachment' => $message->attachment,
            'attachment_name' => $message->attachment_name,
            'attachment_size' => $message->attachment_size,
            'attachment_mime' => $message->attachment_mime,
            'attachment_url' => $message->getAttachmentUrl(),
            'is_read' => $message->is_read,
            'read_at' => $message->read_at,
            'created_at' => $message->created_at->toISOString(),
            'updated_at' => $message->updated_at->toISOString(),
            'sender' => [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'role' => $message->sender->role ?? 'user'
            ],
            'receiver' => [
                'id' => $message->receiver->id,
                'name' => $message->receiver->name,
                'role' => $message->receiver->role ?? 'user'
            ],
            'is_me' => $message->sender_id == $user->id
        ];
        
        \Log::info('🎯 CustomerMessageController::store END', [
            'success' => true,
            'message_id' => $message->id
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $formattedMessage // ✅ Key nya 'message', bukan 'data'
            ]);
        }
        
        return redirect()->back()->with('success', 'Pesan berhasil dikirim!');
        
    } catch (\Exception $e) {
        \Log::error('❌ Error in CustomerMessageController::store', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()->with('error', 'Gagal mengirim pesan: ' . $e->getMessage());
    }
}

    /**
     * Mark message as read
     */
    public function markAsRead(Message $message)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($message->receiver_id !== $user->id) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403);
        }
        
        $message->markAsRead();
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }

    /**
     * Delete message (soft delete for user)
     */
    public function destroy(Message $message)
    {
        $user = Auth::user();
        
        // Cek apakah user bisa menghapus pesan ini
        if (!$message->canBeDeletedBy($user->id)) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus pesan ini'
                ], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus pesan ini');
        }
        
        $message->markAsDeletedForUser($user->id);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dihapus'
            ]);
        }
        
        return redirect()->back()->with('success', 'Pesan berhasil dihapus');
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(Message $message)
    {
        $user = Auth::user();
        
        if (!$message->canAccess($user->id)) {
            abort(403);
        }
        
        if (!$message->hasAttachment()) {
            abort(404);
        }
        
        $path = storage_path('app/public/' . $message->attachment);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        return response()->download($path, $message->attachment_name);
    }

    /**
     * Get unread messages count (for navbar notification)
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Store attachment and return data
     */
    private function storeAttachment($file)
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        // Generate safe filename
        $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9\.]/', '_', $originalName);
        
        // Store file
        $path = $file->storeAs('message-attachments', $safeName, 'public');
        
        return [
            'path' => $path,
            'name' => $originalName,
            'size' => $size,
            'mime' => $mimeType
        ];
    }

    /**
     * Get conversation with specific admin (if multiple admins)
     */
    public function conversation($adminId)
    {
        $user = Auth::user();
        
        // Cek apakah admin ada dan di location yang sama
        $admin = User::where('id', $adminId)
                    ->where('role', 'admin')
                    ->where('location_id', $user->location_id)
                    ->firstOrFail();
        
        $messages = Message::betweenUsers($user->id, $admin->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->paginate(30);
        
        // Mark as read
        Message::where('receiver_id', $user->id)
            ->where('sender_id', $admin->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        return view('customer.messages.conversation', compact('messages', 'admin'));
    }

    // Add these methods to your existing MessageController

    public function getTransactionMessages($transactionId)
{
    \Log::info('🎯 CustomerMessageController::getTransactionMessages START', [
        'transaction_id' => $transactionId,
        'user_id' => Auth::id()
    ]);
    
    try {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        \Log::info('User:', [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role
        ]);
        
        // Get transaction
        $transaction = Transaction::find($transactionId);
        
        if (!$transaction) {
            \Log::warning('Transaction not found');
            return response()->json([
                'success' => false,
                'error' => 'Transaction not found'
            ], 404);
        }
        
        \Log::info('Transaction found:', [
            'id' => $transaction->id,
            'order_id' => $transaction->order_id,
            'user_id' => $transaction->user_id,
            'location_id' => $transaction->location_id
        ]);
        
        // Authorization
        if ($user->role !== 'admin' && $transaction->user_id != $user->id) {
            \Log::warning('User unauthorized', [
                'current_user_id' => $user->id,
                'transaction_user_id' => $transaction->user_id
            ]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        \Log::info('✅ User authorized');
        
        // Get admin based on transaction location
        $admin = User::where('role', 'admin')
            ->where('location_id', $transaction->location_id)
            ->first();
        
        \Log::info('Admin query:', [
            'found' => $admin ? 'YES' : 'NO',
            'admin_id' => $admin->id ?? null,
            'transaction_location' => $transaction->location_id
        ]);
        
        if (!$admin) {
            \Log::warning('Admin not found');
            return response()->json([
                'success' => true,
                'messages' => [],
                'unread_count' => 0
            ]);
        }
        
        // Determine conversation participants
        // Jika user adalah admin, conversation dengan customer
        // Jika user adalah customer, conversation dengan admin
        $otherUserId = ($user->role === 'admin') ? $transaction->user_id : $admin->id;
        
        \Log::info('Conversation participants:', [
            'current_user' => $user->id,
            'other_user' => $otherUserId,
            'user_role' => $user->role
        ]);
        
        // Get ALL messages between users (tanpa filter transaction)
        $messages = Message::where(function($query) use ($user, $otherUserId) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $otherUserId);
            })
            ->orWhere(function($query) use ($user, $otherUserId) {
                $query->where('sender_id', $otherUserId)
                    ->where('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        \Log::info('Messages query result:', [
            'count' => $messages->count(),
            'query' => 'All messages between users'
        ]);
        
        // Jika ingin filter berdasarkan transaction (opsional)
        // Anda bisa menambahkan kolom 'transaction_id' di table messages
        // Atau filter berdasarkan order_id dalam message text
        
        // Format response
        $formattedMessages = $messages->map(function($message) use ($user) {
            return [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'message' => $message->message,
                'attachment' => $message->attachment,
                'attachment_name' => $message->attachment_name,
                'attachment_size' => $message->attachment_size,
                'attachment_mime' => $message->attachment_mime,
                'attachment_url' => $message->getAttachmentUrl(),
                'is_read' => $message->is_read,
                'read_at' => $message->read_at,
                'created_at' => $message->created_at->toISOString(),
                'updated_at' => $message->updated_at->toISOString(),
                'sender' => $message->sender ? [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'role' => $message->sender->role
                ] : null,
                'receiver' => $message->receiver ? [
                    'id' => $message->receiver->id,
                    'name' => $message->receiver->name,
                    'role' => $message->receiver->role
                ] : null,
                // Tambahkan flag untuk styling
                'is_me' => $message->sender_id == $user->id
            ];
        });
        
        $unreadCount = $messages->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        \Log::info('🎯 CustomerMessageController::getTransactionMessages END', [
            'formatted_messages_count' => $formattedMessages->count(),
            'unread_count' => $unreadCount
        ]);
        
        return response()->json([
            'success' => true,
            'messages' => $formattedMessages,
            'unread_count' => $unreadCount,
            'debug' => [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'transaction_user_id' => $transaction->user_id,
                'admin_id' => $admin->id,
                'other_user_id' => $otherUserId
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('❌ Error in getTransactionMessages: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage(),
            'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null
        ], 500);
    }
}
    public function checkUpdates(Request $request)
{
    \Log::info('🔍 checkUpdates START', [
        'user_id' => auth()->id(),
        'transactions' => $request->query('transactions')
    ]);
    
    try {
        $user = auth()->user();
        $transactionIds = explode(',', $request->query('transactions', ''));
        
        $updates = [];
        
        foreach ($transactionIds as $transactionId) {
            $transaction = Transaction::find($transactionId);
            
            if (!$transaction) {
                continue;
            }
            
            // Authorization
            if ($user->role !== 'admin' && $transaction->user_id != $user->id) {
                continue;
            }
            
            // Get admin for conversation
            $admin = User::where('role', 'admin')
                        ->where('location_id', $transaction->location_id)
                        ->first();
            
            if (!$admin) {
                continue;
            }
            
            // Determine other user
            $otherUserId = ($user->role === 'admin') ? $transaction->user_id : $admin->id;
            
            // Get messages
            $messages = Message::where(function($query) use ($user, $otherUserId) {
                    $query->where('sender_id', $user->id)
                          ->where('receiver_id', $otherUserId);
                })
                ->orWhere(function($query) use ($user, $otherUserId) {
                    $query->where('sender_id', $otherUserId)
                          ->where('receiver_id', $user->id);
                })
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Format messages
            $formattedMessages = $messages->map(function($message) use ($user) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'message' => $message->message,
                    'attachment' => $message->attachment,
                    'attachment_name' => $message->attachment_name,
                    'attachment_size' => $message->attachment_size,
                    'attachment_mime' => $message->attachment_mime,
                    'attachment_url' => $message->getAttachmentUrl(),
                    'is_read' => $message->is_read,
                    'read_at' => $message->read_at,
                    'created_at' => $message->created_at->toISOString(),
                    'updated_at' => $message->updated_at->toISOString(),
                    'sender' => $message->sender ? [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'role' => $message->sender->role
                    ] : null,
                    'receiver' => $message->receiver ? [
                        'id' => $message->receiver->id,
                        'name' => $message->receiver->name,
                        'role' => $message->receiver->role
                    ] : null,
                    'is_me' => $message->sender_id == $user->id
                ];
            });
            
            $unreadCount = $messages->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count();
            
            $updates[$transactionId] = [
                'messages' => $formattedMessages,
                'unread_count' => $unreadCount,
                'has_new' => $messages->isNotEmpty()
            ];
        }
        
        \Log::info('✅ checkUpdates END', [
            'updates_count' => count($updates)
        ]);
        
        return response()->json([
            'success' => true,
            'updates' => $updates
        ]);
        
    } catch (\Exception $e) {
        \Log::error('❌ Error in checkUpdates', [
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}