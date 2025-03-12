<div class="max-w-6xl mx-auto px-4 mt-0 md:mt-4 py-2 gap-1 md:rounded-lg text-sm border  border-gray-300 ">

    <div class="w-full  md:mx-auto ">
        <h4 class="font-bold text-md md:text-lg">Quick Actions</h4>
        <form class="block md:hidden w-full md:space-y-6">
            <div class="mt-2" x-data="{ selectedRoute: '' }">
                <label for="wallet" class="block mb-2 text-sm font-medium text-gray-900"></label>
                <select x-model="selectedRoute" @change="if(selectedRoute) window.location.href = selectedRoute" id="wallet" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[{{$site_primary_color}}] focus:border-[{{$site_primary_color}}] block w-full p-2.5">
                    <option value="#">Select Page</option>
                    <option value="{{ route('user.airtime.buy_airtime') }}">Airtime</option>
                    <option value="{{ route('user.data.buy_data') }}">Data</option>
                    <option value="{{ route('user.electricity.buy_electricity_subscription') }}">Electricity</option>
                    <option value="{{ route('user.cable_subscription.buy_cable_subscription') }}">Cable</option>
                    <option value="{{ route('user.transactions.index') }}">Transactions</option>
                    <option value="{{ route('user.api.docs') }}">Check API Docs</option>
                </select>
                
            </div>
        </form>
    </div>

    <div class="hidden w-full mx-auto md:grid grid-cols-2 md:grid-cols-5 items-center py-4 gap-4 ">
        
        <div class="flex items-center shadow-lg p-4 rounded-lg border border-gray-300 space-x-4">
            <div class="bg-[{{$site_secondary_color}}] rounded-full p-3">
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.4999 14.5834H10.5083M6.49992 18.3334H14.4999C15.4333 18.3334 15.9 18.3334 16.2566 18.1518C16.5702 17.992 16.8251 17.737 16.9849 17.4234C17.1666 17.0669 17.1666 16.6002 17.1666 15.6667V4.33341C17.1666 3.39999 17.1666 2.93328 16.9849 2.57676C16.8251 2.26316 16.5702 2.00819 16.2566 1.8484C15.9 1.66675 15.4333 1.66675 14.4999 1.66675H6.49992C5.5665 1.66675 5.09979 1.66675 4.74327 1.8484C4.42966 2.00819 4.1747 2.26316 4.01491 2.57676C3.83325 2.93328 3.83325 3.39999 3.83325 4.33342V15.6667C3.83325 16.6002 3.83325 17.0669 4.01491 17.4234C4.1747 17.737 4.42966 17.992 4.74327 18.1518C5.09979 18.3334 5.5665 18.3334 6.49992 18.3334ZM10.9166 14.5834C10.9166 14.8135 10.73 15.0001 10.4999 15.0001C10.2698 15.0001 10.0833 14.8135 10.0833 14.5834C10.0833 14.3533 10.2698 14.1667 10.4999 14.1667C10.73 14.1667 10.9166 14.3533 10.9166 14.5834Z" stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p> <a href="{{ route('user.airtime.buy_airtime') }}">Buy Airtime</a></p>
            
            </div>
        </div>

        {{-- <div  class="flex items-center shadow-lg p-4 rounded-lg border border-gray-300 space-x-4 hover:bg-[{{$site_primary_color}}] hover:text-white"> --}}
            <div class="flex items-center shadow-lg p-4 rounded-lg border border-gray-300 space-x-4">
            {{-- <a class="block" href="{{ route('user.data.buy_data') }}"> --}}
            
            <div class="bg-[{{$site_secondary_color}}] rounded-full p-3">
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_2115_4190)">
                <path d="M10.4999 16.25H10.5083M19.5053 7.25063C17.1329 5.07666 13.9713 3.75 10.4998 3.75C7.02837 3.75 3.86678 5.07666 1.49438 7.25063M4.4432 10.2025C6.0583 8.77971 8.17831 7.91667 10.4999 7.91667C12.8215 7.91667 14.9415 8.77971 16.5566 10.2025M13.5819 13.1459C12.7326 12.4802 11.6626 12.0833 10.4998 12.0833C9.31949 12.0833 8.23469 12.4923 7.37939 13.1763" stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                <clipPath id="clip0_2115_4190">
                <rect width="20" height="20" fill="white" transform="translate(0.5)"/>
                </clipPath>
                </defs>
                </svg>
            </div>
            <div>
                <p> <a href="{{ route('user.data.buy_data') }}">Buy Data</a></p>
            
            </div>
        </div>

        
        <div class="flex items-center shadow-lg p-4 rounded-lg border border-gray-300 space-x-4">
            <div class="bg-[{{$site_secondary_color}}] rounded-full p-3">
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.83342 14.7156V16.6667C8.83342 17.5872 9.57961 18.3334 10.5001 18.3334C11.4206 18.3334 12.1667 17.5872 12.1667 16.6667V14.7156M10.5001 1.66675V2.50008M3.00008 10.0001H2.16675M5.08341 4.58341L4.58333 4.08333M15.9167 4.58341L16.417 4.08333M18.8334 10.0001H18.0001M15.5001 10.0001C15.5001 12.7615 13.2615 15.0001 10.5001 15.0001C7.73866 15.0001 5.50008 12.7615 5.50008 10.0001C5.50008 7.23866 7.73866 5.00008 10.5001 5.00008C13.2615 5.00008 15.5001 7.23866 15.5001 10.0001Z" stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <div>
                    <p> <a href="{{ route('user.electricity.buy_electricity_subscription') }}">Electricity Bills</a></p>
                
                </div>
            
            </div>
        </div>

        <div class="flex items-center shadow-lg p-4 rounded-lg border border-gray-300 space-x-4">
            <div class="bg-[{{$site_secondary_color}}] rounded-full p-3">
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.6667 17.5L10.5001 14.1667L6.33341 17.5M6.16675 14.1667H14.8334C16.2335 14.1667 16.9336 14.1667 17.4684 13.8942C17.9388 13.6545 18.3212 13.272 18.5609 12.8016C18.8334 12.2669 18.8334 11.5668 18.8334 10.1667V6.5C18.8334 5.09987 18.8334 4.3998 18.5609 3.86502C18.3212 3.39462 17.9388 3.01217 17.4684 2.77248C16.9336 2.5 16.2335 2.5 14.8334 2.5H6.16675C4.76662 2.5 4.06655 2.5 3.53177 2.77248C3.06137 3.01217 2.67892 3.39462 2.43923 3.86502C2.16675 4.3998 2.16675 5.09987 2.16675 6.5V10.1667C2.16675 11.5668 2.16675 12.2669 2.43923 12.8016C2.67892 13.272 3.06137 13.6545 3.53177 13.8942C4.06655 14.1667 4.76662 14.1667 6.16675 14.1667Z" stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p> <a href="{{ route('user.cable_subscription.buy_cable_subscription') }}">Cable Data</a></p>
            
            </div>
        </div>

        <div class="flex items-center shadow-lg p-4 rounded-lg border border-gray-300 space-x-4">
            <div class="bg-[{{$site_secondary_color}}] rounded-full p-3">
                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.66667 3.33333H7C5.59987 3.33333 4.8998 3.33333 4.36502 3.60582C3.89462 3.8455 3.51217 4.22795 3.27248 4.69836C3 5.23314 3 5.9332 3 7.33333V13.5C3 14.9001 3 15.6002 3.27248 16.135C3.51217 16.6054 3.89462 16.9878 4.36502 17.2275C4.8998 17.5 5.59987 17.5 7 17.5H13.1667C14.5668 17.5 15.2669 17.5 15.8016 17.2275C16.272 16.9878 16.6545 16.6054 16.8942 16.135C17.1667 15.6002 17.1667 14.9001 17.1667 13.5V10.8333M11.3333 14.1667H6.33333M13 10.8333H6.33333M17.2678 3.23223C18.2441 4.20854 18.2441 5.79146 17.2678 6.76777C16.2915 7.74408 14.7085 7.74408 13.7322 6.76777C12.7559 5.79146 12.7559 4.20854 13.7322 3.23223C14.7085 2.25592 16.2915 2.25592 17.2678 3.23223Z" stroke="#0F172A" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div>
                <p><a href="{{ route('user.transactions.index') }}">Transactions</a> </p>
            
            </div>
        </div>


        
        </div>
    
</div>