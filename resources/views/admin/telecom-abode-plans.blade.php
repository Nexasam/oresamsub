<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Telecom Abode Data Plans</title>
    <style>
        :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, sans-serif; color: #162033; background: #f3f6fb; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px 18px; }
        main { width: min(1100px, 100%); margin: 0 auto; }
        header { display: flex; align-items: end; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        h1 { margin: 0 0 6px; font-size: clamp(1.65rem, 4vw, 2.5rem); }
        p { margin: 0; color: #657086; }
        a { color: #225bd6; font-weight: 700; text-decoration: none; }
        .panel { overflow: hidden; border: 1px solid #dce3ef; border-radius: 16px; background: white; box-shadow: 0 12px 35px rgba(22, 32, 51, .08); }
        .panel-label { padding: 14px 18px; border-bottom: 1px solid #e8edf5; color: #536079; font-size: .82rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        pre { overflow: auto; max-height: 72vh; margin: 0; padding: 22px; color: #d8e4ff; background: #111827; font: 14px/1.65 ui-monospace, SFMono-Regular, Menlo, monospace; white-space: pre-wrap; overflow-wrap: anywhere; }
        .error { padding: 22px; border-left: 5px solid #dc3545; color: #851f2b; background: #fff5f6; font-weight: 650; }
        @media (max-width: 640px) { header { align-items: start; flex-direction: column; } body { padding: 22px 12px; } }
    </style>
</head>
<body>
<main>
    <header>
        <div>
            <h1>Telecom Abode Data Plans</h1>
            <p>Live response from the production plans API.</p>
        </div>
        <a href="{{ url()->current() }}">Refresh plans</a>
    </header>

    <section class="panel">
        @if ($error)
            <div class="error">{{ $error }}</div>
        @else
            <div class="panel-label">API response</div>
            <pre>{{ json_encode($plans, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        @endif
    </section>
</main>
</body>
</html>
