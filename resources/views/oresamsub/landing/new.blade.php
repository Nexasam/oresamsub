<!DOCTYPE html>
<html lang="en" x-data="app()" x-init="init()" :class="{ 'dark': darkMode }">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>AmazingDataHub — Fast VTU, Data, TV & Power</title>
  <meta name="description" content="Buy data, airtime, TV and electricity instantly at wholesale rates. Built for agents, SMEs and platforms that need speed, reliability and clear earnings." />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- TailwindCSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
          boxShadow: {
            soft: '0 10px 30px -12px rgba(0,0,0,0.16)'
          }
        }
      }
    }
  </script>
  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Favicon placeholder -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='24' fill='%2306b6d4'/><text x='50' y='58' font-size='52' text-anchor='middle' fill='white' font-family='Arial'>A</text></svg>">
  <style>
    html { scroll-behavior: smooth; }
    .container { max-width: 1200px; }
  </style>
</head>
<body class="bg-gradient-to-b from-gray-50 to-white dark:from-gray-950 dark:to-black text-gray-900 dark:text-white antialiased">
  <!-- Top Bar -->
  <div class="hidden md:block bg-gradient-to-r from-emerald-600 to-teal-500 text-white text-sm">
    <div class="container mx-auto px-4 py-2 flex items-center justify-between">
      <p class="opacity-90">Wholesale VTU • 99.98% uptime • Instant vends</p>
      <a href="#pricing" class="font-semibold underline/30 hover:underline">See pricing</a>
    </div>
  </div>

  <!-- Header -->
  <header class="sticky top-0 z-50 border-b border-gray-200/60 dark:border-gray-800/60 bg-white/80 dark:bg-black/60 backdrop-blur">
    <div class="container mx-auto px-4 h-16 flex items-center justify-between">
      <a href="#" class="flex items-center gap-2 font-extrabold text-lg tracking-tight">
        <span class="inline-block h-7 w-7 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400"></span>
        AmazingDataHub
      </a>
      <!-- Desktop Nav -->
      <nav class="hidden md:flex items-center gap-6 text-sm text-gray-700 dark:text-gray-300">
        <a href="#features" class="hover:text-black dark:hover:text-white">Features</a>
        <a href="#how" class="hover:text-black dark:hover:text-white">How it works</a>
        <a href="#pricing" class="hover:text-black dark:hover:text-white">Pricing</a>
        <a href="#faqs" class="hover:text-black dark:hover:text-white">FAQs</a>
      </nav>
      <div class="flex items-center gap-2">
        <button @click="toggleDark()" class="rounded-xl px-3 py-2 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-900" :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
          <span x-show="!darkMode">🌙</span>
          <span x-show="darkMode">☀️</span>
        </button>
        <a href="#login" class="hidden sm:inline-flex rounded-xl px-3 py-2 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-900">Login</a>
        <a href="#register" class="inline-flex items-center gap-2 rounded-xl bg-black text-white dark:bg-white dark:text-black px-4 py-2 text-sm font-semibold">Create free account →</a>
        <button class="md:hidden rounded-xl p-2 hover:bg-gray-100 dark:hover:bg-gray-900" @click="mobileOpen = !mobileOpen" aria-label="Open menu">☰</button>
      </div>
    </div>
    <!-- Mobile Menu -->
    <div x-show="mobileOpen" x-transition.origin.top.left class="md:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-black">
      <div class="px-4 py-3 space-y-2 text-sm">
        <a href="#features" class="block py-2">Features</a>
        <a href="#how" class="block py-2">How it works</a>
        <a href="#pricing" class="block py-2">Pricing</a>
        <a href="#faqs" class="block py-2">FAQs</a>
        <a href="#register" class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-black text-white dark:bg-white dark:text-black px-4 py-2 font-semibold">Create free account</a>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="relative overflow-hidden">
    <div class="container mx-auto px-4 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
      <div>
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/40 bg-emerald-50 text-emerald-700 dark:text-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800 px-3 py-1 text-xs font-medium">No.1 VTU + Utilities Platform</span>
        <h1 class="mt-4 text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight">
          Buy data, airtime & pay bills
          <span class="block bg-gradient-to-r from-emerald-600 to-teal-400 bg-clip-text text-transparent">instantly at wholesale rates</span>
        </h1>
        <p class="mt-4 text-gray-600 dark:text-gray-300 max-w-xl">
          Serve thousands with fast top‑ups for MTN, Airtel, Glo, 9mobile, DStv/GOtv/Startimes and electricity. Earn from referrals with a transparent commission engine.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          <a href="#register" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 px-5 py-3 text-white text-sm font-semibold">
            Start free →
          </a>
          <a href="#demo" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-300 dark:border-gray-800 px-5 py-3 text-sm font-semibold">
            See live demo
          </a>
        </div>
        <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 text-center shadow-soft"><div class="text-3xl font-extrabold tracking-tight">99.98%</div><div class="mt-2 text-sm text-gray-600 dark:text-gray-300">Uptime SLA</div></div>
          <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 text-center shadow-soft"><div class="text-3xl font-extrabold tracking-tight">&lt;2s</div><div class="mt-2 text-sm text-gray-600 dark:text-gray-300">Avg vend time</div></div>
          <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 text-center shadow-soft"><div class="text-3xl font-extrabold tracking-tight">₦0</div><div class="mt-2 text-sm text-gray-600 dark:text-gray-300">Setup fee</div></div>
          <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 text-center shadow-soft"><div class="text-3xl font-extrabold tracking-tight">&gt;250k</div><div class="mt-2 text-sm text-gray-600 dark:text-gray-300">Successful vends</div></div>
        </div>
      </div>

      <!-- Mockup Card -->
      <div class="relative">
        <div class="relative rounded-3xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-2xl">
          <div class="rounded-2xl bg-gradient-to-tr from-gray-900 to-gray-700 text-white p-6">
            <div class="text-sm opacity-90">Quick Vend</div>
            <div class="mt-2 grid grid-cols-2 gap-3 text-sm">
              <div class="rounded-xl bg-white/5 p-3">
                <div class="text-xs opacity-80">Network</div>
                <div class="mt-1 font-semibold">MTN SME</div>
              </div>
              <div class="rounded-xl bg-white/5 p-3">
                <div class="text-xs opacity-80">Amount</div>
                <div class="mt-1 font-semibold">3GB</div>
              </div>
              <div class="col-span-2 rounded-xl bg-white/5 p-3">
                <div class="text-xs opacity-80">Phone</div>
                <div class="mt-1 font-semibold">0803 123 4567</div>
              </div>
            </div>
            <button class="mt-4 w-full rounded-xl bg-emerald-500 py-2.5 text-sm font-semibold">Vend now</button>
          </div>
        </div>
        <div class="absolute -right-6 -bottom-6 hidden sm:block">
          <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-xl">
            <div class="text-xs text-gray-500">Today’s performance</div>
            <div class="mt-2 flex items-center gap-4">
              <div class="h-10 w-10 rounded-xl bg-gray-100 dark:bg-gray-800 grid place-items-center">📊</div>
              <div>
                <div class="text-sm font-bold">1,284 vends</div>
                <div class="text-xs text-gray-500">98.7% success</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Brand strip -->
  <section>
    <div class="container mx-auto px-4 py-10">
      <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-4 items-center opacity-80">
        <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center text-xs">MTN</div>
        <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center text-xs">Airtel</div>
        <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center text-xs">Glo</div>
        <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center text-xs">9mobile</div>
        <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center text-xs">DStv</div>
        <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center text-xs">GOtv</div>
        <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center text-xs">Startimes</div>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section id="features" class="py-16 lg:py-24">
    <div class="container mx-auto px-4">
      <div class="mb-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/40 bg-emerald-50 text-emerald-700 dark:text-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800 px-3 py-1 text-xs font-medium">Why switch</span>
        <h2 class="mt-3 text-3xl lg:text-4xl font-bold tracking-tight">Everything you need to sell digital services at scale</h2>
        <p class="mt-3 text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Built for agents, SMEs and platforms that need speed, reliability and clear earnings.</p>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition">
          <div class="rounded-xl p-2.5 bg-gray-100 dark:bg-gray-800 inline-block">⚡️</div>
          <h3 class="mt-3 text-base font-semibold">Blazing fast vends</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Top-ups complete in seconds with intelligent retries and queueing.</p>
        </div>
        <div class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition">
          <div class="rounded-xl p-2.5 bg-gray-100 dark:bg-gray-800 inline-block">🛡️</div>
          <h3 class="mt-3 text-base font-semibold">Bank-grade security</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">2FA, device locks and AES‑256 encryption keep your account safe.</p>
        </div>
        <div class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition">
          <div class="rounded-xl p-2.5 bg-gray-100 dark:bg-gray-800 inline-block">📱</div>
          <h3 class="mt-3 text-base font-semibold">Works on any device</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Responsive PWA with offline-ready screens and homescreen install.</p>
        </div>
        <div class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition">
          <div class="rounded-xl p-2.5 bg-gray-100 dark:bg-gray-800 inline-block">💰</div>
          <h3 class="mt-3 text-base font-semibold">Transparent commissions</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Tiered referral engine with real‑time tracking and instant payouts.</p>
        </div>
        <div class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition">
          <div class="rounded-xl p-2.5 bg-gray-100 dark:bg-gray-800 inline-block">💳</div>
          <h3 class="mt-3 text-base font-semibold">Multiple payment rails</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Cards, bank transfer, wallet, USSD and virtual accounts supported.</p>
        </div>
        <div class="group rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm hover:shadow-md transition">
          <div class="rounded-xl p-2.5 bg-gray-100 dark:bg-gray-800 inline-block">📈</div>
          <h3 class="mt-3 text-base font-semibold">Analytics that matter</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">See revenue, success rate and failure reasons at a glance.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- How it works -->
  <section id="how" class="py-16 bg-gray-50 dark:bg-gray-950/50">
    <div class="container mx-auto px-4">
      <div class="mb-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/40 bg-emerald-50 text-emerald-700 dark:text-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800 px-3 py-1 text-xs font-medium">3 steps</span>
        <h2 class="mt-3 text-3xl lg:text-4xl font-bold tracking-tight">Start earning in minutes</h2>
      </div>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
          <div class="text-2xl">👤</div>
          <h3 class="mt-3 font-semibold">Create your free account</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Verify your phone/email and secure your wallet with a PIN.</p>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
          <div class="text-2xl">🔌</div>
          <h3 class="mt-3 font-semibold">Fund wallet & connect</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Fund via card/transfer, then pick a service: data, airtime, TV or power.</p>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
          <div class="text-2xl">📈</div>
          <h3 class="mt-3 font-semibold">Vend & grow</h3>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Track commissions from downlines in real time. Withdraw anytime.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing -->
  <section id="pricing" class="py-16 lg:py-24">
    <div class="container mx-auto px-4">
      <div class="mb-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/40 bg-emerald-50 text-emerald-700 dark:text-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800 px-3 py-1 text-xs font-medium">Fair pricing</span>
        <h2 class="mt-3 text-3xl lg:text-4xl font-bold tracking-tight">Wholesale rates that help you scale</h2>
        <p class="mt-3 text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">No hidden fees. Pay as you go. Earn more as your volume grows.</p>
      </div>
      <div class="grid md:grid-cols-3 gap-6">
        <!-- Starter -->
        <div class="flex flex-col rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm">
          <div class="flex items-baseline justify-between">
            <h3 class="text-lg font-bold">Starter</h3>
            <span class="rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200 px-2.5 py-1 text-xs">New</span>
          </div>
          <p class="mt-2 text-gray-600 dark:text-gray-300">Perfect for new agents</p>
          <div class="mt-5"><span class="text-4xl font-extrabold">₦0</span><span class="text-gray-500 dark:text-gray-400">/month</span></div>
          <ul class="mt-5 space-y-3 text-sm">
            <li class="flex gap-3">✅ Free account + wallet</li>
            <li class="flex gap-3">✅ All networks & services</li>
            <li class="flex gap-3">✅ Up to ₦200k/month volume</li>
            <li class="flex gap-3">✅ Basic analytics</li>
          </ul>
          <a href="#register" class="mt-6 inline-flex items-center justify-center gap-2 rounded-2xl bg-black text-white dark:bg-white dark:text-black px-4 py-3 text-sm font-semibold">Get Started →</a>
        </div>
        <!-- Pro -->
        <div class="flex flex-col rounded-2xl border-2 border-emerald-500 bg-white dark:bg-gray-900 p-6 shadow-soft relative">
          <span class="absolute -top-3 right-6 rounded-full bg-black text-white dark:bg-white dark:text-black px-3 py-1 text-xs font-semibold">Most popular</span>
          <div class="flex items-baseline justify-between">
            <h3 class="text-lg font-bold">Pro</h3>
          </div>
          <p class="mt-2 text-gray-600 dark:text-gray-300">Best for growing teams</p>
          <div class="mt-5"><span class="text-4xl font-extrabold">₦5,000</span><span class="text-gray-500 dark:text-gray-400">/month</span></div>
          <ul class="mt-5 space-y-3 text-sm">
            <li class="flex gap-3">✅ Lower data rates</li>
            <li class="flex gap-3">✅ Higher commissions</li>
            <li class="flex gap-3">✅ Priority vend queue</li>
            <li class="flex gap-3">✅ Monthly statements</li>
          </ul>
          <a href="#register" class="mt-6 inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-3 text-sm font-semibold">Upgrade to Pro →</a>
        </div>
        <!-- Business -->
        <div class="flex flex-col rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm">
          <div class="flex items-baseline justify-between">
            <h3 class="text-lg font-bold">Business</h3>
          </div>
          <p class="mt-2 text-gray-600 dark:text-gray-300">For power users & resellers</p>
          <div class="mt-5"><span class="text-4xl font-extrabold">₦10,000</span><span class="text-gray-500 dark:text-gray-400">/month</span></div>
          <ul class="mt-5 space-y-3 text-sm">
            <li class="flex gap-3">✅ Deepest discounts</li>
            <li class="flex gap-3">✅ Dedicated support</li>
            <li class="flex gap-3">✅ API access</li>
            <li class="flex gap-3">✅ Custom limits & SLAs</li>
          </ul>
          <a href="#register" class="mt-6 inline-flex items-center justify-center gap-2 rounded-2xl bg-black text-white dark:bg-white dark:text-black px-4 py-3 text-sm font-semibold">Talk to sales →</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="py-16 bg-gray-50 dark:bg-gray-950/50">
    <div class="container mx-auto px-4">
      <div class="mb-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/40 bg-emerald-50 text-emerald-700 dark:text-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800 px-3 py-1 text-xs font-medium">Loved by agents</span>
        <h2 class="mt-3 text-3xl lg:text-4xl font-bold tracking-tight">Don’t take our word for it</h2>
      </div>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm">
          <div class="flex gap-1" aria-hidden="true">⭐️⭐️⭐️⭐️⭐️</div>
          <p class="mt-4 text-sm text-gray-700 dark:text-gray-200">“Best vend success I’ve used. My customers stopped calling to complain.”</p>
          <div class="mt-4 text-xs text-gray-500">Blessing A. — POS Agent, Ibadan</div>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm">
          <div class="flex gap-1" aria-hidden="true">⭐️⭐️⭐️⭐️⭐️</div>
          <p class="mt-4 text-sm text-gray-700 dark:text-gray-200">“We onboarded 50+ resellers in 2 weeks thanks to the referral tools.”</p>
          <div class="mt-4 text-xs text-gray-500">Haruna M. — Distributor, Abuja</div>
        </div>
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm">
          <div class="flex gap-1" aria-hidden="true">⭐️⭐️⭐️⭐️⭐️</div>
          <p class="mt-4 text-sm text-gray-700 dark:text-gray-200">“Clean UI, fast payouts, excellent support. Highly recommended.”</p>
          <div class="mt-4 text-xs text-gray-500">Olamide K. — Agent Lead, Lagos</div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQs -->
  <section id="faqs" class="py-16">
    <div class="container mx-auto px-4">
      <div class="mb-10 text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/40 bg-emerald-50 text-emerald-700 dark:text-emerald-100 dark:bg-emerald-900/30 dark:border-emerald-800 px-3 py-1 text-xs font-medium">Help</span>
        <h2 class="mt-3 text-3xl lg:text-4xl font-bold tracking-tight">Frequently asked questions</h2>
      </div>
      <div class="max-w-3xl mx-auto divide-y divide-gray-200 dark:divide-gray-800">
        <details class="group py-4">
          <summary class="flex cursor-pointer list-none items-center justify-between text-left font-medium">How do I fund my wallet?<span class="transition group-open:rotate-45">＋</span></summary>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Use card, bank transfer, USSD or virtual account. Funds reflect instantly.</p>
        </details>
        <details class="group py-4">
          <summary class="flex cursor-pointer list-none items-center justify-between text-left font-medium">Do you offer APIs?<span class="transition group-open:rotate-45">＋</span></summary>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Yes. Business plan includes full API access and dedicated support.</p>
        </details>
        <details class="group py-4">
          <summary class="flex cursor-pointer list-none items-center justify-between text-left font-medium">What is the referral commission?<span class="transition group-open:rotate-45">＋</span></summary>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Tiered commissions that increase with volume. Track earnings in real time.</p>
        </details>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div class="rounded-3xl bg-gradient-to-r from-emerald-600 to-teal-500 text-white p-10 text-center shadow-soft">
        <h3 class="text-2xl md:text-3xl font-extrabold">Ready to vend at wholesale rates?</h3>
        <p class="mt-2 opacity-90">Create your free account and start in minutes. No setup fees.</p>
        <a href="#register" class="mt-6 inline-flex items-center justify-center rounded-2xl bg-white text-black px-6 py-3 font-semibold">Create free account →</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="border-t border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4 py-10 grid md:grid-cols-4 gap-8">
      <div>
        <div class="flex items-center gap-2 font-extrabold text-lg tracking-tight">
          <span class="inline-block h-7 w-7 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400"></span>
          AmazingDataHub
        </div>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Fast top‑ups for data, airtime, TV and electricity with transparent commissions.</p>
      </div>
      <div>
        <h4 class="font-semibold">Product</h4>
        <ul class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300">
          <li><a href="#features" class="hover:underline">Features</a></li>
          <li><a href="#pricing" class="hover:underline">Pricing</a></li>
          <li><a href="#faqs" class="hover:underline">FAQs</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-semibold">Company</h4>
        <ul class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300">
          <li><a href="#" class="hover:underline">About</a></li>
          <li><a href="#" class="hover:underline">Blog</a></li>
          <li><a href="#" class="hover:underline">Careers</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-semibold">Get help</h4>
        <ul class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-300">
          <li>📧 support@amazingdatahub.com</li>
          <li>📞 +234 (0) 801 234 5678</li>
          <li>🌍 Lagos, Nigeria</li>
        </ul>
      </div>
    </div>
    <div class="border-t border-gray-200 dark:border-gray-800">
      <div class="container mx-auto px-4 py-6 text-xs text-gray-500 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p>© <span x-text="new Date().getFullYear()"></span> AmazingDataHub. All rights reserved.</p>
        <div class="space-x-4">
          <a href="#" class="hover:underline">Terms</a>
          <a href="#" class="hover:underline">Privacy</a>
          <a href="#" class="hover:underline">Security</a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    function app() {
      return {
        darkMode: false,
        mobileOpen: false,
        init() {
          this.darkMode = JSON.parse(localStorage.getItem('adh_dark') || 'false');
        },
        toggleDark() {
          this.darkMode = !this.darkMode;
          localStorage.setItem('adh_dark', JSON.stringify(this.darkMode));
        }
      }
    }
  </script>
</body>
</html>
