<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Airtel checkout research</title>
    <style>
        body{font:16px/1.45 system-ui,sans-serif;max-width:900px;margin:32px auto;padding:0 16px;color:#17202a}label{display:block;margin:12px 0 4px}input{box-sizing:border-box;width:100%;padding:9px}button{margin-top:18px;padding:10px 16px}pre{overflow:auto;background:#f3f5f7;padding:16px;border-radius:8px}.warning{background:#fff3cd;padding:12px;border-radius:8px}.danger{background:#fde2e2;border:1px solid #c0392b;padding:16px;border-radius:8px;margin-top:32px}.danger button{background:#a61b1b;color:#fff;border:0;border-radius:4px}.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}@media(max-width:700px){.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <h1>Airtel checkout research</h1>
    <p class="warning">Local-only authorized research. Credentials are used for one submission and are not stored or redisplayed. Always run payment-options first with the same fresh session.</p>
    @if ($errors->any())<pre>{{ implode("\n", $errors->all()) }}</pre>@endif
    <form method="post" autocomplete="off">
        @csrf
        <label>Session token<input type="password" name="session_token" required></label>
        <label>UID key<input type="password" name="uid_key" required></label>
        <label>Dynamic token<input type="password" name="dynamic_token" required></label>
        <label>Device ID (16 hex characters)<input name="device_id" value="{{ old('device_id') }}" required></label>
        <label>Subscriber ID<input name="subscriber_id" value="{{ old('subscriber_id') }}" required></label>
        <label>Beneficiary (blank = subscriber)<input name="purchase_beneficiary" value="{{ old('purchase_beneficiary') }}"></label>
        <label>Amount<input name="purchase_amount" type="number" step="0.01" value="{{ old('purchase_amount', '75') }}" required></label>
        <label>Selected pack ID / product code<input name="purchase_product_code" value="{{ old('purchase_product_code', 'Daily_Plan_75') }}" required></label>
        <label>IMEI (optional)<input name="device_imei" value="{{ old('device_imei') }}"></label>
        <label>MAC address (optional)<input name="device_mac_address" value="{{ old('device_mac_address') }}"></label>
        <button type="submit">Call payment options</button>
    </form>
    <section class="danger">
        <h2>Execute one own-line purchase</h2>
        <p>This makes one ₦75 airtime-balance purchase. It never retries automatically. Re-enter the fresh runtime credentials; they are intentionally not copied from the form above.</p>
        <form method="post" action="/api-research/airtel/purchase" autocomplete="off">
            @csrf
            <div class="grid">
                <label>Session token<input type="password" name="session_token" required></label>
                <label>UID key<input type="password" name="uid_key" required></label>
                <label>Dynamic token<input type="password" name="dynamic_token" required></label>
                <label>Device ID<input name="device_id" required pattern="[0-9A-Fa-f]{16}"></label>
                <label>Authenticated subscriber ID<input name="subscriber_id" required></label>
                <label>IMEI (optional)<input name="device_imei"></label>
                <label>MAC address (optional)<input name="device_mac_address"></label>
            </div>
            <input type="hidden" name="purchase_amount" value="75">
            <input type="hidden" name="purchase_product_code" value="Daily_Plan_75">
            <input type="hidden" name="purchase_bundle_name" value="Daily Plan 75">
            <input type="hidden" name="purchase_validity" value="1 Day">
            <label>Type <strong>PURCHASE 75 NGN</strong> to authorize one charge<input name="purchase_confirmation" required autocomplete="off"></label>
            <button type="submit">Charge ₦75 once</button>
        </form>
    </section>
    @isset($result)
        <h2>Result</h2>
        <pre>{{ json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    @endisset
</body>
</html>
