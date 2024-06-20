<aside class="app-sidebar" id="sidebar">

    <!-- Start::main-sidebar-header -->
    <div class="main-sidebar-header ">
        <a href="#" class="header-logo mt-3 mb-20">
            {{-- <img src="../assets/img/brand-logos/desktop-logo.png" alt="logo" class="main-logo desktop-logo">
            <img src="../assets/img/brand-logos/toggle-logo.png" alt="logo" class="main-logo toggle-logo">
            <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="main-logo desktop-dark">
            <img src="../assets/img/brand-logos/toggle-dark.png" alt="logo" class="main-logo toggle-dark"> --}}
            <img src="{{ asset( env('APP_ASSETS_BASE_URL').'img/logos/Crystalpay.png') }}" alt="logo"
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
                    <a href="{{ route('dashboard') }}" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                        {{-- <i class="ri ri-arrow-right-s-line side-menu__angle"></i> --}}
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1"><a href="#">Dashboard</a></li>
                        {{-- <li class="slide"><a href="index.html" class="side-menu__item">Sales</a></li> --}}
                        
                    </ul>
                </li>
                <!-- End::slide -->

                 <!-- Start::slide__category -->
                 <li class="slide__category"><span class="category-name">Modules</span></li>
                 <!-- End::slide__category -->

               
                <!-- Start::slide -->
                   <li class="slide  has-sub mt-10">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-3d-rotate side-menu__icon"></i>
                        <span class="side-menu__label">Users Management</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a href="{{ route('admin.users.index') }}"  class="side-menu__item">Users</a></li>
                        <li class="slide"><a href="{{ route('admin.users.create') }}" class="side-menu__item">Create User</a></li>
                        
                    </ul>
                    </li>
                <!-- End::slide -->

                  <!-- Start::slide: for users -->
                  <li class="slide  has-sub">
                    <a href="{{ route('admin.reseller_plans.index') }}" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Resellers Plans</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item"></a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->

             

                 <!-- Start::slide -->
                 <li class="slide  has-sub mt-10">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-3d-rotate side-menu__icon"></i>
                        <span class="side-menu__label">Data</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a href="{{ route('user.data.buy_data') }}" class="side-menu__item">Buy Data</a></li>
                      
                    </ul>
                </li>
                <!-- End::slide -->

                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-chart-pie-4 side-menu__icon"></i>
                        <span class="side-menu__label">Airtime</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                            <li class="slide"><a href="{{ route('user.airtime.buy_airtime') }}" class="side-menu__item">Buy Airtime</a></li>
                            {{-- <li class="slide"><a href="#" class="side-menu__item">Airtime Transactions</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->
    
                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Bills Payment</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a href="#" class="side-menu__item">Pay bills</a></li>
                        <li class="slide"><a href="#" class="side-menu__item">Bills Transactions</a></li>
                    </ul>
                </li>
                <!-- End::slide -->

                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Electricity</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a href="#" class="side-menu__item">Pay electricity</a></li>
                        <li class="slide"><a href="#" class="side-menu__item">Electricity Transactions</a></li>
                        
                    </ul>
                </li>
                <!-- End::slide -->

                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">E-PIN</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a href="#" class="side-menu__item">Pay electricity</a></li>
                        <li class="slide"><a href="#" class="side-menu__item">Electricity Transactions</a></li>
                        
                    </ul>
                </li>
                <!-- End::slide -->

                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Result Checker</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide"><a href="#" class="side-menu__item">Pay electricity</a></li>
                        <li class="slide"><a href="#" class="side-menu__item">Electricity Transactions</a></li>
                        
                    </ul>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide  has-sub">
                    <a href="{{ route('admin.networks.index')}}" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Networks</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide  has-sub">
                    <a href="{{ route('admin.products.index')}}" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Products</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->

            
                <!-- Start::slide -->
                <li class="slide  has-sub">
                    <a href="{{ route('admin.product_plans.index')}}" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Plans & Prices</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->
       
                <!-- Start::slide -->
                <li class="slide  has-sub">
                    <a href="{{ route('admin.product_plan_categories.index')}}" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Plan Categories</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->

               

               



                 <!-- Start::slide: for users -->
                 <li class="slide  has-sub">
                    <a href="#" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">User Settings</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide  has-sub mt-10">
                    <a href="javascript:void(0);" class="side-menu__item">
                        <i class="ti ti-3d-rotate side-menu__icon"></i>
                        <span class="side-menu__label">Automation Settings</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        @foreach (App\Models\Automation::get() as $automation)
                         <li class="slide"><a href="{{ route('admin.automation.dashboard_view',$automation->slug) }}"  class="side-menu__item">{{ $automation->automation_name }}</a></li>      
                        @endforeach
                        {{-- <li class="slide"><a href="#" class="side-menu__item">OgDams</a></li>
                        <li class="slide"><a href="#" class="side-menu__item">Autopilot</a></li>
                        <li class="slide"><a href="#" class="side-menu__item">CloudsimHost</a></li>
                         --}}
                    </ul>
                    </li>
                <!-- End::slide -->


                <!-- Start::slide -->
                <li class="slide  has-sub">
                    <a href="{{ route('admin.settings.index')}}" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Settings</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->
             

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