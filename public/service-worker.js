const CACHE_NAME = 'sim-maggot-balkot-v6';
const PAGES_CACHE = 'sim-maggot-balkot-pages-v2';
const APP_SHELL = ['/offline.html', '/favicon.ico', '/images/logo.png', '/icons/icon-192.png', '/icons/icon-512.png'];

self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE_NAME).then(cache => Promise.all(
        APP_SHELL.map(url => cache.add(url).catch(() => null))
    )));
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    const keep = [CACHE_NAME, PAGES_CACHE];
    event.waitUntil(caches.keys().then(keys => Promise.all(
        keys.filter(key => !keep.includes(key)).map(key => caches.delete(key))
    )));
    self.clients.claim();
});

async function fetchWithTimeout(request, timeout = 6000) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeout);

    try {
        return await fetch(request, { signal: controller.signal });
    } finally {
        clearTimeout(timer);
    }
}

// Halaman yang boleh disimpan untuk mode offline: hasil GET yang benar-benar
// datang dari server ini (bukan pengalihan ke halaman login) dan berstatus 200.
function isCacheablePage(response) {
    return response && response.ok && response.type === 'basic' && !response.redirected;
}

self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;
    const url = new URL(event.request.url);
    if (url.origin !== self.location.origin) return;

    if (event.request.mode === 'navigate') {
        event.respondWith((async () => {
            // Saat pengguna kembali ke halaman login (termasuk setelah logout),
            // bersihkan halaman tersimpan agar data akun sebelumnya tidak bocor
            // ke pengguna berikutnya pada perangkat yang sama.
            if (url.pathname === '/login') {
                await caches.delete(PAGES_CACHE);
            }

            try {
                const fresh = await fetchWithTimeout(event.request);

                // Selama online, halaman yang berhasil dimuat disimpan sebagai
                // cadangan agar tetap bisa dibuka ketika koneksi terputus.
                if (isCacheablePage(fresh)) {
                    const copy = fresh.clone();
                    caches.open(PAGES_CACHE).then(cache => cache.put(event.request, copy));
                }

                return fresh;
            } catch (error) {
                // Offline: tampilkan kembali halaman yang pernah dibuka. Halaman
                // offline hanya muncul bila halaman ini memang belum pernah dibuka.
                const cachedPage = await caches.match(event.request, { ignoreSearch: true });
                return cachedPage || caches.match('/offline.html', { ignoreSearch: true });
            }
        })());
        return;
    }

    if (url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/') || url.pathname.startsWith('/images/') || url.pathname === '/favicon.ico') {
        event.respondWith((async () => {
            const cached = await caches.match(event.request, { ignoreSearch: true });

            try {
                const fresh = await fetchWithTimeout(event.request);
                if (fresh && fresh.ok) {
                    const copy = fresh.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
                }

                return fresh;
            } catch (error) {
                return cached || Response.error();
            }
        })());
    }
});

self.addEventListener('push', event => {
    const data = event.data ? event.data.json() : {};
    event.waitUntil(self.registration.showNotification(data.title || 'SIM Maggot Balkot', {
        body: data.body || 'Ada pembaruan aktivitas.',
        icon: data.icon || '/icons/icon-192.png',
        badge: '/icons/icon-192.png',
        data: { url: data.url || '/notifikasi' },
        tag: data.tag || 'sim-rumah-maggot',
        renotify: false
    }));
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    const target = event.notification.data?.url || '/notifikasi';
    event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windows => {
        const existing = windows.find(client => client.url.includes(self.location.origin));
        if (existing) { existing.navigate(target); return existing.focus(); }
        return clients.openWindow(target);
    }));
});
