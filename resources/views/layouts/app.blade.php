<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="dark">

<head>

  
     @if (env('APP_NAME') == 'FoxDataHub' )

     <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-NPMMTFT6');</script>
      


      <script async src="https://www.googletagmanager.com/gtag/js?id=G-NCKP7MH1KN"></script>
      <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-NCKP7MH1KN');
      </script>
   
     @endif
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <title> Data App - {{ env('APP_NAME') }} </title>
    <meta name="description" content="We are a data website selling data related products at affordable prices with quality">
    <meta name="keywords" content="data purchase, mtn, airtel, utility bills, cable subscription">

    <!-- Favicon -->
    {{-- <link rel="shortcut icon" href="{{ asset( env('APP_ASSETS_BASE_URL').'img/brand-logos/favicon.ico') }}"> --}}

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <!-- Main JS -->
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'js/main.js') }}"></script>

    <!-- Style Css -->
    {{-- <link rel="stylesheet" href="../../assets/css/style.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'css/style.css') }}">

    <!-- Simplebar Css -->
    {{-- <link rel="stylesheet" href="../../assets/libs/simplebar/simplebar.min.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/simplebar/simplebar.min.css') }}">

    <!-- Color Picker Css -->
    {{-- <link rel="stylesheet" href="../../assets/libs/@simonwep/pickr/themes/nano.min.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'libs/@simonwep/pickr/themes/nano.min.css') }}">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">


    <link rel="stylesheet" href="">
    {{-- 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet"> --}}

    {{-- <link href="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.css" rel="stylesheet" /> --}}
      
    @php
       $admin_site_color =  App\Models\AdminColorSetting::where('color_name','admin_site_color')->first();
       $admin_site_color_value = $admin_site_color->color_value ?? (int) '90, 102, 241'; 
      //  echo $admin_site_color_value;  
    @endphp

    <style>
        :root {
              --color-primary: {{  $admin_site_color_value  }};
              /* --color-primary: 90 102 241; */
              --color-primary-rgb: 90,102,241;
              --color-secondary: 96 165 250;
              --color-success: 34 197 94;
              --color-info: 76 117 207;
              --color-warning: 234 179 8;
              --color-danger: 244 63 94;
              --body-bg: 242 246 249;
              --default-text-color: 71 85 105;
              --default-border: 243 243 243;
              --muted: 140 144 151;
              --dark-rgb: 14 16 20;
              --menu-bg: 255 255 255;
              --menu-border-color: 243 243 243;
              --menu-prime-color: 100 116 139;
              --header-bg: 255 255 255;
              --header-prime-color: 100 116 139;
              --header-border-color: 243 243 243;
              --dark-bg: 30 41 59;
              --dark-bg2: 249 250 251;
          }
         .float{
          position:fixed;
          width:60px;
          height:60px;
          bottom:40px;
          right:40px;
          background-color:#25d366;
          color:#FFF;
          border-radius:50px;
          text-align:center;
          font-size:30px;
          box-shadow: 2px 2px 3px #999;
          z-index:100;
          }

          .my-float{
          margin-top:16px;
          }

          td {
          word-wrap: break-word;
          }

          .modal-style {
            box-shadow: 0 5px 15px rgb(0 0 0 / 50%);
            border: 1px solid rgba(0, 0, 0, .2)
        }

     .table.dataTable  {
          /* font-family: Verdana, Geneva, Tahoma, sans-serif; */
          font-size: 9px;
          
      }

      .table.dataTable td  {
          /* font-family: Verdana, Geneva, Tahoma, sans-serif; */
          font-size: 12px;
      }

      .table.dataTable th  {
          /* font-family: Verdana, Geneva, Tahoma, sans-serif; */
          font-size: 10.5px;
          text-transform: capitalize;
      }
          
    </style>



</head>

<body class="">

       @if (env('APP_NAME') == 'FoxDataHub')
       <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NPMMTFT6"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
       @endif


        {{-- @if (Session::has('whatsapp_support_number')) --}}
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
            {{-- &text=Hola%21%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Varela%202. --}}
            <a href="https://api.whatsapp.com/send?phone={{ session()->get('whatsapp_support_number') }}&text=Hello,%20Please%20I%20need%20help" class="float" target="_blank">
            <i class="fa fa-whatsapp my-float"></i>
            </a>            
        {{-- @endif --}}


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
        {{-- <img src="../../assets/img/media/loader.svg" alt=""> --}}
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

             <input value="{{  env('APP_URL') }}" type="hidden" class="root_url">
            <!-- Start::main-content main content stays here -->
            @yield('content')
            <!-- Start::main-content -->

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
                {{-- <p class="text-center">Copyright © <span id="year"></span> <a href="javascript:void(0)" class="text-primary">DataApp</a>    All rights reserved </p> --}}
                <p class="text-center">Copyright © <span id="year"></span> <a href="https://api.whatsapp.com/send?phone={{  '2347073459839'  }}&text=Hello,%20Please%20I%20want%20to%20own%20a%20data%20website" class="text-primary">Developed with ❤️ by Subutility</a> All rights reserved </p>
              </div>
        </footer>


    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="{{asset(env('APP_ASSETS_BASE_URL').'js/admin_datatables/datatables.js') }}"></script>



    {{-- <script src="{{asset(env('APP_ASSETS_BASE_URL').'js/swetalert.js') }}"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     

<script>
        function numberWithCommas(x) {
          return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function getProductPlans(network_id='', plan_category_id='', product_slug='', amount = ''){
               

                if(network_id != '' && product_slug != '' && plan_category_id == ''){
                  var data = {
                    network_id : network_id,
                    product_slug : product_slug,
                    amount : amount
                  };
               
                  // alert('hhhhh')
                }

                if(network_id != '' && plan_category_id != '' && product_slug != ''){
                  var data = {
                    network_id : network_id,
                    plan_category_id : plan_category_id,
                    product_slug : product_slug,
                    amount : amount
                  };        
                }

                //  console.log(data);
                //  return;
                

                $.ajax({
                          type: 'GET',
                          url: "{{ route('user.fetch_product_plans') }}",
                          data: data,
                          dataType: 'json',
                          success: function(response) {
                              console.log(response)
                              // console.log(response.data)
                              var result = JSON.stringify(response.data);
                              var dataList = JSON.parse(result);
                          
                               $('#product_plan_id').html("");
                               $('#product_plan_id').append('<option value="">Select Product Plan</option>');
       
                                // let jj = jsonn;
                                for (const child in dataList) {
                                 
                                    const idd = dataList[child].product_plan_id;
                                    const product_plan_name = dataList[child].product_plan_name;
                                    const selling_price = dataList[child].selling_price;
                                    if(product_slug == 'data'){
                                      option = "<option value="+idd+">"+product_plan_name+'- &#8358;'+selling_price+"</option>";
                                    }
                                    else if(product_slug == 'airtime' && amount != ''){
                                      option = "<option value="+idd+">"+product_plan_name+'- You are buying for: &#8358;'+selling_price+"</option>";
                                    }
                                    else if(product_slug == 'airtime' && amount == ''){
                                      option = "<option value="+idd+">"+product_plan_name+"</option>";
                                    }else{
                                      option = "<option value="+idd+">"+product_plan_name+"</option>";
                                    }
                                    $('#product_plan_id').append(option);
                                  
                                  
                                }
                              
                            
                          },
                          error: function(xhr, status, error) {
                              // Handle errors if needed
                              console.error(xhr.responseText);
                          }
                 });
        }

    

        function getSingleAirtimePlan(plan_category_id='', amount = ''){
               

               if(plan_category_id != '' && amount != ''){
                 var data = {
                   plan_category_id : plan_category_id,
                   amount : amount
                 };

                 $.ajax({
                         type: 'GET',
                         url: "{{ route('user.airtime.fetch_single_airtime_plan') }}",
                         data: data,
                         dataType: 'json',
                         success: function(response) {
                             console.log(response)
                             // console.log(response.data)
                             var result = JSON.stringify(response.data);
                             var dataList = JSON.parse(result);
                         
                              $('#product_plan_id').html("");
                              // $('#product_plan_id').append('<option value="">Select Product Plan</option>');
      
                               // let jj = jsonn;
                               for (const child in dataList) {
                                
                                   const idd = dataList[child].product_plan_id;
                                   const product_plan_name = dataList[child].product_plan_name;
                                   const selling_price = dataList[child].selling_price;
                               
                                   option = "<option selected value="+idd+">"+product_plan_name+'- You are buying for: &#8358;'+selling_price+"</option>";
                                     $('#product_plan_id').append(option);
                                 
                                 
                               }
                             
                           
                         },
                         error: function(xhr, status, error) {
                             // Handle errors if needed
                             console.error(xhr.responseText);
                         }
                       });
              
               }else{
                return;
               }

              
       }

        function getCableProductPlans(plan_category_id='', product_slug=''){
               
              var data = {
                plan_category_id : plan_category_id,
                product_slug : product_slug
              };
               

               $.ajax({
                         type: 'GET',
                         url: "{{ route('user.fetch_product_plans') }}",
                         data: data,
                         dataType: 'json',
                         success: function(response) {
                             console.log(response)
                             // console.log(response.data)
                             var result = JSON.stringify(response.data);
                             var dataList = JSON.parse(result);
                         
                              $('#cable_product_plan_id').html("");
                              $('#cable_product_plan_id').append('<option value="">Select Product Plan</option>');
      
                               // let jj = jsonn;
                               for (const child in dataList) {
                                
                                   const idd = dataList[child].product_plan_id;
                                   const product_plan_name = dataList[child].product_plan_name;
                                   const selling_price = dataList[child].selling_price;
                                   option = "<option value="+idd+">"+product_plan_name+'- &#8358;'+selling_price+"</option>";
                                   
                                   $('#cable_product_plan_id').append(option);
                                 
                               }
                             
                           
                         },
                         error: function(xhr, status, error) {
                             // Handle errors if needed
                             console.error(xhr.responseText);
                         }
                });
        }

        function getElectricityProductPlans(plan_category_id='', product_slug='', amount = ''){
               
               var data = {
                 plan_category_id : plan_category_id,
                 product_slug : product_slug,
                 amount : amount,
               };
               
               console.log('electric',data);
 
                $.ajax({
                          type: 'GET',
                          url: "{{ route('user.fetch_product_plans') }}",
                          data: data,
                          dataType: 'json',
                          success: function(response) {
                              console.log(response)
                              console.log('e dey reach here o')
                              // console.log(response.data)
                              var result = JSON.stringify(response.data);
                              var dataList = JSON.parse(result);
                          
                               $('#electricity_product_plan_id').html("");
                               $('#electricity_product_plan_id').append('<option value="">Select Product Plan</option>');
       
                                // let jj = jsonn;
                                for (const child in dataList) {
                                    const idd = dataList[child].product_plan_id;
                                    const product_plan_name = dataList[child].product_plan_name;
                                    const selling_price = dataList[child].selling_price;
                                    option = "<option value="+idd+">"+product_plan_name+'- You are buying for: &#8358;'+selling_price+"</option>";     
                                    $('#electricity_product_plan_id').append(option);
                                }
                              
                            
                          },
                          error: function(xhr, status, error) {
                              // Handle errors if needed
                              console.error(xhr.responseText);
                          }
                 });
         }

        

        function reload(timeout = '3000'){
          setTimeout(() => {
            window.location.reload();
          }, timeout);
        }

        function sweetAlertDisplay(message,title,status){
          Swal.fire({
                icon: status,
                title: title,
                text: message,
                // footer: '<a href="">Why do I have this issue?</a>'
                });
        }

        function sweetAlertConfirmation(){
              const swalWithBootstrapButtons = Swal.mixin({
              customClass: {
                confirmButton: 'ti-btn bg-secondary text-white hover:bg-secondary focus:ring-secondary dark:focus:ring-offset-secondary',
                cancelButton: 'ti-btn bg-danger text-white hover:bg-danger focus:ring-danger dark:focus:ring-offset-danger'
              },
              buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Yes, proceed',
              cancelButtonText: 'No, cancel!',
              reverseButtons: true
            }).then((result) => {
              if (result.isConfirmed) {
                swalWithBootstrapButtons.fire(
                  'Deleted!',
                  'Your file has been deleted.',
                  'success'
                )
              } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
              ) {
                swalWithBootstrapButtons.fire(
                  'Cancelled',
                  'Your imaginary file is safe :)',
                  'error'
                )
              }
            })
        }


        function generateCrystalPayDynamicAcct(amount){
          if(amount == ''){
            sweetAlertDisplay('Please enter amount','Amount required','error')
            return;
          }

          $.ajax({
                          type: 'GET',
                          url: "{{ route('user.crystalpay.generate_dynamic_account') }}",
                          data: { amount: amount},
                          dataType: 'json',
                          success: function(response) {
                              var result = JSON.stringify(response.data);
                              var dataList = JSON.parse(result);
                              console.log(dataList);
                              const bank_name = dataList.bank_name;
                              const account_number = dataList.account_number;
    
                              $('.crystal_pay_dynamic_account_details').append(`<p>Bank Name:  ${bank_name} </p>`);
                              $('.crystal_pay_dynamic_account_details').append(`<p>Account No:  ${account_number}</p>`);          
                              $('.crystal_pay_dynamic_account_details').append(`<p><strong>NOTE:</strong> Please ensure that the exact amount of ${amount} is paid into the generated account. </p>`);          
                              $('.crystal_pay_dynamic_account_details').append(`Please complete transaction in 5 minutes else the account will be invalid.</p>`);          
                          },
                          error: function(xhr, status, error) {
                              // Handle errors if needed
                              console.error(xhr.responseText);
                          }
                 });
        }
          
        
       

        function togglePassword(className,id,showValue){
          $('.'+className).change(function(e){
                e.preventDefault();
                var get_attr = $('#'+id).attr('type');
                if(get_attr == "text" || get_attr == "number"){
                    $('#'+id).attr("type", "password");
                    return;
                }

                $('#'+id).attr("type", showValue);
                return;
          })
        }

        
        function toggleProductPlanVisibility(productPlanId,token,checkedd){
        
                  const data = {
                    productPlanId : productPlanId,
                    token : token
                  };
                  // console.log(data);

                   $.ajax({
                    type: 'GET',
                    url: "{{ route('admin.product_plans.toggle_product_visibility') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        $('#nnotification'+productPlanId).removeClass('hidden');
                        $('#nnotification'+productPlanId).html(response.message);
                        console.log(response);
                        // console.log(response.data);
                        // var result = JSON.stringify(response);
                        // var dataList = JSON.parse(result);
                        // if(dataList.status == 1){
                        //    sweetAlertDisplay(dataList.message,'Success','success');
                        //    reload(3000);
                        // }else{
                        //   sweetAlertDisplay(dataList.message,'Error','error');
                        // }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                });
        }

        function toggleProductPlanPublicVisibility(productPlanId,token,checkedd){
        
        const data = {
          productPlanId : productPlanId,
          token : token
        };
        // console.log(data);

         $.ajax({
          type: 'GET',
          url: "{{ route('admin.product_plans.toggle_product_public_visibility') }}",
          data: data,
          dataType: 'json',
          success: function(response) {
              $('#nnotification2'+productPlanId).removeClass('hidden');
              $('#nnotification2'+productPlanId).html(response.message);
              console.log(response);
          },
          error: function(xhr, status, error) {
              // Handle errors if needed
              console.error(xhr.responseText);
          }
      });
}

        function togglePlanCategoryVisibility(productPlanCategoryId,token,checkedd){
               

                  const data = {
                    productPlanCategoryId : productPlanCategoryId,
                    token : token
                  };
                  // console.log(data);

                   $.ajax({
                    type: 'GET',
                    url: "{{ route('admin.product_plan_categories.toggle_plan_category_visibility') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        $('#plan_cat_visibility_notification'+productPlanCategoryId).removeClass('hidden');
                        $('#plan_cat_visibility_notification'+productPlanCategoryId).html(response.message);
                        console.log(response);
                        // console.log(response.data);
                        // var result = JSON.stringify(response);
                        // var dataList = JSON.parse(result);
                        // if(dataList.status == 1){
                        //    sweetAlertDisplay(dataList.message,'Success','success');
                        //    reload(3000);
                        // }else{
                        //   sweetAlertDisplay(dataList.message,'Error','error');
                        // }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                });
        }

        function toggleUserStatus(userId,token,actualValue){
               

               const data = {
                 userId : userId,
                 token : token
               };
              //  console.log(data);
              //  return;
               
                $.ajax({
                 type: 'GET',
                 url: "{{ route('admin.users.toggle_verification_status') }}",
                 data: data,
                 dataType: 'json',
                 success: function(response) {
                     $('#user_verification_notification'+userId).removeClass('hidden');
                     $('#user_verification_notification'+userId).html(response.message);
                     console.log(response);
                 },
                 error: function(xhr, status, error) {
                     // Handle errors if needed
                     console.error(xhr.responseText);
                 }
             });
     }

        function toggleHotSales(planCategoryId,token,checkedd){
                  // alert(planCategoryId)
                  // var check = $('#hs-basic-with-description-checked'+planCategoryId).checked;
                  // if(checkedd != ''){
                  //   alert('checked')
                  //   // $('#hs-basic-with-description-checked'+planCategoryId).checked;
                  //   $('#hs-basic-with-description-checked'+planCategoryId).prop( 'checked', false )
                  // }else{
                  //   $('#hs-basic-with-description-checked'+planCategoryId).prop( 'checked', true )
                  //   alert('unchecked')
                  // }

                  const data = {
                    planCategoryId : planCategoryId,
                    token : token
                  };
                  // console.log(data);

                   $.ajax({
                    type: 'GET',
                    url: "{{ route('admin.product_plan_categories.toggle_hot_sales') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        $('#hot_sales_notification'+planCategoryId).removeClass('hidden');
                        $('#hot_sales_notification'+planCategoryId).html(response.message);
                        console.log(response);
                        // console.log(response.data);
                        // var result = JSON.stringify(response);
                        // var dataList = JSON.parse(result);
                        // if(dataList.status == 1){
                        //    sweetAlertDisplay(dataList.message,'Success','success');
                        //    reload(3000);
                        // }else{
                        //   sweetAlertDisplay(dataList.message,'Error','error');
                        // }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                });
        }

        function copyToClipboard() {
              // Get the text field
              var copyText = document.getElementById("myInput");

              // Select the text field
              copyText.select();
              copyText.setSelectionRange(0, 99999); // For mobile devices

              // Copy the text inside the text field
              navigator.clipboard.writeText(copyText.value);

              // Alert the copied text
              alert("Copied to clipboard");
        }

        function debounce(func, timeout = 3000){
          let timer;
          return (...args) => {
          clearTimeout(timer);
          timer = setTimeout(() => { func.apply(this, args); }, timeout);
          };
        }


        function doValidateNameOnSmartCard(typpe=''){
          // alert(typpe)
          if(typpe == 'electricity'){
            var smart_card_number = $('#metre_number').val();
            var url = "{{ route('user.electricity.validate_metre_number') }}";
            var plan_id = $('#electricity_product_plan_id').val();
            var display_id = 'validation_extra_info';

          }
          else if(typpe == 'cable'){
            console.log('hereee')
            var smart_card_number = $('#smart_card_number').val();
            var url = "{{ route('user.cable_subscription.validate_smart_card_number') }}";
            var plan_id = $('#cable_product_plan_id').val();
            var display_id = 'validation_customer_name';

          }else{
            var smart_card_number = 'ttt';
            //this should never run
          }


          var data = {
            smart_card_number : smart_card_number,
            plan_id : plan_id
          };
          $('#validated_name_on_smart_card').html('Validating...');

          $.ajax({
                    type: 'GET',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        // $('#validated_name_on_smart_card').removeClass('hidden');
                        $('#validated_name_on_smart_card').html('');
                        $('#validated_name_on_smart_card').append('Please validate the details below before payment:<br>');
                        $('#validated_name_on_smart_card').append(`<p>Name on Card: <strong>${response.name}</strong> </p>`);
                        if(typpe == 'electricity'){
                          $('#validated_name_on_smart_card').append(`<p>Address: <strong>${response.address}</strong> </p>`);
                        }
                        $('#'+display_id).val(response.name);
                     
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                });
        }
        
        const validateNameOnSmartCard = debounce((typee) => doValidateNameOnSmartCard(typee));

        togglePassword('show_pin1','pin','number');
        togglePassword('show_pin2','current_pin','number');
        togglePassword('show_pin3','new_pin','number');
        togglePassword('show_pin4','confirm_new_pin','number');
        togglePassword('show_pin5','pin5','number');
        togglePassword('show_password','new_password','text');
        togglePassword('show_password2','confirm_new_password','text');
        togglePassword('show_password_current','current_password','text');
        
        

        $(document).ready(function(){

          // $("[data-toggle='modal']").click(function () {
             $("#popup").fadeIn();
          // });

          $("[data-close]").click(function () {
              $(this).parents(".modal").fadeOut();
          });
        

          //reset
          // $('#buy_data_btn').click
          $('#cancel_disabling').click(function(e){
            e.preventDefault();
            $('#buy_data_btn').html('Buy Data');
            $('#buy_data_btn').prop('disabled',false);
            $(this).addClass('hidden');

            $('#buy_airtime_btn').html('Buy Airtime');
            $('#buy_airtime_btn').prop('disabled',false);
            $(this).addClass('hidden');

            $('#buy_cable_btn').html('Buy Cable TV');
            $('#buy_cable_btn').prop('disabled',false);
            $(this).addClass('hidden');

            $('#buy_electricity_btn').html('Buy Electricity');
            $('#buy_electricity_btn').prop('disabled',false);
            $(this).addClass('hidden');
          
          })

          //reset ends

          // alert('sss');
    
          $('.single_select').select2();
          $('#product_plan_category_id').html('<option value="all">All categories selected</option>');

          // $('.edit_cat').click( (e) => {
          //   e.preventDefault();
          //   var catid = $(this).attr('id');
          //   // let newValue = $('#'+catid).text();
          //   console.log(catid)
          // });

          
          $('#generate_crystalpay_dynamic_account').click(function(e){
              e.preventDefault();
              const amount = $('#amount').val();
              if(amount == ''){
                sweetAlertDisplay('Please enter amount','Amount required','error')
                return;
              }
              $(this).text('Generating dynamic acount...');
              $(this).prop('disabled',true);
              // return;
              generateCrystalPayDynamicAcct(amount);
          })

          $('#wallet_category').change(function(e){
              e.preventDefault();
              const wallet_type = $(this).val();
          });

          $('#utility_amount').keyup(function(e){
              e.preventDefault();
              var amount =  $(this).val();
              var product_slug = $("#product_slug").val();
              var plan_category_id = $('#electricity_product_plan_category_id').val();
               
              var amount = product_slug == 'airtime' || product_slug == 'utility_bills'  ? amount : '';
              // alert(plan_category_id)

              getElectricityProductPlans(plan_category_id,product_slug,amount);

          });

          $('#amount').keyup(function(e){
              e.preventDefault();
              var amount =  $(this).val();
              var network_id = $("#network_id").val();
              var product_slug = $("#product_slug").val();
              var plan_category_id = $('#product_plan_category_id').val();
               
              if(network_id == ''){
                sweetAlertDisplay('Network is required','Network required','error');
                return;
              }
              var amount = product_slug == 'airtime' ? $('#amount').val() : '';
        
              getProductPlans(network_id,plan_category_id,product_slug,amount);
          });

          $('#amount_for_airtime_category').keyup(function(e){
              e.preventDefault();
              var amount =  $(this).val();
              var network_id = $("#network_id").val();
              var product_slug = $("#product_slug").val();
              var plan_category_id = $('#product_plan_category_id').val();
               
              if(network_id == ''){
                sweetAlertDisplay('Network is required','Network required','error');
                return;
              }
              var amount = product_slug == 'airtime' ? $(this).val() : '';
            
              getSingleAirtimePlan(plan_category_id,amount);
          });

          

          


          $('#product_plan_category_id').change(function(){
            var network_id = $("#network_id").val();
            var product_slug = $("#product_slug").val();
            var plan_category_id = $(this).val();

            // alert(plan_category_id)

            if(network_id == ''){
              sweetAlertDisplay('Network is required','Network required','error');
              return;
            }

            if(plan_category_id == '' || plan_category_id == 'all'){
              sweetAlertDisplay('Product plan category is required','Plan category required','error');
              return;
            }
            var amount = product_slug == 'airtime' ? $('#amount').val() : '';
            
            getProductPlans(network_id,plan_category_id,product_slug,amount);
          })

          $('#cable_product_plan_category_id').change(function(){
            var product_slug = $("#product_slug").val();
            var plan_category_id = $(this).val();

            if(plan_category_id == '' || plan_category_id == 'all'){
              sweetAlertDisplay('Product plan category is required','Plan category required','error');
              return;
            }

            getCableProductPlans(plan_category_id,product_slug);
          })

          $('#electricity_product_plan_category_id').change(function(){
            var product_slug = $("#product_slug").val();
            var plan_category_id = $(this).val();

            if(plan_category_id == '' || plan_category_id == 'all'){
              sweetAlertDisplay('Product plan category is required','Plan category required','error');
              return;
            }
            getElectricityProductPlans(plan_category_id,product_slug);
          })
          
         
          $('#buy_bulk_data_btn').click(function(e){
            e.preventDefault();
           
              //display product plans categories
              const bulk_data_plan_id = $('#bulk_data_plan_id').val();
              const bulk_data_wallet_id = $('#bulk_data_wallet_id').val();
              const pin = $('#pin').val();
              const _token = $('#_token').val();
            
              const data = {
                bulk_data_plan_id : bulk_data_plan_id,
                bulk_data_wallet_id : bulk_data_wallet_id,
                pin : pin,
                _token : _token,
              };

              if (confirm("Are you sure you want to complete this purchase?") == true) {
                  // alert('logic happens here')
                  $.ajax({
                    type: 'POST',
                    url: "{{ route('user.data.buy_bulk_data_action') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        // console.log(response);
                        // console.log(response.data);
                        var result = JSON.stringify(response);
                        var dataList = JSON.parse(result);
                        if(dataList.status == 1){
                           sweetAlertDisplay(dataList.message,'Success','success');
                           reload(3000);
                        }else{
                          sweetAlertDisplay(dataList.message,'Error','error');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                });
              } else {
                return;
              }

            
             
            
          })

          $('#bulk_data_plan_id').change(function(e){
            e.preventDefault();
            const bulk_data_plan_id = $(this).val();
            // alert(bulk_data_plan_id);

            if(bulk_data_plan_id == ''){
              sweetAlertDisplay("Please select a plan",'Plan required','error');
            }
        
            const data = {
                bulk_data_plan_id : bulk_data_plan_id,
            };  
            
            $.ajax({
                  type: 'GET',
                  url: "{{ route('user.data.fetch_bulk_data_plan_details') }}",
                  data: data,
                  dataType: 'json',
                  success: function(response) {
                      console.log(response);
                      
                      if(response.status == '1'){
                          var bulk_data_plan_name = response.data.bulk_data_plan_name;
                          var selling_price = numberWithCommas(response.data.selling_price);
                          var data_value_mb = numberWithCommas(response.data.data_value_mb);
                          var data_value_gb = numberWithCommas(response.data.data_value_gb);
                          // var data_value_tb = numberWithCommas(response.data.data_value_tb);
                          var data_value_tb = response.data.data_value_tb;
                          var selling_price = numberWithCommas(response.data.selling_price);
                          var mb_data_measurement = numberWithCommas(response.data.mb_data_measurement);
                          
                          var data_content = '<p>Bulk data plan: '+ bulk_data_plan_name+'</p>';
                          data_content += '<p>Measurement per MB: '+ mb_data_measurement+'</p>';
                          data_content += '<p>Data value in TB: '+ data_value_tb+'</p>';
                          data_content += '<p>Data value in GB: '+ data_value_gb+'</p>';
                          data_content += '<p>Data value in MB: '+ data_value_mb+'</p>';
                          data_content += '<p>Price: &#8358;'+ selling_price+'</p>';
                          $('#bulk_data_plan_details').removeClass('hidden');
                          $('#bulk_data_plan_details').html(data_content);
                      }else{
                          $('#bulk_data_plan_details').removeClass('hidden');
                          $('#bulk_data_plan_details').html('');
                          console.log(response);
                      } 
                    
                  },
                  error: function(xhr, status, error) {
                      // Handle errors if needed
                      // console.error(xhr.responseText);
                      $('#bulk_data_plan_details').html('');
                      console.log('Something went wrong')
                  }
              });

            })

          $('#bulk_data_wallet_id').change(function(e){
              e.preventDefault();
              $('#bulk_data_plan_id').html('<option value="">Select a plan</option>');
              // $('#bulk_data_plan_details').addClass('hidden');
              $('#bulk_data_plan_details').html('');
            //  alert('testing')
              
              const bulk_data_wallet_id = $(this).val();
              const _token = $('#_token').val();
             
              const data = {
                bulk_data_wallet_id : bulk_data_wallet_id,
                _token : _token
              };
              console.log(_token,bulk_data_wallet_id);

              $.ajax({
                  type: 'POST',
                  url: "{{ route('user.data.fetch_bulk_data_plans') }}",
                  data: data,
                  dataType: 'json',
                  success: function(response) {
                      console.log(response);
                     
                      if(response.status == '1'){
                        let dataResult = response?.data;
                        $('#bulk_data_plan_id').html('<option value="">Select a plan</option>');

                        dataResult.forEach(element => {
                            const idd = element.id;
                            const data_value_mb = element.data_value_mb;
                            const data_value_gb = element.data_value_gb;
                            const data_value_tb = element.data_value_tb;
                            const bulk_data_plan_name = element.bulk_data_plan_name;
                            // console.log(element)
                             option = "<option value="+idd+">"+bulk_data_plan_name+"</option>"
                            $('#bulk_data_plan_id').append(option);
                          // console.log(category_name);
                        });

                      }else{
                        
                        console.log(response);
                      }
                    
                  },
                  error: function(xhr, status, error) {
                      // Handle errors if needed
                      console.error(xhr.responseText);
                  }
              });
          });

          $('#network_id').change(function(){
            $('#product_plan_category_id').html('<option value="all">All categories selected</option>');
            $("#product_plan_category_div").addClass('hidden');
            $('#filter_by_plan_category').prop('checked',false)
            var network_id = $(this).val();
            var product_slug = $('#product_slug').val();
           
            // alert(network_id)
            //here you have to display all plans that are tied to this network but only where tied to the automation tied to each product plan category
            var amount = product_slug == 'airtime' ? $('#amount').val() : '';
            
            getProductPlans(network_id,'',product_slug,amount);
          })

          $('#filter_by_plan_category').change(function(e){
            e.preventDefault();
        
            
            if(this.checked){
              $("#product_plan_category_div").removeClass('hidden');
              //display product plans categories
              const network_id = $('#network_id').val();
              const product_slug = $('#product_slug').val();

              if(network_id == ''){
                sweetAlertDisplay("Please select network",'Network required','error');
                $(this).prop('checked', false); // Unchecks it
                return;
              }
              const data = {
                network_id : network_id,
                product_slug : product_slug
              };

                $.ajax({
                    type: 'GET',
                    url: "{{ route('user.fetch_product_plan_categories') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                         console.log('testing',response)
                        // showDisplayButton(id);
                        if(response.status == '1'){
                          let dataResult = response?.data;
                          $('#product_plan_category_id').html('<option value="all">Select category</option>');


                          dataResult.forEach(element => {
                              const idd = element.id;
                              const category_name = element.product_plan_category_name;
                              option = "<option value="+idd+">"+category_name+"</option>"
                              $('#product_plan_category_id').append(option);
                              // console.log(category_name);

                          });
                        }
                        // console.log(response.data);
                        //$('#notify_span'+id).text('successfully saved...');
                        // showDisplayButton(id);
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                });
            }else{
              $('#product_plan_category_id').html('<option value="all">All categories selected</option>');
              $("#product_plan_category_div").addClass('hidden');
            }
          })


          $('#product_plan_id').change(function(e){
            const wallet_category = $('#wallet_category').val();
            const product_plan_id = $('#product_plan_id').val();
            var url = "{{ route('user.data.get_single_bulk_data_wallet', ":product_plan_id") }}";
            url = url.replace(':product_plan_id', product_plan_id);

            if(wallet_category == ''){
              sweetAlertDisplay('Wallet category cannot be empty','Wallet selection required','error');        
            }

            if(wallet_category == 'data_wallet'){
              //then display the wallet for the selected product plan's category
              $.ajax({
                    type: 'GET',
                    url: url,
                    data: {},
                    dataType: 'json',
                    success: function(response) {
                        // console.log(response);
                        if(response.status == 1){
                            const display = 'Your wallet balance for '+ response.data.product_plan_category.product_plan_category_name + ' is '+ response.wallet;
                            $('.display_wallet_details').text(display);
                            return;
                        }else{
                            const display = 'Your wallet balance is 0 MB... Buy bulk data wallet';
                            $('.display_wallet_details').text(display);
                        }

                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                });
            }

          })

          $('#buy_cable_btn').click(function(e){
            e.preventDefault();
              $(this).html('Processing...Please wait');
              $(this).prop('disabled',true);
              $('#cancel_disabling').removeClass('hidden')

           
              //display product plans categories
              const cable_product_plan_category_id = $('#cable_product_plan_category_id').val();
              let smart_card_number = $('#smart_card_number').val();
              const validation_customer_name = $('#validation_customer_name').val();
              const wallet_category = $('#wallet_category').val();
              const cable_product_plan_id = $('#cable_product_plan_id').val();
              const pin = $('#pin').val();
              const no_of_slots = $('#no_of_slots').val();
              
              const data = {
                cable_product_plan_category_id : cable_product_plan_category_id,
                smart_card_number : smart_card_number,
                validation_customer_name : validation_customer_name,
                wallet_category : wallet_category,
                cable_product_plan_id : cable_product_plan_id,
                pin : pin,
                no_of_slots : no_of_slots,
              };


              // console.log(data);
              // return;

              if (confirm("Are you sure you want to complete this cable subscription purchase?") == true) {
                  // alert('logic happens here')
                

                  $.ajax({
                    type: 'GET',
                    url: "{{ route('user.cable_subscription.buy_cable_subscription_action') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        // console.log(response.data);
                        var result = JSON.stringify(response);
                        var dataList = JSON.parse(result);
                        if( parseInt(dataList.status) == 1){
                           sweetAlertDisplay(dataList.message,'Success','success');
                           reload(3000);
                        }
                        else if(dataList.status == 2){
                           //@least 1 tranaction had an issue
                           sweetAlertDisplay(dataList.message,'Info','warning');
                           reload(100000000);
                        }
                        else{
                          sweetAlertDisplay(dataList.message,'Error','error');
                          $(this).prop('disabled',false);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                  });
              } else {
                return;
              }

              
            
          })

          $('#buy_electricity_btn').click(function(e){
            e.preventDefault();
              $(this).html('Processing...Please wait');
              $(this).prop('disabled',true);
              $('#cancel_disabling').removeClass('hidden')

           
              //display product plans categories
              const electricity_product_plan_category_id = $('#electricity_product_plan_category_id').val();
              const metre_number = $('#metre_number').val();
              const validation_extra_info = $('#validation_extra_info').val();
              const wallet_category = $('#wallet_category').val();
              const electricity_product_plan_id = $('#electricity_product_plan_id').val();
              const pin = $('#pin').val();
              const no_of_slots = $('#no_of_slots').val();
              const utility_amount = $('#utility_amount').val();
              
              

              const data = {
                electricity_product_plan_category_id : electricity_product_plan_category_id,
                metre_number : metre_number,
                validation_extra_info : validation_extra_info,
                wallet_category : wallet_category,
                electricity_product_plan_id : electricity_product_plan_id,
                pin : pin,
                no_of_slots : no_of_slots,
                amount : utility_amount,
              };

              // console.log(data);
              // return;

              if (confirm("Are you sure you want to complete this electricity subscription purchase?") == true) {
                  // alert('logic happens here')
                

                  $.ajax({
                    type: 'GET',
                    url: "{{ route('user.electricity.buy_electricity_subscription_action') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        // console.log(response.data);
                        var result = JSON.stringify(response);
                        var dataList = JSON.parse(result);
                        if( parseInt(dataList.status) == 1){
                           sweetAlertDisplay(dataList.message,'Success','success');
                           reload(3000);
                        }
                        else if(dataList.status == 2){
                           //@least 1 tranaction had an issue
                           sweetAlertDisplay(dataList.message,'Info','warning');
                           reload(100000000);
                        }
                        else{
                          sweetAlertDisplay(dataList.message,'Error','error');
                          $(this).prop('disabled',false);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                  });
              } else {
                return;
              }

              
            
          })

          $('#buy_data_btn').click(function(e){
            e.preventDefault();
              $(this).html('Processing...Please wait');
              $(this).prop('disabled',true);
              $('#cancel_disabling').removeClass('hidden')

           
              //display product plans categories
              const network_id = $('#network_id').val();
              const product_plan_category_id = $('#product_plan_category_id').val();
              const phone_number = $('#phone_number').val();
              const wallet_category = $('#wallet_category').val();
              const product_plan_id = $('#product_plan_id').val();
              const pin = $('#pin').val();
              // const validatephonenetwork = $('#validatephonenetwork').val();
              let checkvalidatephonenetwork = $('#validatephonenetwork').is(":checked");
              if(checkvalidatephonenetwork){
                var validatephonenetwork = 1;
              }else{
                var validatephonenetwork = 0;
                
              }
              

              const data = {
                network_id : network_id,
                product_plan_category_id : product_plan_category_id,
                phone_number : phone_number,
                wallet_category : wallet_category,
                product_plan_id : product_plan_id,
                pin : pin,
                validatephonenetwork : validatephonenetwork
              };

              

              // console.log(data);
              // return;

              if (confirm("Are you sure you want to complete this data purchase?") == true) {
                  // alert('logic happens here')
                

                  $.ajax({
                    type: 'GET',
                    url: "{{ route('user.data.buy_data_action') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        // console.log(response.data);
                        var result = JSON.stringify(response);
                        var dataList = JSON.parse(result);
                        if( parseInt(dataList.status) == 1){
                           sweetAlertDisplay(dataList.message,'Success','success');
                           reload(3000);
                        }
                        else if(dataList.status == 2){
                           //@least 1 tranaction had an issue
                           sweetAlertDisplay(dataList.message,'Info','warning');
                           reload(6000);
                        }
                        else{
                          sweetAlertDisplay(dataList.message,'Error','error');
                          $(this).prop('disabled',false);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                  });
              } else {
                return;
              }

              
            
          })

          $('#buy_airtime_btn').click(function(e){
            e.preventDefault();
              $(this).html('Processing...Please wait');
              $(this).prop('disabled',true);
              $('#cancel_disabling').removeClass('hidden')

           
              //display product plans categories
              const network_id = $('#network_id').val();
              const product_plan_category_id = $('#product_plan_category_id').val();
              const phone_number = $('#phone_number').val();
              const wallet_category = $('#wallet_category').val();
              const product_plan_id = $('#product_plan_id').val();
              const pin = $('#pin').val();
              let checkvalidatephonenetwork = $('#validatephonenetwork').is(":checked");
              if(checkvalidatephonenetwork){
                var validatephonenetwork = 1;
              }else{
                var validatephonenetwork = 0;
                
              }

              if($('#amount_for_airtime_category').val() == ''){
                var amount = $('#amount_for_airtime_category').val();
                console.log('this is running')

                if(amount == undefined){
                 var amount = $('#amount').val();
                }
              }else{
                var amount = $('#amount').val();
              }

              if( parseInt(amount) < 50){
                sweetAlertDisplay('You need to buy atleast N50 worth of airtime','Airtime purchase error','error');
                // sweetAlertDisplay('You need to buy atleast &#8358;50 worth of airtime','Airtime purchase error','error');
                return;
              
              }
              

              const data = {
                network_id : network_id,
                product_plan_category_id : product_plan_category_id,
                phone_number : phone_number,
                wallet_category : wallet_category,
                product_plan_id : product_plan_id,
                pin : pin,
                amount : amount,
                validatephonenetwork : validatephonenetwork
              };

              // console.log(data);
              // return;

              if (confirm("Are you sure you want to complete this airtime purchase?") == true) {
                  // alert('logic happens here')
                

                  $.ajax({
                    type: 'GET',
                    url: "{{ route('user.airtime.buy_airtime_action') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        // console.log(response.data);
                        var result = JSON.stringify(response);
                        var dataList = JSON.parse(result);
                        if( parseInt(dataList.status) == 1){
                           sweetAlertDisplay(dataList.message,'Success','success');
                           reload(3000);
                        }
                        else if(dataList.status == 2){
                           //@least 1 tranaction had an issue
                           sweetAlertDisplay(dataList.message,'Info','warning');
                           reload(60000000);
                        }
                        else{
                          sweetAlertDisplay(dataList.message,'Error','error');
                          $(this).prop('disabled',false);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                  });
              } else {
                return;
              }

              
            
          })

        })

    </script>

    <!-- Apex Charts JS -->
    {{-- <script src="../../assets/libs/apexcharts/apexcharts.min.js"></script> --}}
    <script src=" {{asset(env('APP_ASSETS_BASE_URL').'libs/apexcharts/apexcharts.min.js') }}"></script>


    <!-- Index JS -->
    {{-- <script src="../../assets/js/index-2.js"></script> --}}
    <script src=" {{ asset(env('APP_ASSETS_BASE_URL').'js/index-2.js') }}"></script>

    <!-- Back To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill text-xl"></i></span>
    </div>

    <div id="responsive-overlay"></div>

    <!-- popperjs -->
    {{-- <script src="../../assets/libs/@popperjs/core/umd/popper.min.js"></script> --}}
    <script src=" {{ asset(env('APP_ASSETS_BASE_URL').'libs/@popperjs/core/umd/popper.min.js') }}"></script>
    

    <!-- Color Picker JS -->
    {{-- <script src="../../assets/libs/@simonwep/pickr/pickr.es5.min.js"></script> --}}
    <script src=" {{ asset(env('APP_ASSETS_BASE_URL').'libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>


    <!-- sidebar JS -->
    {{-- <script src="../../assets/js/defaultmenu.js"></script> --}}
    <script src=" {{ asset(env('APP_ASSETS_BASE_URL').'js/defaultmenu.js') }}"></script>


    <!-- sticky JS -->
    {{-- <script src="../../assets/js/sticky.js"></script> --}}
    <script src=" {{ asset(env('APP_ASSETS_BASE_URL').'js/sticky.js') }}"></script>


    <!-- Switch JS -->
    {{-- <script src="../../assets/js/switch.js"></script> --}}
    <script src=" {{ asset(env('APP_ASSETS_BASE_URL').'js/switch.js') }}"></script>


    <!-- Preline JS -->
    {{-- <script src="../../assets/libs/preline/preline.js"></script> --}}
    <script src=" {{ asset(env('APP_ASSETS_BASE_URL').'libs/preline/preline.js') }}"></script>


    <!-- Simplebar JS -->
    {{-- <script src="../../assets/libs/simplebar/simplebar.min.js"></script> --}}
    <script src=" {{ asset(env('APP_ASSETS_BASE_URL').'libs/simplebar/simplebar.min.js') }}"></script>


    <!-- Custom JS -->
    {{-- <script src="../../assets/js/custom.js"></script> --}}
    <script src=" {{ asset(env('APP_ASSETS_BASE_URL').'js/custom.js') }}"></script>




    
    <!-- Custom-Switcher JS -->
    {{-- <script src="../../assets/js/custom-switcher.js"></script> --}}
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'js/custom-switcher.js') }}"></script>

      <!-- Choices JS -->
    <script src="../assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="../assets/js/choices.js"></script>

  <!-- Tom Select JS -->
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'libs/tom-select/js/tom-select.complete.min.js') }}"></script>
    <script src="{{ asset(env('APP_ASSETS_BASE_URL').'js/tom-select.js') }}"></script>
    {{-- <script src="../assets/libs/tom-select/js/tom-select.complete.min.js"></script> --}}
    {{-- <script src="../assets/js/tom-select.js"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    {{-- <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script> --}}


</body>

</html>