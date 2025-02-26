@php
$sidebar_color =  App\Models\AdminColorSetting::where('color_name','site_admin_sidebar_color')->first(); 
$site_logo =  App\Models\SiteImage::where('image_category','site_logo')->first();   
@endphp


{{-- FOR MOBILE SCREEN --}}
<nav class="block col-span-12 md:hidden bg-white border-gray-200 ">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
      <div class="flex items-center">
        <a href="https://flowbite.com/" class="flex items-center space-x-3 rtl:space-x-reverse">
            @if ($site_logo)
            <img class="w-12 h-14 md:w-20 md:mx-auto pb-2" src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo->image_name }}" alt="">
    
            @else
            <b>{{env('APP_NAME')}}</b>
            @endif
    
          </a>
          <h3 class=" font-normal text-xs">Welcome back, {{ $user->first_name}}</h3>
      </div>
      <button data-collapse-toggle="navbar-default" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 " aria-controls="navbar-default" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
          </svg>
      </button>
      <div class="hidden w-full  md:block md:w-auto" id="navbar-default">
        <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-white md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white ">
          <li>
            <a href="{{ route('dashboard') }}" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-[{{$site_primary_color}}] md:p-0 {{ Route::currentRouteName() == 'dashboard' ? 'bg-[$site_primary_color] text-white rounded-lg' : '' }}">Home</a>
          </li>
          <li>
            <a href="{{ route('user.data.buy_data') }}" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 {{ Route::currentRouteName() == 'user.data.buy_data' ? 'bg-[$site_primary_color] text-white rounded-lg' : '' }}">Data</a>
          </li>
          <li>
            <a href="{{ route('user.airtime.buy_airtime') }}" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-[{{$site_primary_color}}] md:p-0  {{ Route::currentRouteName() == 'user.airtime.buy_airtime' ? 'bg-[$site_primary_color] text-white rounded-lg' : '' }}">Airtime</a>
          </li>
          <li>
            <a href="{{ route('user.cable_subscription.buy_cable_subscription') }}" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-[{{$site_primary_color}}] md:p-0  {{ Route::currentRouteName() == 'user.cable_subscription.buy_cable_subscription' ? 'bg-[$site_primary_color] text-white rounded-lg' : '' }} ">Cable TV</a>
          </li>
          <li>
            <a href="{{ route('user.electricity.buy_electricity_subscription') }}" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-[{{$site_primary_color}}] md:p-0 {{ Route::currentRouteName() == 'user.electricity.buy_electricity_subscription' ? 'bg-[$site_primary_color] text-white rounded-lg' : '' }}  ">Electricity</a>
          </li>
          <li>
            <a href="{{ route('user.transactions.index') }}" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-[{{$site_primary_color}}] md:p-0  {{ Route::currentRouteName() == 'user.transactions.index' ? 'bg-[$site_primary_color] text-white rounded-lg' : '' }}">Transactions</a>
          </li>
          
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <li  class="flex items-center justify-start cursor-pointer py-2 hover:bg-[{{$site_primary_color}}] hover:text-white  ">

            
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.83344 6.41658C6.83344 6.69273 7.0573 6.91658 7.33344 6.91658C7.60958 6.91658 7.83344 6.69273 7.83344 6.41658H6.83344ZM7.83344 15.5833C7.83344 15.3071 7.60958 15.0833 7.33344 15.0833C7.0573 15.0833 6.83344 15.3071 6.83344 15.5833H7.83344ZM8.33446 19.9668L8.56145 19.5213H8.56145L8.33446 19.9668ZM7.53326 19.1656L7.08776 19.3926H7.08776L7.53326 19.1656ZM18.1336 19.1656L18.5791 19.3926L18.1336 19.1656ZM17.3324 19.9668L17.1054 19.5213H17.1054L17.3324 19.9668ZM17.3324 2.03307L17.1054 2.47858L17.1054 2.47858L17.3324 2.03307ZM18.1336 2.83427L17.6881 3.06126V3.06126L18.1336 2.83427ZM7.53326 2.83427L7.08776 2.60727V2.60727L7.53326 2.83427ZM8.33446 2.03307L8.10746 1.58757V1.58757L8.33446 2.03307ZM2.7501 10.4999C2.47396 10.4999 2.2501 10.7238 2.2501 10.9999C2.2501 11.2761 2.47396 11.4999 2.7501 11.4999L2.7501 10.4999ZM12.8334 11.4999C13.1096 11.4999 13.3334 11.2761 13.3334 10.9999C13.3334 10.7238 13.1096 10.4999 12.8334 10.4999V11.4999ZM4.93699 8.60347C5.13225 8.40821 5.13225 8.09163 4.93699 7.89637C4.74173 7.7011 4.42515 7.7011 4.22988 7.89636L4.93699 8.60347ZM2.87053 9.96283L2.51698 9.60927H2.51698L2.87053 9.96283ZM2.87053 12.037L2.51697 12.3906L2.51697 12.3906L2.87053 12.037ZM4.22989 14.1035C4.42515 14.2987 4.74173 14.2987 4.93699 14.1035C5.13225 13.9082 5.13225 13.5916 4.93699 13.3964L4.22989 14.1035ZM2.258 10.7167L2.73353 10.8712H2.73353L2.258 10.7167ZM2.258 11.2832L2.73353 11.1287L2.258 11.2832ZM7.83344 6.41658V4.76659H6.83344V6.41658H7.83344ZM10.2668 2.33325H15.4001V1.33325H10.2668V2.33325ZM17.8334 4.76659V17.2333H18.8334V4.76659H17.8334ZM15.4001 19.6666H10.2668V20.6666H15.4001V19.6666ZM7.83344 17.2333V15.5833H6.83344V17.2333H7.83344ZM10.2668 19.6666C9.74514 19.6666 9.38399 19.6662 9.10336 19.6433C8.82857 19.6208 8.6753 19.5793 8.56145 19.5213L8.10746 20.4123C8.38578 20.5541 8.68529 20.6124 9.02193 20.6399C9.35272 20.667 9.76164 20.6666 10.2668 20.6666V19.6666ZM6.83344 17.2333C6.83344 17.7384 6.83305 18.1473 6.86008 18.4781C6.88758 18.8147 6.94595 19.1142 7.08776 19.3926L7.97876 18.9386C7.92075 18.8247 7.87921 18.6715 7.85676 18.3967C7.83383 18.116 7.83344 17.7549 7.83344 17.2333H6.83344ZM8.56145 19.5213C8.31057 19.3934 8.10659 19.1895 7.97876 18.9386L7.08776 19.3926C7.31146 19.8316 7.66842 20.1886 8.10746 20.4123L8.56145 19.5213ZM17.8334 17.2333C17.8334 17.7549 17.833 18.116 17.8101 18.3967C17.7877 18.6715 17.7461 18.8247 17.6881 18.9386L18.5791 19.3926C18.7209 19.1142 18.7793 18.8147 18.8068 18.4781C18.8338 18.1473 18.8334 17.7384 18.8334 17.2333H17.8334ZM15.4001 20.6666C15.9052 20.6666 16.3142 20.667 16.645 20.6399C16.9816 20.6124 17.2811 20.5541 17.5594 20.4123L17.1054 19.5213C16.9916 19.5793 16.8383 19.6208 16.5635 19.6433C16.2829 19.6662 15.9217 19.6666 15.4001 19.6666V20.6666ZM17.6881 18.9386C17.5603 19.1895 17.3563 19.3934 17.1054 19.5213L17.5594 20.4123C17.9985 20.1886 18.3554 19.8316 18.5791 19.3926L17.6881 18.9386ZM15.4001 2.33325C15.9217 2.33325 16.2829 2.33364 16.5635 2.35657C16.8383 2.37902 16.9916 2.42057 17.1054 2.47858L17.5594 1.58757C17.2811 1.44576 16.9816 1.38739 16.645 1.35989C16.3142 1.33286 15.9052 1.33325 15.4001 1.33325V2.33325ZM18.8334 4.76659C18.8334 4.26145 18.8338 3.85254 18.8068 3.52174C18.7793 3.18511 18.7209 2.88559 18.5791 2.60727L17.6881 3.06126C17.7461 3.17512 17.7877 3.32838 17.8101 3.60317C17.833 3.8838 17.8334 4.24495 17.8334 4.76659H18.8334ZM17.1054 2.47858C17.3563 2.60641 17.5603 2.81038 17.6881 3.06126L18.5791 2.60727C18.3554 2.16823 17.9985 1.81127 17.5594 1.58757L17.1054 2.47858ZM7.83344 4.76659C7.83344 4.24495 7.83383 3.8838 7.85676 3.60317C7.87921 3.32838 7.92075 3.17512 7.97876 3.06126L7.08776 2.60727C6.94595 2.88559 6.88758 3.18511 6.86008 3.52174C6.83305 3.85254 6.83344 4.26145 6.83344 4.76659H7.83344ZM10.2668 1.33325C9.76164 1.33325 9.35272 1.33286 9.02193 1.35989C8.68529 1.38739 8.38578 1.44576 8.10746 1.58757L8.56145 2.47858C8.6753 2.42057 8.82857 2.37902 9.10336 2.35657C9.38399 2.33364 9.74514 2.33325 10.2668 2.33325V1.33325ZM7.97876 3.06126C8.10659 2.81038 8.31057 2.60641 8.56145 2.47858L8.10746 1.58757C7.66842 1.81127 7.31146 2.16823 7.08776 2.60727L7.97876 3.06126ZM2.7501 11.4999L12.8334 11.4999V10.4999L2.7501 10.4999L2.7501 11.4999ZM4.22988 7.89636L2.51698 9.60927L3.22408 10.3164L4.93699 8.60347L4.22988 7.89636ZM2.51697 12.3906L4.22989 14.1035L4.93699 13.3964L3.22408 11.6835L2.51697 12.3906ZM2.51698 9.60927C2.3413 9.78495 2.18801 9.93769 2.07247 10.0738C1.95313 10.2144 1.84499 10.3697 1.78247 10.5621L2.73353 10.8712C2.73901 10.8543 2.75563 10.8142 2.83481 10.721C2.91777 10.6232 3.03674 10.5037 3.22408 10.3164L2.51698 9.60927ZM3.22408 11.6835C3.03674 11.4961 2.91778 11.3766 2.83481 11.2789C2.75563 11.1856 2.73901 11.1455 2.73353 11.1287L1.78247 11.4377C1.84499 11.6301 1.95314 11.7855 2.07247 11.926C2.18801 12.0621 2.3413 12.2149 2.51697 12.3906L3.22408 11.6835ZM1.78247 10.5621C1.69002 10.8467 1.69002 11.1532 1.78247 11.4377L2.73353 11.1287C2.70634 11.045 2.70634 10.9548 2.73353 10.8712L1.78247 10.5621Z" fill="#0F172A"/>
                </svg>

                    <a type="button" onclick="event.preventDefault(); this.closest('form').submit();" class="ml-3">
                        Sign out
                    </a>

            </li>

        </form>
          
        </ul>
      </div>
    </div>
  </nav>

<div class="grid grid-cols-12 md:h-20 border-t border-b border-r border-gray-300" >
        
    <!-- LOGO -->

    

  
    <div   class="hidden px-2 md:px-0 md:border-r md:border-gray-300 col-span-0 md:col-span-2 w-full md:flex items-center justify-between md:justify-start ">
        <div class="flex items-center justify-start md:hidden">
            <h3 class=" font-bold text-xs">Hello, {{ $user->first_name. ' '. $user->last_name }},</h3>
            {{-- <p class=" font-light text-xs">Welcome to {{env('APP_NAME')}}</p> --}}
        </div>
        {{-- <div class="block md:hidden">
            
            <div class="relative">
                <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26.25 22.5H3.75V20H26.25V22.5ZM26.25 16.25H3.75V13.75H26.25V16.25ZM26.25 10H3.75V7.5H26.25V10Z" fill="#141BD7"/>
                </svg>

                 <ul class=" absolute inset-0 bg-gray-500 rounded-lg w-full p-3 text-xs">
                    <li>Dashboard</li>
                    <li>Data</li>
                    <li>Airtime</li>
                    <li>Cable</li>
                    <li>Electricity</li>
                </ul>
                
            </div>
        </div> --}}

       


       
        @if ($site_logo)
            <img class="w-12 md:w-20 md:mx-auto pb-2" src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo->image_name }}" alt="">
            
        @else
            <b>{{env('APP_NAME')}}</b>
        @endif

    </div>

    

    
    <!-- RIGHT NAV -->
    <div class="col-span-6 md:flex  md:col-span-10  items-center justify-between md:justify-between md:pl-10 md:pr-1">
        <!-- HELLO USER -->
        <div class="hidden md:block">
            <h3 class=" font-bold text-md">Hello, {{ $user->first_name. ' '. $user->last_name }},</h3>
            <p class=" font-light text-xs">Welcome to {{env('APP_NAME')}}</p>
        </div>

        <!-- SEARCH -->
        {{-- <div class="hidden md:block md:w-1/2 md:mx-auto">
                            <form class="">
                                        <label for="simple-search" class="sr-only">Search</label>
                                        <div class="relative w-full">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <!-- dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-[{{$site_primary_color}}] dark:focus:border-[{{$site_primary_color}}] -->
                                            <input type="text" id="simple-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full pl-10 p-2 " placeholder="Search by Name or ID" required="">
                                        </div>
                            </form>
        </div> --}}

        <!-- NOTIFICATION AND ACCOUNT AREA -->
        <div  class="hidden md:flex items-center justify-between space-x-1 md:space-x-10">

            <!-- dark:bg-[{{$site_primary_color}}] dark:hover:bg-[{{$site_primary_color}}] dark:focus:ring-[{{$site_primary_color}}] -->
            {{-- <button @click="open = !open" type="button" class=" relative inline-flex items-center p-2 text-sm font-medium text-center text-white bg-transparent rounded-lg hover:bg-[{{$site_primary_color}}] focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]">
        
                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.38056 21C10.0857 21.6224 11.0119 22 12.0264 22C13.0408 22 13.9671 21.6224 14.6722 21M18.0264 8C18.0264 6.4087 17.3942 4.88258 16.269 3.75736C15.1438 2.63214 13.6177 2 12.0264 2C10.4351 2 8.90896 2.63214 7.78375 3.75736C6.65853 4.88258 6.02639 6.4087 6.02639 8C6.02639 11.0902 5.24686 13.206 4.37605 14.6054C3.64151 15.7859 3.27424 16.3761 3.28771 16.5408C3.30262 16.7231 3.34125 16.7926 3.48816 16.9016C3.62084 17 4.21898 17 5.41524 17H18.6375C19.8338 17 20.4319 17 20.5646 16.9016C20.7115 16.7926 20.7501 16.7231 20.7651 16.5408C20.7785 16.3761 20.4113 15.7859 19.6767 14.6054C18.8059 13.206 18.0264 11.0902 18.0264 8Z" stroke="#E5E7EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>


                <span class="sr-only" >Notifications</span>

                <!-- dark:border-gray-900 -->
                <div class="absolute inline-flex items-center justify-center w-4 h-4 p-2 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full top-0 end-0 ">4</div>
            </button> --}}

       
              <div class=" flex items-center justify-between md:justify-center space-x-1">
                <div class="hidden md:block"> <span class="font-bold text-sm  text-gray-900">{{ $user->first_name  }}</span></div>

                <button>
                    <svg width="19" height="18" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9583 15.3462C9.0907 15.3462 5.78784 15.931 5.78784 18.2729C5.78784 20.6148 9.06975 21.2205 12.9583 21.2205C16.8259 21.2205 20.1278 20.6348 20.1278 18.2938C20.1278 15.9529 16.8469 15.3462 12.9583 15.3462Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9583 12.0059C15.4964 12.0059 17.5535 9.94779 17.5535 7.40969C17.5535 4.8716 15.4964 2.81445 12.9583 2.81445C10.4202 2.81445 8.36209 4.8716 8.36209 7.40969C8.35352 9.93922 10.3973 11.9973 12.9259 12.0059H12.9583Z" stroke="#130F26" stroke-width="1.42857" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <button>
                        <svg width="19" height="18" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.9736 8.5L12.9736 15.5L5.97363 8.5" stroke="#8C8C8C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                </button>

              </div>



        </div>
    </div>

 </div>