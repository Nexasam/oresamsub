<div>
    <section class="my-4">
        <div class="max-w-6xl mx-auto bg-gray-50 rounded-lg px-4 lg:p-4">
            @if ($routeName = Route::currentRouteName() == 'dashboard'            )
                Recent Transactions
            @else
                All Transactions
            @endif

            @php
                $site_primary_colorr =  App\Models\AdminColorSetting::where('color_name','site_primary_color')->first();
                $site_secondary_colorr = App\Models\AdminColorSetting::where('color_name','site_secondary_color')->first();
                $site_primary_color = $site_primary_colorr->color_value ?? (int) '90, 102, 241'; 
                $site_secondary_color = $site_secondary_colorr->color_value ?? (int) '90, 102, 241'; 
            @endphp
            {{-- {{ $transactions }} --}}
            <h3 class="mb-1"></h3>
            <!-- Start coding here -->
            <div class="bg-white  relative shadow-md sm:rounded-lg overflow-hidden">
                <div class="flex items-center justify-between d p-2">
                    <div class="flex">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500"
                                    fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 "
                                placeholder="Search" required="">
                        </div>
                    </div>
                    {{-- <div class="flex space-x-3">
                        <div class="flex space-x-3 items-center">
                            <label class="w-40 text-sm font-medium text-gray-900">User Type :</label>
                            <select 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                                <option value="">All</option>
                                <option value="0">User</option>
                                <option value="1">Admin</option>
                            </select>
                        </div>
                    </div> --}}
                </div>
                <div class="overflow-x-auto">
                    @if (count($transactions) > 0)
                    <table  class="w-full border-collapse border border-gray-300 text-sm text-center text-gray-500">
                        <thead class="text-xs h-12 my-2 text-gray-700 uppercase bg-gray-50 border-collapse border border-gray-300">
                            {{-- <tr>
                                <th scope="col" class=" border border-gray-300 px-4 py-3">name</th>
                                <th scope="col" class="px-4 py-3">email</th>
                                <th scope="col" class="px-4 py-3">Role</th>
                                <th scope="col" class="px-4 py-3">Joined</th>
                                <th scope="col" class="px-4 py-3">Last update</th>
                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr> --}}

                            {{-- border-2 border-gray-400 --}}
                            <th class="border border-gray-300 px-2">ID</th>
                            <th class="border border-gray-300 px-2">User</th>
                            {{-- <th class="border border-gray-300 px-2">Wallet</th>  --}}
                            <th class="border border-gray-300 px-2">Product Details</th>
                            {{-- <th class="border border-gray-300 px-2">Txn Category</th> --}}
                            <th class="border border-gray-300 px-2">Phone</th>
                            <th class="border border-gray-300 px-2">Amount</th>
                            {{-- <th class="border border-gray-300 px-2">Discounted Amount</th> --}}
                            <th class="border border-gray-300 px-2">Balance Before</th>
                            {{-- <th>Data size</th> --}}
                            <th class="border border-gray-300 px-2">Balance After</th>
                            <th class="border border-gray-300 px-2">Status</th>
                            <th class="border border-gray-300 px-2">Date Added</th>
                            <th class="border border-gray-300 px-2">Action</th>
                        </thead>
                        <tbody class="border-collapse border border-gray-300">
                            @foreach ($transactions as $key=>$txn)
                                <tr wire:key class="border-b">
                                    <th scope="row"
                                        class="border border-gray-300 px-1 font-medium text-gray-900 whitespace-nowrap">
                                        {{$key + 1}}</th>
                                    <td class="border border-gray-300 px-1">
                                        {{ $txn->user->first_name }} <br>
                                        {{ $txn->user->last_name }} <br>
                                        {{ $txn->user->phone_number }} <br>
                                        {{ $txn->user->email }} <br>
                                    </td>
                                    {{-- <td class="border border-gray-300 px-1">{{ $txn->wallet_category }}</td> --}}
                                    <td class="border border-gray-300 px-3 text-sm">
                                        @php
                                            if($txn->product_plan != NULL){
                                                
                                                $dataa =  $txn->product_plan->product_plan_name."<br>";
                                                $dataa .=  $txn->product_plan->product_plan_category->product_plan_category_name."<br>";
                                                if($txn->transaction_category == 'cable_subscription'){
                                                    $dataa .=  'Smart Card No: '.$txn->smart_card_number."<br>";
                                                }
                                                if($txn->transaction_category == 'utility_bills'){
                                                    $response_decode = json_decode($txn->admin_screen_message,true);
                                                    $token_details = isset($response_decode['Detail']['info']['realresponse']) ? $response_decode['Detail']['info']['realresponse'] :  '-';
                                                    $prefix = $token_details == '-' ? 'Token details: ' : '';
                                                    $dataa .=  'Metre No: '.$txn->metre_number;
                                                    $dataa .=  $prefix.':  '.$token_details."<br>";
                                                }
                                                if($txn->transaction_category == 'data'){
                                                    $dataa .= number_format($txn->product_plan->data_size_in_mb ?? '0') .' MB';
                                                    $dataa .= "<br>";
                                                }

                                            }else{
                                                $dataa .= 'NIL<br>';
                                            }
                                            $dataa .= strtoupper($txn->transaction_category)." - ";
                                            $dataa .= $txn->wallet_category;
                                            echo $dataa;

                                            @endphp

                                    </td>
                                    {{-- <td class="border border-gray-300 px-1">{{ $txn->transaction_category }}</td> --}}
                                    <td class="border border-gray-300 px-1">{{ $txn->phone_number }}</td>
                                    <td class="border border-gray-300 px-1 text-green-500">
                                        {{ $txn->amount. "[" .$txn->discounted_amount. "]" }}</td>
                                    {{-- <td class="border border-gray-300 px-1">{{ $txn->discounted_amount }}</td> --}}
                                    <td class="border border-gray-300 px-1">{{$txn->balance_before}}</td>
                                    <td class="border border-gray-300 px-1">{{$txn->balance_after}}</td>
                                    <td class="border border-gray-300 px-1">
                                        @switch($txn->status)
                                            @case(1)
                                                <span class="px-2 mx-1 rounded-lg py-1 bg-green-500 text-white">Success</span>
                                                @break
                                            @case(-1)
                                                <span class="px-2 mx-1 rounded-lg py-1 bg-red-500 text-white">Failed</span>
                                                @break
                                            @case(0)
                                            <span class="px-2 mx-1 rounded-lg py-1 bg-yellow-500 text-white">Pending</span>
                                                @break
                                            @case(2)
                                            <span class="px-2 mx-1 rounded-lg py-1 bg-blue-500 text-white">Refunded</span>
                                                @break
                                            @case(3)
                                            <span class="px-2 mx-1 rounded-lg py-1 bg-purple-500 text-white">Processing</span>
                                                @break
                                            @default
                                                
                                        @endswitch
                                    </td>
                                    <td class="border border-gray-300 px-1">{{$txn->created_at}}</td>
                                    <td class="border border-gray-300 flex items-center px-3">
                                        <a href='{{route("transactions.transaction_details",$txn->id)}}' class="w-full text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_primary_color}}]/90 focus:ring-4 focus:outline-none focus:ring-[{{$site_primary_color}}]/50 font-medium rounded-lg text-sm px-2 py-1 text-center items-center dark:focus:ring-[{{$site_primary_color}}]/55 my-2">Details</a>
                                    </td>
                                </tr>
                            @endforeach
                            

                        </tbody>
                    </table>       
                    @else
                        <p class=" text-center p-4">No transactions found</p>
                    @endif
                 
                </div>

                    @if (count($transactions) > 0)
                    <div class="py-4 px-3">
                        <div class="flex justify-between ">
                            <div class="flex space-x-4 items-center mb-3">
                                <label class="w-32 text-sm font-medium text-gray-900">Per Page</label>
                                <select wire:model.live='perPage'
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>

                            <div wire:ignore.self class="flex bg-white text-black border-gray-300">
                                {{ $transactions->links() }}
                            </div>
                        </div>

                    </div>
                    @else
                        
                    @endif
                

            </div>
        </div>
    </section>

</div>