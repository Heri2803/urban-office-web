// resources/js/browser-notification.js

class BrowserNotificationManager {
    constructor() {
        this.serviceWorkerRegistration = null;
        this.permission = 'default';
        this.isSupported = false;
        this.checkInterval = null;
        this.checkIntervalTime = 30000; // 30 detik
        this.userId = null;
    }

    /**
     * Inisialisasi notification manager
     */
    async init(userId) {
        this.userId = userId;
        this.isSupported = this.checkSupport();
        
        if (!this.isSupported) {
            console.log('Browser tidak mendukung Notification API');
            return false;
        }

        // Register Service Worker
        await this.registerServiceWorker();

        // Cek permission
        this.permission = Notification.permission;
        
        // Mulai polling untuk notifikasi baru
        this.startPolling();

        // Handle visibility change (ketika user switch tab)
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                // User kembali ke tab, cek notifikasi
                this.checkForNotifications();
            }
        });

        return true;
    }

    /**
     * Cek apakah browser support Notification API
     */
    checkSupport() {
        return 'Notification' in window && 
               'serviceWorker' in navigator && 
               'PushManager' in window;
    }

    /**
     * Register Service Worker
     */
    async registerServiceWorker() {
        try {
            this.serviceWorkerRegistration = await navigator.serviceWorker.register('/service-worker.js');
            console.log('Service Worker registered successfully');
            return true;
        } catch (error) {
            console.error('Service Worker registration failed:', error);
            return false;
        }
    }

    /**
     * Minta izin notifikasi ke user
     */
    async requestPermission() {
        if (!this.isSupported) {
            console.warn('Notification API not supported');
            return 'denied';
        }

        try {
            const permission = await Notification.requestPermission();
            this.permission = permission;
            
            if (permission === 'granted') {
                console.log('Notification permission granted');
                // Tampilkan test notification
                await this.showTestNotification();
            } else {
                console.log('Notification permission denied');
            }
            
            return permission;
        } catch (error) {
            console.error('Error requesting notification permission:', error);
            return 'denied';
        }
    }

    /**
     * Tampilkan test notification
     */
    async showTestNotification() {
        if (this.permission !== 'granted') return;

        const options = {
            body: 'Anda akan menerima notifikasi saat ada surat masuk baru',
            icon: '/images/logo-192x192.png',
            badge: '/images/badge-icon.png',
            tag: 'test-notification',
            requireInteraction: false,
            vibrate: [200, 100, 200]
        };

        if (this.serviceWorkerRegistration) {
            await this.serviceWorkerRegistration.showNotification(
                '✅ Notifikasi Aktif!', 
                options
            );
        } else {
            new Notification('✅ Notifikasi Aktif!', options);
        }
    }

    /**
     * Mulai polling untuk cek notifikasi baru
     */
    startPolling() {
        // Cek saat halaman pertama kali load
        this.checkForNotifications();

        // Polling setiap interval
        this.checkInterval = setInterval(() => {
            this.checkForNotifications();
        }, this.checkIntervalTime);
    }

    /**
     * Stop polling
     */
    stopPolling() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
    }

    /**
     * Cek notifikasi dari server
     */
    async checkForNotifications() {
        if (this.permission !== 'granted') return;

        try {
            const response = await fetch('/api/browser-notifications/pending', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Authorization': `Bearer ${this.getAuthToken()}`
                },
                credentials: 'same-origin'
            });

            if (!response.ok) return;

            const data = await response.json();
            
            if (data.success && data.notifications.length > 0) {
                // Tampilkan setiap notifikasi
                for (const notification of data.notifications) {
                    await this.displayBrowserNotification(notification);
                    
                    // Delay sebentar antar notifikasi
                    await this.sleep(500);
                }
            }

            // Dispatch event untuk update UI
            if (data.unread_database_count > 0) {
                window.dispatchEvent(new CustomEvent('notification-update', {
                    detail: {
                        unreadCount: data.unread_database_count
                    }
                }));
            }

        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    /**
     * Tampilkan browser notification
     */
    async displayBrowserNotification(notificationData) {
        const options = {
            body: notificationData.body || 'Anda menerima surat baru',
            icon: notificationData.icon || '/images/logo-192x192.png',
            badge: notificationData.badge || '/images/badge-icon.png',
            image: notificationData.image,
            tag: notificationData.tag || `surat-${notificationData.surat_id}`,
            data: {
                url: notificationData.url || '/dashboard/my-surats',
                surat_id: notificationData.surat_id,
                notificationId: notificationData.id
            },
            actions: notificationData.actions || [
                {
                    action: 'view',
                    title: 'Lihat Surat'
                },
                {
                    action: 'close',
                    title: 'Tutup'
                }
            ],
            requireInteraction: true,
            vibrate: [200, 100, 200],
            timestamp: notificationData.timestamp || Date.now(),
            silent: false,
            renotify: true
        };

        try {
            if (this.serviceWorkerRegistration) {
                await this.serviceWorkerRegistration.showNotification(
                    notificationData.title || '📬 Surat Masuk Baru',
                    options
                );
            } else {
                // Fallback ke regular notification
                const notif = new Notification(
                    notificationData.title || '📬 Surat Masuk Baru', 
                    options
                );
                
                notif.onclick = (event) => {
                    event.preventDefault();
                    window.open(notificationData.url || '/dashboard/my-surats', '_self');
                };
            }

            // Play notification sound (optional)
            this.playNotificationSound();

        } catch (error) {
            console.error('Error displaying notification:', error);
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Cannot play audio:', e));
        } catch (error) {
            console.log('Audio not supported');
        }
    }

    /**
     * Get auth token
     */
    getAuthToken() {
        // Sesuaikan dengan cara Anda menyimpan token
        return localStorage.getItem('auth_token') || 
               document.querySelector('meta[name="api-token"]')?.content || '';
    }

    /**
     * Helper: delay
     */
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Cek apakah user sudah subscribe ke Web Push
     */
    async isSubscribed() {
        if (!this.serviceWorkerRegistration) return false;
        
        const subscription = await this.serviceWorkerRegistration.pushManager.getSubscription();
        return subscription !== null;
    }

    /**
     * Subscribe ke Web Push API (untuk future use)
     */
    async subscribeToPush() {
        try {
            const subscription = await this.serviceWorkerRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(
                    document.querySelector('meta[name="vapid-public-key"]')?.content
                )
            });

            // Kirim subscription ke server
            await fetch('/api/browser-notifications/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                body: JSON.stringify({ subscription })
            });

            return subscription;
        } catch (error) {
            console.error('Failed to subscribe:', error);
            return null;
        }
    }

    /**
     * Convert VAPID key
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');
        
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        
        return outputArray;
    }
}

// Export singleton
export default new BrowserNotificationManager();