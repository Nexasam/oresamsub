@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> User Settings</h3>
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
                  <h5 class="box-title font-bold">Create Transaction PIN</h5>
                  <small class="text-red-800 text-base">It is required  for you to set your pin to make sure all your transactions are treated securely</small>
                </div>

                <div class="box-body">
               

                  <div class="mt-3">
                  
                    {{-- class="hidden" --}}
                    <div id="pills-with-brand-color-2"  role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="overflow-auto">
                        <form method="POST" action="{{ route('user.settings.store_set_pin')  }}">
                          @csrf
                          
                          {{-- <div class="grid w-full lg:w-1/2 lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0"> --}}
                          <div class="grid lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0">
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">PIN</label>
                                <input type="text" id="pin" name="pin" value="" class="my-auto ti-form-input" placeholder="PIN">
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Reenter PIN</label>
                                <input type="text" id="confirm_pin" name="confirm_pin" value="" class="my-auto ti-form-input" placeholder="Reenter PIN">
                            </div>
                      
                              <div class="space-y-2 mt-5">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full btn-block">Set Transaction PIN</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>
                      </div>  
                    </div>

    
         

                  </div>
                </div>
        
              </div>
                 
            </div>
          
        </div>
        </div>
        <!-- End::row-1 -->


      </div>
      <!-- Start::main-content -->

       
@endsection

