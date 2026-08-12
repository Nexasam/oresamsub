self.addEventListener("install", (event) => {
    console.log("Service Worker installing.");
    self.skipWaiting();
  });
  
self.addEventListener("activate", (event) => {
    event.waitUntil(self.clients.claim());
  });
