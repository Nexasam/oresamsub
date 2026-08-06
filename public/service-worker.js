self.addEventListener("install", (event) => {
    console.log("Service Worker installing.");
    self.skipWaiting();
  });
  
self.addEventListener("activate", (event) => {
    event.waitUntil(self.clients.claim());
  });

const NAVIGATION_RETRY_DELAY_MS = 350;

const wait = (milliseconds) =>
  new Promise((resolve) => setTimeout(resolve, milliseconds));

const fetchNavigation = async (request) => {
  try {
    return await fetch(request);
  } catch (firstError) {
    await wait(NAVIGATION_RETRY_DELAY_MS);

    try {
      return await fetch(request);
    } catch (secondError) {
      const cachedResponse = await caches.match(request);

      if (cachedResponse) {
        return cachedResponse;
      }

      return new Response(
        "We could not reach OresamSub right now. Please try again in a moment.",
        {
          status: 503,
          headers: { "Content-Type": "text/plain; charset=utf-8" },
        },
      );
    }
  }
};

self.addEventListener("fetch", (event) => {
    if (event.request.method !== "GET" || event.request.mode !== "navigate") {
      return;
    }

    event.respondWith(fetchNavigation(event.request));
});
  
