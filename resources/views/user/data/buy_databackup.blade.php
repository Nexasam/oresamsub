@extends('layouts.app')
@section('content')

<!-- Start::main-content -->
<div class="main-content">

    <!-- Page Header -->
    <div class="block justify-between page-header md:flex">
        <div>
            <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium">Buy Data</h3>
        </div>
        <ol class="flex items-center whitespace-nowrap min-w-0">
            <li class="text-sm">
                <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="javascript:void(0);">
                Data
                <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                </a>
            </li>
            <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                Buy Data
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
                        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
                 
                        <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                            {{-- <div class="space-y-2 ">
                                <label class="ti-form-label mb-0">Wallet type</label>
                                <ul class="flex flex-col sm:flex-row">
                                    <li
                                        class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-white">
                                        <div class="relative flex items-start w-full">
                                            <div class="flex items-center h-5">
                                                <input id="wallet"
                                                    name="wallet" type="radio"
                                                    class="ti-form-radio" checked>
                                            </div>
                                            <label for="wallet"
                                                class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                                Buy from Main Wallet
                                            </label>
                                        </div>
                                    </li>

                                    <li
                                    class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-white">
                                    <div class="relative flex items-start w-full">
                                            <div class="flex items-center h-5">
                                                <input id="wallet"
                                                    name="wallet" type="radio"
                                                    class="ti-form-radio">
                                            </div>
                                            <label for="wallet"
                                                class="ms-3 block w-full text-sm text-gray-600 dark:text-white/70">
                                                Buy from Data Wallet
                                            </label>
                                        </div>
                                    </li>

                                   
                                </ul>
                            </div> --}}
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Choose Wallet</label>
                                <select required id="wallet_category" name="wallet_category" class="my-auto ti-form-select">
                                    <option value="">Select</option>
                                     <option value="main_wallet">Main Wallet</option>                                        
                                     {{-- <option value="data_wallet">Data Wallet</option>                                         --}}
                                 
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Network</label>
                                {{-- single_select --}}
                                <select required id="network_id" name="network_id" class="my-auto ti-form-select">
                                    <option value="">Select</option>
                                    @foreach ($networks as $network)
                                     <option value="{{  $network->id }}">{{ $network->network_name }}</option>                                        
                                    @endforeach
                                  </select>
                            </div>
                            <div class="space-y-2">
                                {{-- <div class="grid sm:grid-cols-2 gap-2"> --}}
                                    <label class="p-3 flex w-full bg-white border border-gray-200 rounded-sm text-sm focus:border-primary focus:ring-primary dark:bg-bgdark dark:border-white/10 dark:text-white/70">
                                      <input type="checkbox" class="ti-form-checkbox mt-0.5 pointer-events-none" id="filter_by_plan_category">
                                      <span class="text-sm text-gray-500 ms-2 dark:text-white/70">Filter by plan categories</span>
                                    </label>
                            </div>

                            {{-- single_select --}}
                            <div id="product_plan_category_div" class="space-y-2 hidden">
                                <label class="ti-form-label mb-0">Product Plan Category</label>
                                <select data-trigger required name="product_plan_category_id" id="product_plan_category_id" class="my-auto ti-form-select">
                                    <option value="all">Select</option>

                                  </select>
                            </div>

                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Product Plans List</label>
                                <select required name="product_plan_id" id="product_plan_id" class="my-auto ti-form-select">
                                    <option value="all">Select</option>

                                  </select>
                            </div>
                          
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Phone Number(s) to recharge</label>
                                <textarea id="phone_number" name="phone_number" class="my-auto ti-form-input"
                                    placeholder="e.g 08168509044, 09011988807"></textarea>
                            </div>

                            <div class="space-y-2">
                                <button type="submit" id="buy_data_btn" class="ti-btn ti-btn-primary w-full">Buy Data</button>
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