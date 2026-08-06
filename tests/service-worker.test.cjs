const assert = require('node:assert/strict');
const fs = require('node:fs');
const test = require('node:test');
const vm = require('node:vm');

function loadServiceWorker(fetchImplementation) {
    const listeners = {};
    const context = {
        Response,
        caches: {
            match: async () => undefined,
        },
        console,
        fetch: fetchImplementation,
        setTimeout,
        self: {
            addEventListener(type, listener) {
                listeners[type] = listener;
            },
            clients: {
                claim: async () => undefined,
            },
            skipWaiting: async () => undefined,
        },
    };

    vm.runInNewContext(
        fs.readFileSync('public/service-worker.js', 'utf8'),
        context,
    );

    return listeners;
}

test('navigation retries once when the first network request fails transiently', async () => {
    let attempts = 0;
    const onlineResponse = new Response('<html>Dashboard</html>', { status: 200 });
    const listeners = loadServiceWorker(async () => {
        attempts += 1;

        if (attempts === 1) {
            throw new TypeError('temporary network failure');
        }

        return onlineResponse;
    });

    let responsePromise;
    listeners.fetch({
        request: { method: 'GET', mode: 'navigate', url: 'https://oresamsub.com/dashboard' },
        respondWith(promise) {
            responsePromise = promise;
        },
    });

    const response = await responsePromise;

    assert.equal(attempts, 2);
    assert.equal(response.status, 200);
    assert.equal(await response.text(), '<html>Dashboard</html>');
});

test('non-navigation requests are not intercepted by the service worker', () => {
    const listeners = loadServiceWorker(async () => new Response('asset'));
    let intercepted = false;

    listeners.fetch({
        request: { method: 'GET', mode: 'cors', url: 'https://oresamsub.com/api/wallet' },
        respondWith() {
            intercepted = true;
        },
    });

    assert.equal(intercepted, false);
});

test('every registration requests the current service worker version without HTTP cache', () => {
    const registrationSources = [
        fs.readFileSync('resources/js/Components/PwaInstallPopup.jsx', 'utf8'),
        fs.readFileSync('resources/views/oresamsub/layouts/app.blade.php', 'utf8'),
    ];

    for (const source of registrationSources) {
        assert.match(source, /register\("\/service-worker\.js\?v=20260806-1", \{/);
        assert.match(source, /updateViaCache: "none"/);
        assert.match(source, /registration\.update\(\)/);
    }
});
