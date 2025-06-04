<!DOCTYPE html>
<html lang="en" dir="ltr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{env('APP_NAME')}} - Enjoy data at the best rate </title>
    <meta name="description" content="A Tailwind CSS admin template is a pre-designed web page for an admin dashboard. Optimizing it for SEO includes using meta descriptions and ensuring it's responsive and fast-loading.">
    <meta name="keywords" content="analytics dashboard,jobs dashboard,crm dashboard examples,personal dashboard,sales dashboard sample,best crm dashboard,crypto dashboard template,sales analytics dashboard,stocks dashboard,hrm dashboard,ecommerce admin panel template,sales admin dashboard,admin panel for ecommerce website,website template ecommerce,template dashboard,course dashboard,template ecommerce website">

    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/brand-logos/favicon.ico">

    <!-- Style Css -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Simplebar Css -->
    <link rel="stylesheet" href="../assets/libs/simplebar/simplebar.min.css">

    <!-- Color Picker Css -->
    <link rel="stylesheet" href="../assets/libs/@simonwep/pickr/themes/nano.min.css">

</head>

<body class="authentication-page !h-full">

        <!-- ========== MAIN CONTENT ========== -->
        <main id="content" class="mx-auto">
        <canvas class="error-basic-background"></canvas>
            <div class="text-center py-10 px-4 sm:px-6 lg:px-8">
                {{-- <h1 class="block font-bold text-primary text-9xl dark:text-primary">4</h1> --}}
                <p class="mt-3 text-2xl font-bold text-gray-800 dark:text-gray-900">Oops, Session Expired.</p>
                <p class="text-gray-600 dark:text-white/70">Sorry, it appears like your session expired. Please use the button below to clear session.</p>
                <div class="mt-5 flex flex-col justify-center items-center gap-2 sm:flex-row sm:gap-3">
                    <a class="w-full sm:w-auto inline-flex justify-center items-center gap-x-3 text-center bg-primary hover:bg-primary border border-transparent text-white text-sm font-medium rounded-sm focus:outline-none focus:ring-0 focus:ring-primary focus:ring-offset-0 focus:ring-offset-white transition py-2 px-3 dark:focus:ring-offset-white/10"
                        href="{{route('artisan.clear_cache')}}">
                        {{-- <i class="ri-arrow-line"></i> --}}
                        Clear session
                    </a>
                    {{-- <a class="w-full sm:w-auto inline-flex justify-center items-center gap-x-3 text-center bg-primary hover:bg-primary border border-transparent text-white text-sm font-medium rounded-sm focus:outline-none focus:ring-0 focus:ring-primary focus:ring-offset-0 focus:ring-offset-white transition py-2 px-3 dark:focus:ring-offset-white/10"
                        href="{{route('dashboard')}}">
                        <i class="ri-arrow-left-line"></i>
                        Get Back to Home
                    </a> --}}
                </div>
            </div>
        </main>
        <!-- ========== END MAIN CONTENT ========== -->

    <!-- popperjs -->
    <script src="../assets/libs/@popperjs/core/umd/popper.min.js"></script>

    <!-- Custom-Switcher JS -->
    <script src="../assets/js/custom-switcher.js"></script>

    <!-- Preline JS -->
    <script src="../assets/libs/preline/preline.js"></script>


    <!-- Particles JS -->
    <script src="../assets/libs/particlesjs/particles.min.js"></script>

    <!-- Internal 401-Error JS -->
    <script src="../assets/js/401-error.js"></script>

</body>

</html>