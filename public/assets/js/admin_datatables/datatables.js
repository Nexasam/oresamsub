$(document).ready(function(){
    const root_url = $('.root_url').val();
    getUsers();        
    getPlanCategories();

    
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
                    {data: 'network_id', name: 'network_id'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action'},
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