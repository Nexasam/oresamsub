<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"> --}}
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">



    <title inertia>{{ config('app.name', 'Dashboard') }}</title>


    {{-- <title>Test</title> --}}

    @viteReactRefresh
    @vite('resources/js/app.jsx')
    @inertiaHead

    {{-- Ziggy routes --}}
    @routes
</head>
<body class="antialiased font-sans">
    @inertia


 
    <script>
      (function() {
        try {
          const theme = localStorage.getItem('theme');
          const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
          if (theme === 'dark' || (!theme && prefersDark)) {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
        } catch(e) {
          console.error(e);
        }
      })();
    </script>
</body>
</html>
