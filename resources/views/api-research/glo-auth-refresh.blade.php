<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Glo auth and refresh research</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 760px; margin: 40px auto; padding: 0 18px; color: #17212b; }
        form, pre { border: 1px solid #d9e0e7; border-radius: 12px; padding: 20px; background: #f8fafc; }
        label { display: block; margin: 14px 0 6px; font-weight: 650; }
        input { width: 100%; box-sizing: border-box; padding: 10px; }
        button { margin-top: 18px; padding: 11px 18px; cursor: pointer; }
        .warning { color: #8a2c0d; }
        .error { color: #b42318; }
    </style>
</head>
<body>
    <h1>Glo login → refresh probe</h1>
    <p class="warning">Local research only. Credentials are used for this request and are never redisplayed or persisted by this probe.</p>

    @if (isset($errors) && $errors->any())
        <ul class="error">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    @endif

    <form method="post" action="/api-research/glo/auth-refresh" autocomplete="off">
        @csrf
        <label for="phone_number">Glo number</label>
        <input id="phone_number" name="phone_number" inputmode="tel" required autocomplete="off">

        <label for="glo_pin">Existing Glo Cafe PIN/password</label>
        <input id="glo_pin" name="glo_pin" type="password" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" required autocomplete="new-password">

        <button type="submit">Login once and test refresh</button>
    </form>

    @isset($result)
        <h2>Sanitized result</h2>
        <pre>{{ json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    @endisset
</body>
</html>
