@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium"> User Settings</h3>
            </div>  
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Home
                </li> 
            </ol> --}}
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">
         
          <div class="col-span-12">
            @if (Session::has('success'))
              <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
                {{ Session::get('success') }}
              </div>
            @endif

            @if (Session::has('failure'))
              <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                {{ Session::get('failure') }}
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

          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">{{__('messages.User Settings')}}</h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Wallet Setting
                    </button> --}}
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-gray-500" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Profile
                    </button> --}}
                    
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-3" data-hs-tab="#pills-with-brand-color-3" aria-controls="pills-with-brand-color-3">
                      Security/2fa Authentication
                    </button> --}}

                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-gray-500" id="pills-with-brand-color-item-4" data-hs-tab="#pills-with-brand-color-4" aria-controls="pills-with-brand-color-4">
                      2FA Authentication
                    </button> --}}
                  </nav>

                  <div class="mt-3">
                    {{-- <div id="pills-with-brand-color-1" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">

                            <form method="POST" action="{{ route('user.settings.update_default_wallet')  }}">
                               @csrf
                                <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                    <div class="space-y-2 mt-5">
                                      <label class="ti-form-label mb-0">  Choose default wallet (Currently: {{ strtoupper(implode(' ',explode('_',$user->default_wallet_setting))) }})
                                        <br>
                                        <small>This wallet will be automatically selected at the point of purchase</small>
                                      </label>
                                      <select id="default_wallet_setting" name="default_wallet_setting" required class="my-auto ti-form-select">
                                          <option value="">Select</option>
                                          <option @if ($user->default_wallet_setting == 'main_wallet')
                                              selected
                                          @endif value="main_wallet">Main Wallet</option>
                                          <option @if ($user->default_wallet_setting == 'bulk_data_wallet')
                                            selected
                                           @endif value="bulk_data_wallet">Data Wallet</option>
                                      </select>
                                        
                                    </div>

                                    <div class="space-y-2">
                                        <button type="submit" class="ti-btn ti-btn-primary w-full">Update default wallet</button>
                                    </div>
                                  
                                    <br>
                                </div>
                            </form>
                        
                      </div>                
                    </div> --}}

                    {{-- class="hidden" --}}
                    <div id="pills-with-brand-color-2"  role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="overflow-auto">
                        <form method="POST" action="{{ route('user.settings.update_profile')  }}">
                          @csrf
                          
                          {{-- <div class="grid w-full lg:w-1/2 lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0"> --}}
                          <div class="grid lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0">
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">{{__('messages.First name')}}</label>
                                <input type="text" id="first_name" name="first_name" value="{{ $user->first_name }}" class="my-auto ti-form-input" placeholder="">
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">{{__('messages.Last name')}}</label>
                                <input type="text" id="last_name" name="last_name" value="{{ $user->last_name }}" class="my-auto ti-form-input" placeholder="">
                            </div>
                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">{{__('messages.Other names')}}</label>
                              <input type="text" id="other_names" name="other_names" value="{{ $user->other_names }}" class="my-auto ti-form-input" placeholder="">
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">PIN</label>
                                <input type="password" id="pin" name="pin" value="" class="my-auto ti-form-input"
                                    placeholder="">
                                <div class="flex items-center">
                                  <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin1">
                                  <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show pin')}}</label>
                                </div> 
                            </div>
                            {{-- <div class="space-y-2">
                                <label class="ti-form-label mb-0">Email Address</label>
                                <input type="email" id="email" value="{{ $user->email }}" name="email" class="my-auto ti-form-input"
                                    placeholder="your@site.com">
                            </div> --}}
                           
                        
                            {{-- <div class="space-y-2 ">
                                <label class="ti-form-label mb-0">Gender</label>
                                <ul class="flex flex-col sm:flex-row">
                                    <li
                                        class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                        <div class="relative flex items-start w-full">
                                            <div class="flex items-center h-5">
                                                <input id="hs-horizontal-list-group-item-radio-1"
                                                    name="gender" type="radio" value="female"
                                                    class="ti-form-radio" @if ($user->gender == 'female')
                                                        checked
                                                    @endif>
                                            </div>
                                            <label for="hs-horizontal-list-group-item-radio-1"
                                                class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                                Female
                                            </label>
                                        </div>
                                    </li>

                                    <li
                                    class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                    <div class="relative flex items-start w-full">
                                            <div class="flex items-center h-5">
                                                <input id="hs-horizontal-list-group-item-radio-2"
                                                    name="gender" type="radio" value="male"
                                                    class="ti-form-radio" @if ($user->gender == 'male')
                                                    checked
                                                @endif>
                                            </div>
                                            <label for="hs-horizontal-list-group-item-radio-2"
                                                class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                                Male
                                            </label>
                                        </div>
                                    </li>

                                
                                </ul>
                            </div> --}}

                              {{-- <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">About Us </label>
                                  <div cols="10" rows="5" id="editor">
                                  </div>
                              </div> --}}

                              <div class="space-y-2 mt-5">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full btn-block">{{__('messages.Update Profile')}}</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>
                      </div>  
                    </div>

                    {{-- class="hidden"  --}}
                    <div id="pills-with-brand-color-3" role="tabpanel" aria-labelledby="pills-with-brand-color-item-3">
                      <div class="overflow-auto">
                        <form method="POST" action="{{ route('user.settings.update_password')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0"> {{__('messages.Current password')}}</label>
                                <input type="password" id="current_password" name="current_password" class="my-auto ti-form-input" placeholder="">    
                                <div class="flex items-center">
                                  <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_password_current">
                                  <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show password')}}</label>
                                </div>                        
                              </div>

                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0"> {{__('messages.New password')}}</label>
                                <input type="password" id="new_password" name="new_password" class="my-auto ti-form-input" placeholder="">                            
                                <div class="flex items-center">
                                  <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_password">
                                  <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show password')}}</label>
                                </div>
                              </div>


                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0"> {{__('messages.Confirm new password')}}</label>
                                <input type="password" id="confirm_new_password" name="confirm_new_password" class="my-auto ti-form-input" placeholder=""> 
                                <div class="flex items-center">
                                  <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_password2">
                                  <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show Confirm Password')}}</label>
                                </div>                           
                              </div>
                             

                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">PIN</label>
                                <input type="password" id="pin5" name="pin5" class="my-auto ti-form-input" placeholder=""> 
                                <div class="flex items-center">
                                  <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin5">
                                  <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show pin')}}</label>
                                </div>                                
                              </div>


                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">{{__('messages.Update Password')}}</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>
                        <hr>
                        <form method="POST" action="{{ route('user.settings.update_pin')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                              {{-- <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0"> Current password</label>
                                <input type="password" id="current_password" name="current_password" class="my-auto ti-form-input" placeholder="enter current password">                            
                              </div> --}}

                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0"> {{__('messages.Current PIN')}}</label>
                                <input type="password" id="current_pin" name="current_pin" class="my-auto ti-form-input" placeholder="">   
                                <div class="flex items-center">
                                  <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin2">
                                  <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show pin')}}</label>
                                </div>                          
                              </div>

                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0"> {{__('messages.New PIN')}}</label>
                                <input type="password" id="new_pin" name="new_pin" class="my-auto ti-form-input" placeholder=""> 
                                <div class="flex items-center">
                                  <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin3">
                                  <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show pin')}}</label>
                                </div>                            
                              </div>

                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">{{__('messages.Confirm New PIN')}}</label>
                                <input type="password" id="confirm_new_pin" name="confirm_new_pin" class="my-auto ti-form-input" placeholder=""> 
                                <div class="flex items-center">
                                  <input type="checkbox" id="hs-basic-with-description-unchecked" class="ti-switch show_pin4">
                                  <label for="hs-basic-with-description-unchecked" class="text-sm text-gray-500 ms-3 dark:text-white/70 ">{{__('messages.Show pin')}}</label>
                                </div>                            
                              </div>


                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">{{__('messages.Update PIN')}}</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>
                        <hr>
                        <div class="overflow-auto">
                          <span class="ti-btn ti-btn-danger w-1/2">{{ __('messages.Note that 2FA feature is currently disabled. It will be enabled as soon as possible.') }}</span>
           

                          {{-- Note: Please we are currently making some updates and as a result, the 2fa will be temporarily unavailable.
                          We are sorry for the inconvenience this might cause. Thanks --}}
                          <br>
                          {{-- <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                                @csrf
                    
                                @if(auth()->user()->two_factor_secret)
                                    <h3>2Factor authentication setup</h3>
                                    <p>Two factor authentication is enabled.</p>
                                    <div class="pt-5 pb-5">
                                        {!!  auth()->user()->twoFactorQrCodeSvg() !!}
                                    </div>
                                    <h3><strong>Please save recovery codes below:</strong></h3>
                                    <textarea name="myInput" id="myInput" cols="35" rows="16">
                                      @foreach(auth()->user()->recoveryCodes() as $code)
                                      {{ $code }}
                                      @endforeach
                                    </textarea>
                                    <br>
                                    <a class="ti-btn ti-btn-info w-1/4" href="#" onclick="copyToClipboard()"><span id="copyText">Copy Codes</span></a>
                                    <br>
                                    <br>
                                    <br>
                                    @method('DELETE')
                                    <div class="space-y-2">
                                      <button type="submit" class="ti-btn ti-btn-danger w-1/2">Disable Two Factor Authentication</button>
                                    </div>
                                @else
                                    Two factor authentication is not enabled.
                                    <div class="space-y-2">
                                      <button type="submit" class="ti-btn ti-btn-primary w-1/2">Enable Two Factor Authentication</button>
                                    </div>
                                @endif
                          </form> --}}


                            {{-- <form method="POST" action="{{ url('user/two-factor-authentication')  }}">
                              @csrf
                              <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                  <div class="space-y-2 mt-5">
                                    <label class="ti-form-label mb-0"> Your 2-factor authentication is currently turned <strong>{{ $user->user_2fa_setting }}</strong></label>
                                    <select id="user_2fa_setting" name="user_2fa_setting" required class="my-auto ti-form-select">
                                      <option value="">Select</option>
                                      <option @if ($user->user_2fa_setting == 'ON')
                                          selected
                                      @endif value="ON">ON</option>
                                      <option @if ($user->user_2fa_setting == 'OFF')
                                        selected
                                    @endif value="OFF">OFF</option>
                                    </select>
                                      
                                  </div>

                                  <div class="space-y-2">
                                      <button type="submit" class="ti-btn ti-btn-primary w-full">Update 2FA setting</button>
                                  </div>
                                
                                  <br>
                              </div>
                          </form> --}}
                        </div>  
                      </div>  
                    </div>

                  

                    {{-- <div id="pills-with-brand-color-4" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-4">
                      <div class="overflow-auto">
                        <form>

                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                              <div class="grid lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0">
                                <div class="space-y-2">
                                  <label class="ti-form-label mb-0">2FA currently: <strong>OFF</strong></label>
                                  <select id="product_commission_feature" name="product_commission_feature" required class="my-auto ti-form-select">
                                    <option value="">Select</option>
                                    <option value="2fa_on">Turn ON</option>
                                    <option value="2fa_off">Turn OFF</option>
                                </select>
                              </div>            
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update 2fa</button>
                              </div>
                            
                              <br>
                          </div>
                      </form>
                      </div>  
                    </div> --}}

                  </div>
                </div>
               
                {{-- <div class="box-body">
                 
                </div> --}}
              </div>
              {{-- <div class="box-body">
                <div class="overflow-auto table-bordered p-4">
                  <table id="basic-table" class="ti-custom-table ti-striped-table ti-custom-table-hover">
                    <thead>
                        <tr>
                       
                            <td>First Name</td>
                            <td>Last Name</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
               
              </div> --}}
               
                
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

