<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>2FA Verification</title>

  <!-- Tailwind CSS -->
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

  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Outfit', sans-serif; }

    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: radial-gradient(circle at 30% 30%, rgba(99, 102, 241, 0.08), transparent 60%),
                  radial-gradient(circle at 70% 80%, rgba(16, 185, 129, 0.08), transparent 60%);
      z-index: -1;
    }
  </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-300" data-theme="light">

  <!-- Dark Mode Toggle -->
  <div class="absolute top-5 right-5">
    <button id="toggle-dark" class="bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-white p-2 rounded-full shadow hover:scale-105 transition-all">
      <span id="toggle-icon">🌙</span>
    </button>
  </div>

  <!-- 2FA Container -->
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
      <div class="px-8 py-10">
        <div class="text-center mb-6">
          <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400">Two-Factor Authentication</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Enter the 6-digit code sent to your device</p>
        </div>

        <!-- 2FA Form -->
        <form id="two-fa-form" class="space-y-6" method="POST" action="#">
          <div class="flex justify-center gap-3">
            <!-- 6 inputs -->
            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input type="text" maxlength="1" class="code-input w-12 h-12 text-center text-xl border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>

          <button type="submit" id="verify-btn" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all flex justify-center items-center gap-2">
            <span class="btn-text">Verify Code</span>
            <svg class="hidden animate-spin h-5 w-5 text-white" id="spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
          </button>
        </form>

        <div class="text-center mt-6 text-sm text-gray-500 dark:text-gray-400">
          Didn't receive the code? <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Resend</a>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery Logic -->
  <script>
    $(document).ready(function () {
      // Auto focus next input
      $('.code-input').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 1);
        const inputs = $('.code-input');
        const index = inputs.index(this);

        if (this.value !== '') {
          if (index < inputs.length - 1) {
            inputs.eq(index + 1).focus();
          } else {
            $('#verify-btn').trigger('click');
          }
        }
      });

      // Submit form with spinner
      $('#two-fa-form').on('submit', function (e) {
        e.preventDefault();
        $('#verify-btn .btn-text').addClass('hidden');
        $('#spinner').removeClass('hidden');

        // Simulate async verification
        setTimeout(function () {
          $('#spinner').addClass('hidden');
          $('#verify-btn .btn-text').removeClass('hidden');
          alert('2FA Verified ✅');
          // Optionally submit the form here
        }, 2000);
      });

      // Dark Mode Toggle
      const darkClass = 'dark';
      const toggleBtn = $('#toggle-dark');
      toggleBtn.on('click', function () {
        $('html').toggleClass(darkClass);
        const isDark = $('html').hasClass(darkClass);
        localStorage.setItem('darkMode', isDark);
        $('#toggle-icon').text(isDark ? '☀️' : '🌙');
      });

      // Set initial dark icon
      $('#toggle-icon').text($('html').hasClass('dark') ? '☀️' : '🌙');
    });
  </script>
</body>
</html>
