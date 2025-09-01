$(document).ready(function(){
    const root_url = $('.root_url').val();
    var root_url_public = $('.root_url2').val();
    // alert(root_url_public)

    getUsers();        
    getPlanCategories();
    userGetTransactions();
    adminGetTransactions();
    getPublicPlans();
    getPlans();
    getAirtimeTransactions();
    getDataTransactions();
    getDataWalletTransactions();
    getCableTransactions();
    getElectricityTransactions();
    getCrystalPayUserFundingTransactions();
    getCrystalPayUserPendingTransactions();
    getCommissions();
    adminGetWalletLogs();
    adminGetUniqueProductPlans();

    function getPublicPlans(date_from ='', date_to =''){

      const data = {
        date_from : date_from,
        date_to : date_to,
      };
      
      // const url = root_url_public + 'product_plans/fetch_public_product_plans?date_from='+date_from+'&&date_to='+date_to;
      const url = root_url_public + 'product_plans/fetch_public_product_plans';
      // alert(url)
      $('#public_product_plans').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 50,
                ajax: root_url_public + 'product_plans/fetch_public_product_plans?date_from='+date_from+'&&date_to='+date_to,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'product_name', name: 'product_name'},
                    {data: 'network_name', name: 'network_name'},
                    {data: 'product_plan_name', name: 'product_plan_name'},
                    {data: 'product_plan_category_id', name: 'product_plan_category_id'},
                    {data: 'data_size_in_mb', name: 'data_size_in_mb'},
                    {data: 'user_level_1_selling_price', name: 'user_level_1_selling_price'},
                    {data: 'validity_in_days', name: 'validity_in_days'}
                  ]
        });
    }

    function getPlans(date_from ='', date_to =''){

      const data = {
        date_from : date_from,
        date_to : date_to,
      };
   
      $('#product_plans_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 50,
                ajax: root_url + 'admin/product_plans/fetch_product_plans?date_from='+date_from+'&&date_to='+date_to,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'product_name', name: 'product_name'},
                    {data: 'network_name', name: 'network_name'},
                    {data: 'product_plan_name', name: 'product_plan_name'},
                    {data: 'product_plan_category_id', name: 'product_plan_category_id'},
                    {data: 'automation', name: 'automation'},
                    {data: 'data_size_in_mb', name: 'data_size_in_mb'},
                    {data: 'validity_in_days', name: 'validity_in_days'},
                    {data: 'cost_price', name: 'cost_price'},
                    {data: 'user_level_1_selling_price', name: 'user_level_1_selling_price'},
                    {data: 'user_level_2_selling_price', name: 'user_level_2_selling_price'},
                    {data: 'user_level_3_selling_price', name: 'user_level_3_selling_price'},
                    {data: 'user_level_4_selling_price', name: 'user_level_4_selling_price'},
                    {data: 'visibility', name: 'visibility'},
                    {data: 'public_visibility', name: 'public_visibility'},
                    {data: 'date', name: 'date'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action'},
                  ]
        });
    }

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
                    // {data: 'automation_id', name: 'automation_id'},
                   
                    {data: 'network_id', name: 'network_id'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'is_hot_sales', name: 'is_hot_sales'},
                    {data: 'visibility', name: 'visibility'},
                    {data: 'action', name: 'action'},
                  ]
        });
    }

   
    function getCommissions(date_from ='', date_to ='', limit = 2000){
      const data = {
        date_from : date_from,
        date_to : date_to,
        limit : limit
      };

      console.log(data);

      $('#commissions_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'commissions/fetch_commissions?date_from='+date_from+'&&date_to='+date_to+'&&limit='+limit,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'user_details', name: 'user_details'},
                    {data: 'commission', name: 'commission'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'created_at'},
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
                    // {data: 'user_id', name: 'user_id'},
                    {data: 'wallet_category', name: 'wallet_category'},
                    {data: 'plan_details', name: 'plan_details'},
                    {data: 'transaction_category', name: 'transaction_category'},
                    // {data: 'response', name: 'response'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'amount', name: 'amount'},
                    {data: 'discounted_amount', name: 'discounted_amount'},
                    {data: 'balance_before', name: 'balance_before'},
                    // {data: 'data_size', name: 'data_size'},
                    {data: 'balance_after', name: 'balance_after'},
                    {data: 'status', name: 'status'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action'},
                  ]
      });
    }

  


    
    function adminGetWalletLogs(date_from ='', date_to ='', product_plan_category_filter = '', phone_recharged = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        product_plan_category_filter : product_plan_category_filter,
        phone_recharged : phone_recharged
      };
      console.log(data);
      // return;
      $('#admin_wallet_logs_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'admin/walletlogs/admin_fetch_wallet_logs?date_from='+date_from+'&&date_to='+date_to+'&&product_plan_category_filter='+product_plan_category_filter+'&&phone_recharged='+phone_recharged,
                columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                  {data: 'user_id', name: 'user_id'},
                  {data: 'transaction_id', name: 'transaction_id'},
                  {data: 'action_by', name: 'action_by'},
                  {data: 'transaction_category', name: 'transaction_category'},
                  {data: 'balance_before', name: 'balance_before'},
                  {data: 'balance_after', name: 'balance_after'},
                  {data: 'description', name: 'description'},
                  {data: 'created_at', name: 'created_at'},
                  {data: 'action', name: 'action'},
                ]
        });
    }

    function adminGetUniqueProductPlans(date_from ='', date_to ='', product_plan_category_filter = '', phone_recharged = ''){
          const data = {
            date_from : date_from,
            date_to : date_to,
            product_plan_category_filter : product_plan_category_filter,
            phone_recharged : phone_recharged
          };
          console.log(data);
          // return;
          $('#admin_unique_product_plans_table').DataTable({
                    autoWidth: false,
                    processing: true,
                    searching: true,
                    bInfo: false,
                    bLengthChange: true,
                    pageLength: 50,
                    ajax: root_url + 'admin/unique_product_plans/fetch?date_from='+date_from+'&&date_to='+date_to+'&&product_plan_category_filter='+product_plan_category_filter+'&&phone_recharged='+phone_recharged,
                    columns: [
                      {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                      {data: 'product_id', name: 'product_id'},
                      {data: 'size', name: 'size'},
                      {data: 'validity', name: 'validity'},
                      {data: 'network_id', name: 'network_id'},
                      {data: 'automations', name: 'automations'},
                      {data: 'visibility', name: 'visibility'},
                    ]
          });
    }

    function adminGetTransactions(date_from ='', date_to ='', product_plan_category_filter = '', phone_recharged = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        product_plan_category_filter : product_plan_category_filter,
        phone_recharged : phone_recharged
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
                ajax: root_url + 'admin/transactions/admin_fetch_transactions?date_from='+date_from+'&&date_to='+date_to+'&&product_plan_category_filter='+product_plan_category_filter+'&&phone_recharged='+phone_recharged,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                  {data: 'user_id', name: 'user_id'},
                  {data: 'wallet_category', name: 'wallet_category'},
                  {data: 'plan_details', name: 'plan_details'},
                  {data: 'transaction_category', name: 'transaction_category'},
                  // {data: 'response', name: 'response'},
                  // {data: 'admin_response', name: 'admin_response'},
                  {data: 'phone_number', name: 'phone_number'},
                  {data: 'amount', name: 'amount'},
                  {data: 'discounted_amount', name: 'discounted_amount'},
                  {data: 'balance_before', name: 'balance_before'},
                  // {data: 'data_size', name: 'data_size'},
                  {data: 'balance_after', name: 'balance_after'},
                  {data: 'status', name: 'status'},
                  {data: 'created_at', name: 'created_at'},
                  {data: 'action', name: 'action'},
                ]
        });
    }

    function getAirtimeTransactions(date_from ='', date_to ='', product_plan_category_filter = '', phone_recharged = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        product_plan_category_filter : product_plan_category_filter,
        phone_recharged : phone_recharged
      };
      console.log(data);
      // return;
      $('#airtime_transactions_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'transactions/fetch_airtime_transactions?date_from='+date_from+'&&date_to='+date_to+'&&product_plan_category_filter='+product_plan_category_filter+'&&phone_recharged='+phone_recharged,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                  // {data: 'user_id', name: 'user_id'},
                  {data: 'wallet_category', name: 'wallet_category'},
                  {data: 'plan_details', name: 'plan_details'},
                  {data: 'transaction_category', name: 'transaction_category'},
                  // {data: 'response', name: 'response'},
                  {data: 'phone_number', name: 'phone_number'},
                  {data: 'amount', name: 'amount'},
                  {data: 'discounted_amount', name: 'discounted_amount'},
                  {data: 'balance_before', name: 'balance_before'},
                  // {data: 'data_size', name: 'data_size'},
                  {data: 'balance_after', name: 'balance_after'},
                  {data: 'status', name: 'status'},
                  {data: 'created_at', name: 'created_at'},
                  {data: 'action', name: 'action'},
                ]
        });
    }

    
    function  getCrystalPayUserPendingTransactions(date_from ='', date_to ='', reference = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        reference : reference
      };
      $('#crystal_pay_pending_logs_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'transactions/fetch_crystal_pay_pending_transactions?date_from='+date_from+'&&date_to='+date_to+'&&reference='+reference,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                  {data: 'user', name: 'user'},
                  {data: 'payment_reference', name: 'payment_reference'},
                  {data: 'amount', name: 'amount'},
                  {data: 'status', name: 'status'},
                  {data: 'created_at', name: 'created_at'},
                  {data: 'action', name: 'action'},
                ]
        });
    }  


    function  getCrystalPayUserFundingTransactions(date_from ='', date_to ='', reference = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        reference : reference
      };
      $('#crystal_pay_funding_logs_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'transactions/fetch_crystal_pay_funding_transactions?date_from='+date_from+'&&date_to='+date_to+'&&reference='+reference,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                  {data: 'user_email', name: 'user_email'},
                  {data: 'transaction_reference', name: 'transaction_reference'},
                  {data: 'status', name: 'status'},
                  {data: 'funding_status', name: 'funding_status'},
                  {data: 'message', name: 'message'},
                  // {data: 'package_id', name: 'package_id'},
                  {data: 'bank_name', name: 'bank_name'},
                  {data: 'account_name', name: 'account_name'},
                  {data: 'account_number', name: 'account_number'},
                  {data: 'account_reference', name: 'account_reference'},
                  // {data: 'data_size', name: 'data_size'},
                  {data: 'amount_paid', name: 'amount_paid'},
                  {data: 'amount_charged', name: 'amount_charged'},
                  {data: 'amount_settled', name: 'amount_settled'},
                  {data: 'created_at', name: 'created_at'},
                  {data: 'action', name: 'action'},
                ]
        });
    }  

    function getDataWalletTransactions(date_from ='', date_to ='', product_plan_category_filter = '', phone_recharged = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        product_plan_category_filter : product_plan_category_filter,
        phone_recharged : phone_recharged
      };
      console.log(data);
      // return;
      $('#data_wallet_transactions_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'transactions/fetch_data_wallet_transactions?date_from='+date_from+'&&date_to='+date_to+'&&product_plan_category_filter='+product_plan_category_filter+'&&phone_recharged='+phone_recharged,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                  // {data: 'user_id', name: 'user_id'},
                  {data: 'wallet_category', name: 'wallet_category'},
                  {data: 'plan_details', name: 'plan_details'},
                  {data: 'transaction_category', name: 'transaction_category'},
                  // {data: 'response', name: 'response'},
                  {data: 'phone_number', name: 'phone_number'},
                  {data: 'amount', name: 'amount'},
                  {data: 'discounted_amount', name: 'discounted_amount'},
                  {data: 'balance_before', name: 'balance_before'},
                  // {data: 'data_size', name: 'data_size'},
                  {data: 'balance_after', name: 'balance_after'},
                  {data: 'status', name: 'status'},
                  {data: 'created_at', name: 'created_at'},
                  {data: 'action', name: 'action'},
                ]
        });
    }

    function getDataTransactions(date_from ='', date_to ='', product_plan_category_filter = '', phone_recharged = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        product_plan_category_filter : product_plan_category_filter,
        phone_recharged : phone_recharged
      };
      console.log(data);
      // return;
      $('#data_transactions_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'transactions/fetch_data_transactions?date_from='+date_from+'&&date_to='+date_to+'&&product_plan_category_filter='+product_plan_category_filter+'&&phone_recharged='+phone_recharged,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                  // {data: 'user_id', name: 'user_id'},
                  {data: 'wallet_category', name: 'wallet_category'},
                  {data: 'plan_details', name: 'plan_details'},
                  {data: 'transaction_category', name: 'transaction_category'},
                  // {data: 'response', name: 'response'},
                  {data: 'phone_number', name: 'phone_number'},
                  {data: 'amount', name: 'amount'},
                  {data: 'discounted_amount', name: 'discounted_amount'},
                  {data: 'balance_before', name: 'balance_before'},
                  // {data: 'data_size', name: 'data_size'},
                  {data: 'balance_after', name: 'balance_after'},
                  {data: 'status', name: 'status'},
                  {data: 'created_at', name: 'created_at'},
                  {data: 'action', name: 'action'},
                ]
        });
    }

    
    function getCableTransactions(date_from ='', date_to ='', product_plan_category_filter = '', smart_card_number = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        product_plan_category_filter : product_plan_category_filter,
        smart_card_number : smart_card_number
      };
      console.log(data);
      // return;
      $('#cable_transactions_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'transactions/fetch_cable_transactions?date_from='+date_from+'&&date_to='+date_to+'&&product_plan_category_filter='+product_plan_category_filter+'&&smart_card_number='+smart_card_number,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                  // {data: 'user_id', name: 'user_id'},
                  {data: 'wallet_category', name: 'wallet_category'},
                  {data: 'plan_details', name: 'plan_details'},
                  {data: 'transaction_category', name: 'transaction_category'},
                  // {data: 'response', name: 'response'},
                  {data: 'phone_number', name: 'phone_number'},
                  {data: 'smart_card_number', name: 'smart_card_number'},
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

    function getElectricityTransactions(date_from ='', date_to ='', product_plan_category_filter = '', metre_number = ''){
      const data = {
        date_from : date_from,
        date_to : date_to,
        product_plan_category_filter : product_plan_category_filter,
        metre_number : metre_number
      };
      console.log(data);
      // return;
      $('#electricty_transactions_table').DataTable({
                autoWidth: false,
                processing: true,
                searching: true,
                bInfo: false,
                bLengthChange: true,
                pageLength: 10,
                ajax: root_url + 'transactions/fetch_electricity_transactions?date_from='+date_from+'&&date_to='+date_to+'&&product_plan_category_filter='+product_plan_category_filter+'&&metre_number='+metre_number,
                // ajax:  "{{ route('admin.users.fetch_users',"+data+") }}",
                columns: [
                  {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                  // {data: 'user_id', name: 'user_id'},
                  {data: 'wallet_category', name: 'wallet_category'},
                  {data: 'plan_details', name: 'plan_details'},
                  {data: 'transaction_category', name: 'transaction_category'},
                  // {data: 'response', name: 'response'},
                  // {data: 'phone_number', name: 'phone_number'},
                  {data: 'metre_number', name: 'metre_number'},
                  {data: 'amount', name: 'amount'},
                  {data: 'discounted_amount', name: 'discounted_amount'},
                  {data: 'balance_before', name: 'balance_before'},
                  // {data: 'data_size', name: 'data_size'},
                  {data: 'balance_after', name: 'balance_after'},
                  {data: 'status', name: 'status'},
                  {data: 'created_at', name: 'created_at'},
                  {data: 'action', name: 'action'},
                ]
        });
    }
    

    $('#filter_crystalpay_txn_table').click(function(e){
      const date_from = $('#date_from_filter').val();
      const date_to = $('#date_to_filter').val();
      const txn_reference = $('#txn_reference').val();

      $("#crystal_pay_funding_logs_table").DataTable().destroy();
      getCrystalPayUserFundingTransactions(date_from,date_to,txn_reference);
    })

  ///txns
    $('#filter_user_txn_table').click(function(e){
      const product_plan_category_filter = $('#product_plan_category_filter').val();
      const date_from = $('#date_from_filter').val();
      const date_to = $('#date_to_filter').val();
      const phone_recharged = $('#phone_recharged').val();
      const smart_card_number = $('#smart_card_numberr').val();
      const metre_number = $('#metre_numberr').val();
      const limit = $('#limit').val();
      

      if(date_from > date_to){
        alert('Date from must be less than Date to');
        return;
      }
   
      $("#user_transactions_table").DataTable().destroy();
      userGetTransactions(date_from,date_to,product_plan_category_filter,phone_recharged);

      $("#admin_transactions_table").DataTable().destroy();
      adminGetTransactions(date_from,date_to,product_plan_category_filter,phone_recharged);

      $("#admin_wallet_logs_table").DataTable().destroy();
      adminGetWalletLogs(date_from,date_to,product_plan_category_filter,phone_recharged);

      

      $("#commissions_table").DataTable().destroy();
      getCommissions(date_from,date_to,limit);

      

      $("#airtime_transactions_table").DataTable().destroy();
      getAirtimeTransactions(date_from,date_to,product_plan_category_filter,phone_recharged);

      $("#data_transactions_table").DataTable().destroy();
      getDataTransactions(date_from,date_to,product_plan_category_filter,phone_recharged);

      $("#data_wallet_transactions_table").DataTable().destroy();
      getDataWalletTransactions(date_from,date_to,product_plan_category_filter,phone_recharged);

      $("#cable_transactions_table").DataTable().destroy();
      getCableTransactions(date_from,date_to,product_plan_category_filter,smart_card_number);
      
      $("#electricity_transactions_table").DataTable().destroy();
      getElectricityTransactions(date_from,date_to,product_plan_category_filter,metre_number);

      })

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

    $('#reload_txns_tbl').click(function(){

      $("#admin_wallet_logs_table").DataTable().destroy();
      adminGetWalletLogs();

      $("#user_transactions_table").DataTable().destroy();
      userGetTransactions();

      $("#admin_transactions_table").DataTable().destroy();
      adminGetTransactions();

      $("#commissions_table").DataTable().destroy();
      getCommissions();

      $("#airtime_transactions_table").DataTable().destroy();
      getAirtimeTransactions();

      $("#data_transactions_table").DataTable().destroy();
      getDataTransactions();

      $("#data_wallet_transactions_table").DataTable().destroy();
      getDataWalletTransactions();

      $("#cable_transactions_table").DataTable().destroy();
      getCableTransactions();

      $("#electricity_transactions_table").DataTable().destroy();
      getElectricityTransactions();

      $("#crystal_pay_funding_logs_table").DataTable().destroy();
      getCrystalPayUserFundingTransactions();

      
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
                    {data: 'status', name: 'status'},
                    {data: 'email', name: 'email'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'updated_at', name: 'updated_at'},
                    {data: 'action', name: 'action'},
                ]
        });
    }
   
})