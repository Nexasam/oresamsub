<div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-5 items-center px-2 md:px-0 py-6 gap-4 md:gap-3 text-[12px] ">
    <div class="bg-[{{$site_secondary_color}}] md:flex items-center p-2 md:p-3 rounded-lg border border-gray-300 space-x-1 md:space-x-4">
        <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/total_txns.png') }}" alt="">

        <div>
            <p>Transaction Volume</p>
            <p class="font-bold">&#8358; {{ number_format( $transactions_sum)  }}</p>
        </div>

    </div>
   <div class="bg-[{{$site_secondary_color}}] md:flex items-center p-2 md:p-3 rounded-lg border border-gray-300 space-x-1 md:space-x-4">
            <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/wallet_balance.png') }}" alt="">

            <div>
                <p>Wallet Balance</p>
                <p class="font-bold">&#8358;{{ number_format($user->main_wallet,2) ?? 0  }}</p>
            </div>

    </div>
   <div class="bg-[{{$site_secondary_color}}] md:flex items-center p-2 md:p-3 rounded-lg border border-gray-300 space-x-1 md:space-x-4">
            <img src="{{asset(env('APP_ASSETS_BASE_URL').'template2/images/total_txns_count.png') }}" alt="">

            <div>
                <p>Total Transactions</p>
                <p class="font-bold"> {{ number_format( count($transactions))  }}</p>
            </div>

    </div>

    @foreach ($user_virtual_accounts as $user_virtual_account)
        {{-- <div class="w-full flex-col text-[10px]  rounded-lg border border-gray-300"> --}}
        <div class="bg-[{{$site_secondary_color}}] md:flex items-center p-2 md:p-3 rounded-lg border border-gray-300 space-x-1 md:space-x-4">
        
            <div class="p-1.5 md:pt-0">
                
                <span class="font-bold my-2 py-1" id="textToCopy">{{ $user_virtual_account->account_number }} </span>
                <span class="font-bold my-2 py-1">{{ $user_virtual_account->account_name }}</span>
                <span class="font-bold my-2 py-1">  {{ $user_virtual_account->bank_name }}</span>
                
                <a href="#" onclick="copyAccountNo({{ $user_virtual_account->account_number }})" class="w-full block rounded-b-lg text-white bg-[{{$site_primary_color}}] hover:bg-[{{$site_secondary_color}}] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm px-4 md:px-8 py-0.5 md:py-0.5 mr-0 md:mr-4 text-center ">copy</a>

                {{-- <p></p> --}}
            </div>
        </div>
    
    @endforeach
  
</div>