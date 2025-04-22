<div>
    <section class="my-4">
        <div class="max-w-6xl mx-auto bg-gray-50 rounded-lg px-4 lg:p-4">
      
             All Wallet Transactions
      
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
                    @if (count($wallet_transactions) > 0)
                    <table  class="w-full border-collapse border border-gray-300 text-sm text-center text-gray-500">
                        <thead class="text-xs h-12 my-2 text-gray-700 uppercase bg-gray-50 border-collapse border border-gray-300">
                            
                            <tr>
                          
            
                                <th class="border border-gray-300 px-2">ID</th>
                                <th class="border border-gray-300 px-2">User</th>
                                <th class="border border-gray-300 px-2">Txn Reference</th>
                                <th class="border border-gray-300 px-2">Txn Status</th>
                                <th class="border border-gray-300 px-2">Funding Status</th>
                                <th class="border border-gray-300 px-2">Txn Message</th>
                                {{-- <th>Package Id</th> --}}
                                <th class="border border-gray-300 px-2">Bank</th>
                                <th class="border border-gray-300 px-2">Account Name</th>
                                <th class="border border-gray-300 px-2">Account No</th>
                                <th class="border border-gray-300 px-2">Account Reference</th>
                                <th class="border border-gray-300 px-2">Amount Paid</th>
                                <th class="border border-gray-300 px-2">Amount Charged</th>
                                <th class="border border-gray-300 px-2">Amount Settled</th>
                                <th class="border border-gray-300 px-2">Date Added</th>
                                {{-- <th class="border border-gray-300 px-2">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody class="border-collapse border border-gray-300">
                            @foreach ($wallet_transactions as $key=>$txn)
                                <tr wire:key class="border-b">
                                    <th scope="row"
                                        class="border border-gray-300 px-1 font-medium text-gray-900 whitespace-nowrap">
                                        {{$key + 1}}</th>
                                    <td class="border border-gray-300 px-3 text-sm">

                                        @php
                                            $first_name = $txn->user->first_name  ?? 'nil';
                                            $last_name = $txn->user->last_name  ?? 'nil';
                                            $phone_number = $txn->user->phone_number  ?? 'nil';
                                            $email = $txn->user->email  ?? 'nil';
                                            $user_details =  $first_name.'<br>'.$last_name.'<br>'.$phone_number.'<br>'.$email.'<br>'; 
                                            echo $user_details;    
                                        @endphp
                                      
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->transaction_reference}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->status}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->funding_status}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->message}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->bank_name}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->account_name}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->account_number}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->account_reference}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->amount_paid}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->amount_charged}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->amount_settled}}
                                    </td>
                                    <td class="border border-gray-300 px-3 text-sm">
                                        {{$txn->created_at}}
                                    </td>

                               
                                </tr>
                            @endforeach
                            

                        </tbody>
                    </table>       
                    @else
                        <p class=" text-center p-4">No  funding transactions found</p>
                    @endif
                 
                </div>

                    @if (count($wallet_transactions) > 0)
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
                                {{ $wallet_transactions->links() }}
                            </div>
                        </div>

                    </div>
                    @else
                        
                    @endif
                

            </div>
        </div>
    </section>

</div>