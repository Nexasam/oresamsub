@extends('layouts.app_two')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-white text-2xl font-medium"> Settings</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                {{-- <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Home
                </li> --}}
            </ol>
        </div>
        <!-- Page Header Close -->

        <!-- Start::row-1 -->
        <div class="grid grid-cols-12 gap-1">
         
          <div class="col-span-12">
            @if (Session::has('success'))
              <div class="bg-success/10 border border-success/10 alert text-success" role="alert">
                {{ Session::get('success') }}
              </div>
            @endif

            @if (Session::has('failure'))
              <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                {{ Session::get('failure') }}
              </div>
            @endif
            
            @if ($errors->any())
              <div class="bg-danger/10 border border-danger/10 alert text-danger" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
              </div>
            @endif
          </div>

          <div class="col-span-12">
          
              <div class="box">
                <div class="box-header">
                  <h5 class="box-title">Admin Settings</h5>
                </div>

                <div class="box-body">
                  <nav class="flex space-x-2" aria-label="Tabs" role="tablist">
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white active" id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Referral Commissions
                    </button>
                    
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Bulk data settings (e.g h)
                    </button> --}}
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Landing pages
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-3" data-hs-tab="#pills-with-brand-color-3" aria-controls="pills-with-brand-color-3">
                      Site logo
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-4" data-hs-tab="#pills-with-brand-color-4" aria-controls="pills-with-brand-color-4">
                      Automation settings
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-white/70 dark:hover:text-white" id="pills-with-brand-color-item-5" data-hs-tab="#pills-with-brand-color-5" aria-controls="pills-with-brand-color-5">
                      Security
                    </button>
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">

                            <form method="POST" action="{{ route('admin.settings.referral_settings')  }}">
                               @csrf
                                <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                    <div class="space-y-2 mt-5">
                                      <label class="ti-form-label mb-0">  Manage commission feature </label>
                                      <select id="product_commission_feature" name="product_commission_feature" required class="my-auto ti-form-select">
                                          <option value="">Select</option>
                                          <option @if ($referral_setting->product_commission_feature == 1) selected @endif value="1">Activate flat rate</option>
                                          <option @if ($referral_setting->product_commission_feature == 2) selected @endif value="2">Activate percentage rate</option>
                                          <option @if ($referral_setting->product_commission_feature == 3) selected @endif value="3">Deactivate both</option>
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Set commission flat rate <br>
                                        <small>This will take effect if commission flat rate is activated</small>
                                      </label>
                                      <input value="{{ $referral_setting->set_product_commission_flat_rate }}" name="set_product_commission_flat_rate" type="number" required class="my-auto ti-form-input" min="0" id="set_product_commission_flat_rate"  placeholder="commission flat rate">
                                     </div>

                                     <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Set commission percentage rate <br>
                                        <small>This will take effect if commission percentage rate is activated</small>
                                      </label>
                                      <input value="{{ $referral_setting->set_product_commission_percentage_rate }}" name="set_product_commission_percentage_rate" type="number" required class="my-auto ti-form-input" max="100" placeholder="commission percentage rate">
                                    </div>


                                    <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Manage first crediting feature
                                          <br>
                                          <small>This determines how the upline is being awarded first crediting commission</small>
                                      </label>
                                      <select name="first_downline_crediting_feature" required class="my-auto ti-form-select">
                                          <option value="">Select</option>
                                          <option  @if ($referral_setting->first_downline_crediting_feature == 1) selected @endif value="1">Activate flat rate</option>
                                          <option  @if ($referral_setting->first_downline_crediting_feature == 2) selected @endif value="2">Activate percentage rate</option>
                                          <option  @if ($referral_setting->first_downline_crediting_feature == 3) selected @endif value="3">Deactivate both</option>
                           
                                        </select>
                                    </div>

                                    <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Set first crediting flat rate <br>
                                        <small>This will take effect if first crediting flat rate is activated</small>
                                      </label>
                                      <input value="{{ $referral_setting->set_first_downline_crediting_flat_rate }}" name="set_first_downline_crediting_flat_rate" type="number" required class="my-auto ti-form-input" min="0" placeholder="first crediting flat rate">
                                     </div>

                                     <div class="space-y-2">
                                      <label class="ti-form-label mb-0">Set first crediting percentage rate <br>
                                        <small>This will take effect if first crediting percentage rate is activated</small>
                                      </label>
                                      <input value="{{ $referral_setting->set_first_downline_crediting_percentage_rate }}" name="set_first_downline_crediting_percentage_rate" type="number" required class="my-auto ti-form-input" min="0" max="100" placeholder="first crediting percentage rate">
                                    </div>
                                

                                    <div class="space-y-2">
                                        <label class="ti-form-label mb-0">Set cap for first crediting commission <br>
                                          <small>This means an upline cannot get more than this value if first crediting commission is percentage-based</small>
                                        </label>
                                        <input value="{{ $referral_setting->set_first_downline_crediting_cap }}" name="set_first_downline_crediting_cap" type="number" required class="my-auto ti-form-input" min="0" max="100" placeholder="cap for first crediting">
                                    </div>
                                    <div class="space-y-2">
                                        <button type="submit" class="ti-btn ti-btn-primary w-full">Update Referral commission settings</button>
                                    </div>
                                  
                                    <br>
                                </div>
                            </form>
                        
                      </div>                
                    </div>
                    <div id="pills-with-brand-color-2" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-2">
                      <div class="overflow-auto">
                        <form method="POST" action="{{ route('admin.settings.manage_landing_page_settings')  }}">
                          @csrf
                          <p> <b> <a target="_blank" href="{{ url('/') }}">Click to preview your landing page </a> </b> </p>
                          <br>
                        
                          {{-- <div class="grid w-full lg:w-1/2 lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0"> --}}
                            <div class="grid lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0">
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Site logo alternative</label>
                                <input type="text" value="{{ $site_logo_alt }}"  name="site_logo_alt" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Site title</label>
                                <input type="text" value="{{ $site_title }}"  name="site_title" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Sub hero 1</label>
                              <input type="text" value="{{ $sub_hero1 }}"   name="sub_hero1" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Hero part 1</label>
                              <input value="{{ $hero1_part1 }}" type="text"  name="hero1_part1" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Hero part 2</label>
                              <input value="{{ $hero1_part2 }}" type="text"  name="hero1_part2" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Sub hero 2</label>
                              <input value="{{ $sub_hero2 }}" type="text"  name="sub_hero2" class="my-auto ti-form-input" placeholder="">
                              </div>   
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Hero2 part 1</label>
                              <input value="{{ $hero2_part1 }}" type="text" name="hero2_part1" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Hero2 part 2</label>
                              <input value="{{ $hero2_part2 }}" type="text"  name="hero2_part2" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">About us intro</label>
                              <input value="{{ $aboutus_introduction }}" type="text"  name="aboutus_introduction" class="my-auto ti-form-input" placeholder="">
                              </div>
                              
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Title analytics 1</label>
                              <input value="{{ $title_analytics1 }}" type="text"  name="title_analytics1" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Description for title analytics 1</label>
                              <input value="{{ $value_analytics1 }}" type="text"  name="value_analytics1" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Title analytics 2</label>
                                <input value="{{ $title_analytics2 }}" type="text"  name="title_analytics2" class="my-auto ti-form-input" placeholder="">
                                </div>
  
                                <div class="space-y-2">
                                <label class="ti-form-label mb-0">Description for title analytics 2</label>
                                <input value="{{ $value_analytics2 }}" type="text"  name="value_analytics2" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                                  <label class="ti-form-label mb-0">Title analytics 3</label>
                                  <input value="{{ $title_analytics3 }}" type="text"  name="title_analytics3" class="my-auto ti-form-input" placeholder="">
                              </div>
    
                              <div class="space-y-2">
                                  <label class="ti-form-label mb-0">Description for title analytics 3</label>
                                  <input value="{{ $value_analytics3 }}" type="text"  name="value_analytics3" class="my-auto ti-form-input" placeholder="">
                              </div>


                              <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Title analytics 4</label>
                                    <input value="{{ $title_analytics4 }}" type="text"  name="title_analytics4" class="my-auto ti-form-input" placeholder="">
                              </div>
      
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">Description for title analytics 4</label>
                                    <input value="{{ $value_analytics4 }}" type="text"  name="value_analytics4" class="my-auto ti-form-input" placeholder="">
                                </div>


                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Service intro</label>
                              <input value="{{ $service_intro }}" type="text"  name="service_intro" class="my-auto ti-form-input" placeholder="">
                              </div>

                              {{-- <div class="space-y-2">
                              <label class="ti-form-label mb-0">Data product title</label>
                              <input value="{{ $data_title }}" type="text" name="data_title" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Data product description</label>
                              <input value="{{ $data_description }}" type="text"  name="data_description" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Data product title</label>
                                <input value="{{ $data_title }}" type="text" name="data_title" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                <label class="ti-form-label mb-0">Data product description</label>
                                <input value="{{ $data_description }}" type="text"  name="data_description" class="my-auto ti-form-input" placeholder="">
                              </div> --}}

                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Data product title</label>
                                <input value="{{ $data_title }}" type="text" name="data_title" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                <label class="ti-form-label mb-0">Data product description</label>
                                <input value="{{ $data_description }}" type="text"  name="data_description" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Airtime product title</label>
                                <input value="{{ $airtime_title }}" type="text" name="airtime_title" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                <label class="ti-form-label mb-0">Airtime product description</label>
                                <input value="{{ $airtime_description }}" type="text"  name="airtime_description" class="my-auto ti-form-input" placeholder="">
                              </div>
                              
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Bills product title</label>
                                <input value="{{ $bills_title }}" type="text" name="bills_title" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                <label class="ti-form-label mb-0">Bills product description</label>
                                <input value="{{ $bills_description }}" type="text"  name="bills_description" class="my-auto ti-form-input" placeholder="">
                              </div>
                              
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Cable TV product title</label>
                                <input value="{{ $cable_tv_title }}" type="text" name="cable_tv_title" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                <label class="ti-form-label mb-0">Cable TV product description</label>
                                <input value="{{ $cable_tv_description }}" type="text"  name="cable_tv_description" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Epins product title</label>
                                <input value="{{ $epins_title }}" type="text" name="epins_title" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                <label class="ti-form-label mb-0">Epins product description</label>
                                <input value="{{ $epins_description }}" type="text"  name="epins_description" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Result checker product title</label>
                                <input value="{{ $result_checker_title }}" type="text" name="result_checker_title" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Result checker product description</label>
                                <input value="{{ $result_checker_description }}" type="text"  name="result_checker_description" class="my-auto ti-form-input" placeholder="">
                              </div>
                              
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Reviewer name1</label>
                              <input value="{{ $reviewer_name1 }}" type="text"  name="reviewer_name1" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Reviewer position1 (e.g Doctor, Chief etc)</label>
                              <input value="{{ $reviewer_position1 }}" type="text"  name="reviewer_position1" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Review1</label>
                                <textarea  name="review1" class="my-auto ti-form-input" placeholder="">{{ $review1 }}</textarea>
                              </div>

                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Reviewer name2</label>
                                <input value="{{ $reviewer_name2 }}" type="text"  name="reviewer_name2" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                <label class="ti-form-label mb-0">Reviewer position2 (e.g Doctor, Chief etc)</label>
                                <input value="{{ $reviewer_position2 }}" type="text"  name="reviewer_position2" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                  <label class="ti-form-label mb-0">Review2</label>
                                  <textarea  name="review2" class="my-auto ti-form-input" placeholder="">{{ $review2 }}</textarea>
                              </div>

                              
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Reviewer name3</label>
                                <input value="{{ $reviewer_name3 }}" type="text"  name="reviewer_name3" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                <label class="ti-form-label mb-0">Reviewer position3 (e.g Doctor, Chief etc)</label>
                                <input value="{{ $reviewer_position3 }}" type="text"  name="reviewer_position3" class="my-auto ti-form-input" placeholder="">
                                </div>
                                <div class="space-y-2">
                                  <label class="ti-form-label mb-0">Review3</label>
                                  <textarea  name="review3" class="my-auto ti-form-input" placeholder="">{{ $review3 }}</textarea>
                              </div>

                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Topnav email </label>
                              <input value="{{ $topnav_email }}" type="email"  name="topnav_email" class="my-auto ti-form-input" placeholder="">
                              </div>
                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Topnav phone</label>
                              <input value="{{ $topnav_phone }}" type="text"  name="topnav_phone" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Facebook link</label>
                              <input value="{{ $facebook_link }}" type="text"  name="facebook_link" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Support Whatsapp number</label>
                                <input value="{{ $support_whatsapp_number }}" type="text"  name="support_whatsapp_number" class="my-auto ti-form-input" placeholder="">
                              </div>
                              
                                <div class="space-y-2">
                              <label class="ti-form-label mb-0">Instagram link</label>
                              <input value="{{ $instagram_link }}" type="text"  name="instagram_link" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Twitter link</label>
                              <input value="{{ $twitter_link }}" type="text"  name="twitter_link" class="my-auto ti-form-input" placeholder="">
                              </div>
                             

                              {{-- <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">About Us </label>
                                  <div cols="10" rows="5" id="editor">
                                  </div>
                              </div> --}}

                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update landing page settings</button>
                              </div>
                            
                              <br>
                          </div>
                      </form>
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-3" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-3">
                      <div class="overflow-auto">
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.manage_site_logo')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Update site logo (ONLY PNG) </label>
                                <input type="file" required class="my-auto ti-form-input" name="site_logo" max="100" placeholder="update site logo">
                              </div>

                              
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update site logo</button>
                              </div>
                            
                              <br>
                          </div>
                      </form>
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-4" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-4">
                      <div class="overflow-auto">
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.manage_automations_keys')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-2 space-y-4 lg:space-y-0">
                              <p>SME PLUG AUTOMATION</p>
                              <div class="">
                                <label class="ti-form-label mb-0">Secret key: </label>
                                <input type="text"  required class="my-auto ti-form-input" name="smeplug_api_secret_key" value="{{ $smeplug->api_secret_key  ?? '' }}"  placeholder="">
                              </div> 
                              <br>
                              <hr>
                              <p>OGDAMS AUTOMATION</p>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Secret key: </label>
                                <input type="text" required class="my-auto ti-form-input"  value="{{ $ogdams->api_secret_key  ?? '' }}"  name="ogdams_api_secret_key"  placeholder="">
                              </div>
                              <br>
                              <hr>
                              <p>MEGASUBPLUG AUTOMATION</p>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Api Password: </label>
                                <input type="text" required class="my-auto ti-form-input" name="megasub_api_password" value="{{ $megasubplug->api_password  ?? '' }}"  placeholder="">
                              </div>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Public key: </label>
                                <input type="text" required class="my-auto ti-form-input" name="megasub_api_public_key" value="{{ $megasubplug->api_public_key  ?? '' }}"   placeholder="">
                              </div>
                              <br>
                              <hr>      
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update keys</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>
                        
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-5" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-5">
                      <div class="overflow-auto">
                        <form  method="POST" action="{{ route('admin.settings.manage_global_user_2fa')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-2 space-y-4 lg:space-y-0">
                              <p>Manage 2FA for all users</p>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">  Visibility status: <strong>{{  $admin_2fa_setting->global_user_2fa_setting == NULL ? 'OFF' : $admin_2fa_setting->global_user_2fa_setting  }}</strong> </label>
                                <select id="global_user_2fa_setting" name="global_user_2fa_setting" required class="my-auto ti-form-select">
                                    <option value="">Select</option>
                                    <option @if ($admin_2fa_setting->global_user_2fa_setting == NULL) selected @endif value="OFF">OFF</option>
                                    <option @if ($admin_2fa_setting->global_user_2fa_setting == 'ON') selected @endif value="ON">ON</option>
                                    <option @if ($admin_2fa_setting->global_user_2fa_setting == 'OFF') selected @endif value="OFF">OFF</option>
                                  </select>
                                </div>     
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Globally Hide/Show 2fa</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>

                        <hr>

                        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                          @csrf
              
                          @if(auth()->user()->two_factor_secret)
                              <h2 class="mt-2"> <strong>2Factor authentication setup</strong></h2>
                              <p>Two factor authentication is enabled.</p>
                              <div class="pt-5 pb-5">
                                  {!!  auth()->user()->twoFactorQrCodeSvg() !!}
                              </div>
                              <h3><strong>Please save recovery codes below:</strong></h3>
                              <ul>
                                  @foreach(auth()->user()->recoveryCodes() as $code)
                                      <p>{{ $code }}</p>
                                  @endforeach
                              </ul>
                              @method('DELETE')
                              <div class="space-y-2">
                                <button type="submit" class="ti-btn ti-btn-danger w-1/2">Disable 2fa</button>
                              </div>
                          @else
                              <div class="space-y-2">
                                <span class="text-red-600 mt-4 block">Two factor authentication is not enabled.</span>
                                <button type="submit" class="ti-btn ti-btn-primary w-1/2">Enable 2fa</button>
                              </div>
                          @endif
                        </form>
                        
                      </div>  
                    </div>
                  </div>
                </div>
               
                {{-- <div class="box-body">
                 
                </div> --}}
              </div>
              {{-- <div class="box-body">
                <div class="overflow-auto table-bordered p-4">
                  <table id="basic-table" class="ti-custom-table ti-striped-table ti-custom-table-hover">
                    <thead>
                        <tr>
                       
                            <td>First Name</td>
                            <td>Last Name</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                  </table>
                </div>
               
              </div> --}}
               
                
            </div>
          </div>
        </div>
        <!-- End::row-1 -->


        <!-- Start::row-3 -->
        {{-- <div class="grid grid-cols-12 gap-6">
          <div class="col-span-12">
            <div class="box">
              <div class="box-header">
                <h5 class="box-title">Reactivity DataTable</h5>
              </div>
              <div class="box-body space-y-3">
                <div class="reactivity-data">
                  <button type="button" class="ti-btn ti-btn-primary" id="reactivity-add">Add New Row</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="reactivity-delete">Remove Row</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="clear">Empty the table</button>
                  <button type="button" class="ti-btn ti-btn-primary" id="reset">Reset</button>
                </div>
                <div class="overflow-hidden table-bordered">
                  <div id="reactivity-table" class="ti-custom-table ti-striped-table ti-custom-table-hover"></div>
                </div>
              </div>
            </div>
          </div>
        </div> --}}
        <!-- End::row-3 -->

        <!-- Start::row-3 -->
        {{-- <div class="grid grid-cols-12 gap-6">
          <div class="col-span-12">
            <div class="box">
              <div class="box-header">
                <h5 class="box-title">Download DataTable</h5>
              </div>
              <div class="box-body space-y-3">
                <div class="download-data">
                    <button type="button" class="ti-btn ti-btn-primary" id="download-csv">Download CSV</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-json">Download JSON</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-xlsx">Download XLSX</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-pdf">Download PDF</button>
                    <button type="button" class="ti-btn ti-btn-primary" id="download-html">Download HTML</button>
                </div>
                <div class="overflow-hidden table-bordered">
                  <div id="download-table" class="ti-custom-table ti-striped-table ti-custom-table-hover"></div>
                </div>
              </div>
            </div>
          </div>
        </div> --}}
        <!-- End::row-3 -->

      </div>
      <!-- Start::main-content -->

       
@endsection

