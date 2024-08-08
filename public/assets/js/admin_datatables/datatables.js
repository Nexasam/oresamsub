$(document).ready(function(){
    const root_url = $('.root_url').val();
    getUsers();        
    getPlanCategories();
    userGetTransactions();
    adminGetTransactions();

    
    // $('#filter_user_table').click(function(e){
    //     const phone = $('#phone_filter').val();
    //     const email = $('#email_filter').val();
    //     const date_from = $('#date_from_filter').val();
    //     const date_to = $('#date_to_filter').val();
    //    $("#plan_categories_table").DataTable().destroy();
    //     getUsers(date_from,date_to,phone,email,'');
    // })

    function getPlanCategories(date_from ='', date_to =''){
      const data = {
        date_from : date_from,
        date_to : date_to,
      };
      console.log(data);
      // return;
      $('#plan_categories_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 50,
                ajax: root_url + 'admin/product_plan_categories/admin_fetch_product_plan_categories?date_from='+date_from+'&&date_to='+date_to,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'product_plan_category_name', name: 'product_plan_category_name'},
                    {data: 'product_id', name: 'product_id'},
                    {data: 'automation_id', name: 'automation_id'},
                    {data: 'discount_value', name: 'discount_value'},
                    {data: 'network_id', name: 'network_id'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'is_hot_sales', name: 'is_hot_sales'},
                    {data: 'action', name: 'action'},
                  ]
        });
    }

    function userGetTransactions(date_from ='', date_to ='', product_plan_category_filter = '', phone_recharged = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        product_plan_category_filter : product_plan_category_filter,
        phone_recharged : phone_recharged
      };
      console.log(data);
      // return;
      $('#user_transactions_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'user/transactions/user_fetch_transactions?date_from='+date_from+'&&date_to='+date_to+'&&product_plan_category_filter='+product_plan_category_filter+'&&phone_recharged='+phone_recharged,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'wallet_category', name: 'wallet_category'},
                    {data: 'plan_details', name: 'plan_details'},
                    {data: 'transaction_category', name: 'transaction_category'},
                    {data: 'response', name: 'response'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'amount', name: 'amount'},
                    {data: 'balance_before', name: 'balance_before'},
                    // {data: 'data_size', name: 'data_size'},
                    {data: 'balance_after', name: 'balance_after'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action'},
                  ]
        });
    }

    $('#filter_user_txn_table').click(function(e){
      const product_plan_category_filter = $('#product_plan_category_filter').val();
      const date_from = $('#date_from_filter').val();
      const date_to = $('#date_to_filter').val();
      const phone_recharged = $('#phone_recharged').val();

      if(date_from > date_to){
        alert('Date from must be less than Date to');
        return;
      }
   
      $("#user_transactions_table").DataTable().destroy();
      userGetTransactions(date_from,date_to,product_plan_category_filter,phone_recharged);
    })

    function adminGetTransactions(date_from ='', date_to =''){
      const data = {
        date_from : date_from,
        date_to : date_to,
      };
      console.log(data);
      // return;
      $('#admin_transactions_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'admin/transactions/admin_fetch_transactions?date_from='+date_from+'&&date_to='+date_to,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'wallet_category', name: 'wallet_category'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'amount', name: 'amount'},
                    {data: 'balance_before', name: 'balance_before'},
                    {data: 'data_size', name: 'data_size'},
                    {data: 'balance_after', name: 'balance_after'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'created_at'},
                    // {data: 'action', name: 'action'},
                  ]
        });
    }

    ///////users
    $('#filter_user_table').click(function(e){
        const phone = $('#phone_filter').val();
        const email = $('#email_filter').val();
        const date_from = $('#date_from_filter').val();
        const date_to = $('#date_to_filter').val();
        // $('#hs-slide-down-animation-modal').hide(); 
        // $('#hs-slide-down-animation-modal-backdrop').removeClass('transition duration fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 hs-overlay-backdrop')                

        $("#userss_table").DataTable().destroy();
        getUsers(date_from,date_to,phone,email,'');
    })
    
    $('#reload_user_tbl').click(function(){
      $("#userss_table").DataTable().destroy();
      getUsers();
    })

    function getUsers(date_from ='', date_to ='', phone = '', email = '', limit = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        limit : limit,
        phone : phone,
        email : email,
      };
      console.log(data);
      // return;
      $('#userss_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 50,
                ajax: root_url +'admin/users/fetch_users?date_from='+date_from+'&&date_to='+date_to+'&&phone='+phone+'&&email='+email,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'full_name', name: 'full_name'},
                    {data: 'main_wallet', name: 'main_wallet'},
                    {data: 'email', name: 'email'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action'},
                ]
        });
    }
   
})