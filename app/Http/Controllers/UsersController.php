<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('admin.users.index');
    }




    public function fetch_users(){
        $data = User::limit(4000)->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('first_name',function($data){
                return $data->first_name;
            })
            ->addColumn('last_name',function($data){
                return $data->last_name;
            }) 
            ->addColumn('phone_number',function($data){
                return $data->phone_number;
            }) 
            ->addColumn('email',function($data){
              return $data->email;
             }) 
            ->addColumn('action', function($data){
                // $actionBtn = ' ';
                $actionBtn = '<a href="#" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
                Edit
              </a><a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>';
              return ' 
              <button href="#" type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
              Edit
              </button>
              <button href="#" type="button" class="hs-dropdown-toggle ti-btn ti-btn-danger" data-hs-overlay="#hs-vertically-centered-scrollable-modal'.$data->email.'">
              Delete
              </button>
              <div id="hs-vertically-centered-scrollable-modal'.$data->email.'" class="hs-overlay hidden ti-modal">
                <div class="hs-overlay-open:mt-7 ti-modal-box mt-0 ease-out h-[calc(100%-3.5rem)] min-h-[calc(100%-3.5rem)] flex items-center">
                  <div class="max-h-full overflow-hidden ti-modal-content">
                    <div class="ti-modal-header">
                      <h3 class="ti-modal-title">
                        Modal title
                      </h3>
                      <button type="button" class="hs-dropdown-toggle ti-modal-close-btn" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
                        <span class="sr-only">Close</span>
                        <svg class="w-3.5 h-3.5" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M0.258206 1.00652C0.351976 0.912791 0.479126 0.860131 0.611706 0.860131C0.744296 0.860131 0.871447 0.912791 0.965207 1.00652L3.61171 3.65302L6.25822 1.00652C6.30432 0.958771 6.35952 0.920671 6.42052 0.894471C6.48152 0.868271 6.54712 0.854471 6.61352 0.853901C6.67992 0.853321 6.74572 0.865971 6.80722 0.891111C6.86862 0.916251 6.92442 0.953381 6.97142 1.00032C7.01832 1.04727 7.05552 1.1031 7.08062 1.16454C7.10572 1.22599 7.11842 1.29183 7.11782 1.35822C7.11722 1.42461 7.10342 1.49022 7.07722 1.55122C7.05102 1.61222 7.01292 1.6674 6.96522 1.71352L4.31871 4.36002L6.96522 7.00648C7.05632 7.10078 7.10672 7.22708 7.10552 7.35818C7.10442 7.48928 7.05182 7.61468 6.95912 7.70738C6.86642 7.80018 6.74102 7.85268 6.60992 7.85388C6.47882 7.85498 6.35252 7.80458 6.25822 7.71348L3.61171 5.06702L0.965207 7.71348C0.870907 7.80458 0.744606 7.85498 0.613506 7.85388C0.482406 7.85268 0.357007 7.80018 0.264297 7.70738C0.171597 7.61468 0.119017 7.48928 0.117877 7.35818C0.116737 7.22708 0.167126 7.10078 0.258206 7.00648L2.90471 4.36002L0.258206 1.71352C0.164476 1.61976 0.111816 1.4926 0.111816 1.36002C0.111816 1.22744 0.164476 1.10028 0.258206 1.00652Z" fill="currentColor"/>
                        </svg>
                      </button>
                    </div>
                    <div class="ti-modal-body">
                      <div class="space-y-4">
                        <div>
                          <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Be bold</h3>
                          <p class="mt-1 text-gray-800 dark:text-white/70">
                            Motivate teams to do their best work. Offer best practices to get users going in the right direction. Be bold and offer just enough help to get the work started, and then get out of the way. Give accurate information so users can make educated decisions. Know your user\'s struggles and desired outcomes and give just enough information to let them get where they need to go.
                          </p>
                        </div>

                        <div>
                          <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Be optimistic</h3>
                          <p class="mt-1 text-gray-800 dark:text-white/70">
                            Focusing on the details gives people confidence in our products. Weave a consistent story across our fabric and be diligent about vocabulary across all messaging by being brand conscious across products to create a seamless flow across all the things. Let people know that they can jump in and start working expecting to find a dependable experience across all the things. Keep teams in the loop about what is happening by informing them of relevant features, products and opportunities for success. Be on the journey with them and highlight the key points that will help them the most - right now. Be in the moment by focusing attention on the important bits first.
                          </p>
                        </div>

                        <div>
                          <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Be practical, with a wink</h3>
                          <p class="mt-1 text-gray-800 dark:text-white/70">
                            Keep our own story short and give teams just enough to get moving. Get to the point and be direct. Be concise - we tell the story of how we can help, but we do it directly and with purpose. Be on the lookout for opportunities and be quick to offer a helping hand. At the same time realize that novbody likes a nosy neighbor. Give the user just enough to know that something awesome is around the corner and then get out of the way. Write clear, accurate, and concise text that makes interusers more usable and consistent - and builds trust. We strive to write text that is understandable by anyone, anywhere, regardless of their culture or language so that everyone feels they are part of the team.
                          </p>
                        </div>
                      </div>
                    </div>
                    <div class="ti-modal-footer">
                      <button type="button" class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10" data-hs-overlay="#hs-vertically-centered-scrollable-modal">
                        Close
                      </button>
                      <a class="ti-btn ti-btn-primary" href="javascript:void(0);">
                        Save changes
                      </a>
                    </div>
                  </div>
                </div>
              </div>';
            
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      // dd('testing');
       return view('admin.users.create'); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'phone_number' => 'required',
        'email' => 'required|unique:users,email',
        'password' => 'required',
        'confirm_password' => 'required',
        'gender' => 'required',
      ]);
      

      if ($validator->stopOnFirstFailure()->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $data['first_name'] = $request->first_name;
      $data['last_name'] = $request->last_name;
      $data['phone_number'] = $request->phone_number;
      $data['email'] = $request->email;
      $data['first_name'] = $request->first_name;
      $data['password'] = Hash::make($request->password);
      $data['confirm_password'] = Hash::make($request->confirm_password);
      $data['gender'] = $request->gender;

      $create_user = User::create($data);

      if($create_user){
        Session::flash('success','User successfully created');
      }else{
        Session::flash('failure','Error occurred while creating user');
      }

      return redirect()->route('admin.users.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
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
