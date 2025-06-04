@extends('layouts.app')
@section('content')
    <!-- Start::main-content -->
    <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium">Create User</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
                <li class="text-sm">
                    <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="javascript:void(0);">
                    Users
                    <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-500 hover:text-primary dark:text-gray-500 " aria-current="page">
                    Create User
                </li>
            </ol>
        </div>
        <!-- Page Header Close -->

    

        <!-- Start::row-3 -->
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
            <div class="col-span-12">
                <div class="box">
                    
                    <div class="box-body">
                        <form method="POST" action="{{ route('admin.users.store')}}">
                            @csrf
                            <div class="grid lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0">
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">First Name</label>
                                    <input type="text" id="first_name" name="first_name" class="my-auto ti-form-input" placeholder="Firstname">
                                </div>
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" class="my-auto ti-form-input" placeholder="Lastname">
                                </div>
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Phone Number</label>
                                    <input type="number" id="phone_number" name="phone_number" class="my-auto ti-form-input"
                                        placeholder="+91 123-456-789">
                                </div>
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Email Address</label>
                                    <input type="email" id="email" name="email" class="my-auto ti-form-input"
                                        placeholder="your@site.com">
                                </div>
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Password</label>
                                    <input type="password" id="password" name="password" class="ti-form-input" placeholder="password">
                                </div>
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Confirm Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="ti-form-input" placeholder="confirm password">
                                </div>
                            
                                <div class="space-y-2 ">
                                    <label class="ti-form-label mb-0">Gender</label>
                                    <ul class="flex flex-col sm:flex-row">
                                        <li
                                            class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                            <div class="relative flex items-start w-full">
                                                <div class="flex items-center h-5">
                                                    <input id="hs-horizontal-list-group-item-radio-1"
                                                        name="gender" type="radio" value="female"
                                                        class="ti-form-radio" checked>
                                                </div>
                                                <label for="hs-horizontal-list-group-item-radio-1"
                                                    class="ms-3 block w-full text-sm text-gray-600 dark:text-gray-500">
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
                                                        class="ti-form-radio">
                                                </div>
                                                <label for="hs-horizontal-list-group-item-radio-2"
                                                    class="ms-3 block w-full text-sm text-gray-600 dark:text-gray-500">
                                                    Male
                                                </label>
                                            </div>
                                        </li>

                                    
                                    </ul>
                                </div>

                        
                                {{-- <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Address</label>
                                    <input type="text" id="address" name="address" class="my-auto ti-form-input" placeholder="Address">
                                </div> --}}
                                <br>
                            </div>
                            <div class="my-5">
                                <button type="submit" class="ti-btn ti-btn-primary w-full">Submit</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End::row-3 -->

    </div>

@endsection
                