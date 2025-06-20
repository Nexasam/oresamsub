<header class="header custom-sticky !top-0 !w-full">
    <nav class="main-header" aria-label="Global">
      <div class="header-content">
        <div class="header-left">
          <!-- Navigation Toggle -->
          <div class="">
            <button type="button" class="sidebar-toggle !w-100 !h-100">
              <span class="sr-only">Toggle Navigation</span>
              <i class="ri-arrow-right-circle-line header-icon"></i>
            </button>
          </div>
          <!-- End Navigation Toggle -->
        </div>

        @php
        $site_logo =  App\Models\SiteImage::where('image_category','site_logo')->first();   
       @endphp

        <div class="responsive-logo">
          @if ($site_logo)
              <a class="responsive-logo-dark" href="#" aria-label="{{env('APP_NAME')}}"><img
              src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo->image_name }}" height="50" width="60" alt="logo" class="mx-auto"></a>
              <a class="responsive-logo-light" href="#" aria-label="{{env('APP_NAME')}}"><img
              src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo->image_name }}" height="50" width="60"  alt="logo" class="mx-auto"></a>
          @endif

          @if (! $site_logo)
           <h1 class="block text-2xl font-bold text-gray-800 dark:text-gray-900">{{ env('APP_NAME') }}</h1>
          @endif
        </div>

        <div class="header-right">
          <div class="responsive-headernav">
            <div class="header-nav-right">
            
              {{-- <div class="header-search">
                <button aria-label="button" type="button" data-hs-overlay="#search-modal"
                  class="inline-flex flex-shrink-0 justify-center items-center gap-2 h-[2.375rem] w-[2.375rem] rounded-full font-medium bg-gray-100 hover:bg-gray-200 text-gray-500 align-middle focus:outline-none focus:ring-0 focus:ring-gray-400 focus:ring-offset-0 focus:ring-offset-white transition-all text-xs dark:bg-bgdark dark:hover:bg-black/20 dark:text-white/70 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                  <i class="ri-search-2-line header-icon"></i>
                </button>
              </div> --}}
              {{-- <div class="header-theme-mode hidden lg:block"> --}}
              <div class="header-theme-mode block">
                <a aria-label="anchor" class="hs-dark-mode-active:hidden flex hs-dark-mode group flex-shrink-0 justify-center items-center gap-2 h-[2.375rem] w-[2.375rem] rounded-full font-medium bg-gray-100 hover:bg-gray-200 text-gray-500 align-middle focus:outline-none focus:ring-0 focus:ring-gray-400 focus:ring-offset-0 focus:ring-offset-white transition-all text-xs dark:bg-bgdark dark:hover:bg-black/20 dark:text-white/70 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
                  href="javascript:;" data-hs-theme-click-value="dark">
                  <i class="ri-moon-line header-icon"></i>
                </a>
                <a aria-label="anchor" class="hs-dark-mode-active:flex hidden hs-dark-mode group flex-shrink-0 justify-center items-center gap-2 h-[2.375rem] w-[2.375rem] rounded-full font-medium bg-gray-100 hover:bg-gray-200 text-gray-500 align-middle focus:outline-none focus:ring-0 focus:ring-gray-400 focus:ring-offset-0 focus:ring-offset-white transition-all text-xs dark:bg-bgdark dark:hover:bg-black/20 dark:text-white/70 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
                  href="javascript:;" data-hs-theme-click-value="light">
                  <i class="ri-sun-line header-icon"></i>
                </a>
              </div>
              <div class="header-fullscreen hidden lg:block">
                <a aria-label="anchor" href="javascript:void(0);" onclick="openFullscreen();"
                  class="inline-flex flex-shrink-0 justify-center items-center gap-2 h-[2.375rem] w-[2.375rem] rounded-full font-medium bg-gray-100 hover:bg-gray-200 text-gray-500 align-middle focus:outline-none focus:ring-0 focus:ring-gray-400 focus:ring-offset-0 focus:ring-offset-white transition-all text-xs dark:bg-bgdark dark:hover:bg-black/20 dark:text-white/70 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10">
                  <i class="ri-fullscreen-line header-icon full-screen-open"></i>
                  <i class="ri-fullscreen-line header-icon fullscreen-exit-line full-screen-close hidden"></i>
                </a>
              </div>
            
              {{-- <div class="header-notification hs-dropdown ti-dropdown hidden sm:block"
                data-hs-dropdown-placement="bottom-right">
                <button id="dropdown-notification" type="button"
                  class="hs-dropdown-toggle ti-dropdown-toggle p-0 border-0 flex-shrink-0 h-[2.375rem] w-[2.375rem] rounded-full shadow-none focus:ring-gray-400 text-xs dark:focus:ring-white/10">
                  <i class="ri-notification-2-line header-icon animate-bell"></i>
                  <span class="flex absolute h-5 w-5 top-0 end-0 -mt-1 -me-1">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success/80 opacity-75"></span>
                    <span
                      class="relative inline-flex rounded-full h-5 w-5 bg-success text-white justify-center items-center" id="notify-data">4</span>
                  </span>
                </button>
                <div class="hs-dropdown-menu ti-dropdown-menu w-[20rem] border-0" aria-labelledby="dropdown-notification">
                  <div class="ti-dropdown-header !bg-primary border-b dark:border-white/10 flex justify-between items-center">
                    <p class="ti-dropdown-header-title !text-white font-semibold">Notifications</p>
                    <a href="javascript:void(0)" class="badge bg-black/20 text-white rounded-sm">Mark All Read</a>
                  </div>
                  <div class="ti-dropdown-divider divide-y divide-gray-200 dark:divide-white/10">
                    <div class="py-2 first:pt-0 last:pb-0" id="allNotifyContainer">
                      <div class="ti-dropdown-item relative header-box">
                        <a href="mail-inbox.html" class="flex space-x-3 rtl:space-x-reverse">
                          <div class="me-2 avatar rounded-full ring-0">
                            <img src="../assets/img/users/17.jpg" alt="img" class="rounded-sm">
                          </div>
                          <div class="relative w-full">
                            <h5 class="text-sm text-gray-800 dark:text-gray-900 font-semibold mb-1">Elon Isk</h5>
                            <p class="text-xs mb-1 max-w-[200px] truncate">Hello there! How are you doing? Call me when...</p>
                            <p class="text-xs text-gray-400 dark:text-white/70">2 min</p>
                          </div>
                        </a>
                        <a aria-label="anchor" href="javascript:void(0);" class="header-remove-btn ms-auto text-lg text-gray-500/20 dark:text-gray-900/20 hover:text-gray-800 dark:hover:text-white">
                          <i class="ri-close-circle-line"></i>
                        </a>
                      </div>
                      <div class="ti-dropdown-item relative header-box">
                        <a href="mail-inbox.html" class="flex items-center space-x-3 rtl:space-x-reverse">
                          <div class="me-2 avatar rounded-full ring-0">
                            <img src="../assets/img/users/2.jpg" alt="img" class="rounded-sm">
                          </div>
                          <div class="relative w-full">
                            <h5 class="text-sm text-gray-800 dark:text-gray-900 font-semibold mb-1">Shakira Sen</h5>
                            <p class="text-xs mb-1 max-w-[200px] truncate">I would like to discuss about that assets...</p>
                            <p class="text-xs text-gray-400 dark:text-white/70">09:43</p>
                          </div>
                        </a>
                        <a aria-label="anchor" href="javascript:void(0);" class="header-remove-btn ms-auto text-lg text-gray-500/20 dark:text-gray-900/20 hover:text-gray-800 dark:hover:text-white">
                          <i class="ri-close-circle-line"></i>
                        </a>
                      </div>
                      <div class="ti-dropdown-item relative header-box">
                        <a href="mail-inbox.html" class="flex items-center space-x-3 rtl:space-x-reverse">
                          <div class="me-2 avatar rounded-full ring-0">
                            <img src="../assets/img/users/21.jpg" alt="img" class="rounded-sm">
                          </div>
                          <div class="relative w-full">
                            <h5 class="text-sm text-gray-800 dark:text-gray-900 font-semibold mb-1">Sebastian</h5>
                            <p class="text-xs mb-1 max-w-[200px] truncate">Shall we go to the cafe at downtown...</p>
                            <p class="text-xs text-gray-400 dark:text-white/70">yesterday</p>
                          </div>
                        </a>
                        <a aria-label="anchor" href="javascript:void(0);" class="header-remove-btn ms-auto text-lg text-gray-500/20 dark:text-gray-900/20 hover:text-gray-800 dark:hover:text-white">
                          <i class="ri-close-circle-line"></i>
                        </a>
                      </div>
                      <div class="ti-dropdown-item relative header-box">
                        <a href="mail-inbox.html" class="flex items-center space-x-3 rtl:space-x-reverse">
                          <div class="me-2 avatar rounded-full ring-0">
                            <img src="../assets/img/users/11.jpg" alt="img" class="rounded-sm">
                          </div>
                          <div class="relative w-full">
                            <h5 class="text-sm text-gray-800 dark:text-gray-900 font-semibold mb-1">Charlie Davieson</h5>
                            <p class="text-xs mb-1 max-w-[200px] truncate">Lorem ipsum dolor sit amet, consectetur</p>
                            <p class="text-xs text-gray-400 dark:text-white/70">yesterday</p>
                          </div>
                        </a>
                        <a aria-label="anchor" href="javascript:void(0);" class="header-remove-btn ms-auto text-lg text-gray-500/20 dark:text-gray-900/20 hover:text-gray-800 dark:hover:text-white">
                          <i class="ri-close-circle-line"></i>
                        </a>
                      </div>
                    </div>
                    <div class="py-2 first:pt-0 px-5">
                      <a class="w-full ti-btn ti-btn-primary p-2" href="mail-inbox.html">
                        View All
                      </a>
                    </div>
                  </div>
                </div> 
              </div> --}}
              {{-- <div class="header-apps hs-dropdown ti-dropdown hidden md:block" data-hs-dropdown-placement="bottom-right">
                <button aria-label="button" id="dropdown-apps" type="button"
                  class="hs-dropdown-toggle ti-dropdown-toggle p-0 border-0 flex-shrink-0 h-[2.375rem] w-[2.375rem] rounded-full shadow-none focus:ring-gray-400 text-xs dark:focus:ring-white/10">
                  <i class="ri-bookmark-line header-icon"></i>
                </button>
                <div class="hs-dropdown-menu ti-dropdown-menu w-[20rem] border-0" aria-labelledby="dropdown-apps">
                  <div
                    class="ti-dropdown-header !bg-primary border-b dark:border-white/10 flex justify-between items-center text-center">
                    <p class="ti-dropdown-header-title font-semibold !text-white">Related Apps</p>
                  </div>
                  <div class="ti-dropdown-divider divide-y divide-gray-200 dark:divide-white/10">
                    <div class="grid grid-cols-3 gap-0 p-4 pt-2">
                      <a href="mail-inbox.html" class="block pt-0 p-2 text-center rounded-sm hover:bg-gray-50 dark:hover:bg-black/20">
                        <i class="ri ri-mail-line leading-none text-2xl avatar ring-0 bg-primary/20 text-primary rounded-sm p-3 my-3 align-middle flex justify-center mx-auto"></i>
                        <div class="text-xs font-semibold text-gray-800 dark:text-gray-900">Mail Inbox</div>
                      </a>
                      <a href="chat.html" class="block pt-0 p-2 text-center rounded-sm hover:bg-gray-50 dark:hover:bg-black/20">
                        <i class="ri ri-chat-2-line leading-none text-2xl avatar ring-0 bg-secondary/20 text-secondary rounded-sm p-3 my-3 align-middle flex justify-center mx-auto"></i>
                        <div class="text-xs font-semibold text-gray-800 dark:text-gray-900">Chat</div>
                      </a>
                      <a href="tasks.html" class="block pt-0 p-2 text-center rounded-sm hover:bg-gray-50 dark:hover:bg-black/20">
                        <i class="ri ri-task-line leading-none text-2xl avatar ring-0 bg-warning/20 text-warning rounded-sm p-3 my-3 align-middle flex justify-center mx-auto"></i>
                        <div class="text-xs font-semibold text-gray-800 dark:text-gray-900">Task</div>
                      </a>
                      <a href="calendar.html" class="block pt-0 p-2 text-center rounded-sm hover:bg-gray-50 dark:hover:bg-black/20">
                        <i class="ri ri-calendar-event-line leading-none text-2xl avatar ring-0 bg-danger/20 text-danger rounded-sm p-3 my-3 align-middle flex justify-center mx-auto"></i>
                        <div class="text-xs font-semibold text-gray-800 dark:text-gray-900">Calendar</div>
                      </a>
                      <a href="filemanager.html" class="block pt-0 p-2 text-center rounded-sm hover:bg-gray-50 dark:hover:bg-black/20">
                        <i class="ri ri-file-copy-2-line leading-none text-2xl avatar ring-0 bg-info/20 text-info rounded-sm p-3 my-3 align-middle flex justify-center mx-auto"></i>
                        <div class="text-xs font-semibold text-gray-800 dark:text-gray-900">FileManager</div>
                      </a>
                      <a href="contacts.html" class="block pt-0 p-2 text-center rounded-sm hover:bg-gray-50 dark:hover:bg-black/20">
                        <i class="ri ri-group-line leading-none text-2xl avatar ring-0 bg-success/20 text-success rounded-sm p-3 my-3 align-middle flex justify-center mx-auto"></i>
                        <div class="text-xs font-semibold text-gray-800 dark:text-gray-900">Contacts</div>
                      </a>
                    </div>
                    <div class="py-2 first:pt-0 px-5">
                      <a class="w-full ti-btn ti-btn-primary p-2" href="javascript:void(0);">
                        View All
                      </a>
                    </div>
                  </div>
                </div>
              </div> --}}
              <div class="header-profile hs-dropdown ti-dropdown" data-hs-dropdown-placement="bottom-right">
                <button id="dropdown-profile" type="button"
                  class="hs-dropdown-toggle ti-dropdown-toggle gap-2 p-0 flex-shrink-0 h-8 w-8 rounded-full shadow-none focus:ring-gray-400 text-xs dark:focus:ring-white/10">
                  <img class="inline-block rounded-full ring-2 ring-white dark:ring-white/10"
                    src="{{ asset(env('APP_ASSETS_BASE_URL').'img/users/default_profile.jpg ') }}" alt="Image Description">
                </button>

                <div class="hs-dropdown-menu ti-dropdown-menu border-0 w-[20rem]" aria-labelledby="dropdown-profile">
                  <div class="ti-dropdown-header !bg-primary flex">
                    <div class="me-3">
                      <img class="avatar shadow-none rounded-full !ring-transparent"
                        src="{{ asset(env('APP_ASSETS_BASE_URL').'img/users/default_profile.jpg') }}"  alt="profile-img">
                    </div>
                    <div>
                      <p class="ti-dropdown-header-title !text-white">{{  auth()->user()->username  }}</p>
                      <p class="ti-dropdown-header-content !text-white/50">{{  auth()->user()->first_name.' '.auth()->user()->last_name  }}</p>
                    </div>
                  </div>
                  <div class="mt-2 ti-dropdown-divider">

                    @if ( auth()->user()->role->role_name == 'User' )
                      <a href="{{ route('user.manage_profile.index') }}" class="ti-dropdown-item">
                        <i class="ti ti-user-circle text-lg"></i>
                        {{__('messages.Profile')}}
                      </a>                     
                    @else
                      <a href="{{ route('admin.manage_profile.index') }}" class="ti-dropdown-item">
                        <i class="ti ti-user-circle text-lg"></i>
                        {{__('messages.Profile')}}

                      </a>
                    @endif
                    
                   
                    {{-- <a href="mail-inbox.html" class="ti-dropdown-item">
                      <i class="ti ti-inbox text-lg"></i>
                      Inbox
                    </a>
                    <a href="tasks.html" class="ti-dropdown-item">
                      <i class="ti ti-clipboard-check text-lg"></i>
                      Task Manager
                    </a>
                    --}}
                    @if ( auth()->user()->role->role_name == 'User' )
                      <a href="{{ route('user.settings.index') }}" class="ti-dropdown-item">
                        <i class="ti ti-adjustments-horizontal text-lg"></i>
                        {{__('messages.Settings')}}

                      </a>                        
                    @else
                      <a href="{{ route('admin.settings.index') }}" class="ti-dropdown-item">
                        <i class="ti ti-adjustments-horizontal text-lg"></i>
                        {{__('messages.Settings')}}
                      </a>
                    @endif

                    @if (auth()->user()->role->role_name == 'User')
                    <a href="#" class="ti-dropdown-item">
                      <i class="ti ti-wallet text-lg"></i>
                      {{__('messages.Main Wallet')}} : &#8358;{{ number_format(auth()->user()->main_wallet ?? 0) }}
                    </a>                        
                    @endif

                    {{-- <a href="signin.html" class="ti-dropdown-item">
                      <i class="ti ti-logout  text-lg"></i>
                      Log Out
                    </a>
                     --}}
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                     <div class="flex items-center justify-between ml-4">
                      <i class="ti ti-logout  text-lg"></i>
                      <x-dropdown-link  :href="route('logout')"
                              onclick="event.preventDefault();
                                          this.closest('form').submit();">
                          {{ __('messages.Logout') }}
                      </x-dropdown-link>
                     </div>
                  </form>
                  </div>
                </div>
              </div>
              {{-- <div class="switcher-icon">
                <button aria-label="button" type="button"
                  class="hs-dropdown-toggle inline-flex flex-shrink-0 justify-center items-center gap-2 h-[2.375rem] w-[2.375rem] rounded-full font-medium bg-gray-100 hover:bg-gray-200 text-gray-500 align-middle focus:outline-none focus-visible:outline-none focus:ring-0 focus:ring-gray-400 focus:ring-offset-0 focus:ring-offset-white transition-all text-xs dark:bg-bgdark dark:hover:bg-black/20 dark:text-white/70 dark:hover:text-white dark:focus:ring-white/10 dark:focus:ring-offset-white/10"
                  data-hs-overlay="#hs-overlay-switcher">
                  <i class="ri-settings-5-line header-icon animate-spin"></i>
                </button>
              </div> --}}
            </div>
          </div>
        </div>
      </div>
    </nav>
  </header>