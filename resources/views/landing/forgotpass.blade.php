<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password</title>

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
    }
  </script>

  <!-- AlpineJS CDN -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- Google Fonts: Outfit -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Background Gradient -->
  <style>
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at 30% 30%, rgba(99, 102, 241, 0.08), transparent 60%),
                  radial-gradient(circle at 70% 80%, rgba(16, 185, 129, 0.08), transparent 60%);
      z-index: -1;
    }
  </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white font-sans transition-colors duration-300">

  <!-- Dark Mode Toggle -->
  <div class="absolute top-5 right-5">
    <button @click="darkMode = !darkMode" class="bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-white p-2 rounded-full shadow hover:scale-105 transition-all">
      <span x-show="!darkMode">🌙</span>
      <span x-show="darkMode">☀️</span>
    </button>
  </div>

  <!-- Forgot Password Container -->
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
      <div class="px-8 py-10">

        <!-- Heading -->
        <div class="text-center mb-6">
          <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400">Forgot Password</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Enter your email to receive a password reset link</p>
        </div>

        <!-- Forgot Password Form -->
        <form action="#" method="POST" class="space-y-5">
          <div>
            <label class="block mb-1 text-sm font-medium">Email Address</label>
            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all">Send Reset Link</button>
        </form>

        <!-- Footer -->
        <div class="text-center mt-6 text-sm text-gray-500 dark:text-gray-400">
          Remembered your password?
          <a href="login.html" class="text-blue-600 dark:text-blue-400 hover:underline">Login</a>
        </div>

      </div>
    </div>
  </div>

</body>
</html>
