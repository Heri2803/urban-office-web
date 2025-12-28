@extends('layouts.app')

@section('head')
<script type="text/javascript" 
src="https://app.sandbox.midtrans.com/snap/snap.js" 
data-client-key="{{ config('midtrans.client_key') }}"></script> 

<script>
document.addEventListener('alpine:init', () => {
    // Fix: Add sidebar data
    Alpine.data('sidebar', () => ({
        isOpen: false,
        toggle() {
            this.isOpen = !this.isOpen;
        }
    }));

    Alpine.data('mailsData', () => ({
        // Existing properties
        isFilterOpen: false,
        allTransactions: @json($transactions), 
        filterStatus: '', 
        filterDate: '',
        
        currentPage: 1,
        itemsPerPage: 5,

        // 🆕 Separate stores for UI state
        messagesStore: {}, // { transactionId: [messages] }
        uiState: {}, // { transactionId: { showMessages, isLoadingMessages, isSending, etc } }
        
        isLoading: false,
        pollingInterval: null,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),

        // Helper: Get UI state for transaction
        getUIState(transactionId) {
            if (!this.uiState[transactionId]) {
                this.uiState[transactionId] = {
                    showMessages: false,
                    isLoadingMessages: false,
                    newMessage: '',
                    attachmentPreview: null,
                    isSending: false
                };
            }
            return this.uiState[transactionId];
        },

        get filteredTransactions() {
            const filtered = this.allTransactions.filter(transaction => {
                const statusMatch = this.filterStatus === '' || transaction.status === this.filterStatus;
                
                let dateMatch = true;
                if (this.filterDate) {
                    const bookingDate = transaction.booking_date ? new Date(transaction.booking_date).toISOString().split('T')[0] : '';
                    dateMatch = bookingDate === this.filterDate;
                }

                return statusMatch && dateMatch;
            });
            
            return filtered.map(transaction => {
                // ✅ Get from stores
                const messages = this.messagesStore[transaction.id] || [];
                const uiState = this.getUIState(transaction.id);
                
                return {
                    ...transaction,
                    // ✅ From UI state store
                    showMessages: uiState.showMessages,
                    isLoadingMessages: uiState.isLoadingMessages,
                    newMessage: uiState.newMessage,
                    attachmentPreview: uiState.attachmentPreview,
                    isSending: uiState.isSending,
                    // ✅ From messages store
                    messages: messages,
                    unreadCount: this.calculateUnreadCount(messages)
                };
            });
        },

        get paginatedTransactions() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredTransactions.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.filteredTransactions.length / this.itemsPerPage);
        },

        get pageNumbers() {
            let pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
        
            if (total <= 5) {
                pages = Array.from({ length: total }, (_, i) => i + 1);
            } else {
                if (current <= 2) {
                    pages = [1, 2, 3, '...', total];
                } else if (current >= total - 1) {
                    pages = [1, '...', total - 2, total - 1, total];
                } else {
                    pages = [1, '...', current, '...', total];
                }
            }
        
            return pages.filter((value, index, self) => self.indexOf(value) === index);
        },

        // Existing methods
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
                this.scrollToTop();
            }
        },

        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.scrollToTop();
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.scrollToTop();
            }
        },

        scrollToTop() {
            const wrapper = document.getElementById('mail-content-wrapper');
            if (wrapper) {
                wrapper.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        },

        resetFilter() {
            this.filterStatus = '';
            this.filterDate = '';
            this.currentPage = 1;
        },

        // 🔧 FIXED: Toggle messages
        toggleMessages(transactionId) {
            const uiState = this.getUIState(transactionId);
            uiState.showMessages = !uiState.showMessages;
            
            console.log('🔘 Toggle messages:', { 
                transactionId, 
                showMessages: uiState.showMessages,
                hasMessages: !!this.messagesStore[transactionId]
            });
            
            // Load messages if showing and not loaded yet
            if (uiState.showMessages && !this.messagesStore[transactionId]) {
                this.loadMessages(transactionId);
            }
        },

        // Find transaction helpers (keep for compatibility)
        findPaginatedTransaction(transactionId) {
            return this.paginatedTransactions.find(t => t.id == transactionId);
        },

        findTransaction(transactionId) {
            return this.allTransactions.find(t => t.id == transactionId);
        },

        // 🔧 FIXED: Load messages
        async loadMessages(transactionId) {
            const uiState = this.getUIState(transactionId);
            if (uiState.isLoadingMessages) return;

            try {
                uiState.isLoadingMessages = true;
                
                const url = `/dashboard/mails/transaction/${transactionId}/messages`;
                console.log('📨 Fetching from URL:', url);
                
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('📨 Response status:', response.status);
                
                const responseText = await response.text();
                console.log('📨 Response text (first 500 chars):', responseText.substring(0, 500));
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = JSON.parse(responseText);
                console.log('📨 Parsed data:', data);
                
                if (data.success) {
                    console.log('📨 Messages count:', data.messages?.length || 0);
                    this.updateTransactionMessages(transactionId, data.messages || []);
                } else {
                    throw new Error(data.error || 'Failed to load messages');
                }
                
            } catch (error) {
                console.error('❌ Error loading messages:', error);
                this.showNotification('error', 'Gagal memuat pesan: ' + error.message);
            } finally {
                uiState.isLoadingMessages = false;
            }
        },

        // 🔧 FIXED: Send message
        async sendMessage(transactionId) {
            const uiState = this.getUIState(transactionId);
            
            console.log('🚀 sendMessage called:', {
                transactionId,
                message: uiState.newMessage,
                hasAttachment: !!uiState.attachmentPreview
            });
            
            if (!uiState.newMessage?.trim()) {
                console.warn('⚠️ Message is empty');
                return;
            }

            try {
                uiState.isSending = true;
                
                const formData = new FormData();
                formData.append('message', uiState.newMessage.trim());
                formData.append('transaction_id', transactionId); // ✅ Tambahkan ini untuk controller
                
                if (uiState.attachmentPreview?.file) {
                    formData.append('attachment', uiState.attachmentPreview.file);
                }

                // ✅ FIX: Gunakan URL sesuai route Anda
                const url = `/dashboard/mails/transaction/${transactionId}/message`;
                console.log('📤 Sending to:', url);
                console.log('📤 FormData:', {
                    message: uiState.newMessage.trim(),
                    transaction_id: transactionId,
                    hasAttachment: !!uiState.attachmentPreview
                });

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                console.log('📥 Response status:', response.status);

                const responseText = await response.text();
                console.log('📥 Response text (first 500):', responseText.substring(0, 500));

                const data = JSON.parse(responseText);
                console.log('📥 Parsed response:', data);

                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }

                if (data.success && data.message) {
                    this.addMessageToTransaction(transactionId, data.message);
                    console.log('✅ Message added to store');
                    
                    // Clear form
                    uiState.newMessage = '';
                    uiState.attachmentPreview = null;
                    
                    this.showNotification('success', 'Pesan berhasil dikirim');
                    
                    this.$nextTick(() => {
                        this.scrollToMessageBottom(transactionId);
                    });
                } else {
                    throw new Error(data.message || 'Failed to send message');
                }
                
            } catch (error) {
                console.error('❌ Error sending message:', error);
                console.error('❌ Error stack:', error.stack);
                this.showNotification('error', error.message || 'Gagal mengirim pesan');
            } finally {
                uiState.isSending = false;
            }
        },

        // 🔧 FIXED: Handle attachment
        handleAttachmentChange(transactionId, event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                this.showNotification('error', 'Ukuran file maksimal 5MB');
                event.target.value = '';
                return;
            }

            const allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif', 
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain'
            ];
            
            if (!allowedTypes.includes(file.type)) {
                this.showNotification('error', 'Tipe file tidak didukung.');
                event.target.value = '';
                return;
            }

            const uiState = this.getUIState(transactionId);
            uiState.attachmentPreview = {
                name: file.name,
                size: file.size,
                type: file.type,
                file: file
            };

            event.target.value = '';
        },

        // 🔧 FIXED: Remove attachment
        removeAttachment(transactionId) {
            const uiState = this.getUIState(transactionId);
            uiState.attachmentPreview = null;
        },

        // Mark all as read
        async markAllAsRead(transactionId) {
            try {
                // ✅ FIX: Gunakan URL sesuai route
                const url = `/dashboard/mails/transaction/${transactionId}/mark-read`;
                console.log('📖 Marking as read:', url);
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        this.updateTransactionUnreadCount(transactionId, 0);
                        console.log('✅ Messages marked as read');
                        this.showNotification('success', 'Pesan ditandai sudah dibaca');
                    }
                } else {
                    console.error('❌ Failed to mark as read:', response.status);
                }
            } catch (error) {
                console.error('❌ Error marking messages as read:', error);
            }
        },

        // 🔧 FIXED: Update messages
        updateTransactionMessages(transactionId, messages) {
            console.log('🔄 updateTransactionMessages called:', {
                transactionId,
                messagesCount: messages.length
            });
            
            // Store messages
            this.messagesStore[transactionId] = messages;
            
            console.log('✅ Updated messagesStore:', this.messagesStore[transactionId]);
            console.log('✅ Total messages in store:', Object.keys(this.messagesStore).length);
        },

        // 🔧 FIXED: Add single message
        addMessageToTransaction(transactionId, message) {
            if (!this.messagesStore[transactionId]) {
                this.messagesStore[transactionId] = [];
            }
            this.messagesStore[transactionId].push(message);
            
            console.log('✅ Message added to store:', {
                transactionId,
                totalMessages: this.messagesStore[transactionId].length
            });
        },

        // Update unread count
        updateTransactionUnreadCount(transactionId, count) {
            const messages = this.messagesStore[transactionId];
            if (messages) {
                messages.forEach(msg => {
                    if (msg.receiver_id == {{ auth()->id() ?? 0 }}) {
                        msg.is_read = true;
                    }
                });
            }
        },

        // Calculate unread count
        calculateUnreadCount(messages) {
            if (!messages || !Array.isArray(messages)) return 0;
            const userId = {{ auth()->id() ?? 0 }};
            return messages.filter(msg => 
                msg.receiver_id == userId && !msg.is_read
            ).length;
        },

        // Format time
        formatMessageTime(timestamp) {
            if (!timestamp) return '';
            
            try {
                const date = new Date(timestamp);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMs / 3600000);
                const diffDays = Math.floor(diffMs / 86400000);

                if (diffMins < 1) return 'Baru saja';
                if (diffMins < 60) return `${diffMins}m yang lalu`;
                if (diffHours < 24) return `${diffHours}j yang lalu`;
                if (diffDays < 7) return `${diffDays}h yang lalu`;
                
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
                });
            } catch (e) {
                return '';
            }
        },

        // Format file size
        formatFileSize(bytes) {
            if (!bytes) return '0 B';
            
            const units = ['B', 'KB', 'MB', 'GB'];
            let size = bytes;
            let unitIndex = 0;

            while (size >= 1024 && unitIndex < units.length - 1) {
                size /= 1024;
                unitIndex++;
            }

            return `${size.toFixed(1)} ${units[unitIndex]}`;
        },

        // Scroll to bottom
        scrollToMessageBottom(transactionId) {
            this.$nextTick(() => {
                const container = document.querySelector(`[data-transaction="${transactionId}"]`);
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        // Show notification
        showNotification(type, message) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                type === 'warning' ? 'bg-yellow-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="mr-2">${type === 'success' ? '✓' : type === 'error' ? '✗' : '!'}</span>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        },

        // Initialize
        init() {
            const mainContent = document.getElementById('mail-content-wrapper');
            if (mainContent) {
                mainContent.classList.remove('opacity-0', 'translate-y-6');
                mainContent.classList.add('opacity-100', 'translate-y-0');
            }

            this.$watch('filterStatus', () => { this.currentPage = 1; });
            this.$watch('filterDate', () => { this.currentPage = 1; });

            this.initPolling();
        },

        // Initialize polling
        initPolling() {
            // setInterval(() => {
            //     this.checkForNewMessages();
            // }, 30000);
        },

        // Check for new messages
        // async checkForNewMessages() {
        //     const openTransactions = Object.keys(this.uiState).filter(
        //         id => this.uiState[id].showMessages
        //     );
            
        //     if (openTransactions.length === 0) return;

        //     try {
        //         const transactionIds = openTransactions.join(',');
        //         const response = await fetch(`/messages/check-updates?transactions=${transactionIds}`, {
        //             headers: {
        //                 'Accept': 'application/json',
        //                 'X-CSRF-TOKEN': this.csrfToken,
        //                 'X-Requested-With': 'XMLHttpRequest'
        //             }
        //         });

        //         if (response.ok) {
        //             const data = await response.json();
        //             // Process updates if any
        //         }
        //     } catch (error) {
        //         console.error('Error checking for updates:', error);
        //     }
        // }
    }));
});

// Existing openSnap and previewFileName functions remain the same
function previewFileName(input) {
    const file = input.files[0];
    if (file) {
        document.getElementById('fileNamePreview').textContent = "File dipilih: " + file.name;
    }
}

function openSnap(token) { 
    document.body.style.overflow = 'hidden'; 
    if (typeof snap === 'undefined') {
        document.body.style.overflow = '';
        alert('Midtrans Snap SDK belum siap. Silakan coba refresh halaman.');
        return;
    }
    snap.pay(token, { 
        onSuccess: function(result) { 
            console.log("Success:", result); 
            document.body.style.overflow = ''; 
            location.reload(); 
        }, 
        onPending: function(result) { 
            console.log("Pending:", result); 
            document.body.style.overflow = '';
        }, 
        onError: function(result) { 
            console.error("Error:", result); 
            document.body.style.overflow = '';
        }, 
        onClose: function() { 
            document.body.style.overflow = '';
            alert('Anda menutup popup tanpa menyelesaikan pembayaran.'); 
        } 
    }); 
}
</script>
@endsection

@section('content')
<div class="flex min-h-screen bg-gray-50">
    <div class="flex-1 ml-0 md:ml-60 lg:ml-64 xl:ml-64 flex flex-col mb-10">
        <div>
            @include('layouts.components.mailsbar')
        </div>

        <div x-data="mailsData" id="mail-content-wrapper" 
            class="flex-1 p-4 md:p-6 opacity-0 translate-y-6 transition-all duration-500 ease-out">
            
            {{-- Mail Header & Filter --}}
            <div class="mb-4 md:mb-6 flex justify-between items-center relative">
                <h2 class="text-lg md:text-xl font-semibold text-gray-800">
                    Kontak Pesan Masuk
                </h2>
                
                <div class="relative" @click.outside="isFilterOpen = false">
                    <button @click="isFilterOpen = !isFilterOpen" 
                            class="p-2 rounded-full text-gray-600 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1h-1a1 1 0 00-1 1v8a1 1 0 01-1 1H7a1 1 0 01-1-1v-8a1 1 0 00-1-1H4a1 1 0 01-1-1V4z"/>
                        </svg>
                    </button>

                    <div x-show="isFilterOpen" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" 
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-64 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 p-4 z-10">
                        
                        <div class="space-y-3">
                            <h4 class="text-sm font-semibold text-gray-700">Filter Transaksi</h4>
                            
                            <select x-model="filterStatus" class="form-select block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:ring-orange-500 focus:border-orange-500 rounded-md shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="settlement">Settlement (Lunas)</option>
                                <option value="pending">Pending (Menunggu)</option>
                                <option value="failure">Failure (Gagal)</option>
                                <option value="expire">Expire (Kadaluarsa)</option>
                                <option value="cancel">Cancel (Dibatalkan)</option>
                            </select>
                            
                            <input x-model="filterDate" type="date" placeholder="Filter Tanggal Booking" class="form-input block w-full px-3 py-2 text-sm border-gray-300 focus:ring-orange-500 focus:border-orange-500 rounded-md shadow-sm">
                            
                            <button @click="resetFilter(); isFilterOpen = false" class="w-full px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md shadow-sm transition-colors">
                                Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loop Transaksi (Paginated) --}}
            <template x-for="(transaction, index) in paginatedTransactions" :key="transaction.order_id">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            
                    <!-- ✅ HEADER CARD — klik untuk buka halaman detail -->
                    <div 
                        class="p-4 md:p-6 border-b border-gray-200 cursor-pointer hover:bg-gray-50 transition"
                        @click="window.location.href = `{{ route('dashboard.transaction.show', '') }}/${transaction.id}`"
                    >
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <div class="w-5 h-5 bg-white rounded"></div>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-800 text-sm md:text-base">
                                    Dari Customer - <span x-text="transaction.nama_lengkap"></span>
                                </h3>
                                <p class="text-xs md:text-sm text-gray-600 mt-1">
                                    No Telp. <span x-text="transaction.phone"></span>
                                </p>
            
                                <div class="mt-2 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-green-100 text-green-800': transaction.status === 'settlement', 
                                            'bg-yellow-100 text-yellow-800': transaction.status === 'pending', 
                                            'bg-red-100 text-red-800': transaction.status !== 'settlement' && transaction.status !== 'pending'
                                        }">
                                        <span x-text="transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)"></span>
                                    </span>
            
                                    <span class="text-xs text-gray-600">
                                        Order ID: <span x-text="transaction.order_id ?? '-'"></span>
                                    </span>
            
                                    <span class="text-xs text-gray-600">
                                        Tanggal Booking: 
                                        <span 
                                            x-text="transaction.booking_date 
                                            ? new Date(transaction.booking_date).toLocaleDateString('id-ID', 
                                                {day: '2-digit', month: '2-digit', year: 'numeric'}
                                              ) 
                                            : '-'">
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
            
                    <!-- ✅ BODY CARD – tombol Snap -->
                    <div class="p-4 md:p-6">
                        <div class="space-y-4">
            
                            <div class="bg-gray-50 rounded-lg p-3 md:p-4">
                                <p class="text-gray-700 text-sm md:text-base">
                                    Terima kasih atas transaksi Anda.
                                </p>
                            </div>
            
                            <div class="border-l-4 border-blue-400 bg-blue-50 pl-4 py-3 rounded-r-lg">
                                <p class="text-blue-700 text-sm md:text-base font-medium"> 
                                    Untuk pelunasan berikut nanti Rp. 
                                    <span x-text="Number(transaction.gross_amount).toLocaleString('id-ID')"></span>
                                </p> 
            
                                <!-- ✅ FIX: Tombol Snap sepenuhnya bebas, tidak dibungkus <a> -->
                                <template x-if="transaction.status === 'pending' && transaction.snap_token">
                                    <button type="button"
                                        @click.stop="openSnap(transaction.snap_token)"
                                        class="mt-2 px-3 py-1.5 text-sm bg-orange-500 hover:bg-orange-600 
                                               text-white rounded-lg shadow transition">
                                        Lanjutkan Pembayaran
                                    </button>
                                </template>
                            </div>
            
                        </div>
                    </div>

                    <!-- 🆕 MESSAGING SECTION -->
                    <div class="border-t border-gray-200 px-4 md:px-6 py-4 bg-gray-50">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-700 flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Percakapan
                            </h4>
                            <button @click.stop="toggleMessages(transaction.id)" 
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                <span x-text="transaction.showMessages ? 'Sembunyikan' : 'Tampilkan'"></span>
                            </button>
                        </div>

                        <!-- Messages Container (Collapsible) -->
                        <div x-show="transaction.showMessages" 
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 max-h-0"
                                x-transition:enter-end="opacity-100 max-h-64"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 max-h-64"
                                x-transition:leave-end="opacity-0 max-h-0"
                                class="space-y-2 mb-4 overflow-y-auto p-3 bg-white rounded-lg border border-gray-200 messages-container"
                                style="max-height: 16rem;"
                                :data-transaction="transaction.id">
                            
                            <!-- Loading State -->
                            <template x-if="transaction.isLoadingMessages">
                                <div class="flex justify-center py-4">
                                    <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </template>
                            <!-- Dalam template messages: -->
                            <template x-if="transaction.messages && transaction.messages.length > 0">
                                <template x-for="msg in transaction.messages" :key="msg.id">
                                    <div class="flex" :class="msg.sender_id == {{ auth()->id() }} ? 'justify-end' : 'justify-start'">
                                        <div class="max-w-xs md:max-w-sm p-3 rounded-lg shadow-sm"
                                            :class="msg.sender_id == {{ auth()->id() }} 
                                                ? 'bg-blue-100 text-blue-900 rounded-br-none border border-blue-200' 
                                                : 'bg-gray-100 text-gray-800 rounded-bl-none border border-gray-200'">
                                            
                                            <!-- Debug: Tampilkan data mentah -->
                                            <div class="text-xs text-gray-500" x-text="'ID: ' + msg.id"></div>
                                            
                                            <div class="flex justify-between items-start mb-1">
                                                <span class="text-xs font-medium" 
                                                    x-text="msg.sender_id == {{ auth()->id() }} ? 'Anda' : (msg.sender?.name || 'Pengirim')">
                                                </span>
                                                <span class="text-xs text-gray-500 ml-2" 
                                                    x-text="formatMessageTime(msg.created_at)">
                                                </span>
                                            </div>
                                            
                                            <!-- Message content -->
                                            <p class="text-sm" x-text="msg.message"></p>
                                            
                                            <!-- Attachment -->
                                            <template x-if="msg.attachment">
                                                <div class="mt-2 pt-2 border-t border-gray-300 border-opacity-50">
                                                    <div class="flex items-center space-x-2">
                                                        <template x-if="msg.attachment_mime && msg.attachment_mime.startsWith('image/')">
                                                            <img :src="msg.attachment_url" 
                                                                :alt="msg.attachment_name"
                                                                class="w-16 h-16 object-cover rounded cursor-pointer"
                                                                @click="window.open(msg.attachment_url, '_blank')">
                                                        </template>
                                                        <template x-if="!msg.attachment_mime || !msg.attachment_mime.startsWith('image/')">
                                                            <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                            </div>
                                                        </template>
                                                        <div class="flex-1 min-w-0">
                                                            <a :href="msg.attachment_url" 
                                                            target="_blank"
                                                            class="text-xs font-medium text-blue-600 hover:text-blue-800 truncate block"
                                                            x-text="msg.attachment_name">
                                                            </a>
                                                            <p class="text-xs text-gray-500" 
                                                            x-text="formatFileSize(msg.attachment_size)">
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            
                                            <!-- Read status -->
                                            <div class="text-xs text-gray-400 mt-1">
                                                <template x-if="msg.sender_id == {{ auth()->id() }}">
                                                    <span x-text="msg.is_read ? '✓✓ Dibaca' : '✓ Terkirim'"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </template>

                            <!-- No Messages -->
                            <template x-if="!transaction.isLoadingMessages && (!transaction.messages || transaction.messages.length === 0)">
                                <div class="text-center py-6 text-gray-500">
                                    <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <p class="text-sm">Belum ada percakapan</p>
                                    <p class="text-xs">Mulai percakapan dengan admin</p>
                                </div>
                            </template>
                        </div>
                        <!-- Message Input Form -->
                        <div class="mt-3">
                            <form @submit.prevent="sendMessage(transaction.id)" class="space-y-3">
                                <div>
                                    <!-- ✅ FIX: Access via getUIState() -->
                                    <textarea x-model="getUIState(transaction.id).newMessage" 
                                            :placeholder="'Kirim pesan ke admin terkait transaksi ' + (transaction.order_id || '')"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                            rows="2"
                                            :disabled="getUIState(transaction.id).isSending"
                                            required></textarea>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <!-- Attachment -->
                                    <div class="flex items-center space-x-2">
                                        <label :for="'attachment-' + transaction.id" 
                                            class="cursor-pointer p-1.5 text-gray-500 hover:text-blue-600 rounded hover:bg-gray-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                            </svg>
                                            <input type="file" 
                                                :id="'attachment-' + transaction.id" 
                                                :name="'attachment-' + transaction.id"
                                                class="hidden"
                                                @change="handleAttachmentChange(transaction.id, $event)">
                                        </label>
                                        
                                        <!-- ✅ FIX: Attachment Preview -->
                                        <template x-if="getUIState(transaction.id).attachmentPreview">
                                            <div class="flex items-center space-x-1 bg-blue-50 px-2 py-1 rounded text-xs">
                                                <span x-text="getUIState(transaction.id).attachmentPreview.name" class="text-blue-700"></span>
                                                <button type="button" 
                                                        @click="removeAttachment(transaction.id)"
                                                        class="text-red-500 hover:text-red-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Send Button -->
                                    <div class="flex items-center space-x-2">
                                        <!-- ✅ FIX: Character counter -->
                                        <span class="text-xs text-gray-500" 
                                            :class="{ 'text-red-500': (getUIState(transaction.id).newMessage?.length || 0) > 1000 }">
                                            <span x-text="getUIState(transaction.id).newMessage?.length || 0"></span>/1000
                                        </span>
                                        
                                        <!-- ✅ FIX: Button state -->
                                        <button type="submit" 
                                                :disabled="getUIState(transaction.id).isSending || !getUIState(transaction.id).newMessage?.trim()"
                                                class="px-4 py-2 text-sm bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                            <span x-show="!getUIState(transaction.id).isSending">Kirim</span>
                                            <span x-show="getUIState(transaction.id).isSending" class="flex items-center">
                                                <svg class="animate-spin h-4 w-4 text-white mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Mengirim...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Unread Message Notification -->
                        <template x-if="transaction.unreadCount && transaction.unreadCount > 0">
                            <div class="mt-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-blue-600 font-medium">
                                        <span x-text="transaction.unreadCount"></span> pesan belum dibaca
                                    </span>
                                    <button @click="markAllAsRead(transaction.id)" 
                                            class="text-blue-500 hover:text-blue-700">
                                        Tandai sudah dibaca
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                    <!-- END MESSAGING SECTION -->
            
                </div>
            </template>

            
            {{-- Message Kosong --}}
            <template x-if="filteredTransactions.length === 0">
                <div class="text-center py-10 text-gray-500">
                    Tidak ada transaksi yang cocok dengan filter.
                </div>
            </template>

            {{-- UNIFIED PAGINATION CONTROLS (ALL SCREEN SIZES) --}}
            <div x-show="totalPages > 1" class="mt-8 mb-6">
                <div class="flex items-center justify-between gap-2">
                    {{-- Previous Button --}}
                    <button @click="prevPage()" 
                            :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 active:bg-gray-200'"
                            class="flex items-center justify-center px-3 md:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="hidden sm:inline ml-2">Previous</span>
                    </button>

                    {{-- Page Numbers --}}
                    <div class="flex items-center gap-1">
                        <template x-for="page in pageNumbers" :key="page">
                            <div>
                                <button x-show="page !== '...'"
                                        @click="goToPage(page)"
                                        :class="page === currentPage ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300'"
                                        class="px-3 md:px-4 py-2 text-sm font-medium border rounded-lg transition-colors"
                                        x-text="page">
                                </button>
                                <span x-show="page === '...'" class="px-1 md:px-2 text-gray-500 text-sm">...</span>
                            </div>
                        </template>
                    </div>

                    {{-- Next Button --}}
                    <button @click="nextPage()" 
                            :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 active:bg-gray-200'"
                            class="flex items-center justify-center px-3 md:px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg transition-colors">
                        <span class="hidden sm:inline mr-2">Next</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- POP-UP NOTIFICATION --}}
@if(session('success') || session('error') || session('warning'))
<div id="paymentNotification" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" 
         id="notificationModal">
        
        <div class="p-8 text-center">
            @if(session('success'))
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Berhasil!</h3>
                <p class="text-gray-600 text-base">{{ session('success') }}</p>
                
            @elseif(session('error'))
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                    <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Gagal</h3>
                <p class="text-gray-600 text-base">{{ session('error') }}</p>
                
            @elseif(session('warning'))
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-10 w-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Perhatian</h3>
                <p class="text-gray-600 text-base">{{ session('warning') }}</p>
            @endif
        </div>

        <div class="px-8 pb-8">
            <button onclick="closeNotification()" 
                class="w-full px-6 py-3 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95
                @if(session('success')) bg-green-600 hover:bg-green-700 shadow-lg shadow-green-500/50
                @elseif(session('error')) bg-red-600 hover:bg-red-700 shadow-lg shadow-red-500/50
                @else bg-yellow-600 hover:bg-yellow-700 shadow-lg shadow-yellow-500/50 @endif">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notification = document.getElementById('paymentNotification');
    const modal = document.getElementById('notificationModal');
    
    if (notification && modal) {
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            modal.classList.remove('scale-95', 'opacity-0');
            modal.classList.add('scale-100', 'opacity-100');
        }, 100);
        setTimeout(() => {
            closeNotification();
        }, 5000);
    }
});

function closeNotification() {
    const notification = document.getElementById('paymentNotification');
    const modal = document.getElementById('notificationModal');
    
    if (notification && modal) {
        document.body.style.overflow = '';
        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeNotification();
    }
});

document.getElementById('paymentNotification')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeNotification();
    }
});
</script>
@endif

@endsection