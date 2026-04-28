<div>
    <section class="my-4">
        <div class="max-w-6xl mx-auto bg-gray-50 rounded-lg px-4 lg:p-4"
            x-data="{
                showTxnModal: false,
                loadingTxnModal: false,
                txnData: null,
                async viewTxnDetails(id) {
                    this.showTxnModal = true;
                    this.loadingTxnModal = true;
                    this.txnData = null;
                    try {
                        const res = await fetch('/transactions/details/' + id + '/json');
                        this.txnData = await res.json();
                    } catch(e) { this.showTxnModal = false; }
                    finally { this.loadingTxnModal = false; }
                },
                closeTxnModal() { this.showTxnModal = false; this.txnData = null; },
                statusText(s) {
                    return { 1: 'Success', '1': 'Success', 0: 'Pending', '0': 'Pending', '-1': 'Failed', 2: 'Refunded', '2': 'Refunded' }[s] || 'Unknown';
                },
                statusClass(s) {
                    return { 1: 'bg-green-100 text-green-800', '1': 'bg-green-100 text-green-800', 0: 'bg-yellow-100 text-yellow-800', '0': 'bg-yellow-100 text-yellow-800', '-1': 'bg-red-100 text-red-800', 2: 'bg-blue-100 text-blue-800', '2': 'bg-blue-100 text-blue-800' }[s] || 'bg-gray-100 text-gray-800';
                }
            }">
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
                                    <td class="border border-gray-300 px-1 text-blue-500">
                                        {{ $txn->amount. "[" .$txn->discounted_amount. "]" }}</td>
                                    {{-- <td class="border border-gray-300 px-1">{{ $txn->discounted_amount }}</td> --}}
                                    <td class="border border-gray-300 px-1">{{$txn->balance_before}}</td>
                                    <td class="border border-gray-300 px-1">{{$txn->balance_after}}</td>
                                    <td class="border border-gray-300 px-1">
                                        @switch($txn->status)
                                            @case(1)
                                                <span class="px-2 mx-1 rounded-lg py-1 bg-blue-500 text-white">Success</span>
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
                                        <button @click="viewTxnDetails({{ $txn->id }})" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-2 py-1 text-center my-2">Details</button>
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

    <!-- Transaction Details Modal -->
    <div x-show="showTxnModal" x-cloak @click.self="closeTxnModal()"
        class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4"
        style="display:none">
        <div @click.stop class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between rounded-t-xl">
                <h3 class="text-lg font-bold text-white">Transaction Details</h3>
                <button @click="closeTxnModal()" class="text-white hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div x-show="loadingTxnModal" class="py-16 text-center">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
            </div>
            <div x-show="!loadingTxnModal && txnData" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full"
                            :class="statusClass(txnData?.status)"
                            x-text="statusText(txnData?.status)"></span>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Category</p>
                        <p class="text-sm font-semibold text-gray-900 uppercase" x-text="txnData?.transaction_category ?? '—'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <p class="text-xs text-gray-500 mb-1">Product</p>
                        <p class="text-sm font-semibold text-gray-900" x-text="txnData?.product_plan?.product_plan_name ?? 'N/A'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Phone Number</p>
                        <p class="text-sm font-semibold font-mono text-gray-900" x-text="txnData?.phone_number ?? '—'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Wallet</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800"
                            x-text="txnData?.wallet_category === 'main_wallet' ? 'MAIN WALLET' : 'DATA WALLET'"></span>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                        <p class="text-xs text-green-700 mb-1">Amount</p>
                        <p class="text-lg font-bold text-green-900">₦<span x-text="txnData?.amount ? parseFloat(txnData.amount).toLocaleString('en-NG', {minimumFractionDigits: 2}) : '—'"></span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Discounted Amount</p>
                        <p class="text-sm font-semibold text-gray-900">₦<span x-text="txnData?.discounted_amount ? parseFloat(txnData.discounted_amount).toLocaleString('en-NG', {minimumFractionDigits: 2}) : '—'"></span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Balance Before</p>
                        <p class="text-sm font-semibold text-gray-900">₦<span x-text="txnData?.balance_before ? parseFloat(txnData.balance_before).toLocaleString('en-NG', {minimumFractionDigits: 2}) : '—'"></span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Balance After</p>
                        <p class="text-sm font-semibold text-gray-900">₦<span x-text="txnData?.balance_after ? parseFloat(txnData.balance_after).toLocaleString('en-NG', {minimumFractionDigits: 2}) : '—'"></span></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Reference</p>
                        <p class="text-xs font-mono text-gray-700 break-all" x-text="txnData?.txn_reference ?? '—'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Date</p>
                        <p class="text-sm text-gray-700" x-text="txnData?.created_at ? new Date(txnData.created_at).toLocaleString('en-GB') : '—'"></p>
                    </div>
                </div>
                <div x-show="txnData?.user_screen_message" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-xs text-blue-700 font-semibold mb-1">Message</p>
                    <p class="text-sm text-blue-900" x-text="txnData?.user_screen_message"></p>
                </div>
            </div>
            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 flex justify-end rounded-b-xl border-t">
                <button @click="closeTxnModal()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium">Close</button>
            </div>
        </div>
    </div>
    </section>

</div>