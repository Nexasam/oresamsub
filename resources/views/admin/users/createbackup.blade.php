@extends('layouts.app')
    @section('content')

    <!-- Start::main-content -->
    <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium">Buy Data</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
                <li class="text-sm">
                    <a class="flex items-center font-semibold text-primary hover:text-primary dark:text-primary truncate" href="javascript:void(0);">
                    Data
                    <i class="ti ti-chevrons-right flex-shrink-0 mx-3 overflow-visible text-gray-300 dark:text-gray-300 rtl:rotate-180"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-500 hover:text-primary dark:text-gray-500 " aria-current="page">
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
                            <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                <div class="space-y-2 ">
                                    <label class="ti-form-label mb-0">Wallet type</label>
                                    <ul class="flex flex-col sm:flex-row">
                                        <li
                                            class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                            <div class="relative flex items-start w-full">
                                                <div class="flex items-center h-5">
                                                    <input id="gender-1"
                                                        name="gender" type="radio"
                                                        class="ti-form-radio" checked>
                                                </div>
                                                <label for="gender-1"
                                                    class="ms-3 block w-full text-sm text-gray-600 dark:text-gray-500">
                                                    Buy from Main Wallet
                                                </label>
                                            </div>
                                        </li>

                                        <li
                                        class="ti-list-group gap-x-2.5 bg-white border text-gray-800 sm:-ms-px sm:mt-0 sm:first:rounded-se-none sm:first:rounded-ss-none sm:first:rounded-es-sm sm:last:rounded-es-none sm:last:rounded-ee-none sm:last:rounded-se-sm dark:bg-bgdark dark:border-white/10 dark:text-gray-900">
                                        <div class="relative flex items-start w-full">
                                                <div class="flex items-center h-5">
                                                    <input id="gender-2"
                                                        name="gender" type="radio"
                                                        class="ti-form-radio">
                                                </div>
                                                <label for="gender-2"
                                                    class="ms-3 block w-full text-sm text-gray-600 dark:text-gray-500">
                                                    Buy from Data Wallet
                                                </label>
                                            </div>
                                        </li>

                                    
                                    </ul>
                                </div>
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Network</label>
                                    <select class="my-auto ti-form-select">
                                        <option selected>Select</option>
                                        <option>MTN</option>
                                        <option>GLO</option>
                                        <option>AIRTEL</option>
                                        <option>9MOBILE</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Data Plans</label>
                                    <select class="my-auto ti-form-select">
                                        <option selected>Select</option>
                                        <option>1 GB - &#8358;300</option>
                                        <option>2 GB - &#8358;500</option>
                                        <option>3 GB - &#8358;800</option>
                                        <option>5 GB - &#8358;2000</option>
                                        <option>10 GB - &#8358;3500</option>
                                        <option>20 GB - &#8358;6000</option>
                                    
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Phone Number(s) to recharge</label>
                                    <textarea class="my-auto ti-form-input"
                                        placeholder="e.g 08168509044, 09011988807"></textarea>
                                </div>

                                <div class="space-y-2">
                                    <button type="submit" class="ti-btn ti-btn-primary w-full">Buy Data</button>
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