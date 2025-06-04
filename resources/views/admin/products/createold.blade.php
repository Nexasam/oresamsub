@extends('layouts.app')
@section('content')

<!-- Start::main-content -->
<div class="main-content">

    <!-- Page Header -->
    <div class="block justify-between page-header md:flex">
        <div>
            <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium">Buy Airtime</h3>
        </div>
        <ol class="flex items-center whitespace-nowrap min-w-0">
            <li class="text-sm">
                <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="javascript:void(0);">
                Airtime
                <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                </a>
            </li>
            <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                Buy Airtime
            </li>
        </ol>   
    </div>
    <!-- Page Header Close -->



    <!-- Start::row-3 -->
    <div class="grid grid-cols-12 gap-x-6">
        
        <div class="col-span-12">
            <div class="box">
                
                <div class="box-body">
                    <form>
                        <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                            <div class="space-y-2 ">
                                <label class="ti-form-label mb-0">Wallet Balance: &#8358;50,000</label>             
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Network</label>
                                <select required class="my-auto ti-form-select">
                                    <option selected>Select</option>
                                    <option>MTN</option>
                                    <option>GLO</option>
                                    <option>AIRTEL</option>
                                    <option>9MOBILE</option>
                                  </select>
                            </div>

                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Airtime value to buy</label>
                                <input type="number" required class="my-auto ti-form-input" min="50" placeholder="airtime value">
                            </div>
                         
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Phone Number(s) to recharge</label>
                                <textarea required class="my-auto ti-form-input"
                                    placeholder="e.g 08168509044, 09011988807"></textarea>
                            </div>

                            <div class="space-y-2">
                                <button type="submit" class="ti-btn ti-btn-primary w-full">Buy Airtime</button>
                            </div>
                           
                            <br>
                        </div>
                        {{-- <div class="my-5">
                            <button type="submit" class="ti-btn ti-btn-primary w-full">Submit</button>
                        </div> --}}

                    </form>
                   
                </div>
            </div>
        </div>
    </div>
    <!-- End::row-3 -->

</div>
<!-- Start::main-content -->
@endsection