<aside class="app-sidebar" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header ">
        <a href="#" class="header-logo mt-3 mb-20">
            {{-- <img src="../assets/img/brand-logos/desktop-logo.png" alt="logo" class="main-logo desktop-logo">
            <img src="../assets/img/brand-logos/toggle-logo.png" alt="logo" class="main-logo toggle-logo">
            <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="main-logo desktop-dark">
            <img src="../assets/img/brand-logos/toggle-dark.png" alt="logo" class="main-logo toggle-dark"> --}}
            <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/logos/logo.png') }}" alt="logo"
            class="w-14 h-16 mx-auto block dark:hidden" >
          

        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar " id="sidebar-scroll">

        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
                    height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg></div>
            <ul class="main-menu ">
                <!-- Start::slide__category -->
                <li class="slide__category"><span class="category-name">Main</span></li>
                <!-- End::slide__category -->

                <!-- Start::slide -->
                <li class="slide  has-sub ">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Dashboards</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1"><a href="javascript:void(0)">Dashboards</a></li>
                        <li class="slide"><a href="index.html" class="side-menu__item">Sales</a></li>
                        
                    </ul>
                </li>
                <!-- End::slide -->

                 <!-- Start::slide__category -->
                 <li class="slide__category"><span class="category-name">Data</span></li>
                 <!-- End::slide__category -->

                 <!-- Start::slide -->
                 <li class="slide  has-sub mt-10">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-3d-rotate side-menu__icon"></i>
                        <span class="side-menu__label">Data</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1"><a href="javascript:void(0)">Dashboards</a></li>
                        <li class="slide"><a href="index.html" class="side-menu__item">Sales</a></li>
                        
                    </ul>
                </li>
                <!-- End::slide -->

                
                 <!-- Start::slide__category -->
                 <li class="slide__category"><span class="category-name">Airtime</span></li>
                 <!-- End::slide__category -->


                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-chart-pie-4 side-menu__icon"></i>
                        <span class="side-menu__label">Airtime</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1"><a href="javascript:void(0)">Dashboards</a></li>
                        <li class="slide"><a href="index.html" class="side-menu__item">Sales</a></li>
                        
                    </ul>
                </li>
                <!-- End::slide -->

                <!-- Start::slide__category -->
                <li class="slide__category"><span class="category-name">Bills</span></li>
                <!-- End::slide__category -->
                
                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Bills Payment</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1"><a href="javascript:void(0)">Dashboards</a></li>
                        <li class="slide"><a href="index.html" class="side-menu__item">Sales</a></li>
                        
                    </ul>
                </li>
                <!-- End::slide -->

                
                 <!-- Start::slide__category -->
                 <li class="slide__category"><span class="category-name">Electricity</span></li>
                 <!-- End::slide__category -->

                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Electricity</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1"><a href="javascript:void(0)">Dashboards</a></li>
                        <li class="slide"><a href="index.html" class="side-menu__item">Sales</a></li>
                        
                    </ul>
                </li>
                <!-- End::slide -->

                 <!-- Start::slide__category -->
                 <li class="slide__category"><span class="category-name"></span></li>
                 <!-- End::slide__category -->



                <!-- Start::slide -->
                <li class="slide">
                    {{-- <a href="widgets.html" class="side-menu__item">
                        <i class="ri-apps-2-line side-menu__icon"></i>
                        <span class="side-menu__label">Logout</span>
                    </a> --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="route('logout')" class="side-menu__item"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                             <i class="ri-apps-2-line side-menu__icon"></i>
                             <span class="side-menu__label">Logout</span>
                        </a>
                       {{-- </div> --}}
                    </form>
                </li>
                <!-- End::slide -->


            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
                    height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
                </svg></div>
        </nav>
        <!-- End::nav -->

    </div>
    <!-- End::main-sidebar -->

</aside>