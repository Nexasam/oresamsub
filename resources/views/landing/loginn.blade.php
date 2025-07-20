<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Outfit', 'sans-serif']
          }
        }
      }
    };
  </script>

  <!-- AlpineJS CDN -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Google Font: Outfit -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white font-sans transition-colors duration-300">

  <!-- Toggle Button -->
  <div class="absolute top-5 right-5">
    <button @click="darkMode = !darkMode" class="bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-white p-2 rounded-full shadow hover:scale-105 transition-all">
      <span x-show="!darkMode">🌙</span>
      <span x-show="darkMode">☀️</span>
    </button>
  </div>

  <!-- Login Container -->
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
      <div class="px-8 py-10">

        <!-- Logo or Title -->
        <div class="text-center mb-6">
          <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400">Welcome Back</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Please sign in to continue</p>
        </div>

        <!-- Form -->
        <form action="#" method="POST" class="space-y-5">
          <div>
            <label class="block mb-1 text-sm font-medium">Email</label>
            <input type="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="you@example.com" required>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium">Password</label>
            <input type="password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="********" required>
          </div>
          <div class="flex items-center justify-between">
            <label class="inline-flex items-center text-sm text-gray-600 dark:text-gray-300">
              <input type="checkbox" class="form-checkbox text-blue-600 dark:text-blue-400">
              <span class="ml-2">Remember me</span>
            </label>
            <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Forgot password?</a>
          </div>
          <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all">Login</button>
        </form>

        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-500 dark:text-gray-400">
          Don't have an account? <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Register</a>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
