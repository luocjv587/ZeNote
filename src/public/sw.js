const CACHE_NAME = 'zenote-v1';
const urlsToCache = [
  './',
  './index.php',
  './login.php',
  './manifest.json',
  './logo.svg',
  'https://cdn.tailwindcss.com',
  'https://cdn.quilljs.com/1.3.6/quill.snow.css',
  'https://cdn.quilljs.com/1.3.6/quill.js'
];

self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  // Only cache GET requests
  if (event.request.method !== 'GET') return;

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response;
        }
        return fetch(event.request).then(response => {
            // Don't cache if not valid
            if(!response || response.status !== 200 || response.type !== 'basic') {
                return response;
            }
            // Clone response to cache
            const responseToCache = response.clone();
            caches.open(CACHE_NAME)
                .then(cache => {
                    cache.put(event.request, responseToCache);
                });
            return response;
        });
      })
  );
});