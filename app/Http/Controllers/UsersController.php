<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPlan;
use App\Models\SiteTemplate;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\UserBulkDataWallet;
use App\Models\UserVirtualAccount;
use App\Models\ProductPlanCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\StoreUserRequest;
use App\Mail\WalletFundingNotification;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Traits\Dashboard\UserDashboardDataTrait;


class UsersController extends Controller
{

    use UserDashboardDataTrait;

    public function impersonate($id){


        $user = User::where('id',$id)->first();
        if(! $user){
          Session::flash('failure','This customer does not exist.');
          return redirect()->back();
        }

        
        if(auth()->user()->role->role_name != 'Admin' && auth()->user()->email != 'adebsholey4real@gmail.com' ){
          Session::flash('failure','You do not have the privilege to do this.');
          return redirect()->back();
        }

        $fullname = $user->first_name.' '.$user->last_name;
      
        session()->put('impersonator', auth()->id()); // Store original user id
        auth()->login($user); // Log in as target user
        Session::flash('success','You are now impersonating customer: '. $fullname .' as an Admin');
        return redirect()->route('dashboard');

    }

    public function exit_impersonate(){
        if (!session()->has('impersonator')) {
          return redirect()->back();
        }

        $originalUserId = session()->pull('impersonator');
        $originalUser = User::find($originalUserId);
        auth()->login($originalUser);

        // return redirect('/')->with('status', 'You have stopped impersonating.');
        Session::flash('success','You have stopped impersonating');
        return redirect()->route('admin.users.index');

    }



    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      //  Gate::authorize('viewAny', User::class);


        $users = User::with(['role' => function($query){
          $query->where('role_name','User');
        }])->limit(2000)->latest()->get();
        $user_plans = UserPlan::all();
        $roles = Role::all();
        $data['user_plans'] = $user_plans;
        $data['roles'] = $roles;
        $data['users'] = $users;
        return view('admin.users.index')->with($data);
    }

    public function api_docs(){
      $data = $this->get_user_dashboard_data();
      $data['hideNav'] = true;
      // dd($data);
      $siteTemplate = SiteTemplate::first();
      if(! $siteTemplate || $siteTemplate->template_name == 'template_1'){
          return view('user.api_docs.index')->with($data);
      }

      return view('template2.user.api_docs.index')->with($data);
    }


    public function generate_user_bulk_data_wallets(){
        $product_plan_categories = ProductPlanCategory::with(['product' => function($query){
            $query->where('slug','data');
        }])->pluck('id');
        // return $product_plan_categories;
        // dd($product_plan_categories);
        foreach($product_plan_categories as $product_plan_category){
            UserBulkDataWallet::updateOrCreate([
                'user_id' => auth()->id(),
                'product_plan_category_id' => $product_plan_category,
            ],[]);
        } 
        
        Session::flash('success','Wallets successfully generated');
        return redirect()->back();
        
    }


    public function manage_profile(Request $request){
      $user = auth()->user();
      if(!$user){
        Session::flash('failure','User not found');
        return redirect()->back();
      }
      return view('user.profile.index')->with(['user' => $user]);
    }

    public function admin_manage_profile(Request $request){
      $user = auth()->user();
      if(!$user){
        Session::flash('failure','User not found');
        return redirect()->back();
      }
      return view('user.profile.index')->with(['user' => $user]);
    }

    

    

    /**
     * Display a listing of the resource. for admin
     */
    public function manage_user(Request $request,$id)
    {
      //  Gate::authorize('viewAny', User::class);

        $user = User::with(['role','upline:id,username,phone_number','virtual_accounts'])->where('id',$id)->first();
        $user_plans = UserPlan::get();
        if(!$user){
          Session::flash('failure','User not found');
          return redirect()->back();
        }

          $upline = User::where('id',$user->upline_id)->first();
       
        return view('admin.users.manage_users')->with(['user' => $user, 'user_plans' => $user_plans, 'upline' => $upline]);
    }

    /**
     * Display a listing of the resource. by ADMIN
     */
    public function fund_user_wallet(Request $request)
    {
      //  Gate::authorize('viewAny', User::class);

      $validator = Validator::make($request->all(), [
        'amount' => 'required|numeric',
        'pin' => 'required|max:255',
        'user_id' => 'required|exists:users,id',
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      if(auth()->user()->pin != $request->pin){
        //end session and redirect to login
        $dataaa['status'] = 'failed';
        Mail::to(auth()->user())->send(new WalletFundingNotification($dataaa));
        Session::flash('failure','Incorrect PIN entered'); 
        return redirect()->back();
      }

      $user_id = $request->user_id;
      $amount = $request->amount;
      // dd($user_id);
      
      $get_user = User::select('first_name','last_name','id','main_wallet')->where('id',$user_id)->first();
      $former_balance = $get_user->main_wallet;
      $full_name = $get_user->first_name .' '. $get_user->last_name;
      $new_main_wallet = $former_balance + $amount;

      $update_user_wallet = User::where('id',$user_id)->update([
          'main_wallet' => $new_main_wallet
      ]);

      $walletLog['user_id'] = $user_id;
      $walletLog['transaction_category'] = 'ADMIN_WALLET_CREDITING';
      $walletLog['balance_before'] = $former_balance;
      $walletLog['balance_after'] = $new_main_wallet;
      $walletLog['transaction_id'] = NULL;
      $walletLog['action_by'] = auth()->user()->id;
      $walletLog['description'] = 'Wallet Crediting by Admin';
      $this->log_wallet_transactions($walletLog);


      if($update_user_wallet){
        $dataaa['status'] = 'successful';
        Mail::to(auth()->user())->send(new WalletFundingNotification($dataaa));
        Session::flash('success','Wallet was successfully funded for '. $full_name);
      }else{
        $dataaa['status'] = 'failed';
        Mail::to(auth()->user())->send(new WalletFundingNotification($dataaa));
        Session::flash('failure','Error occurred while funding account');
      }

      return redirect()->route('admin.users.index');
    }

     /**
     * Display a listing of the resource. by ADMIN
     */
    public function reset_2fa(Request $request)
    {
      //  Gate::authorize('viewAny', User::class);

      $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $user_id = $request->user_id;
      $get_user = User::select('first_name','last_name','id','main_wallet')->where('id',$user_id)->first();
      $full_name = $get_user->first_name .' '. $get_user->last_name;
      

      $update_user_2fa = User::where('id',$user_id)->update([
          'two_factor_secret' => NULL,
          'two_factor_recovery_codes' => NULL,
      ]);

      if($update_user_2fa){
        Session::flash('success', '2FA was successfully reset for '. $full_name);
      }else{
        Session::flash('failure','Error occurred while reseting 2fa');
      }

      return redirect()->route('admin.users.index');
    }


    /** 
    * Update user plan. by ADMIN
    */
   public function update_user_plan(Request $request)
   {
     //  Gate::authorize('viewAny', User::class);

     $validator = Validator::make($request->all(), [
       'user_id' => 'required|exists:users,id',
       'user_plan_id' => 'required|exists:user_plans,id',
     ]);
     

     if ($validator->stopOnFirstFailure()->fails()) {
       return redirect()->back()->withErrors($validator)->withInput();
     }

     
     $user_plan_id = $request->user_plan_id;
     $user_id = $request->user_id;
     User::where('id',$user_id)->update([
        'user_plan_id' => $user_plan_id
     ]);

     Session::flash('success','User plan was successfully updated');

    return redirect()->route('admin.users.index');
  
  }


      /** 
    * Update user plan. by ADMIN
    */
    public function update_user_info(Request $request)
    {
      //  Gate::authorize('viewAny', User::class);
      // dd($request->all());
     
      $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'phone_number' => 'nullable',
        'pin' => 'required','integer','regex:/^\d{4,5}$/',
        'user_plan_id' => ['required','string','exists:user_plans,id'],
      ]);
      
 
      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }
 
     
      if($request->pin != 'xxxx'){
        $dat['pin'] = $request->pin;
      }

      if($request->phone_number == NULL){
        $dat['phone_number'] = $request->phone_number;
      }
      
      $dat['user_plan_id'] = $request->user_plan_id;

      // dd($dat);

      $user_id = $request->user_id;
      
      User::where('id',$user_id)->update($dat);
 
      Session::flash('success','User profile was successfully updated');
 
     return redirect()->back();
   
   }

    

    

    public function toggle_verification_status(Request $request){
      $validator = Validator::make($request->all(), [
        'userId' => 'required|max:255|exists:users,id',
        'token' => 'required',
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $detail = User::where('id',$request->userId)->first();
      $updated_value = $detail->email_verified_at == NULL ? date('Y-m-d H:i:s') : NULL;
     
      $detail->update([
        'email_verified_at' => $updated_value
      ]);

      return response()->json(['status'=>'1', 'message'=>'success']);

    }
    

    public function fetch_users(Request $request){
        // Gate::authorize('viewAny', User::class);
        $phone = $request->phone ?? '';
        // $date_from = $request->date_from ?? date('Y-m-d');
        // $date_to= $request->date_to ?? date('Y-m-d');

        $date_from = $request->date_from ?? '';
        $date_to= $request->date_to ?? '';


        $email = $request->email ?? '';
        $limit = $request->limit ?? 500;


        // $date_from = date('Y-m-d',strtotime($date_from));
        // $date_to = date('Y-m-d',strtotime($date_to));
        
        $data = User::with(['role','upline:id,username,phone_number','virtual_accounts'])->when(!empty($phone) , function ($query) use ($phone){
          $query->where('phone_number',$phone);
        })
        ->when( !empty($email) , function ($query) use ($email){
          $query->where('email',$email)
                ->orWhere('username',$email);
        })
        ->when( !empty($date_from) && !empty($date_to) , function ($query) use ($date_from,$date_to){
          $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
         })
         ->limit($limit)
        ->get();
        
        // return $data;

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('DT_RowIndex',function($data){
              return $data->id;
            })
            ->addColumn('full_name',function($data){
              $fullnameinfo = $data->first_name.' '.$data->last_name.'('.$data->username.') <br>Upline: ';
              $fullnameinfo .= $data->upline != NULL ? $data->upline->username.' '.substr($data->upline->phone_number, 0, 11 - 8) . str_repeat('*', 6) : 'none';
              $fullnameinfo .= '<br>Virtual Accounts Generated: '.count($data->virtual_accounts);
              
              // if(count($data->virtual_accounts) > 0){
              //   $fullnameinfo .= '<br>Virtual Accounts</br>';
              //   foreach($data->virtual_accounts as $va){
              //     $fullnameinfo .= '<br>Bank Name: '.$va->account_name.'&nbsp;';
              //     $fullnameinfo .= '<br>Account Name: '.$va->account_number.'&nbsp;';
              //     $fullnameinfo .= '<br>Account No: '.$va->account_number.'&nbsp;';
              //     $fullnameinfo .= '<br>';

              //   }
              // }

              if(auth()->user()->role->role_name == 'Admin'){
                $fullnameinfo .= '<form action="' . route('user.virtual_accounts.generate') . '" method="POST">
                <input type="hidden" name="_token" value="' . csrf_token() . '">
                <input type="hidden" name="user_id" value="' . $data->id . '">
                <div class="mb-4">
                    <button type="submit" class="ti-btn ti-btn-primary w-full">' . __('messages.Generate Virtual Accounts') . '</button>
                </div>
                </form>';        
              }


              return $fullnameinfo;
            })
            ->addColumn('main_wallet',function($data){
              return number_format($data->main_wallet,2);
            }) 
            ->addColumn('status',function($data){

              $escapedUrl = htmlspecialchars(json_encode($data->id));
              $token = htmlspecialchars(json_encode(csrf_token()));
              $checked = $data->email_verified_at != NULL ? 'checked':'';
              $actual_value = $data->email_verified_at;
              $actual_value = htmlspecialchars(json_encode($actual_value));
              $toggle_btn = '<div class="flex items-center mb-2">';
              $toggle_btn .=  '<input onchange="toggleUserStatus('.$escapedUrl.','.$token.','.$actual_value.')" type="checkbox" id="hs-basic-with-description-checked'.$data->id.'" class="ti-switch" '.$checked.'>';
              $toggle_btn .=  '<label for="hs-basic-with-description-checked" class="text-sm text-gray-500 ms-3 dark:text-white/70 "></label>';
              $toggle_btn .=  ' <span class="badge rounded-sm bg-success/10 text-success hidden" id="user_verification_notification'.$data->id.'"></span>  </div>';

              $toggle_btn .=  $data->email_verified_at == NULL ? '<span class="badge bg-danger text-white">Unverified</span>' : '<span class="badge bg-success text-white">Verified</span>';
              return $toggle_btn;
            }) 
            ->addColumn('email',function($data){
              return $data->email;
             }) 
          
            ->addColumn('phone_number',function($data){
           
                $first_name = $data->first_name;
                $phone_number = $data->phone_number;
                $call = '<strong><a class="underline" href="tel:'.$phone_number.'">Call: '.$phone_number.'</a></strong>';
    
                return $call.'</a>';
            }) 

            ->addColumn('created_at',function($data){
              return $data->created_at;
            }) 

            ->addColumn('updated_at',function($data){
              return $data->updated_at;
             }) 
           
            ->addColumn('action', function($data){
                // $actionBtn = ' ';
                // <a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>
                $route = route('admin.users.manage_user',$data->id);
                $impersonate_route = route('admin.impersonate',$data->id);
                $actionBtn = '<a href="'.$route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
                Manage User
              </a>';
                
                // if($data->role->role_name == 'User'){
                if($data->id != auth()->id()  && $data->email != 'adebsholey4real@gmail.com'){
                  $actionBtn .= '<a href="'.$impersonate_route.'" type="button" class="hs-dropdown-toggle ti-btn ti-btn-info">
                  Impersonate '.$data->username.'
                  </a>';
                }

                
              return $actionBtn;
                  // return ' 
                  // <button href="#" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
                  // Edit
                  // </button>
                  // <button href="#" type="button" class="hs-dropdown-toggle ti-btn ti-btn-danger" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
                  // Delete
                  // </button>
                  // <div id="hs-vertically-centered-scrollable-modal'.$data->email.'" class="hs-overlay hidden ti-modal">
                  //   <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out h-[calc(100%-3.5rem)] min-h-[calc(100%-3.5rem)] flex items-center">
                  //     <div class="max-h-full overflow-hidden ti-modal-content">
                  //       <div class="ti-modal-header">
                  //         <h3 class="ti-modal-title">
                  //           Modal title
                  //         </h3>
                  //         <button type="button" class="hs-dropdown-toggle ti-modal-close-btn" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
                  //           <span class="sr-only">Close</span>
                  //           <svg class="w-3.5 h-3.5" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                  //             <path d="M0.258206 1.00652C0.351976 0.912791 0.479126 0.860131 0.611706 0.860131C0.744296 0.860131 0.871447 0.912791 0.965207 1.00652L3.61171 3.65302L6.25822 1.00652C6.30432 0.958771 6.35952 0.920671 6.42052 0.894471C6.48152 0.868271 6.54712 0.854471 6.61352 0.853901C6.67992 0.853321 6.74572 0.865971 6.80722 0.891111C6.86862 0.916251 6.92442 0.953381 6.97142 1.00032C7.01832 1.04727 7.05552 1.1031 7.08062 1.16454C7.10572 1.22599 7.11842 1.29183 7.11782 1.35822C7.11722 1.42461 7.10342 1.49022 7.07722 1.55122C7.05102 1.61222 7.01292 1.6674 6.96522 1.71352L4.31871 4.36002L6.96522 7.00648C7.05632 7.10078 7.10672 7.22708 7.10552 7.35818C7.10442 7.48928 7.05182 7.61468 6.95912 7.70738C6.86642 7.80018 6.74102 7.85268 6.60992 7.85388C6.47882 7.85498 6.35252 7.80458 6.25822 7.71348L3.61171 5.06702L0.965207 7.71348C0.870907 7.80458 0.744606 7.85498 0.613506 7.85388C0.482406 7.85268 0.357007 7.80018 0.264297 7.70738C0.171597 7.61468 0.119017 7.48928 0.117877 7.35818C0.116737 7.22708 0.167126 7.10078 0.258206 7.00648L2.90471 4.36002L0.258206 1.71352C0.164476 1.61976 0.111816 1.4926 0.111816 1.36002C0.111816 1.22744 0.164476 1.10028 0.258206 1.00652Z" fill="currentColor"/>
                  //           </svg>
                  //         </button>
                  //       </div>
                  //       <div class="ti-modal-body">
                  //         <div class="space-y-4">
                  //           <div>
                  //             <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-900">Be bold</h3>
                  //             <p class="mt-1 text-gray-800 dark:text-white/70">
                  //               Motivate teams to do their best work. Offer best practices to get users going in the right direction. Be bold and offer just enough help to get the work started, and then get out of the way. Give accurate information so users can make educated decisions. Know your user\'s struggles and desired outcomes and give just enough information to let them get where they need to go.
                  //             </p>
                  //           </div>

                  //           <div>
                  //             <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-900">Be optimistic</h3>
                  //             <p class="mt-1 text-gray-800 dark:text-white/70">
                  //               Focusing on the details gives people confidence in our products. Weave a consistent story across our fabric and be diligent about vocabulary across all messaging by being brand conscious across products to create a seamless flow across all the things. Let people know that they can jump in and start working expecting to find a dependable experience across all the things. Keep teams in the loop about what is happening by informing them of relevant features, products and opportunities for success. Be on the journey with them and highlight the key points that will help them the most - right now. Be in the moment by focusing attention on the important bits first.
                  //             </p>
                  //           </div>

                  //           <div>
                  //             <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-900">Be practical, with a wink</h3>
                  //             <p class="mt-1 text-gray-800 dark:text-white/70">
                  //               Keep our own story short and give teams just enough to get moving. Get to the point and be direct. Be concise - we tell the story of how we can help, but we do it directly and with purpose. Be on the lookout for opportunities and be quick to offer a helping hand. At the same time realize that novbody likes a nosy neighbor. Give the user just enough to know that something awesome is around the corner and then get out of the way. Write clear, accurate, and concise text that makes interusers more usable and consistent - and builds trust. We strive to write text that is understandable by anyone, anywhere, regardless of their culture or language so that everyone feels they are part of the team.
                  //             </p>
                  //           </div>
                  //         </div>
                  //       </div>
                  //       <div class="ti-modal-footer">
                  //         <button type="button" class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
                  //           Close
                  //         </button>
                  //         <a class="ti-btn ti-btn-primary" href="javascript:void(0);">
                  //           Save changes
                  //         </a>
                  //       </div>
                  //     </div>
                  //   </div>
                  // </div>';  
            
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       return view('admin.users.create'); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      // Gate::authorize('create', User::class);

      //for ADMIN
      $validator = Validator::make($request->all(), [
        'username' => ['required', 'string', 'unique:users,username'],
        'pin' => ['required','string','regex:/^\d{4,5}$/'],
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        // 'other_names' => 'nullable|max:255',
        'phone_number' => 'required|digits:11',
        'email' => 'required|unique:users,email',
        // 'role_id' => 'required|unique:roles,id',
        'user_plan_id' => 'required|exists:user_plans,id',
        'password' => ['required', 'confirmed', Password::min(8)
        ->letters()
        ->mixedCase()
        ->numbers()
        ->symbols()
        ->uncompromised()::defaults()],
        // 'confirm_password' => 'required',
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $role_details = Role::where('role_name','User')->first();
      $default_reseller_plan = UserPlan::where('is_default',1)->first();
      $data['first_name'] = $request->first_name;
      $data['last_name'] = $request->last_name;
      $data['username'] = $request->username;
      // $data['other_names'] = $request->other_names;
      $data['phone_number'] = $request->phone_number;
      $data['email'] = $request->email;
      $data['role_id'] = $role_details->id;
      $data['user_plan_id'] = $request->user_plan_id;
      $data['password'] = Hash::make($request->password);
      // $data['confirm_password'] = Hash::make($request->confirm_password);
      // $data['gender'] = $request->gender;

      $create_user = User::create($data);

      if($create_user){
        Session::flash('success','Account successfully created');
      }else{
        Session::flash('failure','Error occurred while creating account');
      }

      return redirect()->route('admin.users.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
      //  Gate::authorize('view', User::class);
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
      //  Gate::authorize('update', User::class);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

}
