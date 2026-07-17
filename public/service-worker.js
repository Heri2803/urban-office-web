// public/service-worker.js
// Service Worker untuk menangani push notification

self.addEventListener('install', (event) => {
    console.log('Service Worker installed');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('Service Worker activated');
    return self.clients.claim();
});

// Menangani notification click
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    // Ambil URL dari data notifikasi
    const urlToOpen = event.notification.data?.url || '/';
    
    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then((clientList) => {
            // Cek apakah sudah ada tab yang terbuka
            for (const client of clientList) {
                if (client.url.includes(urlToOpen) && 'focus' in client) {
                    return client.focus();
                }
            }
            // Jika belum ada, buka tab baru
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Optional: Push event listener (jika pakai Web Push API)
self.addEventListener('push', (event) => {
    if (event.data) {
        const data = event.data.json();
        
        const options = {
            body: data.body || 'Ada surat baru untuk Anda',
            icon: '/assets/LOGO_URBAN_OFFICE.png',
            badge: '/assets/LOGO_URBAN_OFFICE.png',
            image: '/assets/LOGO_URBAN_OFFICE.png',
            vibrate: [200, 100, 200],
            tag: data.tag || 'surat-masuk',
            data: {
                url: data.url || '/dashboard/my-surats',
                surat_id: data.surat_id
            },
            actions: [
                {
                    action: 'view',
                    title: 'Lihat Surat'
                },
                {
                    action: 'close',
                    title: 'Tutup'
                }
            ],
            requireInteraction: true, // Notifikasi tidak hilang otomatis
            timestamp: Date.now()
        };
        
        event.waitUntil(
            self.registration.showNotification(data.title, options)
        );
    }
});

// Menangani notification action button click
self.addEventListener('notificationaction', (event) => {
    if (event.action === 'view') {
        const urlToOpen = event.notification.data?.url || '/';
        clients.openWindow(urlToOpen);
    }
    event.notification.close();
});