{{-- resources/views/layouts/components/notification-manager.blade.php --}}

<div x-data="browserNotification" x-init="init()">
    {{-- Optional: Bell icon kecil (jika Anda tetap ingin ada indicator) --}}
    @auth
    @php $unreadSuratsCount = auth()->user()->unreadSuratsCount(); @endphp
    @if($unreadSuratsCount > 0)
    <div x-data="{ showToast: false }" x-init="
        if (!sessionStorage.getItem('unreadToastShown')) {
            setTimeout(() => showToast = true, 500);
            sessionStorage.setItem('unreadToastShown', 'true');
            setTimeout(() => showToast = false, 7000); // Auto hide after 7s
        }
    ">
        {{-- Toast Notification --}}
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             class="fixed top-4 right-4 z-50 bg-white rounded-xl shadow-2xl border-l-4 border-orange-500 p-4 w-72 sm:w-80 flex items-start gap-3 cursor-pointer hover:bg-orange-50 transition-colors"
             @click="window.location.href='{{ route('dashboard.surats.index') }}'"
             style="display: none;">
             
             <div class="p-2 bg-orange-100 rounded-full text-orange-600 flex-shrink-0 mt-0.5">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                 </svg>
             </div>
             <div class="flex-1 min-w-0">
                 <h4 class="text-sm font-bold text-gray-800">Surat Baru Masuk!</h4>
                 <p class="text-xs text-gray-500 mt-0.5 leading-snug">Anda memiliki {{ $unreadSuratsCount }} surat fisik baru yang belum dibaca.</p>
             </div>
             <button @click.stop="showToast = false" class="text-gray-400 hover:text-gray-600 p-1 bg-gray-50 hover:bg-gray-100 rounded-md transition-colors flex-shrink-0">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
             </button>
        </div>
    </div>
    @endif
    @endauth
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('browserNotification', () => ({
            unreadCount: {{ auth()->check() ? auth()->user()->unreadNotifications()->count() : 0 }},
            isSupported: false,
            permission: 'default',
            pollingInterval: null,
            
            async init() {
                this.isSupported = 'Notification' in window && 'serviceWorker' in navigator;
                
                if (!this.isSupported) {
                    console.log('Browser tidak mendukung notifikasi');
                    return;
                }
                
                this.permission = Notification.permission;
                
                // Register Service Worker
                await this.registerServiceWorker();
                
                // Jika permission granted, mulai polling
                if (this.permission === 'granted') {
                    this.startPolling();
                }
                
                // Jika permission default, minta izin dengan cara subtle
                // (tidak pakai banner, tapi saat user interaksi pertama)
                if (this.permission === 'default') {
                    this.requestPermissionOnInteraction();
                }
                
                // Cek notifikasi saat tab aktif kembali
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden && this.permission === 'granted') {
                        this.checkNotifications();
                    }
                });
            },
            
            async registerServiceWorker() {
                try {
                    let registration = await navigator.serviceWorker.getRegistration();
                    
                    if (!registration) {
                        registration = await navigator.serviceWorker.register('/service-worker.js', {
                            scope: '/'
                        });
                        console.log('Service Worker registered:', registration.scope);
                    }
                    
                    await navigator.serviceWorker.ready;
                    console.log('Service Worker ready');
                    
                } catch (error) {
                    console.error('Service Worker registration failed:', error);
                }
            },
            
            requestPermissionOnInteraction() {
                // Minta izin saat user klik atau scroll pertama kali
                const requestPermission = async () => {
                    try {
                        const result = await Notification.requestPermission();
                        this.permission = result;
                        
                        if (result === 'granted') {
                            this.startPolling();
                            this.showWelcomeNotification();
                        }
                    } catch (error) {
                        console.error('Error requesting permission:', error);
                    }
                    
                    // Hapus event listener setelah pertama kali
                    document.removeEventListener('click', requestPermission);
                    document.removeEventListener('scroll', requestPermission);
                };
                
                // Attach ke event klik atau scroll pertama
                document.addEventListener('click', requestPermission, { once: true });
                document.addEventListener('scroll', requestPermission, { once: true });
            },
            
            startPolling() {
                this.checkNotifications();
                
                this.pollingInterval = setInterval(() => {
                    this.checkNotifications();
                }, 30000);
            },
            
            async checkNotifications() {
                if (this.permission !== 'granted') return;
                
                try {
                    const response = await fetch('/api/browser-notifications/pending', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (!response.ok) return;
                    
                    const data = await response.json();
                    
                    // Update unread count
                    this.unreadCount = data.unread_database_count || 0;
                    
                    // Tampilkan browser notification
                    if (data.notifications && data.notifications.length > 0) {
                        for (const notification of data.notifications) {
                            await this.showNotification(notification);
                            await new Promise(resolve => setTimeout(resolve, 500));
                        }
                    }
                    
                } catch (error) {
                    console.error('Error checking notifications:', error);
                }
            },
            
            async showNotification(notificationData) {
                try {
                    const options = {
                        body: notificationData.body || 'Anda menerima surat baru',
                        icon: notificationData.icon || '/assets/LOGO_URBAN_OFFICE.png',
                        badge: notificationData.badge || '/assets/LOGO_URBAN_OFFICE.png',
                        tag: notificationData.tag || 'surat-masuk',
                        data: {
                            url: notificationData.url || '/dashboard/my-surats',
                            surat_id: notificationData.surat_id
                        },
                        actions: [
                            { action: 'view', title: 'Lihat Surat' },
                            { action: 'close', title: 'Tutup' }
                        ],
                        requireInteraction: true,
                        vibrate: [200, 100, 200]
                    };
                    
                    if (navigator.serviceWorker.controller) {
                        const registration = await navigator.serviceWorker.ready;
                        await registration.showNotification(
                            notificationData.title || '📬 Surat Masuk Baru',
                            options
                        );
                    } else {
                        const notif = new Notification(
                            notificationData.title || '📬 Surat Masuk Baru',
                            options
                        );
                        notif.onclick = (e) => {
                            e.preventDefault();
                            window.open(notificationData.url || '/dashboard/my-surats', '_self');
                        };
                    }
                    
                } catch (error) {
                    console.error('Error showing notification:', error);
                }
            },
            
            async showWelcomeNotification() {
                try {
                    if (navigator.serviceWorker.controller) {
                        const registration = await navigator.serviceWorker.ready;
                        await registration.showNotification('✅ Notifikasi Aktif!', {
                            body: 'Anda akan menerima notifikasi saat ada surat masuk baru',
                            icon: '/images/logo-192x192.png',
                            badge: '/images/badge-icon.png',
                            tag: 'welcome',
                            requireInteraction: false
                        });
                    }
                } catch (error) {}
            },
            
            destroy() {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval);
                }
            }
        }));
    });
</script>
@endpush