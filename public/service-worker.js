self.addEventListener("install", (event) => {
    console.log("Service Worker installing.");
    self.skipWaiting();
  });
  
self.addEventListener("activate", (event) => {
    event.waitUntil(self.clients.claim());
  });

self.addEventListener("fetch", (event) => {
    if (event.request.method !== "GET") {
      return;
    }

    event.respondWith(
      fetch(event.request).catch(async () => {
        const cachedResponse = await caches.match(event.request);

        if (cachedResponse) {
          return cachedResponse;
        }

        if (event.request.mode === "navigate") {
          return new Response("You appear to be offline. Please reconnect and try again.", {
            status: 503,
            headers: { "Content-Type": "text/plain; charset=utf-8" },
          });
        }

        return new Response(null, { status: 503, statusText: "Offline" });
      })
    );
});
  
