<!DOCTYPE html>
<html lang="en" class="font-sans bg-gray-100 text-gray-800">
<head>
  <meta charset="UTF-8">
  <title>{{ $title ?? 'OresamSub' }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- DARK MODE PREVENT FLASH -->
  <script>
    if (localStorage.getItem('theme') === 'dark' ||
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
      document.documentElement.classList.add('bg-gray-900');
    } else {
      document.documentElement.classList.remove('dark');
      document.documentElement.classList.add('bg-gray-100');
    }
  </script>

  <!-- Tailwind CSS + Alpine.js -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { darkMode: 'class' };
  </script>
  <script src="https://unpkg.com/alpinejs@3.x.x" defer></script>



  <style>
    [x-cloak] { display: none !important; }
  </style>
</head>

<body
  x-data="themeToggle()"
  x-init="init(); $watch('showLoader', val => document.body.classList.toggle('overflow-hidden', val))"
  class="min-h-screen text-gray-800 dark:text-gray-100"
>



  <!-- App Container -->
  <div class="max-w-md mx-auto border border-gray-300 dark:border-gray-700 rounded-xl shadow-lg overflow-hidden bg-white dark:bg-gray-900">

    <!-- Header -->
    {{-- <div class="p-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
      <h1 class="text-xl font-bold">OresamSub</h1>
      <button @click="toggle()" class="text-xl">
        <span x-text="darkMode ? '☀️' : '🌙'"></span>
      </button>
    </div> --}}

    <div class="p-4 flex justify-between items-center border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
      <div class="flex items-center space-x-2">
        <h1 class="text-xl font-bold text-gray-800 dark:text-white">OresamSub</h1>
    
        <!-- WhatsApp Support Link (compact + bold + emerald green) -->
        <a
          href="https://wa.me/2349163128718?text=Hello%20OresamSub%20Support"
          target="_blank"
          class="flex items-center px-2.5 py-0.5 text-xs font-bold text-emerald-900 dark:text-white bg-gradient-to-r from-emerald-100 to-emerald-200 dark:from-emerald-700 dark:to-emerald-800 hover:brightness-110 dark:hover:brightness-125 rounded-full transition duration-300 ease-in-out shadow-sm"
        >
          <svg class="w-3.5 h-3.5 mr-1 fill-current" viewBox="0 0 24 24">
            <path d="M20.52 3.48A11.86 11.86 0 0012.02 0C5.39 0 0 5.38 0 12a11.89 11.89 0 001.65 6L0 24l6.42-1.68A11.84 11.84 0 0012 24c6.63 0 12-5.38 12-12a11.86 11.86 0 00-3.48-8.52zM12 22.06a10.1 10.1 0 01-5.17-1.42l-.37-.22-3.81 1 1-3.7-.24-.38A10.07 10.07 0 011.94 12c0-5.57 4.5-10.06 10.06-10.06 2.69 0 5.21 1.05 7.11 2.95a10.06 10.06 0 01-7.11 17.17zM17 14.41l-2.17-.62a1.33 1.33 0 00-1.25.34l-.6.61a9.55 9.55 0 01-4.51-4.5l.6-.61a1.33 1.33 0 00.34-1.25l-.62-2.17A1.33 1.33 0 007.12 6H5.65a1.33 1.33 0 00-1.33 1.33A9.7 9.7 0 0015.67 18a1.33 1.33 0 001.33-1.33v-1.47a1.33 1.33 0 00-.95-1.26z"/>
          </svg>
          Support
        </a>
      </div>
    
      <button @click="toggle()" class="text-xl">
        <span x-text="darkMode ? '☀️' : '🌙'"></span>
      </button>
    </div>
    
    
    
    
    

    <!-- Main Content -->
    <main class="px-4 pt-4 pb-28 min-h-[calc(100vh-96px)]">
      @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 inset-x-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-lg z-50">
      <div class="max-w-md mx-auto flex justify-around py-2 text-xs font-medium text-gray-700 dark:text-gray-200">
        @foreach ([
          ['label' => 'Dashboard', 'icon' => '🏠', 'route' => 'dashboard'],
          ['label' => 'Data', 'icon' => '📶', 'route' => 'ore.data'],
          ['label' => 'Airtime', 'icon' => '📞', 'route' => 'ore.airtime'],
          ['label' => 'Cable', 'icon' => '📺', 'route' => 'ore.cable'],
          ['label' => 'Electricity', 'icon' => '⚡', 'route' => 'ore.electricity'],
          ['label' => 'Profile', 'icon' => '👤', 'route' => 'dashboard'], // Replace 'profile' with your actual route
        ] as $item)
          <a 
            href="{{ route($item['route']) }}"
            @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route($item['route']) }}', 200)"
            class="flex flex-col items-center hover:text-blue-600 dark:hover:text-blue-400"
          >
            <div class="text-xl">{{ $item['icon'] }}</div>
            <span>{{ $item['label'] }}</span>
          </a>
        @endforeach
      </div>
    </nav>
    

  </div>

  <!-- Global Loader -->
  {{-- <div x-show="showLoader" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-white dark:bg-gray-900 bg-opacity-80">
    <div class="animate-spin h-12 w-12 border-4 border-blue-500 border-t-transparent rounded-full"></div>
  </div> --}}

<!-- Loader Overlay -->
{{-- <div
  x-show="showLoader"
  class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50"
  style="display: none;"
>
  <div class="text-center text-white space-y-4">
  
    <svg class="animate-spin h-10 w-10 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
    </svg>

    
    <p class="text-sm max-w-xs mx-auto">
      Please wait... If this loader appears for too long, check your internet connection and reload this page again.
    </p>

   
    <button
      @click="window.location.reload()"
      class="mt-2 px-4 py-2 bg-white text-black rounded shadow hover:bg-gray-200 transition"
    >
      Reload Page
    </button>
  </div>
</div> --}}

<!-- Loader Overlay -->
<div
  x-show="showLoader"
  class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50"
  style="display: none;"
>
  <div class="text-center text-white space-y-4">
    <!-- Logo -->
    <img src="{{ asset('assets/logo_imgs/oresamsublogo.jpeg') }}" alt="Oresamsub Logo" class="h-12 mx-auto rounded-full" />
    {{-- <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 mx-auto animate-pulse" /> --}}


    <!-- Spinner -->
    <svg class="animate-spin h-10 w-10 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
    </svg>

    <!-- Message -->
    <p class="text-sm max-w-xs mx-auto">
      Please wait... If this loader appears for too long, check your internet connection and reload this page again.
    </p>

    <!-- Reload Button -->
    <button
      @click="window.location.reload()"
      class="mt-2 px-4 py-2 bg-white text-black rounded shadow hover:bg-gray-200 transition"
    >
      Reload Page
    </button>
  </div>
</div>





  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  var APP_NAME = @json(env('APP_NAME'));
   $(document).ready(function(){
    //  alert('this is working') 

    function getProductPlans(network_id='', plan_category_id='', product_slug='', amount = ''){
              if(network_id != '' && product_slug != '' && plan_category_id == ''){
                var data = {
                  network_id : network_id,
                  product_slug : product_slug,
                  amount : amount
                };
              
                // alert('hhhhh')
              }

              if(network_id != '' && plan_category_id != '' && product_slug != ''){
                var data = {
                  network_id : network_id,
                  plan_category_id : plan_category_id,
                  product_slug : product_slug,
                  amount : amount
                };        
              }

              //  console.log(data);
              //  return;
              

              $.ajax({
                        type: 'GET',
                        url: "{{ route('user.fetch_product_plans') }}",
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            console.log(response)
                            // console.log(response.data)
                            var result = JSON.stringify(response.data);
                            var dataList = JSON.parse(result);
                        
                              $('#product_plan_id').html("");
                              $('#product_plan_id').append('<option value="">Select Product Plan</option>');
      
                              // let jj = jsonn;
                              for (const child in dataList) {
            
                                 
                                  const idd = dataList[child].product_plan_id;
                                  const product_plan_name = dataList[child].product_plan_name;
                                  const upline_commission = dataList[child].upline_commission;
                                  const selling_price = dataList[child].selling_price;
                                  if(product_slug == 'data'){

                                    
                                    // if(APP_NAME == 'OresamSub'){
                                    //   option = "<option value="+idd+">"+product_plan_name+"- &#8358; "+selling_price+" - Upline Commission:&#8358;"+upline_commission+"</option>";
                                    // }else{
                                    //   option = "<option value="+idd+">"+product_plan_name+'- &#8358;'+selling_price+"</option>";
                                    // }


                                    $('#plan_grid').html(""); // Clear previous
                                    let planBoxes = '';

                                    for (const child in dataList) {
                                      const idd = dataList[child].product_plan_id;
                                      const name = dataList[child].product_plan_name;
                                      const price = dataList[child].selling_price;
                                      const commission = dataList[child].upline_commission;

                                      let label = APP_NAME == 'OresamSub'
                                        ? `${name}<br><span class='text-xs text-green-600'>₦${price} | Upline: ₦${commission}</span>`
                                        : `${name}<br><span class='text-xs'>₦${price}</span>`;

                                      planBoxes += `
                                        <div 
                                          class="border rounded-lg p-3 text-center cursor-pointer bg-white dark:bg-gray-800 hover:border-blue-600 transition"
                                          data-id="${idd}"
                                          onclick="selectPlan(this)"
                                        >
                                          ${label}
                                        </div>
                                      `;
                                    }
                                    $('#plan_grid').html(planBoxes);

                                   

                                  
                                  
                                  }
                                  else if(product_slug == 'airtime' && amount != ''){

                                    // $('.display_actual_amount').html("<b>You are buying for: &#8358;"+selling_price+"</b>");
                                    // $('#product_plan_id').val(idd);
                                    option = "<option selected value="+idd+">"+product_plan_name+' - &#8358;'+selling_price+"</option>";
                                  }
                                  else if(product_slug == 'airtime' && amount == ''){
                                    option = "<option value="+idd+">"+product_plan_name+"</option>";
                                  }else{
                                    if(APP_NAME == 'OresamSub'){
                                      option = "<option value="+idd+">"+product_plan_name+" &nbsp;&nbsp;Upline Commission:&#8358;"+upline_commission+"</option>";
                                    }else{
                                      option = "<option value="+idd+">"+product_plan_name+"</option>";
                                    }
                                  }
                                  $('#product_plan_id').append(option);
                                
                                
                              }
                            
                          
                        },
                        error: function(xhr, status, error) {
                            // Handle errors if needed
                            console.error(xhr.responseText);
                        }
                });
    }

    function getCableProductPlans(plan_category_id='', product_slug=''){
              
              var data = {
                plan_category_id : plan_category_id,
                product_slug : product_slug
              };
                
  
                $.ajax({
                          type: 'GET',
                          url: "{{ route('user.fetch_product_plans') }}",
                          data: data,
                          dataType: 'json',
                          success: function(response) {
                              console.log(response)
                              // console.log(response.data)
                              var result = JSON.stringify(response.data);
                              var dataList = JSON.parse(result);
                          
                              $('#cable_product_plan_id').html("");
                              $('#cable_product_plan_id').append('<option value="">Select Product Plan</option>');
  
                                // let jj = jsonn;

                                  $('#plan_grid').html(""); // Clear previous
                                  let boxes = '';
                             
                                  for (const child in dataList) {
                                      const idd = dataList[child].product_plan_id;
                                      const name = dataList[child].product_plan_name;
                                      const price = dataList[child].selling_price;
                                      const commission = dataList[child].upline_commission;

                                      let label = APP_NAME == 'OresamSub'
                                        ? `${name}<br><span class='text-xs text-green-600'>₦${price} | Upline: ₦${commission}</span>`
                                        : `${name}<br><span class='text-xs'>₦${price}</span>`;

                                        boxes += `
                                        <div 
                                          class="border rounded-lg p-3 text-center cursor-pointer bg-white dark:bg-gray-800 hover:border-blue-600 transition plan-card"
                                          data-id="${idd}">
                                          ${name}<br>
                                          <span class="text-xs text-green-600">₦${price}</span>
                                        </div>`;
                                    }
                                    $('#plan_grid').html(boxes);
                              
                            
                          },
                          error: function(xhr, status, error) {
                              // Handle errors if needed
                              console.error(xhr.responseText);
                          }
                });
    }


    function getElectricityProductPlans(plan_category_id='', product_slug='', amount = ''){
              
              var data = {
                plan_category_id : plan_category_id,
                product_slug : product_slug,
                amount : amount,
              };
              
              console.log('electric',data);

              $.ajax({
                        type: 'GET',
                        url: "{{ route('user.fetch_product_plans') }}",
                        data: data,
                        dataType: 'json',
                        success: function(response) {
                            console.log(response)
                            console.log('e dey reach here o')
                            // console.log(response.data)
                            var result = JSON.stringify(response.data);
                            var dataList = JSON.parse(result);
                        
                              $('#electricity_product_plan_id').html("");
                              $('#electricity_product_plan_id').append('<option value="">Select Product Plan</option>');
      
                              $('#plan_grid').html(""); // Clear previous
                              let boxes = '';
                             
                              for (const child in dataList) {
                                  const idd = dataList[child].product_plan_id;
                                  const name = dataList[child].product_plan_name;
                                  const price = dataList[child].selling_price;
                                  const commission = dataList[child].upline_commission;

                                  let label = APP_NAME == 'OresamSub'
                                    ? `${name}<br><span class='text-xs text-green-600'>₦${price} | Upline: ₦${commission}</span>`
                                    : `${name}<br><span class='text-xs'>₦${price}</span>`;

                                    boxes += `
                                    <div 
                                      class="border rounded-lg p-3 text-center cursor-pointer bg-white dark:bg-gray-800 hover:border-blue-600 transition plan-card-elect"
                                      data-id="${idd}">
                                      ${name}<br>
                                      <span class="text-xs text-green-600">₦${price}</span>
                                    </div>`;
                              }
                              $('#plan_grid').html(boxes);
                            
                          
                        },
                        error: function(xhr, status, error) {
                            // Handle errors if needed
                            console.error(xhr.responseText);
                        }
                });
        }


        $('#buy_electricity_btn').click(function(e){
          e.preventDefault();
            $(this).html('Processing...Please wait');
            $(this).prop('disabled',true);
            $('#cancel_disabling').removeClass('hidden')

          
            //display product plans categories
            const electricity_product_plan_category_id = $('#electricity_product_plan_category_id').val();
            const metre_number = $('#metre_number').val();
            const validation_extra_info = $('#meter_name_preview').text();
            const validation_address = $('#meter_address_preview').text();
            const wallet_category = $('#wallet_category').val();
            const electricity_product_plan_id = $('#electricity_product_plan_id').val();
            const pin = $('#pin').val();
            const no_of_slots = 1;
            const utility_amount = $('#utility_amount').val();
            
            

            const data = {
              electricity_product_plan_category_id : electricity_product_plan_category_id,
              metre_number : metre_number,
              validation_extra_info : validation_extra_info,
              validation_address : validation_address,
              wallet_category : wallet_category,
              electricity_product_plan_id : electricity_product_plan_id,
              pin : pin,
              no_of_slots : no_of_slots,
              amount : utility_amount,
            };

            // console.log(data);
            // return;

            if (confirm("Are you sure you want to complete this electricity subscription purchase?") == true) {
                // alert('logic happens here')
              

                $.ajax({
                  type: 'GET',
                  url: "{{ route('user.electricity.buy_electricity_subscription_action') }}",
                  data: data,
                  dataType: 'json',
                  success: function(response) {
                      console.log(response);
                      // console.log(response.data);
                      var result = JSON.stringify(response);
                      var dataList = JSON.parse(result);
                      if( parseInt(dataList.status) == 1){
                          sweetAlertDisplay(dataList.message,'Success','success');
                          $('#buy_electricity_btn').html('Buy Token');
                          $('#buy_electricity_btn').prop('disabled',false);

                          reload(3000,'dashboard');
                      }
                      else if(dataList.status == 2){
                          //@least 1 tranaction had an issue
                          sweetAlertDisplay(dataList.message,'Info','warning');
                          $('#buy_electricity_btn').html('Buy Token');
                          $('#buy_electricity_btn').prop('disabled',false);

                          reload(100000000);
                      }
                      else{
                        sweetAlertDisplay(dataList.message,'Error','error');
                        $('#buy_electricity_btn').html('Buy Token');
                        $('#buy_electricity_btn').prop('disabled',false);
                      }
                    

                  },
                  error: function(xhr, status, error) {
                      // Handle errors if needed
                      console.error(xhr.responseText);
                  }
                });
            } else {
              $('#buy_electricity_btn').html('Buy Token');
              $('#buy_electricity_btn').prop('disabled',false);

              return;
            }  
        })

    //cable
    $(document).on('click', '.plan-card', function () {
      $('#plan_grid .plan-card').removeClass('border-blue-600 ring-2 ring-blue-200');
      $(this).addClass('border-blue-600 ring-2 ring-blue-200');
      $('#cable_product_plan_id').val($(this).data('id'));
      $('#plan_error').addClass('hidden');
    });

    //electricity
      $(document).on('click', '.plan-card-elect', function () {
        // alert('asdfsfd')
      $('#plan_grid .plan-card-elect').removeClass('border-blue-600 ring-2 ring-blue-200');
      $(this).addClass('border-blue-600 ring-2 ring-blue-200');
      $('#electricity_product_plan_id').val($(this).data('id'));
      $('#plan_error').addClass('hidden');
    });

  
    let verifyTimer;
    $('#smart_card_number').on('keyup', function () {
        clearTimeout(verifyTimer); // Cancel previous timer

        const number = $(this).val();
        const plan_id = $('#cable_product_plan_id').val();

        console.log(plan_id,number);

        

        if (number.length >= 10 && plan_id) {
          $('#smartcard_name_preview').text('Typing...');

          verifyTimer = setTimeout(() => {
            $('#smartcard_name_preview').text('Verifying...');
           var urll = "{{ route('user.cable_subscription.validate_smart_card_number') }}";


            $.ajax({
              type: 'GET',
              url: urll,
              data: {
                smart_card_number : number,
                plan_id : plan_id
              },
              success: function (response) {
               console.log(response);
                $('#smartcard_name_preview').text(response.name || 'Not Found');
              },
              error: function () {
                $('#smartcard_name_preview').text('Verification failed');
              }
            });
          }, 500); // Wait 500ms after user stops typing
        } else {
          $('#smartcard_name_preview').text('Not yet verified');
        }
    });


    let verifyTimer2;
    $('#metre_number').on('keyup', function () {
        clearTimeout(verifyTimer2); // Cancel previous timer

        const number = $(this).val();
        const plan_id = $('#electricity_product_plan_id').val();

        console.log(plan_id,number);

        

        if (number.length >= 10 && plan_id) {
          $('#meter_name_preview').text('Checking...');
          $('#meter_address_preview').text('Checking...');

          verifyTimer = setTimeout(() => {
            $('#meter_name_preview').text('Verifying...');
            $('#meter_addresspreview').text('Verifying...');
             var urll = "{{ route('user.electricity.validate_metre_number') }}";


            $.ajax({
              type: 'GET',
              url: urll,
              data: {
                smart_card_number : number,
                plan_id : plan_id
              },
              success: function (response) {
               console.log(response);
                $('#meter_name_preview').text(response.name || 'No Name Found');
                $('#meter_address_preview').text(response.address || 'No Address Found');
              },
              error: function () {
                $('#meter_name_preview').text('Verification failed');
                $('#meter_address_preview').text('Verification failed');
              }
            });
          }, 500); // Wait 500ms after user stops typing
        } else {
          $('#meter_name_preview').text('Not yet verified');
          $('#meter_address_preview').text('Not yet verified');
        }
    });



    function sweetAlertDisplay(message,title,status){
        Swal.fire({
              icon: status,
              title: title,
              text: message,
              // footer: '<a href="">Why do I have this issue?</a>'
              });
    }

    function reload(timeout = 3000, redirectUrl = null) {
      setTimeout(() => {
        if (redirectUrl) {
          window.location.href = redirectUrl;
        } else {
          window.location.reload();
        }
      }, timeout);
     }


     $('#buy_cable_btn').click(function(e){
          e.preventDefault();
            $(this).html('Processing...Please wait');
            $(this).prop('disabled',true);
            $('#cancel_disabling').removeClass('hidden')

          
            //display product plans categories
            const cable_product_plan_category_id = $('#cable_product_plan_category_id').val();
            let smart_card_number = $('#smart_card_number').val();
            const validation_customer_name = $('#smartcard_name_preview').text();
            const wallet_category = $('#wallet_category').val();
            const cable_product_plan_id = $('#cable_product_plan_id').val();
            const pin = $('#pin').val();
            const no_of_slots = $('#no_of_slots').val();
            
            const data = {
              cable_product_plan_category_id : cable_product_plan_category_id,
              smart_card_number : smart_card_number,
              validation_customer_name : validation_customer_name,
              wallet_category : wallet_category,
              cable_product_plan_id : cable_product_plan_id,
              pin : pin,
              no_of_slots : no_of_slots,
            };


            // console.log(data);
            // return;

            if (confirm("Are you sure you want to complete this cable subscription purchase?") == true) {
                // alert('logic happens here')
              

                $.ajax({
                  type: 'GET',
                  url: "{{ route('user.cable_subscription.buy_cable_subscription_action') }}",
                  data: data,
                  dataType: 'json',
                  success: function(response) {
                      console.log(response);
                      var result = JSON.stringify(response);
                      var dataList = JSON.parse(result);
                      if( parseInt(dataList.status) == 1){
                          sweetAlertDisplay(dataList.message,'Success','success');
                          reload(3000,'dashboard');
                      }
                      else if(dataList.status == 2){
                          //@least 1 tranaction had an issue
                          sweetAlertDisplay(dataList.message,'Info','warning');
                          reload(100000000);
                      }
                      else{
                        sweetAlertDisplay(dataList.message,'Error','error');
                        $(this).prop('disabled',false);
                      }
                      $('#buy_cable_btn').html('Subscribe');
                      $('#buy_cable_btn').prop('disabled',false);

                  },
                  error: function(xhr, status, error) {
                      // Handle errors if needed
                      console.error(xhr.responseText);
                  }
                });
            } else {
              $('#buy_cable_btn').html('Subscribe');
              $('#buy_cable_btn').prop('disabled',false);
              return;
            }

            
          
        })

    



    $('#utility_amount').keyup(function(e){
        e.preventDefault();
        var amount =  $(this).val();
        var product_slug = $("#product_slug").val();
        var plan_category_id = $('#electricity_product_plan_category_id').val();
          
        var amount = product_slug == 'airtime' || product_slug == 'utility_bills'  ? amount : '';
        // alert(plan_category_id)

        getElectricityProductPlans(plan_category_id,product_slug,amount);

    });

    $('#amount').keyup(function(e){
            e.preventDefault();
            var amount =  $(this).val();
            var network_id = $("#network_id").val();
            var product_slug = $("#product_slug").val();
            var plan_category_id = $('#product_plan_category_id').val();
            var product_plan_id = $('#product_product_plan_id').val();
            
              
            if(network_id == ''){
              sweetAlertDisplay('Network is required','Network required','error');
              return;
            }
            var amount = product_slug == 'airtime' ? $('#amount').val() : '';
      
            getProductPlans(network_id,plan_category_id,product_slug,amount);
    });

    $('#cable_product_plan_category_id').change(function(){
          var product_slug = $("#product_slug").val();
          var plan_category_id = $(this).val();

          if(plan_category_id == '' || plan_category_id == 'all'){
            sweetAlertDisplay('Product plan category is required','Plan category required','error');
            return;
          }

          getCableProductPlans(plan_category_id,product_slug);
    })

    $('#electricity_product_plan_category_id').change(function(){
          var product_slug = $("#product_slug").val();
          var plan_category_id = $(this).val();

          if(plan_category_id == '' || plan_category_id == 'all'){
            sweetAlertDisplay('Product plan category is required','Plan category required','error');
            return;
          }

          getCableProductPlans(plan_category_id,product_slug);
    })


    $('#buy_data_btn').click(function(e){
          e.preventDefault();
            $(this).html('Processing...Please wait');
            $(this).prop('disabled',true);
            $('#cancel_disabling').removeClass('hidden')

          
            //display product plans categories
            const network_id = $('#network_id').val();
            const product_plan_category_id = $('#product_plan_category_id').val();
            const phone_number = $('#phone_number').val();
            const wallet_category = $('#wallet_category').val();
            const product_plan_id = $('#product_plan_id').val();
            const pin = $('#pin').val();
            // const validatephonenetwork = $('#validatephonenetwork').val();
            let checkvalidatephonenetwork = $('#validatephonenetwork').is(":checked");
            if(checkvalidatephonenetwork){
              var validatephonenetwork = 1;
            }else{
              var validatephonenetwork = 0;
              
            }
            

            const data = {
              network_id : network_id,
              product_plan_category_id : product_plan_category_id,
              phone_number : phone_number,
              wallet_category : wallet_category,
              product_plan_id : product_plan_id,
              pin : pin,
              validatephonenetwork : validatephonenetwork
            };

            

            // console.log(data);
            // return;

            if (confirm("Are you sure you want to complete this data purchase?") == true) {
                // alert('logic happens here')
              

                $.ajax({
                  type: 'GET',
                  url: "{{ route('user.data.buy_data_action') }}",
                  data: data,
                  dataType: 'json',
                  success: function(response) {
                      console.log(response);
                      // console.log(response.data);
                      var result = JSON.stringify(response);
                      var dataList = JSON.parse(result);
                      if( parseInt(dataList.status) == 1){
                          sweetAlertDisplay(dataList.message,'Success','success');
                          reload(3000,'dashboard');
                      }
                      else if(dataList.status == 2){
                          //@least 1 tranaction had an issue
                          sweetAlertDisplay(dataList.message,'Info','warning');
                          $("#buy_data_btn").text('Buy Data');
                          $("#buy_data_btn").prop('disabled',false);
                          $('#cancel_disabling').addClass('hidden')
                          reload(6000);

                      }
                      else{
                        sweetAlertDisplay(dataList.message,'Error','error');
                          $("#buy_data_btn").text('Buy Data');
                          $("#buy_data_btn").prop('disabled',false);
                          $('#cancel_disabling').addClass('hidden')
                          // reload(6000);
                      }        
                      $('#buy_data_btn').html('Buy Data');
                      $('#buy_data_btn').prop('disabled',false);

                  },
                  error: function(xhr, status, error) {
                      // Handle errors if needed
                      console.error(xhr.responseText);
                  }
                });
            } else {
              $('#buy_data_btn').html('Buy Data');
              $('#buy_data_btn').prop('disabled',false);
              return;
            }
      })

    $('#buy_airtime_btn').click(function(e){
          e.preventDefault();
            $(this).html('Processing...Please wait');
            $(this).prop('disabled',true);
            $('#cancel_disabling').removeClass('hidden')

            //display product plans categories
            const network_id = $('#network_id').val();
            const product_plan_category_id = $('#product_plan_category_id').val();
            const phone_number = $('#phone_number').val();
            const wallet_category = $('#wallet_category').val();
            const product_plan_id = $('#product_plan_id').val();
            const pin = $('#pin').val();
            let checkvalidatephonenetwork = $('#validatephonenetwork').is(":checked");
            if(checkvalidatephonenetwork){
              var validatephonenetwork = 1;
            }else{
              var validatephonenetwork = 0;
              
            }

            if($('#amount_for_airtime_category').val() == ''){
              var amount = $('#amount_for_airtime_category').val();
              console.log('this is running')

              if(amount == undefined){
                var amount = $('#amount').val();
              }
            }else{
              var amount = $('#amount').val();
            }

            if( parseInt(amount) < 50){
              $(this).html('Buy Airtime');
              sweetAlertDisplay("{{ __('messages.You need to buy at least N50 worth of airtime.') }}",'Airtime purchase error','error');
              // sweetAlertDisplay('You need to buy atleast &#8358;50 worth of airtime','Airtime purchase error','error');
              return;
            
            }
            

            const data = {
              network_id : network_id,
              product_plan_category_id : product_plan_category_id,
              phone_number : phone_number,
              wallet_category : wallet_category,
              product_plan_id : product_plan_id,
              pin : pin,
              amount : amount,
              validatephonenetwork : validatephonenetwork
            };

            // console.log(data);
            // return;

            if (confirm("Are you sure you want to complete this airtime purchase?") == true) {
                // alert('logic happens here')
                $.ajax({
                  type: 'GET',
                  url: "{{ route('user.airtime.buy_airtime_action') }}",
                  data: data,
                  dataType: 'json',
                  success: function(response) {
                      console.log(response);
                      // console.log(response.data);
                      var result = JSON.stringify(response);
                      var dataList = JSON.parse(result);
                      console.log(dataList);
                      if( parseInt(dataList.status) == 1){
                          sweetAlertDisplay(dataList.message,'Success','success');
                          reload(3000,'dashboard');
                      }
                      else if(dataList.status == 2){
                          //@least 1 tranaction had an issue
                          sweetAlertDisplay(dataList.message,'Info','warning');
                          reload(60000000);
                      }
                      else{
                        sweetAlertDisplay(dataList.message,'Error','error');
                        $(this).prop('disabled',false);
                      }
                      $('#buy_airtime_btn').html('Buy Airtime');
                      $('#buy_airtime_btn').prop('disabled',false);

                  },
                  error: function(xhr, status, error) {
                      // Handle errors if needed
                      console.error(xhr.responseText);
                  }
                });
            } else {
              $('#buy_airtime_btn').html('Buy Airtime');
              $('#buy_airtime_btn').prop('disabled',false);
              return;
            }      
    })
 
  
    $('#network_id').change(function(){
      
          $('#product_plan_category_id').html('<option value="all">All categories selected</option>');
          $("#product_plan_category_div").addClass('hidden');
          $('#filter_by_plan_category').prop('checked',false)
          var network_id = $(this).val();
          var product_slug = $('#product_slug').val();
          let selectedNetwork = $('#network_id option:selected').text();
          // alert(selectedNetwork);return;


          if(selectedNetwork == 'MTN'){
            $('#mtn_svg').show();
            $('#airtel_svg').hide();
            $('#glo_svg').hide();
            $('#9mobile_svg').hide();
          }else if(selectedNetwork == 'GLO'){
            $('#mtn_svg').hide();
            $('#airtel_svg').hide();
            $('#glo_svg').show();
            $('#9mobile_svg').hide();
          }else if(selectedNetwork == 'AIRTEL'){
            $('#mtn_svg').hide();
            $('#airtel_svg').show();
            $('#glo_svg').hide();
            $('#9mobile_svg').hide();
          }else if(selectedNetwork == '9MOBILE'){
            $('#mtn_svg').hide();
            $('#airtel_svg').hide();
            $('#glo_svg').hide();
            $('#9mobile_svg').show();
          }else{
            $('#mtn_svg').hide();
            $('#airtel_svg').hide();
            $('#glo_svg').hide();
            $('#9mobile_svg').hide();
          }
          // $('#network_id').prepend('<option selected value="'+response.network_id+'">'+selectedNetwork+'</option>');

          
          // alert(network_id)
          //here you have to display all plans that are tied to this network but only where tied to the automation tied to each product plan category
          var amount = product_slug == 'airtime' ? $('#amount').val() : '';
          
          getProductPlans(network_id,'',product_slug,amount);
        })    
   })
  </script>


  
  <script>
    function themeToggle() {
      return {
        darkMode: false,
        showLoader: false,

        init() {
          this.darkMode = document.documentElement.classList.contains('dark');
        },

        toggle() {
          this.darkMode = !this.darkMode;
          localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
          document.documentElement.classList.toggle('dark', this.darkMode);
        }
      }
    }
  </script>

  <script>
     function selectPlan(element,idselector = 'product_plan_id') {
        // alert('sdfsfd')
        // Remove active class from others
        $('#plan_grid div').removeClass('border-blue-600 ring-2 ring-blue-200');

        // Add active class to selected
        $(element).addClass('border-blue-600 ring-2 ring-blue-200');

        // Set selected value
        const planId = $(element).data('id');
        $('#'+idselector).val(planId);
        $('#plan_error').addClass('hidden');
     }
  </script>


</body>
</html>
