@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-100" x-data="messageManager()" x-init="init()">
    <div class="flex h-screen">
        <!-- Sidebar: WhatsApp Style -->
        <div class="w-full md:w-1/3 lg:w-1/4 bg-white border-r border-gray-300 flex flex-col h-screen" 
             :class="{ 'hidden md:flex': selectedConversation, 'flex': !selectedConversation }">
            
            <!-- Header Sidebar -->
            <div class="p-3 bg-whatsapp-green">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Avatar User -->
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                            <i class="fas fa-user text-whatsapp-green"></i>
                        </div>
                        <span class="text-white font-semibold">Urban Office Message</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="text-white hover:bg-white/20 p-2 rounded-full">
                            <i class="fas fa-users"></i>
                        </button>
                        <button class="text-white hover:bg-white/20 p-2 rounded-full">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- New Message & Search -->
            <div class="p-3 bg-gray-50 border-b border-gray-300">
                <div class="flex space-x-2">
                    <button @click="showNewMessage = !showNewMessage" 
                            class="flex-1 bg-whatsapp-green text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i>
                        <span>Percakapan Baru</span>
                    </button>
                </div>
                
                <!-- New Message Form -->
                <div x-show="showNewMessage" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-cloak 
                     class="mt-3 p-3 bg-white rounded-lg shadow-lg border border-gray-200">
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Penerima</label>
                            <select x-model="selectedUserId"
                                    @change="console.log('🔄 Dropdown changed to:', selectedUserId)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-whatsapp-green focus:border-transparent text-sm">
                                <option value="">Pilih Kontak</option>
                                <template x-for="user in users" :key="user.id">
                                    <option :value="user.id" x-text="user.name + ' (' + user.role_badge.label + ')'"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div>
                            <textarea x-model="newMessage" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-whatsapp-green focus:border-transparent text-sm resize-none" 
                                      placeholder="Tulis pesan..." 
                                      rows="3"></textarea>
                        </div>
                        
                        <div class="flex justify-between items-center gap-2">
                            <div>
                                <input type="file" @change="previewNewAttachment" accept="image/*" class="hidden" id="newAttachment">
                                <label for="newAttachment" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 cursor-pointer transition-colors">
                                    <i class="fas fa-image text-gray-600 text-sm"></i>
                                </label>
                            </div>
                            <button @click="sendNewMessage" 
                                    :disabled="!selectedUserId || !newMessage.trim()" 
                                    :class="!selectedUserId || !newMessage.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-600'"
                                    class="flex-1 bg-whatsapp-green text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-paper-plane text-xs"></i>
                                Kirim
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto bg-white">
                <template x-for="conversation in conversations" :key="conversation.user_id">
                    <div @click="selectConversation(conversation.user_id)"
                         :class="{'bg-gray-100': selectedConversationId === conversation.user_id}"
                         class="flex items-center p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors">
                        
                        <!-- Avatar -->
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full bg-whatsapp-green flex items-center justify-center text-white font-semibold">
                                <span x-text="conversation.user_name.charAt(0).toUpperCase()"></span>
                            </div>
                            <div x-show="conversation.unread_count > 0" 
                                 class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                                <span x-text="conversation.unread_count"></span>
                            </div>
                        </div>
                        
                        <!-- Conversation Info -->
                        <div class="ml-3 flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h4 class="font-medium text-gray-800 truncate" x-text="conversation.user_name"></h4>
                                <span class="text-xs text-gray-500 whitespace-nowrap ml-2" x-text="conversation.last_message_time"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-600 truncate" x-text="conversation.last_message"></p>
                                <span x-show="conversation.unread_count > 0" 
                                      class="ml-2">
                                    <i class="fas fa-check-double text-blue-500 text-xs"></i>
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800" 
                                      x-text="conversation.role"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="conversations.length === 0" class="flex flex-col items-center justify-center h-full p-8">
                    <div class="text-center">
                        <i class="fas fa-comments text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">Tidak ada percakapan</p>
                        <p class="text-gray-400 text-sm mt-1">Mulai percakapan baru!</p>
                    </div>
                </div>
            </div>
            
            <!-- Broadcast Section -->
            <div class="p-3 border-t border-gray-300 bg-gray-50">
                <div class="space-y-3">
                    <!-- Textarea -->
                    <textarea x-model="broadcastMessage" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm resize-none" 
                            placeholder="Tulis broadcast message..." 
                            rows="2"></textarea>
                    <!-- ✅ TAMBAHKAN: Preview Image (tampil jika ada gambar) -->
                    <div x-show="broadcastPreviewUrl" 
                        x-cloak
                        class="relative inline-block">
                        <img :src="broadcastPreviewUrl" 
                            alt="Preview" 
                            class="h-16 w-16 object-cover rounded-lg border-2 border-gray-300"
                            style="max-height: 64px; max-width: 64px;">
                        <button @click="removeBroadcastPreview" 
                                type="button"
                                class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center hover:bg-red-600 transition-colors shadow-md">
                            <i class="fas fa-times" style="font-size: 10px;"></i>
                        </button>
                    </div>
                    <!-- Button Actions -->
                    <div class="flex items-center gap-2">
                        <input type="file" 
                            @change="previewBroadcastAttachment" 
                            accept="image/*" 
                            class="hidden" 
                            id="broadcastAttachment">
                        <label for="broadcastAttachment" 
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-lg hover:border-amber-500 hover:bg-amber-50 cursor-pointer transition-colors text-sm gap-2">
                            <i class="fas fa-image text-gray-600"></i>
                            <span>Gambar</span>
                        </label>
                        <button @click="sendBroadcast" 
                                :disabled="!broadcastMessage.trim()" 
                                :class="!broadcastMessage.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-300'"
                                class="flex-1 bg-orange-400 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-bullhorn text-xs"></i>
                            Send Broadcast
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col h-screen" 
             :class="{ 'hidden md:flex': !selectedConversation, 'flex': selectedConversation }">
            
            <!-- Chat Header -->
            <div class="bg-whatsapp-green p-3 flex items-center justify-between">
                <!-- Back Button for Mobile -->
                <button @click="selectedConversation = null" 
                        class="md:hidden text-white mr-3">
                    <i class="fas fa-arrow-left"></i>
                </button>
                
                <div class="flex items-center">
                    <!-- Avatar -->
                    <div class="relative">
                            <div class="w-12 h-12 rounded-full bg-whatsapp-green flex items-center justify-center text-white font-semibold">
                                <span x-text="conversation.user_name.charAt(0).toUpperCase()"></span>
                            </div>
                            <div x-show="conversation.unread_count > 0" 
                                 class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                                <span x-text="conversation.unread_count"></span>
                            </div>
                        </div>
                    
                    <!-- Contact Info -->
                    <div class="ml-3">
                        <h3 class="text-white font-semibold" 
                            x-text="selectedConversation ? selectedConversation.user_name : 'Pilih Percakapan'"></h3>
                        <p class="text-white/90 text-sm" x-text="selectedConversation?.role || ''"></p>
                    </div>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-4">
                    <button class="text-white hover:bg-white/20 p-2 rounded-full">
                        <i class="fas fa-phone-alt"></i>
                    </button>
                    <button class="text-white hover:bg-white/20 p-2 rounded-full">
                        <i class="fas fa-video"></i>
                    </button>
                    <button class="text-white hover:bg-white/20 p-2 rounded-full">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
            
            <!-- Messages Container -->
            <div class="flex-1 overflow-y-auto bg-whatsapp-bg p-4" id="messagesContainer">
                <!-- Date Separator -->
                <div class="text-center my-4">
                    <span class="bg-gray-300 text-gray-600 text-xs px-3 py-1 rounded-full">Hari Ini</span>
                </div>
                
                <!-- Messages -->
                <template x-for="msg in messages" :key="msg.id">
                    <div :class="{'flex justify-end': msg.is_sender, 'flex justify-start': !msg.is_sender}"
                         class="mb-3">
                        
                        <!-- Message Bubble -->
                        <div class="max-w-[70%] md:max-w-[60%] relative">
                            <div :class="{'bg-whatsapp-sender rounded-tr-none': msg.is_sender, 
                                          'bg-white rounded-tl-none': !msg.is_sender}"
                                 class="rounded-2xl p-3 shadow-sm">
                                
                                <!-- Message Content -->
                                <p class="text-sm md:text-base" x-text="msg.message"></p>
                                
                                <!-- Attachment -->
                                <div x-show="msg.attachment" class="mt-2">
                                    <a :href="msg.attachment" target="_blank" class="block">
                                        <img x-show="msg.is_image" 
                                             :src="msg.attachment" 
                                             class="rounded-lg max-w-full h-auto">
                                        <div x-show="!msg.is_image" 
                                             class="flex items-center gap-2 p-2 bg-gray-100 rounded-lg">
                                            <i class="fas fa-file text-gray-600"></i>
                                            <span class="text-sm text-gray-700" x-text="msg.attachment_name"></span>
                                            <i class="fas fa-download ml-auto text-gray-500"></i>
                                        </div>
                                    </a>
                                </div>
                                
                                <!-- Timestamp & Status -->
                                <div class="flex justify-end items-center gap-1 mt-1">
                                    <small class="text-xs opacity-75" 
                                           x-text="msg.time_ago"></small>
                                    <span x-show="msg.is_sender && msg.is_edited" 
                                          class="text-xs opacity-75 italic">(diedit)</span>
                                    <span x-show="msg.is_sender" class="ml-1">
                                        <i class="fas fa-check text-xs opacity-75"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Message Options -->
                            <div x-show="msg.is_sender" 
                                 class="absolute -right-8 top-1/2 transform -translate-y-1/2 opacity-0 hover:opacity-100 transition-opacity">
                                <div class="flex flex-col gap-1">
                                    <button @click="startEditMessage(msg)" 
                                            class="w-6 h-6 bg-gray-600 text-white rounded-full flex items-center justify-center hover:bg-gray-700">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button @click="deleteMessage(msg.id)" 
                                            class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Empty State -->
                <div x-show="messages.length === 0 && selectedConversation" 
                     class="flex flex-col items-center justify-center h-full">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-whatsapp-green/20 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-comment-dots text-whatsapp-green text-3xl"></i>
                        </div>
                        <p class="text-gray-500 text-lg">Mulai percakapan</p>
                        <p class="text-gray-400 text-sm mt-1">Kirim pesan pertama Anda!</p>
                    </div>
                </div>
                
                <!-- Loading -->
                <div x-show="isLoading && messages.length === 0" 
                     class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-whatsapp-green mb-2"></div>
                        <p class="text-gray-500">Memuat pesan...</p>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            <div class="bg-gray-100 p-3 border-t border-gray-300">

            <!-- Edit Message Modal -->
            <div x-show="editingMessageId" 
                x-cloak
                class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
                
                <div class="bg-white rounded-xl w-full max-w-md" 
                    @click.away="cancelEdit">
                    
                    <div class="p-4 border-b">
                        <h3 class="font-semibold text-gray-800">Edit Pesan</h3>
                    </div>
                    
                    <div class="p-4">
                        <textarea x-model="editMessageText" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                                rows="3"
                                x-ref="editTextarea"
                                x-init="$watch('editingMessageId', value => value && $nextTick(() => $refs.editTextarea.focus()))"></textarea>
                        
                        <div class="flex justify-end gap-2 mt-4">

                            <!-- Batal -->
                            <button @click="cancelEdit" 
                                    class="px-4 py-2 border border-orange-500 text-orange-500 rounded-lg hover:bg-orange-100 flex items-center gap-2">
                                
                                <!-- X ICON -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>

                                Batal
                            </button>

                            <!-- Simpan -->
                            <button @click="saveEditMessage" 
                                    :disabled="!editMessageText.trim()"
                                    :class="!editMessageText.trim() ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-600'"
                                    class="px-4 py-2 bg-orange-500 text-white rounded-lg flex items-center gap-2">

                                <!-- CHECK ICON -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>

                                Simpan
                            </button>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Form -->
            <form @submit.prevent="sendReply" class="flex items-center gap-2">

                <!-- Emoji & Attachment -->
                <div class="flex items-center gap-1">

                    <!-- Emoji Button -->
                    <!-- <button type="button" 
                            class="w-10 h-10 rounded-full flex items-center justify-center bg-orange-500 text-white hover:bg-orange-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828A4 4 0 0112 16a4 4 0 01-2.828-1.172M9 9h.01M15 9h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                        </svg>

                    </button> -->

                    <!-- Attachment Button -->
                    <input type="file" @change="previewReplyAttachment" accept="image/*" class="hidden" id="replyAttachment">

                    <label for="replyAttachment"
                        class="w-10 h-10 rounded-full flex items-center justify-center bg-orange-500 text-white cursor-pointer hover:bg-orange-600">

                        <!-- PAPERCLIP ICON -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.5V7a4 4 0 00-8 0v7a3 3 0 006 0v-6" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12v5a5 5 0 0010 0v-5" />
                        </svg>

                    </label>
                </div>
                
                <!-- Message Input -->
                <div class="flex-1 relative">
                    <textarea x-model="replyMessage" 
                            @keydown.enter.prevent="sendReply" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-full focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none" 
                            placeholder="Ketik pesan..." 
                            rows="1"
                            x-ref="replyTextarea"></textarea>
                    
                    <!-- Preview Image -->
                    <div x-show="replyPreviewUrl" 
                        class="absolute -top-16 left-0 bg-white p-2 rounded-lg shadow-lg border">
                        <div class="relative">
                            <img :src="replyPreviewUrl" alt="Preview" class="rounded w-20 h-20 object-cover">

                            <button @click="removeReplyPreview" 
                                    class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center">
                                
                                <!-- SMALL X ICON -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>

                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Send Button -->
                <button type="submit" 
                        :disabled="!replyMessage.trim() && !replyFile" 
                        :class="(!replyMessage.trim() && !replyFile) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-orange-600'"
                        class="w-10 h-10 bg-orange-500 text-white rounded-full flex items-center justify-center">

                    <!-- PAPER AIRPLANE ICON -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2z" />
                    </svg>

                </button>

            </form>
        </div>

        </div>
    </div>

    <!-- Toast Notification -->
    <div class="fixed bottom-4 right-4 z-50 space-y-2" 
         x-data="{ toasts: [] }"
         x-on:toast.window="
             toasts.push({ 
                 id: Date.now(), 
                 message: $event.detail.message, 
                 type: $event.detail.type 
             });
             setTimeout(() => { toasts.shift(); }, 3000);
         ">
        <template x-for="toast in toasts" :key="toast.id">
            <div :class="{
                'bg-green-600': toast.type === 'success',
                'bg-red-600': toast.type === 'error',
                'bg-yellow-500': toast.type === 'warning',
                'bg-blue-500': toast.type === 'info'
            }" 
                 class="text-white px-4 py-3 rounded-lg shadow-lg min-w-64 transform transition-all"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-2 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100">
                <div class="flex items-center gap-3">
                    <i :class="{
                        'fas fa-check-circle': toast.type === 'success',
                        'fas fa-times-circle': toast.type === 'error',
                        'fas fa-exclamation-triangle': toast.type === 'warning',
                        'fas fa-info-circle': toast.type === 'info'
                    }"></i>
                    <span x-text="toast.message"></span>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Alpine.js Component Script -->
<script>
function messageManager() {
    return {
        // Data
        users: [],
        conversations: [],
        messages: [],
        selectedConversationId: null,
        selectedConversation: null,
        selectedUserId: '',
        newMessage: '',
        replyMessage: '',
        broadcastMessage: '',
        
        // File handling
        newFile: null,
        newPreviewUrl: null,
        replyFile: null,
        replyPreviewUrl: null,
        broadcastFile: null,
        broadcastPreviewUrl: null,
        
        // UI state
        showNewMessage: false,
        isLoading: false,
        editingMessageId: null,
        editMessageText: '',
        deletingMessageId: null,
        
        // Initialize
        async init() {
            await this.loadUsers();
            await this.loadConversations();
            this.setupAutoRefresh();
        },
        
        // Load all users for new message
        async loadUsers() {
            try {
                console.log('Loading users...');
                
                // Tambahkan cache busting
                const url = '/admin/messages/users?' + new Date().getTime();
                
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include'
                });
                
                console.log('Response status:', response.status);
                
                // Cek jika response bukan JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text.substring(0, 200));
                    
                    // Coba parse sebagai JSON meski content-type salah
                    try {
                        const data = JSON.parse(text);
                        this.handleUserData(data);
                    } catch {
                        console.error('Could not parse response as JSON');
                        this.users = [];
                    }
                    return;
                }
                
                const data = await response.json();
                this.handleUserData(data);
                
            } catch (error) {
                console.error('Error loading users:', error);
                this.users = [];
            }
        },

        handleUserData(data) {
            console.log('=== 🎯 handleUserData START ===');
            console.log('📦 Full API response:', data);
            
            if (data.success) {
                const adminLocationId = data.debug?.admin_location_id || 1;
                
                // ✅ FILTER: Hanya tampilkan user dengan location_id yang sama dengan admin
                this.users = (data.users || []).filter(user => {
                    // User harus punya location_id yang matching
                    const isValidLocation = user.location_id === adminLocationId;
                    
                    if (!isValidLocation) {
                        console.log(`❌ User ${user.id} (${user.name}) - location_id: ${user.location_id} ≠ ${adminLocationId} (filtered out)`);
                    } else {
                        console.log(`✅ User ${user.id} (${user.name}) - location_id: ${user.location_id} = ${adminLocationId} (valid)`);
                    }
                    
                    return isValidLocation;
                });
                
                console.log(`✅ Filtered: ${this.users.length} valid users from ${data.users.length} total`);
                console.log('Valid users for dropdown:', this.users.map(u => `${u.id}: ${u.name} (loc: ${u.location_id})`));
                
                if (this.users.length === 0) {
                    console.warn('⚠️ WARNING: No valid users after filtering! Check if users have correct location_id.');
                }
                
                console.log('=== 🎯 handleUserData END ===');
            } else {
                console.error('❌ API returned success: false', data.message);
                this.users = [];
            }
        },
        
        // Load conversation history
        async loadConversations() {
            try {
                const response = await fetch('/admin/messages/conversations');
                const data = await response.json();
                if (data.success) {
                    this.conversations = data.conversations;
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
            }
        },

        // Load messages for selected conversation
        async loadMessages(userId) {
            try {
                this.isLoading = true;
                const response = await fetch(`/admin/messages/conversation/${userId}`);
                const data = await response.json();
                
                if (data.success) {
                    this.messages = data.messages || [];
                    this.$nextTick(() => this.scrollToBottom());
                } else {
                    this.messages = [];
                    console.error('Error loading messages:', data.message);
                }
            } catch (error) {
                console.error('Error loading messages:', error);
                this.messages = [];
            } finally {
                this.isLoading = false;
            }
        },
        
        // Select conversation
        async selectConversation(userId) {
            console.log('🎯 selectConversation called with userId:', userId);
            
            this.selectedConversationId = userId;
            this.selectedUserId = userId; // ✅ Update dropdown juga
            this.showNewMessage = false;  // ✅ Tutup form "Percakapan Baru"
            
            console.log('✅ AFTER update - selectedUserId:', this.selectedUserId);
            
            const conversation = this.conversations.find(c => c.user_id === userId);
            this.selectedConversation = conversation || null;
            
            if (this.selectedConversation) {
                await this.loadMessages(userId);
                this.scrollToBottom();
            }
        },

        
        // Send new message
        async sendNewMessage() {
            console.log('📤 sendNewMessage called');
            console.log('📝 selectedUserId:', this.selectedUserId);
            console.log('📝 newMessage:', this.newMessage);
            
            if (!this.selectedUserId || !this.newMessage.trim()) {
                console.error('❌ Validation failed!');
                console.error('selectedUserId:', this.selectedUserId);
                console.error('newMessage:', this.newMessage);
                return;
            }
            
            this.isLoading = true;
            const formData = new FormData();
            formData.append('message', this.newMessage.trim());
            if (this.newFile) {
                formData.append('attachment', this.newFile);
            }
            
            const sendUrl = `/admin/messages/send/${this.selectedUserId}`;
            console.log('🌐 Sending to URL:', sendUrl);
            
            try {
                const response = await fetch(sendUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                console.log('📡 Response status:', response.status);
                
                const data = await response.json();
                console.log('📦 Response data:', data);
                
                if (data.success) {
                    this.newMessage = '';
                    this.removeNewPreview();
                    this.selectedUserId = '';
                    this.showNewMessage = false;
                    
                    // Reload conversations and messages
                    await this.loadConversations();
                    if (this.selectedConversationId === parseInt(this.selectedUserId)) {
                        await this.loadMessages(this.selectedUserId);
                    }
                    
                    this.dispatchToast('Pesan terkirim!', 'success');
                } else {
                    console.error('❌ Send failed:', data.message);
                    this.dispatchToast('Gagal mengirim pesan: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('❌ Error sending message:', error);
                this.dispatchToast('Gagal mengirim pesan', 'error');
            }
            
            this.isLoading = false;
        },
        
        // Send reply
        async sendReply() {
            console.log('💬 sendReply called');
            console.log('📝 selectedConversationId:', this.selectedConversationId);
            console.log('📝 replyMessage:', this.replyMessage);
            
            if ((!this.replyMessage.trim() && !this.replyFile) || !this.selectedConversationId) {
                console.error('❌ Reply validation failed!');
                return;
            }
            
            const formData = new FormData();
            formData.append('message', this.replyMessage.trim());
            if (this.replyFile) {
                formData.append('attachment', this.replyFile);
            }
            
            const replyUrl = `/admin/messages/send/${this.selectedConversationId}`;
            console.log('🌐 Replying to URL:', replyUrl);
            
            try {
                const response = await fetch(replyUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                console.log('📡 Reply response status:', response.status);
                
                const data = await response.json();
                console.log('📦 Reply response data:', data);
                
                if (data.success) {
                    this.replyMessage = '';
                    this.removeReplyPreview();
                    await this.loadMessages(this.selectedConversationId);
                    await this.loadConversations();
                    this.scrollToBottom();
                    this.dispatchToast('Pesan terkirim!', 'success');
                } else {
                    console.error('❌ Reply failed:', data.message);
                    this.dispatchToast('Gagal mengirim pesan: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('❌ Error sending reply:', error);
                this.dispatchToast('Gagal mengirim pesan', 'error');
            }
        },
        
        // Send broadcast
        async sendBroadcast() {
            if (!this.broadcastMessage.trim()) {
                this.dispatchToast('Tulis pesan broadcast terlebih dahulu', 'warning');
                return;
            }
            
            if (!confirm(`Broadcast pesan ke ${this.users.length} user?`)) return;
            
            const formData = new FormData();
            formData.append('message', this.broadcastMessage.trim());
            if (this.broadcastFile) {
                formData.append('attachment', this.broadcastFile);
            }
            
            try {
                const response = await fetch('/admin/messages/broadcast', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    this.broadcastMessage = '';
                    this.removeBroadcastPreview();
                    this.dispatchToast(`Broadcast berhasil dikirim ke ${data.data.user_count} user`, 'success');
                    await this.loadConversations();
                }
            } catch (error) {
                console.error('Error broadcasting:', error);
                this.dispatchToast('Gagal melakukan broadcast', 'error');
            }
        },

        // Edit message functions
        startEditMessage(msg) {
            // Cek apakah pesan bisa diedit (hanya sender, max 15 menit)
            if (!msg.is_sender) {
                this.dispatchToast('Hanya pesan yang Anda kirim yang bisa diedit', 'warning');
                return;
            }
            
            // Cek waktu (15 menit limit)
            const messageTime = new Date(msg.created_at);
            const now = new Date();
            const minutesDiff = (now - messageTime) / (1000 * 60);
            
            if (minutesDiff > 15) {
                this.dispatchToast('Pesan hanya bisa diedit dalam 15 menit setelah dikirim', 'warning');
                return;
            }
            
            this.editingMessageId = msg.id;
            // Hapus "(edited)" jika ada
            this.editMessageText = msg.message.replace(/\s*\(edited\)\s*$/, '');
        },

        cancelEdit() {
            this.editingMessageId = null;
            this.editMessageText = '';
        },

        async saveEditMessage() {
            if (!this.editingMessageId || !this.editMessageText.trim()) return;
            
            try {
                const response = await fetch(`/admin/messages/${this.editingMessageId}/update`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: this.editMessageText.trim()
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    // Update message di local array
                    const messageIndex = this.messages.findIndex(m => m.id === this.editingMessageId);
                    if (messageIndex !== -1) {
                        this.messages[messageIndex].message = data.data.message;
                        this.messages[messageIndex].updated_at = data.data.updated_at;
                        this.messages[messageIndex].is_edited = true;
                    }
                    
                    this.cancelEdit();
                    this.dispatchToast('Pesan berhasil diupdate!', 'success');
                } else {
                    this.dispatchToast('Gagal mengupdate pesan: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error updating message:', error);
                this.dispatchToast('Gagal mengupdate pesan', 'error');
            }
        },

        async deleteMessage(messageId) {
            if (!confirm('Apakah Anda yakin ingin menghapus pesan ini?\n\nPesan yang dihapus tidak dapat dikembalikan.')) return;
            
            try {
                const response = await fetch(`/admin/messages/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    // Hapus message dari local array
                    this.messages = this.messages.filter(m => m.id !== messageId);
                    this.dispatchToast('Pesan berhasil dihapus!', 'success');
                } else {
                    this.dispatchToast('Gagal menghapus pesan: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting message:', error);
                this.dispatchToast('Gagal menghapus pesan', 'error');
            }
        },
        
        // File preview functions
        previewNewAttachment(event) {
            this.handleFilePreview(event, 'new');
        },
        
        previewReplyAttachment(event) {
            this.handleFilePreview(event, 'reply');
        },
        
        previewBroadcastAttachment(event) {
            this.handleFilePreview(event, 'broadcast');
        },
        
        handleFilePreview(event, type) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                if (type === 'new') {
                    this.newFile = file;
                    this.newPreviewUrl = URL.createObjectURL(file);
                } else if (type === 'reply') {
                    this.replyFile = file;
                    this.replyPreviewUrl = URL.createObjectURL(file);
                } else if (type === 'broadcast') {
                    this.broadcastFile = file;
                    this.broadcastPreviewUrl = URL.createObjectURL(file);
                }
            } else {
                this.dispatchToast('Hanya file gambar yang diizinkan', 'warning');
            }
        },
        
        removeNewPreview() {
            this.newPreviewUrl = null;
            this.newFile = null;
        },
        
        removeReplyPreview() {
            this.replyPreviewUrl = null;
            this.replyFile = null;
        },
        
        removeBroadcastPreview() {
            this.broadcastPreviewUrl = null;
            this.broadcastFile = null;
        },
        
        // Helper functions
        getRoleColor(role) {
            if (!role) return 'secondary';
            
            const colors = {
                'customer': 'info',
                'mitra': 'success',
                'admin': 'warning',
                'superadmin': 'danger'
            };
            return colors[role] || 'secondary';
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('messagesContainer');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },
        
        setupAutoRefresh() {
            // Auto refresh conversations every 30 seconds
            setInterval(async () => {
                if (this.selectedConversationId) {
                    await this.loadConversations();
                    await this.loadMessages(this.selectedConversationId);
                }
            }, 30000);
        },

        // Toast notification
        dispatchToast(message, type = 'info') {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { message, type }
            }));
        }
    }
}
</script>

<style>
[x-cloak] { 
    display: none !important; 
}
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
/* Animasi untuk message bubble */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.message-item {
    animation: fadeIn 0.3s ease-out;
}
/* WhatsApp Colors */
.bg-whatsapp-green {
    background-color: #e77000ff;
}
.bg-whatsapp-bg {
    background-color: #ECE5DD;
}
.bg-whatsapp-sender {
    background-color: #ffbf77ff;
}

/* WhatsApp Message Bubbles */
.rounded-tr-none {
    border-top-right-radius: 0px !important;
}
.rounded-tl-none {
    border-top-left-radius: 0px !important;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .max-w-\[70\%\] {
        max-width: 85% !important;
    }
}
</style>
@endsection