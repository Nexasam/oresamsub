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

test('requests including page navigations are never intercepted by the service worker', () => {
    const listeners = loadServiceWorker(async () => new Response('asset'));

    assert.equal(listeners.fetch, undefined);
});

test('service worker does not contain a synthetic connection error response', () => {
    const source = fs.readFileSync('public/service-worker.js', 'utf8');

    assert.doesNotMatch(source, /We could not reach OresamSub right now/);
    assert.doesNotMatch(source, /respondWith/);
});

test('every registration requests the current service worker version without HTTP cache', () => {
    const registrationSources = [
        fs.readFileSync('resources/js/Components/PwaInstallPopup.jsx', 'utf8'),
        fs.readFileSync('resources/views/oresamsub/layouts/app.blade.php', 'utf8'),
    ];

    for (const source of registrationSources) {
        assert.match(source, /register\("\/service-worker\.js\?v=20260808-1", \{/);
        assert.match(source, /updateViaCache: "none"/);
        assert.match(source, /registration\.update\(\)/);
    }
});
