const CACHE = 'invocely-v2';
const OFFLINE_URL = '/offline.html';

// Only static, versioned, public assets are precached. Never precache '/'
// or any authenticated HTML: it varies by auth state and would leak one
// user's redirect/page to another profile on a shared device.
const PRECACHE = [
  '/offline.html',
  '/favicon.ico',
  '/manifest.webmanifest',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/icons/icon-maskable-512.png',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE).then((cache) => cache.addAll(PRECACHE))
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const req = event.request;

  // Only handle same-origin GET; pass through POST/PUT/DELETE and
  // cross-origin requests untouched.
  if (req.method !== 'GET') return;
  let url;
  try {
    url = new URL(req.url);
  } catch (e) {
    return;
  }
  if (url.origin !== self.location.origin) return;

  // API and any authenticated data endpoint: network-only. NEVER cache
  // private JSON — a cached response keyed by URL alone could be served
  // offline to a different account on a shared device.
  if (url.pathname.startsWith('/api/')) {
    return;
  }

  // Page navigations: network first, offline fallback.
  // Authenticated HTML is never written to cache, so a logout / account
  // switch can never resurface the previous account's pages offline.
  if (req.mode === 'navigate') {
    event.respondWith(
      fetch(req)
        .catch(() => caches.match(OFFLINE_URL))
    );
    return;
  }

  // Static assets: cache first, then network
  event.respondWith(
    caches.match(req).then((cached) => {
      if (cached) return cached;
      return fetch(req).then((resp) => {
        if (resp.status === 200 && resp.type === 'basic') {
          const copy = resp.clone();
          caches.open(CACHE).then((c) => c.put(req, copy));
        }
        return resp;
      });
    })
  );
});

// Push notifications
self.addEventListener('push', (event) => {
  if (!event.data) return;
  const data = event.data.json();
  const title = data.title || 'Invocely';
  const options = {
    body: data.body || '',
    icon: '/icons/icon-192.png',
    badge: '/icons/icon-192.png',
    data: { url: data.url || '/dashboard' },
  };
  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const url = event.notification.data?.url || '/dashboard';
  event.waitUntil(self.clients.openWindow(url));
});
