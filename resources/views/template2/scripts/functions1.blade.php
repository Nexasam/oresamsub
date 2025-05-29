<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="{{asset(env('APP_ASSETS_BASE_URL').'js/admin_datatables/datatables.js') }}"></script>



{{-- <script src="{{asset(env('APP_ASSETS_BASE_URL').'js/swetalert.js') }}"></script> --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
  function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

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
                              const selling_price = dataList[child].selling_price;
                              if(product_slug == 'data'){
                                option = "<option value="+idd+">"+product_plan_name+'- &#8358;'+selling_price+"</option>";
                              }
                              else if(product_slug == 'airtime' && amount != ''){
                                option = "<option value="+idd+">"+product_plan_name+'- You are buying for: &#8358;'+selling_price+"</option>";
                              }
                              else if(product_slug == 'airtime' && amount == ''){
                                option = "<option value="+idd+">"+product_plan_name+"</option>";
                              }else{
                                option = "<option value="+idd+">"+product_plan_name+"</option>";
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



  function getSingleAirtimePlan(plan_category_id='', amount = ''){
          

          if(plan_category_id != '' && amount != ''){
            var data = {
              plan_category_id : plan_category_id,
              amount : amount
            };

            $.ajax({
                    type: 'GET',
                    url: "{{ route('user.airtime.fetch_single_airtime_plan') }}",
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        console.log(response)
                        // console.log(response.data)
                        var result = JSON.stringify(response.data);
                        var dataList = JSON.parse(result);
                    
                        $('#product_plan_id').html("");
                        // $('#product_plan_id').append('<option value="">Select Product Plan</option>');

                          // let jj = jsonn;
                          for (const child in dataList) {
                          
                              const idd = dataList[child].product_plan_id;
                              const product_plan_name = dataList[child].product_plan_name;
                              const selling_price = dataList[child].selling_price;
                          
                              option = "<option selected value="+idd+">"+product_plan_name+'- You are buying for: &#8358;'+selling_price+"</option>";
                                $('#product_plan_id').append(option);
                            
                            
                          }
                        
                      
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                  });
        
          }else{
          return;
          }

        
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
                          for (const child in dataList) {
                          
                              const idd = dataList[child].product_plan_id;
                              const product_plan_name = dataList[child].product_plan_name;
                              const selling_price = dataList[child].selling_price;
                              option = "<option value="+idd+">"+product_plan_name+'- &#8358;'+selling_price+"</option>";
                              
                              $('#cable_product_plan_id').append(option);
                            
                          }
                        
                      
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
  
                          // let jj = jsonn;
                          for (const child in dataList) {
                              const idd = dataList[child].product_plan_id;
                              const product_plan_name = dataList[child].product_plan_name;
                              const selling_price = dataList[child].selling_price;
                              option = "<option value="+idd+">"+product_plan_name+'- You are buying for: &#8358;'+selling_price+"</option>";     
                              $('#electricity_product_plan_id').append(option);
                          }
                        
                      
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
            });
    }

  

  function reload(timeout = '3000'){
    setTimeout(() => {
      window.location.reload();
    }, timeout);
  }

  function sweetAlertDisplay(message,title,status){
    Swal.fire({
          icon: status,
          title: title,
          text: message,
          // footer: '<a href="">Why do I have this issue?</a>'
          });
  }

  function sweetAlertConfirmation(){
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'ti-btn bg-secondary text-white hover:bg-secondary focus:ring-secondary dark:focus:ring-offset-secondary',
          cancelButton: 'ti-btn bg-danger text-white hover:bg-danger focus:ring-danger dark:focus:ring-offset-danger'
        },
        buttonsStyling: false
      })

      swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          swalWithBootstrapButtons.fire(
            'Deleted!',
            'Your file has been deleted.',
            'success'
          )
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalWithBootstrapButtons.fire(
            'Cancelled',
            'Your imaginary file is safe :)',
            'error'
          )
        }
      })
  }


  function generateCrystalPayDynamicAcct(amount){
    if(amount == ''){
      sweetAlertDisplay('Please enter amount','Amount required','error')
      return;
    }

    $.ajax({
                    type: 'GET',
                    url: "{{ route('user.crystalpay.generate_dynamic_account') }}",
                    data: { amount: amount},
                    dataType: 'json',
                    success: function(response) {
                        var result = JSON.stringify(response.data);
                        var dataList = JSON.parse(result);
                        console.log(dataList);
                        const bank_name = dataList.bank_name;
                        const account_number = dataList.account_number;

                        $('.crystal_pay_dynamic_account_details').append(`<p>Bank Name:  ${bank_name} </p>`);
                        $('.crystal_pay_dynamic_account_details').append(`<p>Account No:  ${account_number}</p>`);          
                        $('.crystal_pay_dynamic_account_details').append(`<p><strong>NOTE:</strong> Please ensure that the exact amount of ${amount} is paid into the generated account. </p>`);          
                        $('.crystal_pay_dynamic_account_details').append(`Please complete transaction in 5 minutes else the account will be invalid.</p>`);          
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
            });
  }
    
  
  

  function togglePassword(className,id,showValue){
    $('.'+className).change(function(e){
          e.preventDefault();
          var get_attr = $('#'+id).attr('type');
          if(get_attr == "text" || get_attr == "number"){
              $('#'+id).attr("type", "password");
              return;
          }

          $('#'+id).attr("type", showValue);
          return;
    })
  }

  
  function toggleProductPlanVisibility(productPlanId,token,checkedd){
  
            const data = {
              productPlanId : productPlanId,
              token : token
            };
            // console.log(data);

              $.ajax({
              type: 'GET',
              url: "{{ route('admin.product_plans.toggle_product_visibility') }}",
              data: data,
              dataType: 'json',
              success: function(response) {
                  $('#nnotification'+productPlanId).removeClass('hidden');
                  $('#nnotification'+productPlanId).html(response.message);
                  console.log(response);
                  // console.log(response.data);
                  // var result = JSON.stringify(response);
                  // var dataList = JSON.parse(result);
                  // if(dataList.status == 1){
                  //    sweetAlertDisplay(dataList.message,'Success','success');
                  //    reload(3000);
                  // }else{
                  //   sweetAlertDisplay(dataList.message,'Error','error');
                  // }
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
          });
  }

  function toggleProductPlanPublicVisibility(productPlanId,token,checkedd){
  
  const data = {
    productPlanId : productPlanId,
    token : token
  };
  // console.log(data);

    $.ajax({
    type: 'GET',
    url: "{{ route('admin.product_plans.toggle_product_public_visibility') }}",
    data: data,
    dataType: 'json',
    success: function(response) {
        $('#nnotification2'+productPlanId).removeClass('hidden');
        $('#nnotification2'+productPlanId).html(response.message);
        console.log(response);
    },
    error: function(xhr, status, error) {
        // Handle errors if needed
        console.error(xhr.responseText);
    }
});
}

  function togglePlanCategoryVisibility(productPlanCategoryId,token,checkedd){
          

            const data = {
              productPlanCategoryId : productPlanCategoryId,
              token : token
            };
            // console.log(data);

              $.ajax({
              type: 'GET',
              url: "{{ route('admin.product_plan_categories.toggle_plan_category_visibility') }}",
              data: data,
              dataType: 'json',
              success: function(response) {
                  $('#plan_cat_visibility_notification'+productPlanCategoryId).removeClass('hidden');
                  $('#plan_cat_visibility_notification'+productPlanCategoryId).html(response.message);
                  console.log(response);
                  // console.log(response.data);
                  // var result = JSON.stringify(response);
                  // var dataList = JSON.parse(result);
                  // if(dataList.status == 1){
                  //    sweetAlertDisplay(dataList.message,'Success','success');
                  //    reload(3000);
                  // }else{
                  //   sweetAlertDisplay(dataList.message,'Error','error');
                  // }
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
          });
  }

  function toggleUserStatus(userId,token,actualValue){
          

          const data = {
            userId : userId,
            token : token
          };
        //  console.log(data);
        //  return;
          
          $.ajax({
            type: 'GET',
            url: "{{ route('admin.users.toggle_verification_status') }}",
            data: data,
            dataType: 'json',
            success: function(response) {
                $('#user_verification_notification'+userId).removeClass('hidden');
                $('#user_verification_notification'+userId).html(response.message);
                console.log(response);
            },
            error: function(xhr, status, error) {
                // Handle errors if needed
                console.error(xhr.responseText);
            }
        });
}

  function toggleHotSales(planCategoryId,token,checkedd){
            // alert(planCategoryId)
            // var check = $('#hs-basic-with-description-checked'+planCategoryId).checked;
            // if(checkedd != ''){
            //   alert('checked')
            //   // $('#hs-basic-with-description-checked'+planCategoryId).checked;
            //   $('#hs-basic-with-description-checked'+planCategoryId).prop( 'checked', false )
            // }else{
            //   $('#hs-basic-with-description-checked'+planCategoryId).prop( 'checked', true )
            //   alert('unchecked')
            // }

            const data = {
              planCategoryId : planCategoryId,
              token : token
            };
            // console.log(data);

              $.ajax({
              type: 'GET',
              url: "{{ route('admin.product_plan_categories.toggle_hot_sales') }}",
              data: data,
              dataType: 'json',
              success: function(response) {
                  $('#hot_sales_notification'+planCategoryId).removeClass('hidden');
                  $('#hot_sales_notification'+planCategoryId).html(response.message);
                  console.log(response);
                  // console.log(response.data);
                  // var result = JSON.stringify(response);
                  // var dataList = JSON.parse(result);
                  // if(dataList.status == 1){
                  //    sweetAlertDisplay(dataList.message,'Success','success');
                  //    reload(3000);
                  // }else{
                  //   sweetAlertDisplay(dataList.message,'Error','error');
                  // }
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
          });
  }

  function copyToClipboard() {
        // Get the text field
        var copyText = document.getElementById("myInput");

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

        // Alert the copied text
        alert("Copied to clipboard");
  }

  function debounce(func, timeout = 3000){
    let timer;
    return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
  }


  function doValidateNameOnSmartCard(typpe=''){
    // alert(typpe)
    if(typpe == 'electricity'){
      var smart_card_number = $('#metre_number').val();
      var url = "{{ route('user.electricity.validate_metre_number') }}";
      var plan_id = $('#electricity_product_plan_id').val();
      var display_id = 'validation_extra_info';
      var display_address = 'validation_address';


    }
    else if(typpe == 'cable'){
      console.log('hereee')
      var smart_card_number = $('#smart_card_number').val();
      var url = "{{ route('user.cable_subscription.validate_smart_card_number') }}";
      var plan_id = $('#cable_product_plan_id').val();
      var display_id = 'validation_customer_name';

    }else{
      var smart_card_number = 'ttt';
      //this should never run
    }


    var data = {
      smart_card_number : smart_card_number,
      plan_id : plan_id
    };
    $('#validated_name_on_smart_card').html('<span style="color:black;">Validating...</span>');

    $.ajax({
              type: 'GET',
              url: url,
              data: data,
              dataType: 'json',
              success: function(response) {
                  // $('#validated_name_on_smart_card').removeClass('hidden');
                  $('#validated_name_on_smart_card').html('');
                  $('#validated_name_on_smart_card').append('Please validate the details below before payment:<br>');
                  $('#validated_name_on_smart_card').append(`<p>Name on Card: <strong>${response.name}</strong> </p>`);
                  if(typpe == 'electricity'){
                    $('#validated_name_on_smart_card').append(`<p>Address: <strong>${response.address}</strong> </p>`);
                    $('#'+display_address).val(response.address);
                  }
                  $('#'+display_id).val(response.name);
                
                  console.log(response);
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
          });
  }
  
  const validateNameOnSmartCard = debounce((typee) => doValidateNameOnSmartCard(typee));

  togglePassword('show_pin1','pin','number');
  togglePassword('show_pin2','current_pin','number');
  togglePassword('show_pin3','new_pin','number');
  togglePassword('show_pin4','confirm_new_pin','number');
  togglePassword('show_pin5','pin5','number');
  togglePassword('show_password','new_password','text');
  togglePassword('show_password2','confirm_new_password','text');
  togglePassword('show_password_current','current_password','text');
  
  

  $(document).ready(function(){

    // alert('kaososos')

    //TEMPLATE TWO STARTS HERE
    $("input[name='network_id']").click(function(){
            var network_id = $(this).val(); // Get selected value
            console.log("Selected Network ID: " + network_id); // Log to console
            // alert(network_id + 'working now')
            $('#product_plan_category_id').html('<option value="all">All categories selected</option>');
            $("#product_plan_category_div").addClass('hidden');
            $('#filter_by_plan_category').prop('checked',false)
            
            var product_slug = $('#product_slug').val();

            // // alert(network_id)
            // //here you have to display all plans that are tied to this network but only where tied to the automation tied to each product plan category
            var amount = product_slug == 'airtime' ? $('#amount').val() : '';

            getProductPlans(network_id,'',product_slug,amount);
    });

    $("input[name='cable_product_plan_category_id']").change(function(){
      var plan_category_id = $(this).val();
      var product_slug = $("#product_slug").val();
      // alert(plan_category_id);return;
      if(plan_category_id == '' || plan_category_id == 'all'){
        sweetAlertDisplay('Product plan category is required','Plan category required','error');
        return;
      }

      getCableProductPlans(plan_category_id,product_slug);
    })
    //TEMPLATE TWO ENDS HERE

    $('#utility_amount').keyup(function(e){
        e.preventDefault();
        var amount =  $(this).val();
        var product_slug = $("#product_slug").val();
        var plan_category_id = $('#electricity_product_plan_category_id').val();
          
        var amount = product_slug == 'airtime' || product_slug == 'utility_bills'  ? amount : '';
        // alert(plan_category_id)

        getElectricityProductPlans(plan_category_id,product_slug,amount);

    });

    $('#electricity_product_plan_category_id').change(function(){
      var product_slug = $("#product_slug").val();
      var plan_category_id = $(this).val();

      if(plan_category_id == '' || plan_category_id == 'all'){
        sweetAlertDisplay('Product plan category is required','Plan category required','error');
        return;
      }
      getElectricityProductPlans(plan_category_id,product_slug);
    })

    $('#buy_electricity_btn').click(function(e){
      e.preventDefault();
        $(this).html('Processing...Please wait');
        $(this).prop('disabled',true);
        $('#cancel_disabling').removeClass('hidden')

      
        //display product plans categories
        const electricity_product_plan_category_id = $('#electricity_product_plan_category_id').val();
        const metre_number = $('#metre_number').val();
        const validation_extra_info = $('#validation_extra_info').val();
        const validation_address = $('#validation_address').val();
        const wallet_category = $('#wallet_category').val();
        const electricity_product_plan_id = $('#electricity_product_plan_id').val();
        const pin = $('#pin').val();
        const no_of_slots = $('#no_of_slots').val();
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
                      reload(3000);
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
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
            });
        } else {
          return;
        }

        
      
    })

    $('#buy_data_btn').click(function(e){
      e.preventDefault();
        $(this).html('Processing...Please wait');
        $(this).prop('disabled',true);
        $('#cancel_disabling').removeClass('hidden')

      
        //display product plans categories
        var network_id = $('#network_id').val();
        if(network_id == undefined){
          var network_id = $("input[name='network_id']:checked").val();
        }
        // alert(network_id);return;
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
                      reload(3000);
                  }
                  else if(dataList.status == 2){
                      //@least 1 tranaction had an issue
                      sweetAlertDisplay(dataList.message,'Info','warning');
                      reload(6000);
                  }
                  else{
                    sweetAlertDisplay(dataList.message,'Error','error');
                    $(this).prop('disabled',false);
                  }
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
            });
        } else {
          return;
        }   
    })


    $('#buy_airtime_btn').click(function(e){
      e.preventDefault();
        $(this).html('Processing...Please wait');
        $(this).prop('disabled',true);
        $('#cancel_disabling').removeClass('hidden')

      
         //display product plans categories
         var network_id = $('#network_id').val();
        if(network_id == undefined){
          var network_id = $("input[name='network_id']:checked").val();
        }
        // alert(network_id);return;
       
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
          sweetAlertDisplay('You need to buy atleast N50 worth of airtime','Airtime purchase error','error');
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
                  if( parseInt(dataList.status) == 1){
                      sweetAlertDisplay(dataList.message,'Success','success');
                      reload(3000);
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
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
            });
        } else {
          return;
        }

        
      
    })


    $('#buy_cable_btn').click(function(e){
      e.preventDefault();
        $(this).html('Processing...Please wait');
        $(this).prop('disabled',true);
        $('#cancel_disabling').removeClass('hidden')

      
        //display product plans categories
        var cable_product_plan_category_id = $('#cable_product_plan_category_id').val();
        // alert(cable_product_plan_category_id);return;
        if(cable_product_plan_category_id == undefined){
          var cable_product_plan_category_id = $("input[name='cable_product_plan_category_id']:checked").val();
        }
        alert(cable_product_plan_category_id);
        let smart_card_number = $('#smart_card_number').val();
        const validation_customer_name = $('#validation_customer_name').val();
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
                  // console.log(response.data);
                  var result = JSON.stringify(response);
                  var dataList = JSON.parse(result);
                  if( parseInt(dataList.status) == 1){
                      sweetAlertDisplay(dataList.message,'Success','success');
                      reload(3000);
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
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
            });
        } else {
          alert('wetinnn')
          return;
        }      
    })


    // $("[data-toggle='modal']").click(function () {
        $("#popup").fadeIn();
    // });

    $("[data-close]").click(function () {
        $(this).parents(".modal").fadeOut();
    });
  

    //reset
    // $('#buy_data_btn').click
    $('#cancel_disabling').click(function(e){
      e.preventDefault();
      $('#buy_data_btn').html('Buy Data');
      $('#buy_data_btn').prop('disabled',false);
      $(this).addClass('hidden');

      $('#buy_airtime_btn').html('Buy Airtime');
      $('#buy_airtime_btn').prop('disabled',false);
      $(this).addClass('hidden');

      $('#buy_cable_btn').html('Buy Cable TV');
      $('#buy_cable_btn').prop('disabled',false);
      $(this).addClass('hidden');

      $('#buy_electricity_btn').html('Buy Electricity');
      $('#buy_electricity_btn').prop('disabled',false);
      $(this).addClass('hidden');
    
    })

    //reset ends

    // alert('sss');

    $('.single_select').select2();
    $('#product_plan_category_id').html('<option value="all">All categories selected</option>');

    // $('.edit_cat').click( (e) => {
    //   e.preventDefault();
    //   var catid = $(this).attr('id');
    //   // let newValue = $('#'+catid).text();
    //   console.log(catid)
    // });

    
    $('#generate_crystalpay_dynamic_account').click(function(e){
        e.preventDefault();
        const amount = $('#amount').val();
        if(amount == ''){
          sweetAlertDisplay('Please enter amount','Amount required','error')
          return;
        }
        $(this).text('Generating dynamic acount...');
        $(this).prop('disabled',true);
        // return;
        generateCrystalPayDynamicAcct(amount);
    })

    $('#wallet_category').change(function(e){
        e.preventDefault();
        const wallet_type = $(this).val();
    });

  

    $('#amount').keyup(function(e){
        e.preventDefault();
        var amount =  $(this).val();
        var network_id = $("#network_id").val();
        var product_slug = $("#product_slug").val();
        var plan_category_id = $('#product_plan_category_id').val();
          
        if(network_id == ''){
          sweetAlertDisplay('Network is required','Network required','error');
          return;
        }
        var amount = product_slug == 'airtime' ? $('#amount').val() : '';
  
        getProductPlans(network_id,plan_category_id,product_slug,amount);
    });

    $('#amount_for_airtime_category').keyup(function(e){
        e.preventDefault();
        var amount =  $(this).val();
        var network_id = $("#network_id").val();
        var product_slug = $("#product_slug").val();
        var plan_category_id = $('#product_plan_category_id').val();
          
        if(network_id == ''){
          sweetAlertDisplay('Network is required','Network required','error');
          return;
        }
        var amount = product_slug == 'airtime' ? $(this).val() : '';
      
        getSingleAirtimePlan(plan_category_id,amount);
    });

    

    


    $('#product_plan_category_id').change(function(){
      var network_id = $("#network_id").val();
      var product_slug = $("#product_slug").val();
      var plan_category_id = $(this).val();

      // alert(plan_category_id)

      if(network_id == ''){
        sweetAlertDisplay('Network is required','Network required','error');
        return;
      }

      if(plan_category_id == '' || plan_category_id == 'all'){
        sweetAlertDisplay('Product plan category is required','Plan category required','error');
        return;
      }
      var amount = product_slug == 'airtime' ? $('#amount').val() : '';
      
      getProductPlans(network_id,plan_category_id,product_slug,amount);
    })

    $('#cable_product_plan_category_id').change(function(){
      var product_slug = $("#product_slug").val();
      var plan_category_id = $(this).val();

      if(plan_category_id == '' || plan_category_id == 'all'){
        sweetAlertDisplay('Product plan category is required','Plan category required','error');
        return;
      }

      getCableProductPlans(plan_category_id,product_slug);
    })

    
    
    
    $('#buy_bulk_data_btn').click(function(e){
      e.preventDefault();
      
        //display product plans categories
        const bulk_data_plan_id = $('#bulk_data_plan_id').val();
        const bulk_data_wallet_id = $('#bulk_data_wallet_id').val();
        const pin = $('#pin').val();
        const _token = $('#_token').val();
      
        const data = {
          bulk_data_plan_id : bulk_data_plan_id,
          bulk_data_wallet_id : bulk_data_wallet_id,
          pin : pin,
          _token : _token,
        };

        if (confirm("Are you sure you want to complete this purchase?") == true) {
            // alert('logic happens here')
            $.ajax({
              type: 'POST',
              url: "{{ route('user.data.buy_bulk_data_action') }}",
              data: data,
              dataType: 'json',
              success: function(response) {
                  // console.log(response);
                  // console.log(response.data);
                  var result = JSON.stringify(response);
                  var dataList = JSON.parse(result);
                  if(dataList.status == 1){
                      sweetAlertDisplay(dataList.message,'Success','success');
                      reload(3000);
                  }else{
                    sweetAlertDisplay(dataList.message,'Error','error');
                  }
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
          });
        } else {
          return;
        }

      
        
      
    })

    $('#bulk_data_plan_id').change(function(e){
      e.preventDefault();
      const bulk_data_plan_id = $(this).val();
      // alert(bulk_data_plan_id);

      if(bulk_data_plan_id == ''){
        sweetAlertDisplay("Please select a plan",'Plan required','error');
      }
  
      const data = {
          bulk_data_plan_id : bulk_data_plan_id,
      };  
      
      $.ajax({
            type: 'GET',
            url: "{{ route('user.data.fetch_bulk_data_plan_details') }}",
            data: data,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                
                if(response.status == '1'){
                    var bulk_data_plan_name = response.data.bulk_data_plan_name;
                    var selling_price = numberWithCommas(response.data.selling_price);
                    var data_value_mb = numberWithCommas(response.data.data_value_mb);
                    var data_value_gb = numberWithCommas(response.data.data_value_gb);
                    // var data_value_tb = numberWithCommas(response.data.data_value_tb);
                    var data_value_tb = response.data.data_value_tb;
                    var selling_price = numberWithCommas(response.data.selling_price);
                    var mb_data_measurement = numberWithCommas(response.data.mb_data_measurement);
                    
                    var data_content = '<p>Bulk data plan: '+ bulk_data_plan_name+'</p>';
                    data_content += '<p>Measurement per MB: '+ mb_data_measurement+'</p>';
                    data_content += '<p>Data value in TB: '+ data_value_tb+'</p>';
                    data_content += '<p>Data value in GB: '+ data_value_gb+'</p>';
                    data_content += '<p>Data value in MB: '+ data_value_mb+'</p>';
                    data_content += '<p>Price: &#8358;'+ selling_price+'</p>';
                    $('#bulk_data_plan_details').removeClass('hidden');
                    $('#bulk_data_plan_details').html(data_content);
                }else{
                    $('#bulk_data_plan_details').removeClass('hidden');
                    $('#bulk_data_plan_details').html('');
                    console.log(response);
                } 
              
            },
            error: function(xhr, status, error) {
                // Handle errors if needed
                // console.error(xhr.responseText);
                $('#bulk_data_plan_details').html('');
                console.log('Something went wrong')
            }
        });

      })

    $('#bulk_data_wallet_id').change(function(e){
        e.preventDefault();
        $('#bulk_data_plan_id').html('<option value="">Select a plan</option>');
        // $('#bulk_data_plan_details').addClass('hidden');
        $('#bulk_data_plan_details').html('');
      //  alert('testing')
        
        const bulk_data_wallet_id = $(this).val();
        const _token = $('#_token').val();
        
        const data = {
          bulk_data_wallet_id : bulk_data_wallet_id,
          _token : _token
        };
        console.log(_token,bulk_data_wallet_id);

        $.ajax({
            type: 'POST',
            url: "{{ route('user.data.fetch_bulk_data_plans') }}",
            data: data,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                
                if(response.status == '1'){
                  let dataResult = response?.data;
                  $('#bulk_data_plan_id').html('<option value="">Select a plan</option>');

                  dataResult.forEach(element => {
                      const idd = element.id;
                      const data_value_mb = element.data_value_mb;
                      const data_value_gb = element.data_value_gb;
                      const data_value_tb = element.data_value_tb;
                      const bulk_data_plan_name = element.bulk_data_plan_name;
                      // console.log(element)
                        option = "<option value="+idd+">"+bulk_data_plan_name+"</option>"
                      $('#bulk_data_plan_id').append(option);
                    // console.log(category_name);
                  });

                }else{
                  
                  console.log(response);
                }
              
            },
            error: function(xhr, status, error) {
                // Handle errors if needed
                console.error(xhr.responseText);
            }
        });
    });

    $('#network_id').change(function(){
      $('#product_plan_category_id').html('<option value="all">All categories selected</option>');
      $("#product_plan_category_div").addClass('hidden');
      $('#filter_by_plan_category').prop('checked',false)
      var network_id = $(this).val();
      var product_slug = $('#product_slug').val();
      
      // alert(network_id)
      //here you have to display all plans that are tied to this network but only where tied to the automation tied to each product plan category
      var amount = product_slug == 'airtime' ? $('#amount').val() : '';
      
      getProductPlans(network_id,'',product_slug,amount);
    })

    $('#filter_by_plan_category').change(function(e){
      e.preventDefault();
  
      
      if(this.checked){
        $("#product_plan_category_div").removeClass('hidden');
        //display product plans categories
        const network_id = $('#network_id').val();
        const product_slug = $('#product_slug').val();

        if(network_id == ''){
          sweetAlertDisplay("Please select network",'Network required','error');
          $(this).prop('checked', false); // Unchecks it
          return;
        }
        const data = {
          network_id : network_id,
          product_slug : product_slug
        };

          $.ajax({
              type: 'GET',
              url: "{{ route('user.fetch_product_plan_categories') }}",
              data: data,
              dataType: 'json',
              success: function(response) {
                    console.log('testing',response)
                  // showDisplayButton(id);
                  if(response.status == '1'){
                    let dataResult = response?.data;
                    $('#product_plan_category_id').html('<option value="all">Select category</option>');


                    dataResult.forEach(element => {
                        const idd = element.id;
                        const category_name = element.product_plan_category_name;
                        option = "<option value="+idd+">"+category_name+"</option>"
                        $('#product_plan_category_id').append(option);
                        // console.log(category_name);

                    });
                  }
                  // console.log(response.data);
                  //$('#notify_span'+id).text('successfully saved...');
                  // showDisplayButton(id);
              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
          });
      }else{
        $('#product_plan_category_id').html('<option value="all">All categories selected</option>');
        $("#product_plan_category_div").addClass('hidden');
      }
    })


    $('#product_plan_id').change(function(e){
      const wallet_category = $('#wallet_category').val();
      const product_plan_id = $('#product_plan_id').val();
      var url = "{{ route('user.data.get_single_bulk_data_wallet', ":product_plan_id") }}";
      url = url.replace(':product_plan_id', product_plan_id);

      if(wallet_category == ''){
        sweetAlertDisplay('Wallet category cannot be empty','Wallet selection required','error');        
      }

      if(wallet_category == 'data_wallet'){
        //then display the wallet for the selected product plan's category
        $.ajax({
              type: 'GET',
              url: url,
              data: {},
              dataType: 'json',
              success: function(response) {
                  // console.log(response);
                  if(response.status == 1){
                      const display = 'Your wallet balance for '+ response.data.product_plan_category.product_plan_category_name + ' is '+ response.wallet;
                      $('.display_wallet_details').text(display);
                      return;
                  }else{
                      const display = 'Your wallet balance is 0 MB... Buy bulk data wallet';
                      $('.display_wallet_details').text(display);
                  }

              },
              error: function(xhr, status, error) {
                  // Handle errors if needed
                  console.error(xhr.responseText);
              }
          });
      }

    })

   
    

  


  })

</script>