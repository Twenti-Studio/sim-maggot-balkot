const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;

document.querySelector('[data-menu-toggle]')?.addEventListener('click', () => {
    document.querySelector('[data-sidebar]')?.classList.toggle('-translate-x-full');
    document.querySelector('[data-overlay]')?.classList.toggle('hidden');
});
document.querySelector('[data-overlay]')?.addEventListener('click', () => {
    document.querySelector('[data-sidebar]')?.classList.add('-translate-x-full');
    document.querySelector('[data-overlay]')?.classList.add('hidden');
});

let deferredInstall;
window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstall = event;
    const button = document.querySelector('[data-install-pwa]');
    button?.classList.remove('hidden');
    button?.classList.add('inline-flex');
});
document.querySelector('[data-install-pwa]')?.addEventListener('click', async () => {
    if (!deferredInstall) return;
    await deferredInstall.prompt();
    deferredInstall = null;
    document.querySelector('[data-install-pwa]')?.classList.add('hidden');
});

if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        const registration = await navigator.serviceWorker.register('/service-worker.js?v=5');
        registration.update();
    });
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    return Uint8Array.from(atob(base64), char => char.charCodeAt(0));
}

async function enablePush(button) {
    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !vapidPublicKey) {
        throw new Error('Notifikasi belum tersedia pada perangkat ini.');
    }
    const installed = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    if (!installed) throw new Error('Pasang aplikasi ke perangkat terlebih dahulu sebelum mengaktifkan notifikasi.');
    button.disabled = true;
    const registration = await navigator.serviceWorker.ready;
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') throw new Error('Izin notifikasi tidak diberikan.');
    const subscription = await registration.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: urlBase64ToUint8Array(vapidPublicKey) });
    const payload = subscription.toJSON();
    payload.contentEncoding = (PushManager.supportedContentEncodings || ['aes128gcm'])[0];
    payload.device_name = navigator.userAgent;
    const response = await fetch('/push-subscriptions', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body: JSON.stringify(payload) });
    if (!response.ok) throw new Error('Perangkat gagal disimpan untuk notifikasi.');
    button.textContent = 'Notifikasi aktif';
    button.classList.add('opacity-70');
}

document.querySelector('[data-enable-push]')?.addEventListener('click', async (event) => {
    try { await enablePush(event.currentTarget); }
    catch (error) { alert(error.message); event.currentTarget.disabled = false; }
});

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        if (!input) return;
        const hidden = input.type === 'password';
        input.type = hidden ? 'text' : 'password';
        button.setAttribute('aria-label', hidden ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');
        button.setAttribute('aria-pressed', hidden ? 'true' : 'false');
    });
});
