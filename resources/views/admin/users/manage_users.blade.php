@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

     
             <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium"> Profile Settings for <strong>{{ $user->first_name.' '.$user->last_name }}  ({{ $user->role->role_name }})</strong></h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
                <li class="text-sm">
                  <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="{{ route('admin.users.index') }}">
                    Users
                    <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                  </a>
                </li>
                {{-- <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Profile Settings
                </li> --}}
            </ol>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-x-6">
          <div class="col-span-12">
            @if (Session::has('success'))
            <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
              Great! {{ Session::get('success') }}
              </div>
            @endif

            @if (Session::has('failure'))
              <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
               Ops! {{ Session::get('failure') }}
              </div>
            @endif
            
            @if ($errors->any())
              <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
              </div>
            @endif
          </div>
          <div class="col-span-12 xl:col-span-3">
            <div class="box">
              <div class="box-body relative">
                {{-- <div class="flex relative before:bg-black/50 before:absolute before:w-full before:h-full before:rounded-sm">
                  <img src="../assets/img/png-images/2.png" alt="" class="h-[200px] w-full rounded-sm" id="profile-img2">
                  <span class="absolute top-5 end-5 flex p-2 rounded-sm ring-1 ring-black/10 text-white bg-black/10 leading-none">
                    <i class="ri ri-pencil-line"></i>
                    <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="profile-change2">
                  </span>
                </div>
                <div class="absolute top-[4.5rem] inset-x-0 text-center space-y-3">
                  <div class="flex justify-center w-full">
                    <div class="relative">
                      <img src="../assets/img/users/1.jpg" class="w-24 h-24 rounded-full ring-4 ring-white/10 mx-auto" id="profile-img" alt="pofile-img">
                      <span class="absolute bottom-0 end-0 block p-1 rounded-full ring-2 ring-white/10 text-white bg-white/10 dark:bg-bgdark leading-none">
                        <i class="ri ri-pencil-line"></i>
                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="profile-change">
                      </span>
                    </div>
                  </div>
                </div> --}}
              </div>
              <div class="box-body pt-0">
                <nav class="flex flex-col space-y-2" aria-label="Tabs" role="tablist" data-hs-tabs-vertical="true">
                  <button type="button" class="hs-tab-active:bg-primary hs-tab-active:border-primary hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:border-primary dark:hs-tab-active:text-white -me-px py-3 px-3 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:text-white/70 active" id="profile-settings-item-1" data-hs-tab="#profile-settings-1" aria-controls="profile-settings-1" role="tab">
                    <i class="ri ri-shield-user-line"></i> Personal Settings
                  </button>
                  <button type="button" class="hs-tab-active:bg-primary hs-tab-active:border-primary hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:border-primary dark:hs-tab-active:text-white -me-px py-3 px-3 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:text-white/70 dark:hover:text-gray-300" id="profile-settings-item-2" data-hs-tab="#profile-settings-2" aria-controls="profile-settings-2" role="tab">
                    <i class="ri ri-global-line"></i> Fund Wallet
                  </button>
                  <button type="button" class="hs-tab-active:bg-primary hs-tab-active:border-primary hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:border-primary dark:hs-tab-active:text-white -me-px py-3 px-3 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:text-white/70 dark:hover:text-gray-300" id="profile-settings-item-3" data-hs-tab="#profile-settings-3" aria-controls="profile-settings-3" role="tab">
                    <i class="ri ri-global-line"></i> Reset 2FA
                  </button>
              
                  {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:border-primary hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:border-primary dark:hs-tab-active:text-white -me-px py-3 px-3 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:text-white/70 dark:hover:text-gray-300" id="profile-settings-item-3" data-hs-tab="#profile-settings-3" aria-controls="profile-settings-3" role="tab">
                    <i class="ri ri-lock-line"></i> Password Settings
                  </button>
                  <button type="button" class="hs-tab-active:bg-primary hs-tab-active:border-primary hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:border-primary dark:hs-tab-active:text-white -me-px py-3 px-3 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:text-white/70 dark:hover:text-gray-300" id="profile-settings-item-4" data-hs-tab="#profile-settings-4" aria-controls="profile-settings-4" role="tab">
                    <i class="ri ri-account-circle-line"></i> Account Settings
                  </button>
                  <button type="button" class="hs-tab-active:bg-primary hs-tab-active:border-primary hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:border-primary dark:hs-tab-active:text-white -me-px py-3 px-3 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:text-white/70 dark:hover:text-gray-300" id="profile-settings-item-5" data-hs-tab="#profile-settings-5" aria-controls="profile-settings-5" role="tab">
                    <i class="ri ri-notification-4-line"></i> Notifications Settings
                  </button> --}}
                </nav>
              </div>
            </div>
          </div>
          <div class="col-span-12 xl:col-span-9">
            <div class="box">
              <div class="box-body p-0">
                <div id="profile-settings-1" role="tabpanel" aria-labelledby="profile-settings-item-1">
                  <div class="box border-0 shadow-none mb-0">
                    <div class="box-header">
                      <h5 class="box-title leading-none flex"><i class="ri ri-shield-user-line me-2"></i> Personal Settings  </h5>
                      @php
                      $fullnameinfo = '<br><b>Virtual Accounts Generated: '.count($user->virtual_accounts).'</b></br>'; 
                      @endphp 
                      @if (count($user->virtual_accounts) > 0)
                          <div class="grid grid-cols-3">
                            @foreach($user->virtual_accounts as $va)
                              @php
                                  $fullnameinfo .= '<div>Bank Name: '.$va->bank_name.'&nbsp;';
                                  $fullnameinfo .= 'Account Name: '.$va->account_name.'&nbsp;';
                                  $fullnameinfo .= 'Account No: '.$va->account_number.'&nbsp;';
                                  $fullnameinfo .= '</div>';
                              @endphp  

                            @endforeach
                          </div>
                       
                          {!! $fullnameinfo !!}
                          
                       @endif
                    </div>
                    <div class="box-body">
                      <div>
                        {{-- <form method="POST" action="{{ route('admin.users.reset_password')  }}">
                          <input type="submit" class="ti-btn ti-btn-primary w-1/2" value="Reset Customer Password">           
                        </form> --}}

                        <form method="POST" action="{{ route('admin.users.update_user_info')  }}">
                          @csrf
                        <div class="grid lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">User Name</label>
                                <input type="text" readonly class="my-auto ti-form-input" id="username" name="username" value="{{ $user->username }}" placeholder="Username">
                            </div>
                            <div class="space-y-2">
                                <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}">

                                <label class="ti-form-label mb-0">First Name</label>
                                <input type="text" readonly class="my-auto ti-form-input" id="first_name" name="first_name" value="{{ $user->first_name }}" placeholder="Firstname">
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Last Name</label>
                                <input type="text" readonly class="my-auto ti-form-input" id="last_name" name="last_name" value="{{ $user->last_name }}" placeholder="Lastname">
                            </div>
                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Other Names</label>
                              <input type="text" readonly class="my-auto ti-form-input" id="other_names" name="other_names" value="{{ $user->other_names }}" placeholder="Othernames">
                          </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Phone Number</label>
                                @if ($user->phone_number != NULL)
                                <input readonly type="number" class="my-auto ti-form-input" id="phone_number" name="phone_number" value="{{ $user->phone_number }}" placeholder="Phone">
                                @else
                                <input  type="number" class="my-auto ti-form-input" id="phone_number" name="phone_number" value="{{ $user->phone_number }}" placeholder="Phone">
                                @endif
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Email Address</label>
                                <input type="email" readonly class="my-auto ti-form-input" id="email_address" name="email_address" value="{{ $user->email }}" placeholder="Email">
                            </div>
                            @if (env('APP_NAME') == 'OresamSub')
                            
                                <div class="space-y-2">
                                  <label class="ti-form-label mb-0">Customer Landmark: Describe a way for us to know how the business meet this customer</label>
                                  <input type="text"  class="my-auto ti-form-input" id="customer_landmark" name="customer_landmark" value="{{ $user->customer_landmark ?? NULL }}" placeholder="Landmark">
                                </div>

                                <div class="space-y-2">
                                  <label class="ti-form-label mb-0">Customer Category</label>
                                  <select required id="customer_category" name="customer_category"  class="my-auto ti-form-select">
                                    <option value="">Select</option>
                                    <option @if ($user->customer_category == 'generic') selected @endif  value="generic">Generic</option>
                                    <option @if ($user->customer_category == 'pos') selected @endif  value="pos">POS</option>
                                  </select>
                                  </div>
                            @endif


                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">PIN</label>
                              @if ($user->role->role_name == 'User')
                                <input type="number" class="my-auto ti-form-input" id="pin" name="pin" value="{{  $user->pin }}" placeholder="PIN">
                                  
                              @else
                              <input type="text" class="my-auto ti-form-input" id="pin" name="pin" value="xxxx" placeholder="PIN">
                                  
                              @endif
                            </div>
                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Upline: @if ($upline != NULL){{ $upline->email_address .' '.$upline->phone_number }}@endif</label>
                             
                              <input type="text" class="my-auto ti-form-input" id="upline_id" name="upline_id" value="@if ($upline != NULL){{ $upline->first_name .' '.$upline->last_name }}@endif" placeholder="Upline">
                           </div>
                      
                           <div class="space-y-2">
                            <label class="ti-form-label mb-0">User Plan</label>
                            <select required id="user_plan_id" name="user_plan_id"  class="my-auto ti-form-select">
                              <option value="">Select</option>
                                
                              @foreach ($user_plans as $user_plan)
                                  <option  
                                  @if ($user_plan->id == $user->user_plan_id)
                                  selected
                                  @endif 
                                  value="{{ $user_plan->id  }}">{{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name  }}</option>
                              @endforeach
                            </select>

                            <input type="submit" class="ti-btn ti-btn-primary w-1/2" value="Update User Details">           
                          
                            </div>
                            <br>
                            <br>
                           
                            {{-- <div class="space-y-2">
                                <label class="ti-form-label mb-0">Date Of Birth</label>
                                <input type="text" class="ti-form-input flatpickr-input date" placeholder="Choose date" readonly>
                            </div> --}}
                            {{-- <div class="space-y-2">
                                <label class="ti-form-label mb-0">Gender</label>
                                <ul class="flex flex-col sm:flex-row">
                                    <li class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none  sm:first:rounded-es-sm sm:last:rounded-es-none  sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                      <div class="relative flex items-start w-full">
                                        <div class="flex items-center h-5">
                                          <input id="hs-horizontal-list-group-item-radio-1" name="hs-horizontal-list-group-item-radio" type="radio" class="ti-form-radio" checked>
                                        </div>
                                        <label for="hs-horizontal-list-group-item-radio-1" class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                         Female
                                        </label>
                                      </div>
                                    </li>

                                    <li class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none  sm:first:rounded-es-sm sm:last:rounded-es-none  sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                      <div class="relative flex items-start w-full">
                                        <div class="flex items-center h-5">
                                          <input id="hs-horizontal-list-group-item-radio-2" name="hs-horizontal-list-group-item-radio" type="radio" class="ti-form-radio">
                                        </div>
                                        <label for="hs-horizontal-list-group-item-radio-2" class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                          Male
                                        </label>
                                      </div>
                                    </li>

                                    <li class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none  sm:first:rounded-es-sm sm:last:rounded-es-none  sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                      <div class="relative flex items-start w-full">
                                        <div class="flex items-center h-5">
                                          <input id="hs-horizontal-list-group-item-radio-3" name="hs-horizontal-list-group-item-radio" type="radio" class="ti-form-radio">
                                        </div>
                                        <label for="hs-horizontal-list-group-item-radio-3" class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                        Others
                                        </label>
                                      </div>
                                    </li>
                                  </ul>
                            </div> --}}
                        </div>

                        </form>
                        {{-- <div class="my-5">
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Address</label>
                                <input type="text" class="my-auto ti-form-input" placeholder="Address">
                            </div>
                        </div>
                        <div class="grid lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">City</label>
                                <input type="text" class="my-auto ti-form-input" placeholder="city">
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Country</label>
                                <input type="text" class="my-auto ti-form-input" placeholder="state">
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">State</label>
                                <input type="text" class="my-auto ti-form-input" placeholder="state">
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Pincode</label>
                                <input type="number" class="my-auto ti-form-input" placeholder="pincode">
                            </div>
                        </div>
                        <div class="mt-5">
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Bio</label>
                                <textarea class="ti-form-input" rows="3" placeholder="Add Your Bio"></textarea>
                            </div>
                        </div> --}}
                      </div>

                      <div class="py-4">
                        <hr>

                        <div class="grid mt-3">
                          <h5 class="text-base font-semibold">Update User Plan</h5>
                          <form method="POST" action="{{ route('admin.users.update_user_plan')  }}">
                               @csrf    
                               <div class="space-y-2">
                                {{-- <label class="ti-form-label mb-0">Update User Plan</label> --}}
                                <select required id="user_plan_id" name="user_plan_id"  class="my-auto ti-form-select w-1/2">
                                  <option value="">Select</option>   
                                  @foreach ($user_plans as $user_plan)
                                      <option  
                                      @if ($user_plan->id == $user->user_plan_id)
                                      selected
                                      @endif 
                                      value="{{ $user_plan->id  }}">{{ $user_plan->updated_user_plan_name ?? $user_plan->user_plan_name  }}</option>
                                  @endforeach
                                </select> 
                                <input type="hidden" id="user_id" name="user_id" value="{{$user->id}}">
                
                                <input type="submit" class="ti-btn ti-btn-primary w-1/2" value="Update plan for this user">           
                              </div>

                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="profile-settings-2" class="hidden" role="tabpanel" aria-labelledby="profile-settings-item-2">
                  <div class="box border-0 shadow-none mb-0">
                    <div class="box-header">
                      <h5 class="box-title leading-none flex"><i class="ri ri-global-line me-2"></i> Wallet Balance : &#8358;{{ number_format($user->main_wallet,2) }}</h5>
                    </div>
                    <div class="box-body">
                      <h5 class="text-base font-semibold">Fund Main Wallet</h5>
                      <form method="POST" action="{{ route('admin.users.fund_user_wallet')  }}">
                           @csrf
                            <div class="my-4">
                                <div class="grid lg:grid-cols-2 gap-6 mb-6">
                                    <div class="space-y-2">
                                        <label class="ti-form-label mb-0">Enter amount to fund</label>
                                        <input type="text" class="my-auto ti-form-input" name="amount" id="amount" placeholder="">
                                        <input type="hidden" class="my-auto ti-form-input" value="{{ $user->id }}" name="user_id" id="user_id" placeholder="">
                                    </div>
                                </div>
                                <div class="grid lg:grid-cols-2 gap-6 mb-6">
                                  <div class="space-y-2">
                                      <label class="ti-form-label mb-0">PIN</label>
                                      <input type="password" class="my-auto ti-form-input" required name="pin" id="pin" placeholder="Enter pin">
                                  </div>
                              </div>
                            </div>
    
                            <div class="my-5">
                                <button type="submit" class="ti-btn ti-btn-primary w-full">Fund</button>
                            </div>
                      </form>
                     
                 
                    </div>
                  </div>
                </div>
                <div id="profile-settings-3" class="hidden" role="tabpanel" aria-labelledby="profile-settings-item-3">
                  <div class="box border-0 shadow-none mb-0">
                    <div class="box-header">
                      <h5 class="box-title leading-none flex"><i class="ri ri-global-line me-2"></i> Wallet Balance : &#8358;{{ number_format($user->main_wallet,2) }}</h5>
                    </div>
                    <div class="box-body">
                      <h5 class="text-base font-semibold">Reset 2FA</h5>
                      <p>Currently: {{   $user->two_factor_secret == NULL && $user->two_factor_recovery_codes == NULL ? 'OFF' : 'ON' }}</p>
                      <form method="POST" action="{{ route('admin.users.reset_2fa')  }}">
                           @csrf    
                            <div class="my-2">
                              <input type="hidden" class="my-auto ti-form-input" value="{{ $user->id }}" name="user_id" id="user_id" placeholder="">
                            
                              <button type="submit" class="ti-btn ti-btn-primary w-full">Reset 2FA</button>
                            </div>
                      </form>
                     
                 
                    </div>
                  </div>
                </div>
              
                {{-- <div id="profile-settings-3" class="hidden" role="tabpanel" aria-labelledby="profile-settings-item-3">
                  <div class="box border-0 shadow-none mb-0">
                    <div class="box-header">
                      <h5 class="box-title leading-none flex"><i class="ri ri-lock-line me-2"></i> Password Settings</h5>
                    </div>
                    <div class="box-body p-0">
                      <div class="grid grid-cols-12">
                          <div class="col-span-12 xl:col-span-6 xl:border-e xl:border-b-0 border-b p-6 border-gray-200 dark:border-white/10">
                            <div class="space-y-4">
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Email Id<sup class="text-danger">*</sup></label>
                                <input type="email" class="my-auto ti-form-input" placeholder="Email Id" required>
                              </div>
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Current Password<sup class="text-danger">*</sup></label>
                                <input type="password" class="my-auto ti-form-input" autocomplete="off" placeholder="Current Password" required>
                              </div>
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">New Password<sup class="text-danger">*</sup></label>
                                <input type="password" class="my-auto ti-form-input" autocomplete="off" placeholder="New Password" required>
                              </div>
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Confirm Password<sup class="text-danger">*</sup></label>
                                <input type="password" class="my-auto ti-form-input" autocomplete="off" placeholder="Confirm Password" required>
                              </div>
                              <p class="text-xs text-gray-500 dark:text-white/70">Password should be min of <b class="text-success">10 characters <sup>*</sup> </b> (and up to 100 characters),<b class="text-success">One Upper Case Character<sup>*</sup></b> and <b class="text-success">One Special Character<sup>*</sup></b>   e.g., ! @ # ? included.</p>
                            </div>
                          </div>
                          <div class="col-span-12 xl:col-span-6">
                            <div class="box border-0 shadow-none">
                              <div class="box-header">
                                <div class="sm:flex sm:space-y-4 justify-between">
                                  <h5 class="box-title my-auto">Web Linked Devices</h5>
                                  <button type="button" class="py-1 px-3 ti-btn ti-btn-primary text-sm m-0">Log out From All Devices </button>
                                </div>
                              </div>
                              <div class="box-body">
                                  <ul class="flex flex-col">
                                      <li class="ti-list-group bg-white text-gray-800 dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                        <div class="sm:flex w-full space-y-2">
                                          <div class="flex space-x-3 rtl:space-x-reverse">
                                            <div class="avatar rounded-sm avatar-sm bg-gray-100 dark:bg-bodybg p-2.5">
                                              <i class="ri ri-smartphone-line text-xl leading-none text-gray-500 dark:text-white/70"></i>
                                            </div>
                                            <div class="">
                                              <p class="mb-0 text-sm">Mobile-Poco-M2-Pro</p>
                                              <p class="mb-0 text-gray-500 dark:text-white/70 text-xs">Manchester, UK-Nov 30, 04:45PM</p>
                                            </div>
                                          </div>
                                          <div class="ms-auto my-auto text-end">
                                            <button type="button" class="px-2 py-1 ti-btn ti-btn-soft-info text-xs">ReWoke</button>
                                            <button type="button" class="px-2 py-1 ti-btn ti-btn-soft-danger text-xs">Logout</button>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="ti-list-group bg-white text-gray-800 dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                        <div class="sm:flex w-full space-y-2">
                                          <div class="flex space-x-3 rtl:space-x-reverse">
                                            <div class="avatar rounded-sm avatar-sm bg-gray-100 dark:bg-bodybg p-2.5">
                                              <i class="ri ri-tablet-line text-xl leading-none text-gray-500 dark:text-white/70"></i>
                                            </div>
                                            <div class="">
                                              <p class="mb-0 text-sm">Apple Tablet</p>
                                              <p class="mb-0 text-gray-500 dark:text-white/70 text-xs">Manchester, UK-Nov 30, 02:45PM</p>
                                            </div>
                                          </div>
                                          <div class="ms-auto my-auto text-end">
                                            <button type="button" class="px-2 py-1 ti-btn ti-btn-soft-info text-xs">ReWoke</button>
                                            <button type="button" class="px-2 py-1 ti-btn ti-btn-soft-danger text-xs">Logout</button>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="ti-list-group bg-white text-gray-800 dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                        <div class="sm:flex w-full space-y-2">
                                          <div class="flex space-x-3 rtl:space-x-reverse">
                                            <div class="avatar rounded-sm avatar-sm bg-gray-100 dark:bg-bodybg p-2.5">
                                              <i class="ri ri-airplay-line text-xl leading-none text-gray-500 dark:text-white/70"></i>
                                            </div>
                                            <div class="">
                                              <p class="mb-0 text-sm">Dell Desktop</p>
                                              <p class="mb-0 text-gray-500 dark:text-white/70 text-xs">Manchester, UK-Nov 30, 02:45PM</p>
                                            </div>
                                          </div>
                                          <div class="ms-auto my-auto text-end">
                                            <button type="button" class="px-2 py-1 ti-btn ti-btn-soft-info text-xs">ReWoke</button>
                                            <button type="button" class="px-2 py-1 ti-btn ti-btn-soft-danger text-xs">Logout</button>
                                          </div>
                                        </div>
                                      </li>
                                      <li class="ti-list-group bg-white text-gray-800 dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                        <div class="sm:flex w-full space-y-2">
                                          <div class="flex space-x-3 rtl:space-x-reverse">
                                            <div class="avatar rounded-sm avatar-sm bg-gray-100 dark:bg-bodybg p-2.5">
                                              <i class="ri ri-macbook-line text-xl leading-none text-gray-500 dark:text-white/70"></i>
                                            </div>
                                            <div class="">
                                              <p class="mb-0 text-sm">Lenovo Laptop</p>
                                              <p class="mb-0 text-gray-500 dark:text-white/70 text-xs">Manchester, UK-Nov 30, 02:45PM</p>
                                            </div>
                                          </div>
                                          <div class="ms-auto my-auto text-end">
                                            <button type="button" class="px-2 py-1 ti-btn ti-btn-soft-info text-xs">ReWoke</button>
                                            <button type="button" class="px-2 py-1 ti-btn ti-btn-soft-danger text-xs">Logout</button>
                                          </div>
                                        </div>
                                      </li>
                                  </ul>
                              </div>
                            </div>
                            <div class="my-5 px-6">
                              <div class="sm:space-x-6 rtl:space-x-reverse sm:flex space-y-4">
                                <label class="ti-form-label my-auto">Account :</label>
                                  <button type="button" class="ti-btn ti-btn-danger">
                                    Deactivate Account
                                  </button>
                                  <button type="button" class="ti-btn-disabled ti-btn ti-btn-success">
                                    Activate Account
                                  </button>
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                </div> --}}
                {{-- <div id="profile-settings-4" class="hidden" role="tabpanel" aria-labelledby="profile-settings-item-4">
                  <div class="box border-0 shadow-none mb-0">
                    <div class="box-header">
                      <h5 class="box-title leading-none flex"><i class="ri ri-account-circle-line me-2"></i> Account Settings</h5>
                    </div>
                    <div class="box-body">
                      <div class="sm:grid grid-cols-12 gap-6 space-y-6">
                        <div class="col-span-2 my-auto">
                          <label class="ti-form-label mb-0">Verfication Step - 2
                            <a aria-label="anchor" class="ms-2" href="javascript:void(0);">
                              <i class="ri ri-question-line"></i>
                            </a>
                          </label>
                        </div>
                        <div class="col-span-10">
                          <div class="flex items-center">
                              <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5 m-0">
                          </div>
                        </div>
                      </div>
                      <div class="my-5">
                        <nav class="flex space-x-2 rtl:space-x-reverse mb-4" aria-label="Tabs" role="tablist">
                          <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:text-white py-2 px-4 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:text-white/70 active" id="adhar-tab" data-hs-tab="#adhar" aria-controls="adhar" role="tab">
                            Adhar Number
                          </button>
                          <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white dark:hs-tab-active:bg-primary dark:hs-tab-active:text-white py-2 px-4 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center text-gray-500 rounded-sm hover:text-gray-700 dark:bg-bodybg dark:border-white/10 dark:text-white/70 dark:hover:text-gray-300" id="mobile-tab" data-hs-tab="#mobile" aria-controls="mobile" role="tab">
                            mobile Number
                          </button>
                        </nav>
                        <div>
                          <div id="adhar" role="tabpanel" aria-labelledby="adhar-tab">
                            <input type="text" class="ti-form-input" placeholder="name" value="5353 2525 2525">
                          </div>
                          <div id="mobile" class="hidden" role="tabpanel" aria-labelledby="mobile-tab">
                            <input type="number" class="ti-form-input" placeholder="name" value="9699696996">
                          </div>
                        </div>
                      </div>
                      <h5 class="text-base font-semibold ">Social Accounts</h5>
                      <div class="space-y-3 mt-5">
                        <div class="sm:grid grid-cols-12 gap-6 space-y-4">
                          <div class="col-span-2 my-auto">
                            <label class="ti-form-label">Facebook</label>
                          </div>
                          <div class="col-span-10">
                            <input type="email" class="ti-form-input" value="https://www.facebook.com/Spruha">
                          </div>
                        </div>
                        <div class="sm:grid grid-cols-12 gap-6 space-y-4">
                          <div class="col-span-2 my-auto">
                            <label class="ti-form-label">Twitter</label>
                          </div>
                          <div class="col-span-10">
                            <input type="email" class="ti-form-input" value="twitter.com/spruko.me">
                          </div>
                        </div>
                        <div class="sm:grid grid-cols-12 gap-6 space-y-4">
                          <div class="col-span-2 my-auto">
                            <label class="ti-form-label">Google+</label>
                          </div>
                          <div class="col-span-10">
                            <input type="email" class="ti-form-input" value="spruko.com">
                          </div>
                        </div>
                        <div class="sm:grid grid-cols-12 gap-6 space-y-4">
                          <div class="col-span-2 my-auto">
                            <label class="ti-form-label">Linked in</label>
                          </div>
                          <div class="col-span-10">
                            <input type="email" class="ti-form-input" value="linkedin.com/in/spruko">
                          </div>
                        </div>
                        <div class="sm:grid grid-cols-12 gap-6 space-y-4">
                          <div class="col-span-2 my-auto">
                            <label class="ti-form-label">Github</label>
                          </div>
                          <div class="col-span-10">
                            <input type="email" class="ti-form-input" value="github.com/spruko">
                          </div>
                        </div>
                        <div class="sm:grid grid-cols-12 gap-6 space-y-4">
                          <div class="col-span-2 my-auto">
                            <label class="ti-form-label">Website</label>
                          </div>
                          <div class="col-span-10">
                            <input type="email" class="ti-form-input" value="www.andersonitumay.com">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="profile-settings-5" class="hidden" role="tabpanel" aria-labelledby="profile-settings-item-5">
                  <div class="box border-0 shadow-none mb-0">
                    <div class="box-header">
                      <h5 class="box-title leading-none flex"><i class="ri ri-notification-4-line me-2"></i> Notifications Settings</h5>
                    </div>
                    <div class="box-body">
                      <div class="space-y-4">
                        <div class="p-4 border border-gray-200 dark:border-white/10 rounded-sm">
                          <div class="md:grid grid-cols-12 gap-6 space-y-4">
                            <div class="col-span-12 md:col-span-6 my-auto">
                              <p class="text-base mb-1 font-semibold">Comments</p>
                              <p class="text-xs mb-0 text-gray-500 dark:text-white/70">The Comment Notifications are the notifications you get for your posts and replies for your comments.</p>
                            </div>
                            <div class="col-span-12 md:col-span-6">
                              <div class="space-y-2">
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Notify Me</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5" checked>
                                  </div>
                                </div>
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Notify Me If Mentioned</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5">
                                  </div>
                                </div>
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Notify For My Posts</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5" checked>
                                  </div>
                                </div>
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">All Comments</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="p-4 border border-gray-200 dark:border-white/10 rounded-sm">
                          <div class="md:grid grid-cols-12 gap-6 space-y-4">
                            <div class="col-span-12 md:col-span-6 my-auto">
                              <p class="text-base mb-1 font-semibold">Tags</p>
                              <p class="text-xs mb-0 text-gray-500 dark:text-white/70">The Tag Notifications are the notifications you get when you are tagged for others posts.</p>
                            </div>
                            <div class="col-span-12 md:col-span-6">

                              <div class="space-y-2">
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Notify Me</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5" checked>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="p-4 border border-gray-200 dark:border-white/10 rounded-sm">
                          <div class="md:grid grid-cols-12 gap-6 space-y-4">
                            <div class="col-span-12 md:col-span-6 my-auto">
                              <p class="text-base mb-1 font-semibold">Reminders</p>
                              <p class="text-xs mb-0 text-gray-500 dark:text-white/70">The Reminder Notifications are the notifications you get when you missed any update .</p>
                            </div>
                            <div class="col-span-12 md:col-span-6">
                              <div class="space-y-2">
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Remind Me</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5" checked>
                                  </div>
                                </div>
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Remind Me only Important Updates</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5" checked>
                                  </div>
                                </div>
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Remind Me All updates</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="p-4 border border-gray-200 dark:border-white/10 rounded-sm">
                          <div class="md:grid grid-cols-12 gap-6 space-y-4">
                            <div class="col-span-12 md:col-span-6 my-auto">
                              <p class="text-base mb-1 font-semibold">More Activity</p>
                              <p class="text-xs mb-0 text-gray-500 dark:text-white/70">The Notifications is for likes ,comments,reactions for your profile  .</p>
                            </div>
                            <div class="col-span-12 md:col-span-6">
                              <div class="space-y-2">
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Notify Me</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5">
                                  </div>
                                </div>
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Notify Me only Important</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5">
                                  </div>
                                </div>
                                <div class="sm:grid grid-cols-12 gap-6 space-y-4 md:text-end">
                                  <div class="col-span-9 my-auto">
                                    <label class="text-sm text-gray-500 ms-3 dark:text-white/70">Notify Me All</label>
                                  </div>
                                  <div class="col-span-3 my-auto">
                                    <input type="checkbox" class="ti-switch shrink-0 w-11 h-6 before:w-5 before:h-5" checked>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div> --}}
              </div>
              {{-- <div class="box-footer text-end space-x-3 rtl:space-x-reverse">
                <a href="javascript:void(0);" class="ti-btn m-0 ti-btn-soft-primary"><i class="ri ri-refresh-line"></i> Update</a>
                <a href="javascript:void(0);" class="ti-btn m-0 ti-btn-soft-secondary"><i class="ri ri-close-circle-line"></i> Cancel</a>
              </div> --}}
            </div>
          </div>
        </div>
        <!-- End::row-1 -->

                
                
                {{-- <div class="box-body">
                 
                </div> --}}
              </div>
             
               
                
            </div>
          </div>
        </div>
        <!-- End::row-1 -->


        <!-- Start::row-3 -->
        {{-- <div class="grid grid-cols-12 gap-6">
          <div class="col-span-12">
            <div class="box">
              <div class="box-header">
                <h5 class="box-title">Reactivity DataTable</h5>
              </div>
              <div class="box-body space-y-3">
                <div class="reactivity-data">
                  <button type="button" class="ti-btn ti-btn-primary" id="reactivity-add">Add New Row</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="reactivity-delete">Remove Row</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="clear">Empty the table</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="reset">Reset</button>
                </div>
                <div class="overflow-hidden table-bordered">
                  <div id="reactivity-table" class="ti-custom-table ti-striped-table ti-custom-table-hover"></div>
                </div>
              </div>
            </div>
          </div>
        </div> --}}
        <!-- End::row-3 -->

        <!-- Start::row-3 -->
        {{-- <div class="grid grid-cols-12 gap-6">
          <div class="col-span-12">
            <div class="box">
              <div class="box-header">
                <h5 class="box-title">Download DataTable</h5>
              </div>
              <div class="box-body space-y-3">
                <div class="download-data">
                    <button type="button" class="ti-btn ti-btn-primary" id="download-csv">Download CSV</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-json">Download JSON</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-xlsx">Download XLSX</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-pdf">Download PDF</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-html">Download HTML</button>
                </div>
                <div class="overflow-hidden table-bordered">
                  <div id="download-table" class="ti-custom-table ti-striped-table ti-custom-table-hover"></div>
                </div>
              </div>
            </div>
          </div>
        </div> --}}
        <!-- End::row-3 -->

      </div>
      <!-- Start::main-content -->

       
@endsection

