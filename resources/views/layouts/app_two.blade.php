<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Data App - {{ env('APP_NAME') }} </title>

    <meta name="description" content="Empowering Connections, One Byte at a Time - {{ env('APP_NAME') }}">
    <meta name="keywords" content="data purchase, mtn, airtel, utility bills, cable subscription">

    <!-- Quil Css -->    
    <link id="style" rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/quill/quill.snow.css') }}">

    
    <!-- Favicon -->
    {{-- <link rel="shortcut icon" href="../../assets/img/brand-logos/favicon.ico"> --}}
    {{-- <link rel="shortcut icon" href="{{ asset(env('APP_ASSETS_BASE_URL').'img/brand-logos/favicon.ico') }}"> --}}

    <!-- Main JS -->
    {{-- <script src="../../../assets/js/main.js"></script> --}}
    <script  src="{{ asset(env('APP_ASSETS_BASE_URL').'js/main.js') }}"></script>

    <!-- Style Css -->
    {{-- <link rel="stylesheet" href="../../../assets/css/style.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'css/style.css') }}">


    <!-- Simplebar Css -->
    {{-- <link rel="stylesheet" href="../../../assets/libs/simplebar/simplebar.min.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/simplebar/simplebar.min.css') }}">


    <!-- Color Picker Css -->
    {{-- <link rel="stylesheet" href="../../../assets/libs/@simonwep/pickr/themes/nano.min.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/@simonwep/pickr/themes/nano.min.css') }}">

<!-- Tabulator Css -->
{{-- <link rel="stylesheet" href="../../../assets/libs/tabulator-tables/css/tabulator.min.css"> --}}
<link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/tabulator-tables/css/tabulator.min.css') }}">


<!-- Choices Css -->
{{-- <link rel="stylesheet" href="../../../assets/libs/choices.js/public/assets/styles/choices.min.css"> --}}
<link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/choices.js/public/assets/styles/choices.min.css') }}">


{{-- 
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"/>
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet"> --}}



   <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"/> --}}


</head>

<body class="">

  <!-- ========== Switcher  ========== -->
  <div id="hs-overlay-switcher" class="hs-overlay hidden ti-offcanvas ti-offcanvas-right" tabindex="-1">
    <div class="ti-offcanvas-header z-10 relative">
      <h3 class="ti-offcanvas-title">
        Switcher
      </h3>
      <button type="button"
        class="ti-btn flex-shrink-0 p-0 transition-none text-gray-500 hover:text-gray-700 focus:ring-gray-400 focus:ring-offset-white dark:text-white/70 dark:hover:text-white/80 dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
        data-hs-overlay="#hs-overlay-switcher">
        <span class="sr-only">Close modal</span>
        <i class="ri-close-circle-line leading-none text-lg"></i>
      </button>
    </div>
    <div class="ti-offcanvas-body pt-0 border-b dark:border-white/10 z-10 relative">
      <div class="flex space-x-2 rtl:space-x-reverse" aria-label="Tabs" role="tablist">
        <button type="button"
          class="hs-tab-active:bg-secondary/10 w-full hs-tab-active:border-b-transparent hs-tab-active:text-secondary dark:hs-tab-active:bg-black/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-secondary -mb-px py-2 px-3 bg-white text-xs font-medium text-center border text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bgdark dark:border-white/10 dark:text-white/70 active"
          id="switcher-item-1" data-hs-tab="#switcher-1" aria-controls="switcher-1" role="tab">
          Theme Style
        </button>
        <button type="button"
          class="hs-tab-active:bg-secondary/10 w-full hs-tab-active:border-b-transparent hs-tab-active:text-secondary dark:hs-tab-active:bg-black/20 dark:hs-tab-active:border-b-white/10 dark:hs-tab-active:text-secondary -mb-px py-2 px-3 bg-white text-xs font-medium text-center border text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bgdark dark:border-white/10 dark:text-white/70 dark:hover:text-gray-300"
          id="switcher-item-2" data-hs-tab="#switcher-2" aria-controls="switcher-2" role="tab">
          Theme Colors
        </button>
      </div>
    </div>
    <div class="ti-offcanvas-body" id="switcher-body">
      <div id="switcher-1" role="tabpanel" aria-labelledby="switcher-item-1" class="space-y-6">
        <div class="space-y-6">
          <p class="switcher-style-head">Theme Color Mode:</p>
          <div class="grid grid-cols-3 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="theme-style" class="ti-form-radio" id="switcher-light-theme" checked>
              <label for="switcher-light-theme"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">Light</label>
            </div>
            <div class="flex">
              <input type="radio" name="theme-style" class="ti-form-radio" id="switcher-dark-theme">
              <label for="switcher-dark-theme"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">Dark</label>
            </div>
          </div>
        </div>
        <div class="space-y-6">
          <p class="switcher-style-head">Directions:</p>
          <div class="grid grid-cols-3 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="direction" class="ti-form-radio" id="switcher-ltr" checked>
              <label for="switcher-ltr" class="text-xs text-gray-500 ms-2 dark:text-white/70">LTR</label>
            </div>
            <div class="flex">
              <input type="radio" name="direction" class="ti-form-radio" id="switcher-rtl">
              <label for="switcher-rtl" class="text-xs text-gray-500 ms-2 dark:text-white/70">RTL</label>
            </div>
          </div>
        </div>
        <div class="space-y-6">
          <p class="switcher-style-head">Navigation Styles:</p>
          <div class="grid grid-cols-3 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="navigation-style" class="ti-form-radio" id="switcher-vertical" checked>
              <label for="switcher-vertical"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">Vertical</label>
            </div>
            <div class="flex">
              <input type="radio" name="navigation-style" class="ti-form-radio" id="switcher-horizontal">
              <label for="switcher-horizontal"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">Horizontal</label>
            </div>
          </div>
        </div>
        <div class="space-y-6">
          <p class="switcher-style-head">Navigation Menu Style:</p>
          <div class="grid grid-cols-2 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="navigation-data-menu-styles" class="ti-form-radio" id="switcher-menu-click"
                checked>
              <label for="switcher-menu-click" class="text-xs text-gray-500 ms-2 dark:text-white/70">Menu
                Click</label>
            </div>
            <div class="flex">
              <input type="radio" name="navigation-data-menu-styles" class="ti-form-radio" id="switcher-menu-hover">
              <label for="switcher-menu-hover" class="text-xs text-gray-500 ms-2 dark:text-white/70">Menu
                Hover</label>
            </div>
            <div class="flex">
              <input type="radio" name="navigation-data-menu-styles" class="ti-form-radio" id="switcher-icon-click">
              <label for="switcher-icon-click" class="text-xs text-gray-500 ms-2 dark:text-white/70">Icon
                Click</label>
            </div>
            <div class="flex">
              <input type="radio" name="navigation-data-menu-styles" class="ti-form-radio" id="switcher-icon-hover">
              <label for="switcher-icon-hover" class="text-xs text-gray-500 ms-2 dark:text-white/70">Icon
                Hover</label>
            </div>
          </div>
          <div class="px-4 text-secondary text-xs"><b class="me-2">Note:</b>Works same for both Vertical and
            Horizontal
          </div>
        </div>
        <div class="space-y-6">
          <p class="switcher-style-head">Page Styles:</p>
          <div class="grid grid-cols-3 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="data-page-styles" class="ti-form-radio" id="switcher-regular" checked>
              <label for="switcher-regular"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">Regular</label>
            </div>
            <div class="flex">
              <input type="radio" name="data-page-styles" class="ti-form-radio" id="switcher-classic">
              <label for="switcher-classic"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">Classic</label>
            </div>
          </div>
        </div>
        <div class="space-y-6">
          <p class="switcher-style-head">Layout Width Styles:</p>
          <div class="grid grid-cols-3 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="layout-width" class="ti-form-radio" id="switcher-full-width" checked>
              <label for="switcher-full-width"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">FullWidth</label>
            </div>
            <div class="flex">
              <input type="radio" name="layout-width" class="ti-form-radio" id="switcher-boxed">
              <label for="switcher-boxed" class="text-xs text-gray-500 ms-2 dark:text-white/70">Boxed</label>
            </div>
          </div>
        </div>
        <div class="space-y-6">
          <p class="switcher-style-head">Menu Positions:</p>
          <div class="grid grid-cols-3 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="data-menu-positions" class="ti-form-radio" id="switcher-menu-fixed" checked>
              <label for="switcher-menu-fixed"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">Fixed</label>
            </div>
            <div class="flex">
              <input type="radio" name="data-menu-positions" class="ti-form-radio" id="switcher-menu-scroll">
              <label for="switcher-menu-scroll"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">Scrollable </label>
            </div>
          </div>
        </div>
        <div class="space-y-6">
          <p class="switcher-style-head">Header Positions:</p>
          <div class="grid grid-cols-3 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="data-header-positions" class="ti-form-radio" id="switcher-header-fixed" checked>
              <label for="switcher-header-fixed" class="text-xs text-gray-500 ms-2 dark:text-white/70">
                Fixed</label>
            </div>
            <div class="flex">
              <input type="radio" name="data-header-positions" class="ti-form-radio" id="switcher-header-scroll">
              <label for="switcher-header-scroll"
                class="text-xs text-gray-500 ms-2 dark:text-white/70">Scrollable
              </label>
            </div>
          </div>
        </div>
        <div class="space-y-6 sidemenu-layout-styles">
          <p class="switcher-style-head">Sidemenu Layout Syles:</p>
          <div class="grid grid-cols-2 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-default-menu" checked>
              <label for="switcher-default-menu"
                class="text-xs text-gray-500 ms-2 dark:text-white/70 ">Default
                Menu</label>
            </div>
            <div class="flex">
              <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-closed-menu">
              <label for="switcher-closed-menu" class="text-xs text-gray-500 ms-2 dark:text-white/70 ">
                Closed
                Menu</label>
            </div>
            <div class="flex">
              <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-icontext-menu">
              <label for="switcher-icontext-menu" class="text-xs text-gray-500 ms-2 dark:text-white/70 ">Icon
                Text</label>
            </div>
            <div class="flex">
              <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-icon-overlay">
              <label for="switcher-icon-overlay" class="text-xs text-gray-500 ms-2 dark:text-white/70 ">Icon
                Overlay</label>
            </div>
            <div class="flex">
              <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-detached">
              <label for="switcher-detached"
                class="text-xs text-gray-500 ms-2 dark:text-white/70 ">Detached</label>
            </div>
            <div class="flex">
              <input type="radio" name="sidemenu-layout-styles" class="ti-form-radio" id="switcher-double-menu">
              <label for="switcher-double-menu" class="text-xs text-gray-500 ms-2 dark:text-white/70">Double
                Menu</label>
            </div>
          </div>
          <div class="px-4 text-secondary text-xs"><b class="me-2">Note:</b>Navigation menu styles won't work
            here.</div>
        </div>
        <div class="space-y-6">
          <p class="switcher-style-head">Loader:</p>
          <div class="grid grid-cols-3 gap-6 switcher-style">
            <div class="flex">
              <input type="radio" name="page-loader" class="ti-form-radio" id="switcher-loader-enable" checked>
              <label for="switcher-loader-enable" class="text-xs text-gray-500 ms-2 dark:text-white/70"> Enable</label>
            </div>
            <div class="flex">
              <input type="radio" name="page-loader" class="ti-form-radio" id="switcher-loader-disable">
              <label for="switcher-loader-disable" class="text-xs text-gray-500 ms-2 dark:text-white/70">Disable</label>
            </div>
          </div>
        </div>
      </div>
      <div id="switcher-2" class="hidden space-y-6" role="tabpanel" aria-labelledby="switcher-item-2">
        <div class="theme-colors">
          <p class="switcher-style-head">Menu Colors:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-white" type="radio" name="menu-colors"
                id="switcher-menu-light" checked>
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Light Menu
              </span>
            </div>
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-dark" type="radio" name="menu-colors"
                id="switcher-menu-dark" checked>
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Dark Menu
              </span>
            </div>
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-primary" type="radio" name="menu-colors"
                id="switcher-menu-primary">
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Color Menu
              </span>
            </div>
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-gradient" type="radio" name="menu-colors"
                id="switcher-menu-gradient">
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Gradient Menu
              </span>
            </div>
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-transparent" type="radio" name="menu-colors"
                id="switcher-menu-transparent">
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Transparent Menu
              </span>
            </div>
          </div>
          <div class="px-4 text-secondary text-xs"><b class="me-2">Note:</b>If you want to change color Menu
            dynamically
            change from below Theme Primary color picker.</div>
        </div>
        <div class="theme-colors">
          <p class="switcher-style-head">Header Colors:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-white" type="radio" name="header-colors"
                id="switcher-header-light" checked>
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Light Header
              </span>
            </div>
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-dark" type="radio" name="header-colors"
                id="switcher-header-dark">
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Dark Header
              </span>
            </div>
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-primary" type="radio" name="header-colors"
                id="switcher-header-primary">
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Color Header
              </span>
            </div>
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-gradient" type="radio" name="header-colors"
                id="switcher-header-gradient">
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Gradient Header
              </span>
            </div>
            <div class="hs-tooltip ti-main-tooltip ti-form-radio switch-select ">
              <input class="hs-tooltip-toggle ti-form-radio color-input color-transparent" type="radio"
                name="header-colors" id="switcher-header-transparent">
              <span
                class="hs-tooltip-content ti-main-tooltip-content py-1 px-2 bg-gray-900 text-xs font-medium text-white shadow-sm dark:bg-slate-700"
                role="tooltip">
                Transparent Header
              </span>
            </div>
          </div>
          <div class="px-4 text-secondary text-xs"><b class="me-2">Note:</b>If you want to change color
            Header dynamically
            change from below Theme Primary color picker.</div>
        </div>
        <div class="theme-colors">
          <p class="switcher-style-head">Theme Primary:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-primary-1" type="radio" name="theme-primary"
                id="switcher-primary" checked>
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-primary-2" type="radio" name="theme-primary"
                id="switcher-primary1">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-primary-3" type="radio" name="theme-primary"
                id="switcher-primary2">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-primary-4" type="radio" name="theme-primary"
                id="switcher-primary3">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-primary-5" type="radio" name="theme-primary"
                id="switcher-primary4">
            </div>
            <div class="ti-form-radio switch-select ps-0 mt-1 color-primary-light">
              <div class="theme-container-primary"></div>
              <div class="pickr-container-primary"></div>
            </div>
          </div>
        </div>
        <div class="theme-colors">
          <p class="switcher-style-head">Theme Background:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-bg-1" type="radio" name="theme-background"
                id="switcher-background" checked>
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-bg-2" type="radio" name="theme-background"
                id="switcher-background1">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-bg-3" type="radio" name="theme-background"
                id="switcher-background2">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-bg-4" type="radio" name="theme-background"
                id="switcher-background3">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio color-input color-bg-5" type="radio" name="theme-background"
                id="switcher-background4">
            </div>
            <div class="ti-form-radio switch-select ps-0 mt-1 color-bg-transparent">
              <div class="theme-container-background"></div>
              <div class="pickr-container-background"></div>
            </div>
          </div>
        </div>
        <div class="menu-image theme-colors">
          <p class="switcher-style-head">Menu With Background Image:</p>
          <div class="flex switcher-style space-x-3 rtl:space-x-reverse">
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio bgimage-input bg-img1" type="radio" name="theme-images" id="switcher-bg-img">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio bgimage-input bg-img2" type="radio" name="theme-images" id="switcher-bg-img1">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio bgimage-input bg-img3" type="radio" name="theme-images" id="switcher-bg-img2">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio bgimage-input bg-img4" type="radio" name="theme-images" id="switcher-bg-img3">
            </div>
            <div class="ti-form-radio switch-select">
              <input class="ti-form-radio bgimage-input bg-img5" type="radio" name="theme-images" id="switcher-bg-img4">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="ti-offcanvas-footer !justify-center">
      <a id="reset-all" class="ti-btn ti-btn-danger" href="javascript:void(0);">Reset</a>
    </div>
  </div>
  <!-- ========== END Switcher  ========== -->
   <!-- Loader -->
   <div id="loader" >
      {{-- <img src="../../../assets/img/media/loader.svg" alt=""> --}}
      <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/media/loader.svg') }}" alt="">
  </div>
  <!-- Loader -->

  <div class="page">
 <!-- Start::Header -->
 @include('partials.topnav')
 <!-- End::Header -->
 <!-- Start::app-sidebar -->
 @include('partials.sidebar')
 <!-- End::app-sidebar -->

    <div class="content">

         @yield('content')

    </div>

    <!-- ========== Search Modal ========== -->
    <div id="search-modal" class="hs-overlay ti-modal hidden">
      <div class="ti-modal-box">
        <div class="ti-modal-content">
          <div class="ti-modal-body">
            <div class="header-search">
              <label for="icon" class="sr-only">Search</label>
              <div class="relative">
                <div class="search-btn">
                  <i class="ri ri-search-2-line search-btn-icon"></i>
                </div>
                <input type="text" id="icon" name="icon" class="py-2 ps-11 ti-form-input focus:z-10"
                  placeholder="Search">
                <div class="voice-search">
                  <i class="ri ri-mic-2-line voice-btn-icon"></i>
                </div>
                <div class="search-dropdown hs-dropdown ti-dropdown">
                  <button type="button" aria-label="button"
                    class="hs-dropdown-toggle ti-dropdown-toggle text-gray-500 dark:!bg-transparent dark:text-white/70 p-0 !border-0 shadow-none">
                    <i class="ri ri-more-2-line search-dropdown-btn-icon"></i> </button>
                  <div class="hs-dropdown-menu ti-dropdown-menu">
                    <a class="ti-dropdown-item" href="javascript:void(0)">Action</a>
                    <a class="ti-dropdown-item" href="javascript:void(0)">Another Acttion</a>
                    <a class="ti-dropdown-item" href="javascript:void(0)">Something Else Here</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-5">
              <p class="font-semibold text-[13px] text-gray-400 dark:text-gray-200 mb-2">Are You Looking For...</p>
              <div class="badge rounded-sm bg-secondary/20 text-secondary relative header-box">
                <a href="team.html" class="w-full my-auto items-center flex space-x-2 rtl:space-x-reverse">
                  <span class="inline-block text-secondary me-1"><i class="ri ri-user-line text-sm"></i></span>
                  Team
                </a>
                <a href="javascript:void(0);"
                  class="header-remove-btn flex-shrink-0 h-4 w-4 inline-flex items-center justify-center rounded-full text-secondary hover:bg-secondary hover:text-secondary focus:outline-none focus:bg-secondary focus:text-white">
                  <span class="sr-only">Remove badge</span>
                  <svg class="h-4 w-4 hover:fill-white" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path
                      d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z">
                    </path>
                  </svg>
                </a>
              </div>
              <div class="badge rounded-sm bg-secondary/20 text-secondary relative header-box">
                <a href="form-elements.html" class="w-full my-auto items-center flex space-x-2 rtl:space-x-reverse">
                  <span class="inline-block text-secondary me-1"><i class="ri ri-file-text-line text-sm"></i></span>
                  Forms
                </a>
                <a href="javascript:void(0);"
                  class="header-remove-btn flex-shrink-0 h-4 w-4 inline-flex items-center justify-center rounded-full text-secondary hover:bg-secondary hover:text-secondary focus:outline-none focus:bg-secondary focus:text-white">
                  <span class="sr-only">Remove badge</span>
                  <svg class="h-4 w-4 hover:fill-white" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path
                      d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z">
                    </path>
                  </svg>
                </a>
              </div>
              <div class="badge rounded-sm bg-secondary/20 text-secondary relative header-box">
                <a href="vector-maps.html" class="w-full my-auto items-center flex space-x-2 rtl:space-x-reverse">
                  <span class="inline-block text-secondary me-1"><i class="ri ri-map-pin-line text-sm"></i></span>
                  Maps
                </a>
                <a href="javascript:void(0);"
                  class="header-remove-btn flex-shrink-0 h-4 w-4 inline-flex items-center justify-center rounded-full text-secondary hover:bg-secondary hover:text-secondary focus:outline-none focus:bg-secondary focus:text-white">
                  <span class="sr-only">Remove badge</span>
                  <svg class="h-4 w-4 hover:fill-white" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path
                      d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z">
                    </path>
                  </svg>
                </a>
              </div>
              <div class="badge rounded-sm bg-secondary/20 text-secondary relative header-box">
                <a href="widgets.html" class="w-full my-auto items-center flex space-x-2 rtl:space-x-reverse">
                  <span class="inline-block text-secondary me-1"><i class="ri ri-server-line text-sm"></i></span>
                  Widgets
                </a>
                <a href="javascript:void(0);"
                  class="header-remove-btn flex-shrink-0 h-4 w-4 inline-flex items-center justify-center rounded-full text-secondary hover:bg-secondary hover:text-secondary focus:outline-none focus:bg-secondary focus:text-white">
                  <span class="sr-only">Remove badge</span>
                  <svg class="h-4 w-4 hover:fill-white" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" viewBox="0 0 16 16">
                    <path
                      d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z">
                    </path>
                  </svg>
                </a>
              </div>
            </div>
            <div class="mt-5">
              <p class="font-semibold text-sm text-gray-500 mb-2">Recent Search :</p>
              <div class="p-2 border dark:border-white/10 rounded-sm flex items-center text-gray-500 mb-1 relative header-box">
                <a href="notifications.html" class="w-full my-auto items-center flex">
                  <span class="text-sm">Notifications</span>
                </a>
                <a aria-label="anchor" href="javascript:void(0);"
                  class="ms-auto flex-shrink-0 h-4 w-4 inline-flex items-center justify-center rounded-full text-gray-500 focus:outline-none header-remove-btn">
                  <i class="ri-close-line"></i>
                </a>
              </div>
              <div class="p-2 border dark:border-white/10 rounded-sm flex items-center text-gray-500 mb-1 relative header-box">
                <a href="alerts.html" class="w-full my-auto items-center flex">
                  <span class="text-sm">Alerts</span>
                </a>
                <a aria-label="anchor" href="javascript:void(0);"
                  class="ms-auto flex-shrink-0 h-4 w-4 inline-flex items-center justify-center rounded-full text-gray-500 focus:outline-none header-remove-btn">
                  <i class="ri-close-line"></i>
                </a>
              </div>
              <div class="p-2 border dark:border-white/10 rounded-sm flex items-center text-gray-500 relative header-box">
                <a href="tables.html" class="w-full my-auto items-center flex">
                  <span class="text-sm">Tables</span>
                </a>
                <a aria-label="anchor" href="javascript:void(0);"
                  class="ms-auto flex-shrink-0 h-4 w-4 inline-flex items-center justify-center rounded-full text-gray-500 focus:outline-none header-remove-btn">
                  <i class="ri-close-line"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="ti-modal-footer">
            <div class="inline-flex rounded-md shadow-sm">
              <button type="button" class="ti-btn-group py-1 ti-btn-soft-primary dark:border-white/10">
                Search
              </button>
              <button type="button" class="ti-btn-group py-1 ti-btn-primary dark:border-white/10">
                Clear Recents
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ========== END Search Modal ========== -->

    <footer class="mt-auto py-3 border-t dark:border-white/10 bg-white dark:bg-bgdark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
           <p class="text-center">Copyright © <span id="year"></span> <a href="javascript:void(0)" class="text-primary">Developed with ❤️ by Subutility</a> All rights reserved </p>

          </div>
    </footer>


  </div>

  <!-- Back To Top -->
  <div class="scrollToTop">
      <span class="arrow"><i class="ri-arrow-up-s-fill text-xl"></i></span>
  </div>

  <div id="responsive-overlay"></div>


  {{-- //TEMPORAL...MOVE TO A SEPARATE JS FILE --}}
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>



    $(document).ready(function(){
        $('.display_span').show();
        $('.loading_span').hide();
        $('.ti-spinner').hide();

      function showDisplayButton(id){
          $('#display_span'+id).show();
          $('#loading_span'+id).hide();
          $('#ti-spinner_'+id).hide();
      }

      function showLoadingButton(id=''){
          $('#display_span'+id).hide();
          $('#loading_span'+id).show();
          $('#ti-spinner_'+id).show();
      }

      function update_user_plan(){
            alert('id')
      }

      function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }

      function displayDataMeasurements(data_measurement,data_value_tb){
         const compute_gb_value = parseFloat(data_value_tb) * parseFloat(data_measurement); 
         const compute_mb_value = parseInt(parseFloat(data_value_tb) * parseFloat(data_measurement) * parseFloat(data_measurement)); 
        //  var display_result = "Data in MB: "+ compute_mb_value+"&nbsp;&nbsp;";
        //      display_result += "Data in MB: "+ compute_gb_value;
         $('#display_data_measurements').removeClass('hidden');
         $('#display_data_in_tb').text(numberWithCommas(data_value_tb) + ' TB');
         $('#display_data_in_gb').text(numberWithCommas(compute_gb_value) + ' GB');
         $('#display_data_in_mb').text(numberWithCommas(compute_mb_value) + ' MB');
      }

      //get other data messaurements
      $('#mb_data_measurement').keyup(function(){
          const data_measurement = $(this).val();
          const data_value_tb = $('#data_value_tb').val();
          displayDataMeasurements(data_measurement,data_value_tb);
      })

      $('#data_value_tb').keyup(function(){
          const data_value_tb = $(this).val();
          const data_measurement = $('#data_measurement').val();
          displayDataMeasurements(data_measurement,data_value_tb);
      })
      

      //edit user plan
      save_quick_edit('edit_class','prefix_id');
      $('.reseller_inputs').css('background-color', 'lightGray');

      function save_quick_edit(className,prefix_id = ''){
         
          $('.'+className).click(function(e){
          e.preventDefault();
        

          if(! $(this).hasClass('updater')  ){
                //means its currently editing....
                let id = $(this).attr('id');
                let newValue = $('#'+prefix_id+id).val();
                showLoadingButton(id);
                $('#'+prefix_id+id).removeAttr("disabled");
                $('#'+prefix_id+id).css('background-color', 'white');
                $('#'+prefix_id+id).focus();
                $(this).addClass('updater');
                return;
          }else{
                let id = $(this).attr('id');
                
                showDisplayButton(id);
                let name = $('#'+prefix_id+id).val();
                let _token = $('#_token').val();

                const data = {
                  id:id,
                  name:name,
                  _token:_token,
                };

                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin.reseller_plans.update_name') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        // showDisplayButton(id);
                        console.log(response);
                        $('#notify_span'+id).text('successfully saved...');
                        // showDisplayButton(id);
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                });

                $('#'+prefix_id+id).attr('disabled','');
                $('#'+prefix_id+id).css('background-color', 'lightGray');
                $(this).removeClass('updater');
          }          
        });
      }
      
      // alert('sss')
       $('.save_product_plan').click(function(e){


            e.preventDefault();

            let id = $(this).attr('id');
            // alert(id)
            // return

            let validity_in_day = $(`#validity_in_day_${id}`).val();
            let automation_id = $('#automation_id').val();
            let data_size_in_mb = $(`#data_size_in_mb_${id}`).val();
            let cost_price = $(`#cost_price_${id}`).val();
            let selling_price = $(`#selling_price_${id}`).val();
            let product_plan_name = $(`#product_plan_name_${id}`).val();
            //let product_id = $(`#product_id_${id}`).val();
            //let network_id = $(`#network_id_${id}`).val();
            let product_plan_category_id = $(`#product_plan_category_id_${id}`).val();
            let user_plan_1 = $(`#user_plan_${id}_1`).val();
            let user_plan_2 = $(`#user_plan_${id}_2`).val();
            let user_plan_3 = $(`#user_plan_${id}_3`).val();
            let user_plan_4 = $(`#user_plan_${id}_4`).val();
            let _token = $('#_token').val();
            

            showLoadingButton(id);
           
            //call a store endpoint to submit the request
            const data = {
              id:id,
              automation_id:automation_id,
              validity_in_day:validity_in_day,
              data_size_in_mb:data_size_in_mb,
              cost_price:cost_price,
              selling_price:selling_price,
              product_plan_name:product_plan_name,
              // product_id:product_id,
              product_plan_category_id:product_plan_category_id,
              user_plan_1:user_plan_1,
              user_plan_2:user_plan_2,
              user_plan_3:user_plan_3,
              user_plan_4:user_plan_4,
              // network_id:network_id,
              _token: _token
            };

            console.log(data);
            // return;

            $.ajax({
                type: 'POST',
                url: "{{ route('admin.product_plans.store') }}",
                data: data,
                dataType: 'json',
                success: function(response) {
                    // showDisplayButton(id);
                    console.log(response);
                    // $('#notify_span'+id).text(response.message);
                    $('#notify_span'+id).text(response.message);
                    showDisplayButton(id);
                },
                error: function(xhr, status, error) {
                    // Handle errors if needed
                    console.error(xhr.responseText);
                }
            });

       });

       $('.update_automation_product_plan_category').change(function(e){

          e.preventDefault();

          // let product_plan_id = $(this).attr('id');
          // let product_category_id = $('.product_category_id').val();
          let id = $(this).attr('id');
          let product_category_id = $("#product_category_id_"+id).val();
          let automation_id = $("#"+id).val();
          let automation_name = $("#"+id).find(":selected").text();
          // alert(prod_cat_id);
          // return;
          const data = {
            product_category_id,
            automation_id
          };
         
          // showLoadingButton(id);

          $.ajax({
              type: 'GET',
              url: "{{ route('admin.product_plan_categories.update_automation') }}",
              data: data,
              dataType: 'json',
              success: function(response) {
                  // console.log(response);
                  $('#notify_span'+id).text('Successfully updated to: '+ automation_name);
               
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
          });

        });

       
    })
  </script>

  <!-- popperjs -->
  {{-- <script src="../../../assets/libs/@popperjs/core/umd/popper.min.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'libs/@popperjs/core/umd/popper.min.js') }}"></script>


  <!-- Color Picker JS -->
  {{-- <script src="../../../assets/libs/@simonwep/pickr/pickr.es5.min.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>


  <!-- sidebar JS -->
  {{-- <script src="../../../assets/js/defaultmenu.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'js/defaultmenu.js') }}"></script>


  <!-- sticky JS -->
  {{-- <script src="../../../assets/js/sticky.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'js/sticky.js') }}"></script>


  <!-- Switch JS -->
  {{-- <script src="../../../assets/js/switch.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'js/switch.js') }}"></script>


  <!-- Preline JS -->
  {{-- <script src="../../../assets/libs/preline/preline.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'libs/preline/preline.js') }}"></script>


  <!-- Simplebar JS -->
  {{-- <script src="../../../assets/libs/simplebar/simplebar.min.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'js/assets/libs/simplebar/simplebar.min.js') }}"></script>


  <!-- Custom JS -->
  {{-- <script src="../../../assets/js/custom.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'js/custom.js') }}"></script>




    
    <!-- Custom-Switcher JS -->
    {{-- <script src="../../../assets/js/custom-switcher.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'js/custom-switcher.js') }}"></script>


  <!-- Tabulator JS -->
  {{-- <script src="../../../assets/libs/tabulator-tables/js/tabulator.min.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'libs/tabulator-tables/js/tabulator.min.js') }}"></script>


  <!-- Choices JS -->
  {{-- <script src="../../../assets/libs/choices.js/public/assets/scripts/choices.min.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>


  <!-- XLXS JS -->
  {{-- <script src="../../../assets/libs/xlsx/xlsx.full.min.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'libs/xlsx/xlsx.full.min.js') }}"></script>


  <!-- JSPDF JS -->
  {{-- <script src="../../../assets/libs/jspdf/jspdf.umd.min.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'libs/jspdf/jspdf.umd.min.js') }}"></script>

  <!-- Tabulator Custom JS -->
  {{-- <script src="../../../assets/js/datatable.js"></script> --}}
  <script src=" {{asset(env('APP_ASSETS_BASE_URL').'js/datatable.js') }}"></script>

  <script src="{{asset(env('APP_ASSETS_BASE_URL').'libs/quill/quill.min.js')}}"></script>
  <script src="{{asset(env('APP_ASSETS_BASE_URL').'js/quill.js')}}"></script>
 
  

</body>

</html>