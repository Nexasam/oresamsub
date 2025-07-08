<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AdPilot - Facebook Ads SaaS</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-white text-gray-800 font-sans">
  <!-- Header -->
  <header class="w-full py-6 px-4 shadow-md flex justify-between items-center">
    <h1 class="text-xl font-bold">AdPilot</h1>
    <nav class="space-x-4">
      <a href="#features" class="hover:underline">Features</a>
      <a href="#pricing" class="hover:underline">Pricing</a>
      <a href="#contact" class="hover:underline">Contact</a>
      <a href="#login" class="hover:underline">Login</a>
    </nav>
  </header>

  <!-- Hero Section -->
  <section class="py-20 px-4 text-center bg-gray-50">
    <h2 class="text-4xl font-bold mb-4">Launch & Manage Facebook Ads With Ease</h2>
    <p class="mb-6 text-gray-600 max-w-xl mx-auto">
      AdPilot helps businesses run effective Facebook ad campaigns in minutes—right from a single dashboard.
    </p>
    <button class="px-6 py-3 text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
      Get Started Free
    </button>
  </section>

  <!-- Features Section -->
  <section id="features" class="py-16 px-4 bg-white">
    <h3 class="text-2xl font-semibold text-center mb-12">Key Features</h3>
    <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
      <div class="p-6 border rounded-lg shadow-sm">
        <h4 class="text-lg font-bold mb-2">Campaign Builder</h4>
        <p class="text-sm text-gray-600">Set up and launch ads in just a few clicks.</p>
      </div>
      <div class="p-6 border rounded-lg shadow-sm">
        <h4 class="text-lg font-bold mb-2">Ad Performance Analytics</h4>
        <p class="text-sm text-gray-600">Track clicks, conversions, and ROI easily.</p>
      </div>
      <div class="p-6 border rounded-lg shadow-sm">
        <h4 class="text-lg font-bold mb-2">Client Access</h4>
        <p class="text-sm text-gray-600">Let your clients monitor their ad performance.</p>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section id="pricing" class="py-16 px-4 bg-gray-50">
    <h3 class="text-2xl font-semibold text-center mb-12">Simple Pricing</h3>
    <div class="max-w-4xl mx-auto grid md:grid-cols-3 gap-8">
      <div class="border rounded-lg p-6 text-center shadow-sm">
        <h4 class="text-lg font-bold mb-2">Starter</h4>
        <p class="text-2xl font-bold mb-4">₦5,000/mo</p>
        <p class="text-sm text-gray-600 mb-4">For small businesses running basic campaigns.</p>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Choose Plan</button>
      </div>
      <div class="border rounded-lg p-6 text-center shadow-lg bg-white">
        <h4 class="text-lg font-bold mb-2">Pro</h4>
        <p class="text-2xl font-bold mb-4">₦15,000/mo</p>
        <p class="text-sm text-gray-600 mb-4">For growing businesses and agencies.</p>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Choose Plan</button>
      </div>
      <div class="border rounded-lg p-6 text-center shadow-sm">
        <h4 class="text-lg font-bold mb-2">Enterprise</h4>
        <p class="text-2xl font-bold mb-4">Contact Us</p>
        <p class="text-sm text-gray-600 mb-4">Custom plan for larger organizations.</p>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Contact Sales</button>
      </div>
    </div>
  </section>

  <!-- Login Section -->
  <section id="login" class="py-16 px-4 max-w-md mx-auto">
    <h3 class="text-2xl font-semibold text-center mb-6">Login</h3>
    <form class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1" for="email">Email</label>
        <input type="email" id="email" class="w-full px-4 py-2 border rounded-md" placeholder="you@example.com">
      </div>
      <div>
        <label class="block text-sm font-medium mb-1" for="password">Password</label>
        <input type="password" id="password" class="w-full px-4 py-2 border rounded-md" placeholder="********">
      </div>
      <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Login</button>
    </form>
  </section>

  <!-- Footer -->
  <footer class="py-6 px-4 text-center bg-gray-100 mt-10 text-sm text-gray-500">
    &copy; <span id="year"></span> AdPilot. All rights reserved.
  </footer>

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
