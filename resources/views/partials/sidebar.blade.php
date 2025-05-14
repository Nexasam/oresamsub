<aside class="app-sidebar" id="sidebar">

    {{-- style="background-color: {{ 'blue'  }};
    style="background-color: {{ 'blue'  }}; --}}
    <!-- Start::main-sidebar-header -->
    @php
       $sidebar_color =  App\Models\AdminColorSetting::where('color_name','site_admin_sidebar_color')->first(); 
       $site_logo =  App\Models\SiteImage::where('image_category','site_logo')->first();   
    @endphp
    <div class="main-sidebar-header " style="background-color: {{ $sidebar_color != NULL && $sidebar_color->color_name != '#000000' ? $sidebar_color->color_value: ''  }} ;">
        <a href="#" class="header-logo mt-3 mb-20" >
            {{-- <img src="../assets/img/brand-logos/desktop-logo.png" alt="logo" class="main-logo desktop-logo">
            <img src="../assets/img/brand-logos/toggle-logo.png" alt="logo" class="main-logo toggle-logo">
            <img src="../assets/img/brand-logos/desktop-dark.png" alt="logo" class="main-logo desktop-dark">
            <img src="../assets/img/brand-logos/toggle-dark.png" alt="logo" class="main-logo toggle-dark"> --}}
            {{-- <img src="{{ asset( env('APP_ASSETS_BASE_URL').'img/logos/logo.png') }}" alt="logo"
            class="w-14 h-16 mx-auto block dark:hidden" > --}}

            @if ($site_logo)
            <img style="max-height: 70px; max-width:75px;" src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo->image_name }}" alt="logo" class="main-logo desktop-logo">
            <img style="max-height: 70px; max-width:75px;" src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo->image_name }}" alt="logo" class="main-logo toggle-logo">
            <img style="max-height: 70px; max-width:75px;" src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo->image_name }}" alt="logo" class="main-logo desktop-dark">
            <img style="max-height: 70px; max-width:75px;" src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo->image_name }}" alt="logo" class="main-logo toggle-dark">
            {{-- <img src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo->image_name }}" alt="logo" --}}
            {{-- class="w-14 h-16 mx-auto block dark:hidden" > --}}
            @endif

            @if (! $site_logo)
            <h1 class="block text-2xl font-bold text-white dark:text-white">{{ env('APP_NAME') }}</h1>                
            @endif

            
          

        </a>
    </div>
    <!-- End::main-sidebar-header -->

    <!-- Start::main-sidebar -->
    <div class="main-sidebar"  id="sidebar-scroll" style="background-color: {{ $sidebar_color != NULL && $sidebar_color->color_name != '#000000' ? $sidebar_color->color_value : ''  }} ;">

        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <div class="slide-left" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24"
                    height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                </svg></div>
            <ul class="main-menu text-md ">
                <!-- Start::slide__category -->
                <li class="slide__category"><span class="category-name">Main</span></li>
                <!-- End::slide__category -->

                <!-- Start::slide -->
                <li class="slide  has-sub ">
                    <a href="{{ route('dashboard') }}" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard </span>
                        {{-- <i class="ri ri-arrow-right-s-line side-menu__angle"></i> --}}
                    </a>
                    <ul class="slide-menu child1">
                        <li class="slide side-menu__label1"><a href="#">Dashboard</a></li>
                        {{-- <li class="slide"><a href="index.html" class="side-menu__item">Sales</a></li> --}}
                        
                    </ul>
                </li>
                <!-- End::slide -->

                @if (strtolower(auth()->user()->role->role_name) == 'admin')

                <li class="slide__category"><span class="category-name">Customer Modules</span></li>
                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="{{ route('user.data.buy_data') }}" class="side-menu__item">
                        <i class="ti ti-device-mobile side-menu__icon"></i>
                        <span class="side-menu__label">Data</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                 </li>
                 <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide  has-sub">
                <a href="{{ route('user.airtime.buy_airtime') }}" class="side-menu__item">
                    <i class="ti ti-phone-call  side-menu__icon"></i>
                    <span class="side-menu__label">Airtime</span>
                    <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                </a>
                <ul class="slide-menu child1">
                    {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                </ul>
                </li>
                <!-- End::slide -->

                  <!-- Start::slide -->
                  <li class="slide  has-sub">
                    <a href="{{ route('user.cable_subscription.buy_cable_subscription') }}" class="side-menu__item">
                        <i class="ti ti-device-tv side-menu__icon"></i>
                        <span class="side-menu__label">Cable</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                 </li>
                 <!-- End::slide -->

                  <!-- Start::slide -->
                  <li class="slide  has-sub">
                    <a href="{{ route('user.electricity.buy_electricity_subscription') }}" class="side-menu__item">
                        <i class="ti ti-recharging side-menu__icon"></i>
                        <span class="side-menu__label">Electricity</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                 </li>
                 <!-- End::slide -->

             
                


                <!-- Start::slide__category -->
                 <li class="slide__category"><span class="category-name">Admin Modules</span></li>
                 <!-- End::slide__category -->

                  <!-- Start::slide -->
                  <li class="slide  has-sub">
                    <a href="{{ route('admin.users.index') }}" class="side-menu__item">
                        <i class="ti ti-users side-menu__icon"></i>
                        <span class="side-menu__label">Users Management</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                 </li>
                 <!-- End::slide -->

                 <!-- Start::slide: for users -->
                 <li class="slide  has-sub">
                    <a href="{{ route('admin.reseller_plans.index') }}" class="side-menu__item">
                        <i class="ti ti-medal side-menu__icon"></i>
                        <span class="side-menu__label">Resellers Plans</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item"></a></li> --}}
                    </ul>
                  </li>
                <!-- End::slide -->

                  <!-- Start::slide -->
                  <li class="slide  has-sub">
                    <a href="{{ route('admin.networks.index')}}" class="side-menu__item">
                        <i class="ti ti-wifi side-menu__icon"></i>
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
                    <a href="{{ route('admin.transactions.index')}}" class="side-menu__item">
                        <i class="ti ti-exchange side-menu__icon"></i>
                        <span class="side-menu__label">Transactions</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                  </li>
                <!-- End::slide -->

                

                <!-- Start::slide -->
                <li class="slide  has-sub">
                    <a href="{{ route('admin.wallet_creditings.index')}}" class="side-menu__item">
                        <i class="ti ti-credit-card side-menu__icon"></i>
                        <span class="side-menu__label">Wallet Creditings</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide  has-sub">
                    <a href="{{ route('admin.transactions.pending_funding_transactions')}}" class="side-menu__item">
                        <i class="ti ti-credit-card side-menu__icon"></i>
                        <span class="side-menu__label">Pending Fundings</span>
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
                        <i class="ti ti-devices side-menu__icon"></i>
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
                    <a href="{{ route('admin.product_plan_categories.index')}}" class="side-menu__item">
                        <i class="ti ti-device-speaker side-menu__icon"></i>
                        <span class="side-menu__label">Plan Categories</span>
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
                        <i class="ti ti-artboard side-menu__icon"></i>
                        <span class="side-menu__label">Plans & Prices</span>
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
                        <i class="ti ti-engine side-menu__icon"></i>
                        <span class="side-menu__label">Automation Settings</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- //superadmin --}}
                        {{-- @if (auth()->user()->email == 'adebsholey4real@gmail.com') --}}
                        <li class="slide"><a href="{{ route('admin.automation.index') }}"  class="side-menu__item">Automations</a></li>  
                        <li class="slide"><a href="{{ route('admin.automation.dashboard_view','megasubplug') }}"  class="side-menu__item">MegasubPlug Plans</a></li>      

                        {{-- @endif --}}

                        {{-- <li class="slide"><a href="{{ route('admin.automation.dashboard_view','ogdams') }}"  class="side-menu__item">Ogdams</a></li>--}}
                        {{-- <li class="slide"><a href="{{ route('admin.automation.dashboard_view','ogdams_v2') }}"  class="side-menu__item">Ogdams V2</a></li>--}}
                        {{-- <li class="slide"><a href="{{ route('admin.automation.dashboard_view','megasubplug') }}"  class="side-menu__item">MegasubPlug</a></li>       --}}
                    </ul>
                    </li>
                <!-- End::slide -->


                <!-- Start::slide -->
                {{-- <li class="slide  has-sub">
                    <a href="{{ route('admin.roles.index')}}" class="side-menu__item">
                        <i class="ri-home-8-line side-menu__icon"></i>
                        <span class="side-menu__label">Authorization</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                  
                </li> --}}
                <!-- End::slide -->


                  <!-- Start::slide -->
                  {{-- in progress --}}
                  <li class="slide  has-sub">
                    <a href="{{ route('admin.addons.index')}}" class="side-menu__item">
                        <i class="ti ti-settings side-menu__icon"></i>
                        <span class="side-menu__label">Add ons</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                    </ul>
                  </li>
                  <!-- End::slide -->

                <!-- Start::slide -->
                <li class="slide  has-sub">
                    <a href="{{ route('admin.settings.index')}}" class="side-menu__item">
                        <i class="ti ti-settings side-menu__icon"></i>
                        <span class="side-menu__label">Settings</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->
             


                @else

                {{-- ///USER PAGES HERE --}}

                <li class="slide__category"><span class="category-name">Modules</span></li>
                

                <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="{{ route('user.wallet.index')}}" class="side-menu__item">
                        <i class="ti ti-report-money  side-menu__icon"></i>
                        <span class="side-menu__label">Fund Wallet</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                  </li>
                <!-- End::slide -->


                

                <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="{{ route('user.data.buy_data') }}" class="side-menu__item">
                        <i class="ti ti-device-mobile side-menu__icon"></i>
                        <span class="side-menu__label">Data</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                 </li>
                  <!-- End::slide -->

                 

                <!-- Start::slide -->
                   <li class="slide  has-sub">
                    <a href="{{ route('user.airtime.buy_airtime')}}" class="side-menu__item">
                        <i class="ti ti-phone-call  side-menu__icon"></i>
                        <span class="side-menu__label">Airtime</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                  </li>
                <!-- End::slide -->

                  <!-- Start::slide -->
                  <li class="slide  has-sub">
                    <a href="{{ route('user.cable_subscription.buy_cable_subscription')}}" class="side-menu__item">
                        <i class="ti ti-device-tv  side-menu__icon"></i>
                        <span class="side-menu__label">Cable Subscription</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                  </li>
                <!-- End::slide -->


                <!-- Start::slide -->
                <li class="slide  has-sub">
                    <a href="{{ route('user.electricity.buy_electricity_subscription')}}" class="side-menu__item">
                        <i class="ti ti-recharging  side-menu__icon"></i>
                        <span class="side-menu__label">Electricity Subscription</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                </li>
                <!-- End::slide -->
    

                 <!-- Start::slide -->
                 <li class="slide  has-sub">
                    <a href="{{ route('user.transactions.index')}}" class="side-menu__item">
                        <i class="ti ti-exchange side-menu__icon"></i>
                        <span class="side-menu__label">Transactions</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                        {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                    </ul>
                  </li>
                <!-- End::slide -->

                @if (env('APP_NAME') == 'FoxDataHub' || env('APP_NAME') == 'OresamSub')
                     <!-- Start::slide -->
                    <li class="slide  has-sub">
                        <a href="{{ route('user.api.docs')}}" class="side-menu__item">
                            <i class="ti ti-briefcase  side-menu__icon"></i>
                            <span class="side-menu__label">API Docs</span>
                            <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                        </a>
                        <ul class="slide-menu child1">
                            {{-- <li class="slide"><a href="#" class="side-menu__item">View Networks</a></li> --}}
                        </ul>
                    </li>
                    <!-- End::slide -->  
                @endif
             

                 <!-- Start::slide: for users -->
                 <li class="slide  has-sub">
                    <a href="{{ route('user.settings.index') }}" class="side-menu__item">
                        <i class="ti ti-settings side-menu__icon"></i>
                        <span class="side-menu__label">User Settings</span>
                        <i class="ri ri-arrow-right-s-line side-menu__angle"></i>
                    </a>
                    <ul class="slide-menu child1">
                       
                    </ul>
                </li>
                <!-- End::slide -->
                


                    
                @endif

                
               
                 
             

                
               
              

                <!-- Start::slide -->
                <li class="slide">
                
                    {{-- <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a type="button" href="" class="side-menu__item"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                             <i class="ri-apps-2-line side-menu__icon"></i>
                             <span class="side-menu__label">Logout</span>
                        </a>
                      
                    </form> --}}

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                       <div class="flex items-center">
                        <i class="ti ti-logout text-white mr-3"></i>
                        <a type="button" href="" onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            
                             <span class="side-menu__label text-white">Logout</span>
                        </a>
                       </div>
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