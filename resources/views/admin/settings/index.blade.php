@extends('layouts.app')
@section('content')

      <!-- Start::main-content -->
      <div class="main-content">

        <!-- Page Header -->
        <div class="block justify-between page-header md:flex">
            {{-- <div>
                <h3 class="text-gray-700 hover:text-gray-900 dark:text-gray-900 dark:hover:text-white text-2xl font-medium"> Settings</h3>
            </div>
            <ol class="flex items-center whitespace-nowrap min-w-0">
              
                <li class="text-sm text-gray-500 hover:text-primary dark:text-white/70 " aria-current="page">
                    Home
                </li> 
            </ol> --}}
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
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-gray-500 active" id="pills-with-brand-color-item-1" data-hs-tab="#pills-with-brand-color-1" aria-controls="pills-with-brand-color-1">
                      Referral Commissions
                    </button>
                    
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-gray-500" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Bulk data settings (e.g h)
                    </button> --}}
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hovertext-gray-500" id="pills-with-brand-color-item-2" data-hs-tab="#pills-with-brand-color-2" aria-controls="pills-with-brand-color-2">
                      Landing pages
                    </button>
                    {{-- <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-gray-500" id="pills-with-brand-color-item-7" data-hs-tab="#pills-with-brand-color-7" aria-controls="pills-with-brand-color-7">
                      Template 2
                    </button> --}}
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-gray-500" id="pills-with-brand-color-item-3" data-hs-tab="#pills-with-brand-color-3" aria-controls="pills-with-brand-color-3">
                      Site Images & Colors
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-gray-500" id="pills-with-brand-color-item-4" data-hs-tab="#pills-with-brand-color-4" aria-controls="pills-with-brand-color-4">
                      Settings
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-gray-500" id="pills-with-brand-color-item-6" data-hs-tab="#pills-with-brand-color-6" aria-controls="pills-with-brand-color-6">
                      Funding settings
                    </button>
                    <button type="button" class="hs-tab-active:bg-primary hs-tab-active:text-white py-3 px-4 inline-flex items-center gap-2 bg-transparent text-sm font-medium text-center text-gray-500 rounded-sm hover:text-primary  dark:text-gray-500 dark:hover:text-gray-500" id="pills-with-brand-color-item-5" data-hs-tab="#pills-with-brand-color-5" aria-controls="pills-with-brand-color-5">
                      Security
                    </button>
                  </nav>

                  <div class="mt-3">
                    <div id="pills-with-brand-color-1" role="tabpanel" aria-labelledby="pills-with-brand-color-item-1">
                      <div class="overflow-auto">

                            <form method="POST" action="{{ route('admin.settings.referral_settings')  }}">
                               @csrf
                                <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                                    
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
                          <h1><strong>Template 2 Settings Below</strong> 👇👇👇👇👇👇👇👇👇</h1>

                          <div class="grid lg:grid-cols-2 gap-6 space-y-4 lg:space-y-0">
                            
                            <div class="space-y-2">
                            <label class="ti-form-label mb-0">Site Title</label>
                            <input type="text" value="{{ $site_title_template2 }}"   name="site_title_template2" class="my-auto ti-form-input" placeholder="">
                            </div>
                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Site Logo Alt</label>
                              <input type="text" value="{{ $site_logo_alt_template2 }}"   name="site_logo_alt_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                            <label class="ti-form-label mb-0">Hero Main Text</label>
                            <input value="{{ $hero_main_text_template2 }}" type="text"  name="hero_main_text_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Hero Main Stylish Word</label>
                              <input value="{{ $hero_main_text_stylish_template2 }}" type="text"  name="hero_main_text_stylish_template2" class="my-auto ti-form-input" placeholder="">
                              </div>
                            
                            <div class="space-y-2">
                            <label class="ti-form-label mb-0">Hero Sub Text</label>
                            <input value="{{ $hero_sub_text_template2 }}" type="text"  name="hero_sub_text_template2" class="my-auto ti-form-input" placeholder="">
                            </div>
                            <div class="space-y-2">
                            <label class="ti-form-label mb-0">Hero Lovers Count</label>
                            <input value="{{ $hero_lovers_count_template2 }}" type="text"  name="hero_lovers_count_template2" class="my-auto ti-form-input" placeholder="">
                            </div>  
                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">About us description</label>
                              <input value="{{ $about_us_description_template2 }}" type="text"  name="about_us_description_template2" class="my-auto ti-form-input" placeholder="">
                              </div>   
                            <div class="space-y-2">
                            <label class="ti-form-label mb-0">Data description</label>
                            <input value="{{ $data_description_template2 }}" type="text" name="data_description_template2" class="my-auto ti-form-input" placeholder="">
                            </div>
                   
                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Airtime description</label>
                              <input value="{{ $airtime_description_template2 }}" type="text" name="airtime_description_template2" class="my-auto ti-form-input" placeholder="">
                            </div>
                   
                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Cable description</label>
                                <input value="{{ $cable_description_template2 }}" type="text" name="cable_description_template2" class="my-auto ti-form-input" placeholder="">
                            </div>
                   
                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Electricity description</label>
                              <input value="{{ $electricity_description_template2 }}" type="text" name="electricity_description_template2" class="my-auto ti-form-input" placeholder="">
                            </div>
                   
                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Epins description</label>
                              <input value="{{ $epins_description_template2 }}" type="text" name="epins_description_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Resultchecker description</label>
                              <input value="{{ $resultchecker_description_template2 }}" type="text" name="resultchecker_description_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Testimony1</label>
                              <input value="{{ $testimony1_template2 }}" type="text" name="testimony1_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Testimony1 Position</label>
                              <input value="{{ $testimony1_position_template2 }}" type="text" name="testimony1_position_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Testimony1 Name</label>
                              <input value="{{ $testimony1_name_template2 }}" type="text" name="testimony1_name_template2" class="my-auto ti-form-input" placeholder="">
                            </div>


                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Testimony2</label>
                              <input value="{{ $testimony2_template2 }}" type="text" name="testimony2_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Testimony2 Position</label>
                              <input value="{{ $testimony2_position_template2 }}" type="text" name="testimony2_position_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Testimony2 Name</label>
                              <input value="{{ $testimony2_name_template2 }}" type="text" name="testimony2_name_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Testimony3</label>
                              <input value="{{ $testimony3_template2 }}" type="text" name="testimony3_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Testimony3 Position</label>
                              <input value="{{ $testimony3_position_template2 }}" type="text" name="testimony3_position_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Testimony3 Name</label>
                              <input value="{{ $testimony3_name_template2 }}" type="text" name="testimony3_name_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Google map link</label>
                              <input value="{{ $google_map_link }}" type="text" name="google_map_link" class="my-auto ti-form-input" placeholder="">
                            </div>
                           
                            

                            <div class="space-y-2">
                            <label class="ti-form-label mb-0">Topnav email </label>
                            <input value="{{ $topnav_email_template2 }}" type="email"  name="topnav_email_template2" class="my-auto ti-form-input" placeholder="">
                            </div>
                            <div class="space-y-2">
                            <label class="ti-form-label mb-0">Phone</label>
                            <input value="{{ $phone_template2 }}" type="text"  name="phone_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Email Address</label>
                              <input value="{{ $email_address_template2 }}" type="text"  name="email_address_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                                <label class="ti-form-label mb-0">Twitter link</label>
                                <input value="{{ $twitter_link_template2 }}" type="text"  name="twitter_link_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                            <label class="ti-form-label mb-0">Facebook link</label>
                            <input value="{{ $facebook_template2 }}" type="text"  name="facebook_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Support Whatsapp number (format: e.g 2348133494364)</label>
                              <input value="{{ $support_whatsapp_number_template2 }}" type="text"  name="support_whatsapp_number_template2" class="my-auto ti-form-input" placeholder="">
                            </div>
                            
                              <div class="space-y-2">
                            <label class="ti-form-label mb-0">Instagram link</label>
                            <input value="{{ $instagram_template2 }}" type="text"  name="instagram_template2" class="my-auto ti-form-input" placeholder="">
                            </div>

                           
                            <div class="space-y-2">
                              <label class="ti-form-label mb-0">Physical Address</label>
                              <input value="{{ $physical_address_template2 }}" type="text"  name="physical_address_template2" class="my-auto ti-form-input" placeholder="">
                            </div>
                            

                            {{-- <div class="space-y-2 mt-5">
                              <label class="ti-form-label mb-0">About Us </label>
                                <div cols="10" rows="5" id="editor">
                                </div>
                            </div> --}}

                            {{-- <div class="space-y-2">
                                <button type="submit" class="ti-btn ti-btn-primary w-full">Update landing pages for TEMPLATE 2</button>
                            </div> --}}
                          
                            <br>
                        </div>
                          
                          <br>
                          <br>
                          <br>
                          <h1><strong>Template 1 Settings Below</strong> 👇👇👇👇👇👇👇👇👇</h1>
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
                              <label class="ti-form-label mb-0">{{ env('APP_NAME') == 'QuickConnect' ? 'Step 1 title for site usage' : 'Title analytics 1'  }} </label>
                              <input value="{{ $title_analytics1 }}" type="text"  name="title_analytics1" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Description for title analytics 1</label>
                              <label class="ti-form-label mb-0">{{ env('APP_NAME') == 'QuickConnect' ? 'Step 1 description for site usage' : 'Description for Title analytics 1'  }} </label>

                              <input value="{{ $value_analytics1 }}" type="text"  name="value_analytics1" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">{{ env('APP_NAME') == 'QuickConnect' ? 'Step 2 title for site usage' : 'itle analytics 2'  }} </label>
                                <input value="{{ $title_analytics2 }}" type="text"  name="title_analytics2" class="my-auto ti-form-input" placeholder="">
                                </div>
  
                                <div class="space-y-2">
                                  <label class="ti-form-label mb-0">{{ env('APP_NAME') == 'QuickConnect' ? 'Step 2 description for site usage' : 'Description for Title analytics 2'  }} </label>
                                  <input value="{{ $value_analytics2 }}" type="text"  name="value_analytics2" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                                  <label class="ti-form-label mb-0">{{ env('APP_NAME') == 'QuickConnect' ? 'Step 3 title for site usage' : 'Title analytics 3'  }} </label>

                                  <input value="{{ $title_analytics3 }}" type="text"  name="title_analytics3" class="my-auto ti-form-input" placeholder="">
                              </div>
    
                              <div class="space-y-2">
                                  <label class="ti-form-label mb-0">{{ env('APP_NAME') == 'QuickConnect' ? 'Step 3 description for site usage' : 'Description for Title analytics 3'  }} </label>
                                  <input value="{{ $value_analytics3 }}" type="text"  name="value_analytics3" class="my-auto ti-form-input" placeholder="">
                              </div>


                              <div class="space-y-2">
                                    <label class="ti-form-label mb-0">{{ env('APP_NAME') == 'QuickConnect' ? 'Disregard this.' : 'Title analytics 4'  }} </label>
                                    <input value="{{ $title_analytics4 }}" type="text"  name="title_analytics4" class="my-auto ti-form-input" placeholder="">
                              </div>
      
                                <div class="space-y-2">
                                    <label class="ti-form-label mb-0">{{ env('APP_NAME') == 'QuickConnect' ? 'Disregard this.' : 'Description for Title analytics 4'  }} </label>
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
                                <label class="ti-form-label mb-0">Support Whatsapp number (format: e.g 2348133494364)</label>
                                <input value="{{ $support_whatsapp_number }}" type="text"  name="support_whatsapp_number" class="my-auto ti-form-input" placeholder="">
                              </div>

                              {{-- <div class="space-y-2">
                                <label class="ti-form-label mb-0">Support Whatsapp Community Link</label>
                                <input value="{{ $support_whatsapp_number_community ?? '' }}" type="text"  name="support_whatsapp_number_community" class="my-auto ti-form-input" placeholder="">
                              </div> --}}
                              
                                <div class="space-y-2">
                              <label class="ti-form-label mb-0">Instagram link</label>
                              <input value="{{ $instagram_link }}" type="text"  name="instagram_link" class="my-auto ti-form-input" placeholder="">
                              </div>

                              <div class="space-y-2">
                              <label class="ti-form-label mb-0">Twitter link</label>
                              <input value="{{ $twitter_link }}" type="text"  name="twitter_link" class="my-auto ti-form-input" placeholder="">
                              </div>
                             
                              <div class="space-y-2">
                                <label class="ti-form-label mb-0">Physical Address</label>
                                <input value="{{ $physical_address }}" type="text"  name="physical_address" class="my-auto ti-form-input" placeholder="">
                              </div>
                              

                              {{-- <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">About Us </label>
                                  <div cols="10" rows="5" id="editor">
                                  </div>
                              </div> --}}

                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update landing page settings[Template 1 and 2]</button>
                              </div>
                            
                              <br>
                          </div>
                      </form>
                      </div>  
                    </div>
                    <div id="pills-with-brand-color-3" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-3">
                      <div class="overflow-auto">
                        @if (isset($site_logo))
                        {{-- hidden dark:block --}}
                         <img src="{{ env('APP_URL').'assets/landing_page_assets/img/site_logo/'.$site_logo }}" alt="logo"
                         class="w-20 h-20 " alt="logo" class=""> 
                         <a href="{{ route('admin.settings.remove_logo') }}" style="color: red">remove image</a>
                       @else
                           No logo upload found.
                           <br>  
                       @endif
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.manage_site_logo')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                              <div class="space-y-2 mt-5">
                              
              
                                <b>Please ensure the logo is a square dimension to display perfectly on the site</b>
                                <label class="ti-form-label mb-0">Update site logo (ONLY PNG)  </label>
                                <input type="file" required class="my-auto ti-form-input" name="site_logo" max="100" placeholder="update site logo">
                              </div>

                              
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update site logo</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>
                      </div> 
                      <hr>
                      <div class="overflow-auto">
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.manage_site_images')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                              <div class="space-y-2 mt-5">
                                @if (isset($hero_image1))
                                  {{-- hidden dark:block --}}
                                  <img src="{{ env('APP_URL').'assets/landing_page_assets/img/hero_image1/'.$hero_image1 }}" alt="logo"
                                  class="w-20 h-20 " alt="logo" class=""> 
                                @else
                                    No upload found.
                                    <br>  
                                @endif
                                <label class="ti-form-label mb-0">Update Slider Image 1 (This also works for hero image in Template 2)</label>
                                <input type="file"  class="my-auto ti-form-input" name="hero_image1" max="100" placeholder="update hero image">
                              </div>
                          </div>
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                            <div class="space-y-2 mt-5">
                              @if (isset($hero_image2))
                                {{-- hidden dark:block --}}
                                <img src="{{ env('APP_URL').'assets/landing_page_assets/img/hero_image2/'.$hero_image2 }}" alt="logo"
                                class="w-20 h-20 " alt="logo" class=""> 
                              @else
                                  No upload found.
                                  <br>  
                              @endif
                              <label class="ti-form-label mb-0">Update Slider Image 2</label>
                              <input type="file"  class="my-auto ti-form-input" name="hero_image2" max="100" placeholder="update hero image">
                            </div>
                          </div>

                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                            <div class="space-y-2 mt-5">
                              @if (isset($aboutus_image))
                                {{-- hidden dark:block --}}
                                <img src="{{ env('APP_URL').'assets/landing_page_assets/img/aboutus_image/'.$aboutus_image }}" alt="about us image"
                                class="w-20 h-20 " alt="logo" class=""> 
                              @else
                                  No upload found.
                                  <br>  
                              @endif
                              <label class="ti-form-label mb-0">Update About Us Image</label>
                              <input type="file"  class="my-auto ti-form-input" name="aboutus_image" max="100" placeholder="update aboutus image">
                            </div>
                          </div>

                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                            <div class="space-y-2 mt-5">
                              @if (isset($login_image))
                                {{-- hidden dark:block --}}
                                <img src="{{ env('APP_URL').'assets/landing_page_assets/img/authentication/login/'.$login_image }}" alt="login image"
                                class="w-20 h-20 "  class=""> 
                              @else
                                  No upload found.
                                  <br>  
                              @endif
                              <label class="ti-form-label mb-0">Update Login Image</label>
                              <input type="file"  class="my-auto ti-form-input" name="login_image" max="100" placeholder="update login image">
                            </div>
                          </div>


                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                            <div class="space-y-2 mt-5">
                              @if (isset($signup_image))
                                {{-- hidden dark:block --}}
                                <img src="{{ env('APP_URL').'assets/landing_page_assets/img/authentication/signup/'.$signup_image }}" alt="signup image"
                                class="w-20 h-20 " class=""> 
                              @else
                                  No upload found.
                                  <br>  
                              @endif
                              <label class="ti-form-label mb-0">Update Signup Image</label>
                              <input type="file"  class="my-auto ti-form-input" name="signup_image" max="100" placeholder="update signup image">
                            </div>
                          </div>
                          
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-6 space-y-4 lg:space-y-0">
                            <button type="submit" class="ti-btn ti-btn-primary w-full">Update Site Images</button>
                          </div>
                        </form>
                      </div> 
                      <br>
                      <hr>
                      <div class="overflow-auto">
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.manage_site_colors')  }}">
                          @csrf
                          <div class="grid w-full lg:w-full lg:grid-cols-1 gap-6 space-y-1 lg:space-y-0">
                              
                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Site Secondary Color <b>This is secondary color for both templates</b></label>
                                  <input type="color" value="{{ isset($site_secondary_color) && $site_secondary_color != NULL ? $site_secondary_color : "#5a66f2"  }}"  class="p-1 h-10 w-32 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input" name="site_secondary_color" title="Choose your color">
                                </div>
                              </div>

                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Site Primary Color <b>This is main color for both templates</b> </label>
                                  <input type="color" value="{{ isset($site_primary_color) && $site_primary_color != NULL ? $site_primary_color : "#5a66f2"  }}"  class="p-1 h-10 w-32 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input" name="site_primary_color" title="Choose your color">
                                </div>
                              </div>

                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Site Secondary Color <b>This is secondary color for both templates</b></label>
                                  <input type="color" value="{{ isset($site_secondary_color) && $site_secondary_color != NULL ? $site_secondary_color : "#5a66f2"  }}"  class="p-1 h-10 w-32 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input" name="site_secondary_color" title="Choose your color">
                                </div>
                              </div>

                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Transaction Volume Analytics Color <b>This is for second template</b></label>
                                  <input type="text" value="{{ isset($site_txn_volume_color) && $site_txn_volume_color != NULL ? $site_txn_volume_color : "#ffffff"  }}"  class="p-1 h-10 w-32 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input" name="site_txn_volume_color" title="Choose your color">
                                </div>
                              </div>

                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Wallet Balance Analytics Color <b>This is for second template</b></label>
                                  <input type="text" value="{{ isset($site_wallet_balance_color) && $site_wallet_balance_color != NULL ? $site_wallet_balance_color : "#ffffff"  }}"  class="p-1 h-10 w-32 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input" name="site_wallet_balance_color" title="Choose your color">
                                </div>
                              </div>


                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Txns Count Analytics Color <b>This is for second template</b></label>
                                  <input type="text" value="{{ isset($site_txns_count_analytics_color) && $site_txns_count_analytics_color != NULL ? $site_txns_count_analytics_color : "#ffffff"  }}"  class="p-1 h-10 w-32 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input" name="site_txns_count_analytics_color" title="Choose your color">
                                </div>
                              </div>

                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Virtual Wallets Analytics Color <b>This is for second template</b></label>
                                  <input type="text" value="{{ isset($site_virtual_accounts_color) && $site_virtual_accounts_color != NULL ? $site_virtual_accounts_color : "#ffffff"  }}"  class="p-1 h-10 w-32 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input" name="site_virtual_accounts_color" title="Choose your color">
                                </div>
                              </div>

                             
                             

                              <div class="box-body">
                                <label class="ti-form-label mb-0">Choose Admin Site Color (In RGB):</label>
                                <div class="flex items-center space-x-2 ">
                                  <input type="number"  value="{{ isset($admin_site_color_r) && $admin_site_color_r != NULL ? $admin_site_color_r : 90 }}" name="admin_site_color_r" class="p-1 h-10 w-20 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                  <input type="number"  value="{{ isset($admin_site_color_g) && $admin_site_color_g != NULL ? $admin_site_color_g : 102  }}" name="admin_site_color_g" class="p-1 h-10 w-20 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                  <input type="number"  value="{{ isset($admin_site_color_b) && $admin_site_color_b != NULL ? $admin_site_color_b : 241  }}" name="admin_site_color_b" class="p-1 h-10 w-20 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                </div>
                              </div>

                             

                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Landing Page Hover Color</label>
                                  <input type="color" value="{{ isset($site_landing_page_hover_color) && $site_landing_page_hover_color != NULL ? $site_landing_page_hover_color : "#d64022"  }}"  class="p-1 h-10 w-10 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input" name="site_landing_page_hover_color"  title="Choose your color">
                                </div>
                              </div>

                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Admin Sidebar Color</label>
                                  <input type="color" value="{{ isset($site_admin_sidebar_color) && $site_admin_sidebar_color != NULL ? $site_admin_sidebar_color : NULL  }}" name="site_admin_sidebar_color" class="p-1 h-10 w-10 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input" title="Choose your color">
                                </div>
                              </div>  

                              <div class="box-body">
                                <label class="ti-form-label mb-0">Choose Landing Page Analytics Color (In RGB) </label>
                                <div class="flex items-center space-x-2 ">
                                  <input type="number"  value="{{ isset($site_landing_analytics_color_r) && $site_landing_analytics_color_r != NULL ? $site_landing_analytics_color_r : 90  }}" name="site_landing_analytics_color_r" class="p-1 h-10 w-20 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                  <input type="number"  value="{{ isset($site_landing_analytics_color_g) && $site_landing_analytics_color_g != NULL ? $site_landing_analytics_color_g : 102  }}" name="site_landing_analytics_color_g" class="p-1 h-10 w-20 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                  <input type="number"  value="{{ isset($site_landing_analytics_color_b) && $site_landing_analytics_color_b != NULL ? $site_landing_analytics_color_b : 204  }}" name="site_landing_analytics_color_b" class="p-1 h-10 w-20 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                </div>
                              </div>


                              {{-- <div class="box-body">
                                <label class="ti-form-label mb-0">Choose Landing Page Reviews (In RGB) </label>
                                <div class="flex items-center space-x-2 ">
                                  <input type="number"  value="{{ isset($site_landing_review_r) && $site_landing_review_r != NULL ? explode(',',$site_landing_review_color)[0] : 8  }}" name="site_landing_review_color_r" class="p-1 h-10 w-20 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                  <input type="number"  value="{{ isset($site_landing_review_g) && $site_landing_review_g != NULL ? explode(',',$site_landing_review_color)[1] : 32  }}" name="site_landing_review_color_g" class="p-1 h-10 w-20 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                  <input type="number"  value="{{ isset($site_landing_review_b) && $site_landing_review_b != NULL ? explode(',',$site_landing_review_color)[2] : 50  }}" name="site_landing_review_color_b" class="p-1 h-10 w-20 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                </div>
                              </div> --}}
                            

                             
                              
                              {{-- <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Analytics Color 1 (In RGB)</label>
                                  <input type="color" value="{{ isset($site_landing_analytics_color1) && $site_landing_analytics_color1 != NULL ? $site_landing_analytics_color1 : "#5a66f2"  }}" name="site_landing_analytics_color1" class="p-1 h-10 w-10 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                </div>
                              </div>  

                              <div class="space-y-2 mt-1">
                                <div class="box-body">
                                  <label class="ti-form-label mb-0">Choose Analytics Color 2 (In RGB)</label>
                                  <input type="color" value="{{ isset($site_landing_analytics_color2) && $site_landing_analytics_color2 != NULL ? $site_landing_analytics_color2 : "#5a66f2"  }}" name="site_landing_analytics_color2" class="p-1 h-10 w-10 block bg-white border border-gray-200 cursor-pointer rounded-sm disabled:opacity-50 disabled:pointer-events-none dark:bg-bgdark dark:border-white/10" id="hs-color-input"  title="Choose your color">
                                </div>
                              </div>   --}}

                              
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update Site Colors</button>
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
                              {{-- <p>SME PLUG AUTOMATION</p>
                              <div class="">
                                <label class="ti-form-label mb-0">Secret key: </label>
                                <input type="text"  required class="my-auto ti-form-input" name="smeplug_api_secret_key" value="{{ $smeplug->api_secret_key  ?? '' }}"  placeholder="">
                              </div> 
                              <br>
                              <hr>
                              <p>OGDAMS AUTOMATION</p>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Secret key: </label>
                                <input type="text" required class="my-auto ti-form-input"  value="{{ $ogdams->api_secret_key != NULL  ? substr($ogdams->api_secret_key,0,2).str_repeat('X',8).substr($ogdams->api_secret_key,-2)  : '' }}"  name="ogdams_api_secret_key"  placeholder="">
                              </div>
                              <br>
                              <hr> --}}
                              {{-- <p>MEGASUBPLUG AUTOMATION</p>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Api Password: </label>
                                <input type="text" required class="my-auto ti-form-input" name="megasub_api_password" value="{{ $megasubplug->api_password != NULL  ? substr($megasubplug->api_password,0,2).str_repeat('X',5).substr($megasubplug->api_password,-3)  : '' }}"  placeholder="">
                              </div>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Public key: </label>
                                <input type="text" required class="my-auto ti-form-input" name="megasub_api_public_key" value="{{ $megasubplug->api_public_key != NULL  ? substr($megasubplug->api_public_key,0,2).str_repeat('X',12).substr($megasubplug->api_public_key,-3)  : '' }}" placeholder="">
                              </div>
                              <br>
                              <hr>      
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update keys</button>
                              </div> --}}
                            
                              <br>
                          </div>
                        </form>
                        <br>
                        <hr>
                        <br>
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.update')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-2 space-y-4 lg:space-y-0">
                             
                              <div class="">
                                <label class="ti-form-label mb-0">Maximum Automatic Crediting Allowed: </label>
                                <input type="number"  required class="my-auto ti-form-input" name="max_automatic_crediting_allowed" value="{{ $max_automatic_crediting_allowed  ?? '' }}"  placeholder="">
                              </div> 
                                  
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update Setting</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>

                        <br>
                        <hr>
                        <br>
                        <br>
                        <hr>
                        <br>
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.update_api_key')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-2 space-y-4 lg:space-y-0">
                             
                              <div class="">
                                <label class="ti-form-label mb-0">API key to allow connection to your website: </label>
                                <input type="text"  required class="my-auto ti-form-input" name="api_key" value="{{ $api_key  ?? '' }}"  placeholder="">
                              </div> 
                                  
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update Api Key</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>

                        <br>
                        <hr>
                        <br>
                        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.update_purchase_limit_settings')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-2 space-y-4 lg:space-y-0">
                             
                              <div class="">
                                <label class="ti-form-label mb-2">Product Purchase Limit (Daily) </label>
                                <input type="number"  required class="my-auto ti-form-input" name="product_purchase_limit_daily" value="{{ $product_purchase_limit_daily  ?? '' }}"  placeholder="">
                              </div> 

                              <div class="">
                                <label class="ti-form-label mb-2">Product Purchase Limit (Last 7 days) </label>
                                <input type="number"  required class="my-auto ti-form-input" name="product_purchase_limit_last_7_days" value="{{ $product_purchase_limit_last_7_days  ?? '' }}"  placeholder="">
                              </div> 

                                 
                              <div class="">
                                <label class="ti-form-label mb-2">Product Purchase Limit (Last 30 days) </label>
                                <input type="number"  required class="my-auto ti-form-input" name="product_purchase_limit_last_30_days" value="{{ $product_purchase_limit_last_30_days  ?? '' }}"  placeholder="">
                              </div> 
                                  
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update Purchase Limit Setting</button>
                              </div>
                            
                              <br>
                          </div>
                        </form>

                        <br>
                        <hr>
                        <br>
                        {{-- <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.update_user_authentication_dashboard')  }}">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-2 space-y-4 lg:space-y-0">
                             
                              <div class="">
                                <label class="ti-form-label mb-2">Redirect users to this page after authentication: </label>
                                <select id="users_redirect_after_authentication" name="users_redirect_after_authentication" required class="my-auto ti-form-select">
                                 
                                  <option @if ($users_redirect_after_authentication != NULL && $users_redirect_after_authentication == 'dashboard') selected @endif value="dashboard">Select</option>
                                  <option @if ($users_redirect_after_authentication != NULL && $users_redirect_after_authentication == 'dashboard') selected @endif  value="dashboard">Main Dashboard Page</option>
                                  <option @if ($users_redirect_after_authentication != NULL && $users_redirect_after_authentication == 'user/data/buy_data') selected @endif value="user/data/buy_data">Buy Data Page</option>
                                  <option @if ($users_redirect_after_authentication != NULL && $users_redirect_after_authentication == 'user/airtime/buy_airtime') selected @endif value="user/airtime/buy_airtime">Buy Airtime Page</option>
                                  <option @if ($users_redirect_after_authentication != NULL && $users_redirect_after_authentication == 'user/electricity/buy_electricity') selected @endif value="user/electricity/buy_electricity">Buy Electricity Page</option>
                                  <option @if ($users_redirect_after_authentication != NULL && $users_redirect_after_authentication == 'user/cable_subscription/buy_cable_subscription') selected @endif value="user/cable_subscription/buy_cable_subscription">Buy Cable subscription Page</option>
                                </select>
                              </div>   
                                
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update Page after authentication</button>
                              </div>
                            
                              <br>
                          </div>
                        </form> --}}
                        
                        <hr>
                        <br>
                        @if (env('APP_NAME') == 'OresamSub')
                            <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.emails_to_notify_failed_transactions')  }}">
                              @csrf
                              <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-2 space-y-4 lg:space-y-0">
                                
                                    <div class="">
                                      <label class="ti-form-label mb-2">Add list of emails that should be notified when a transaction fails or set to pending. separate with a comma</label>
                                      <input type="text"  required class="my-auto ti-form-input" name="emails_to_notify_failed_transactions" value="{{ $emails_to_notify_failed_transactions  ?? '' }}"  placeholder="">
                                    </div> 
                                  
                                    
                                  <div class="space-y-2">
                                      <button type="submit" class="ti-btn ti-btn-primary w-full">Update Emails to be notified of a failed transaction</button>
                                  </div>
                                
                                  <br>
                              </div>
                            </form>
                                
                        @endif
                       
                      </div>  



                      </div>  
                    </div>
                    <div id="pills-with-brand-color-6" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-6">
                      <div class="overflow-auto">
                          @csrf
                          <div class="grid w-full lg:w-1/2 lg:grid-cols-1 gap-2 space-y-4 lg:space-y-0">
                            <table  class="ti-custom-table ti-custom-table-head ti-striped-table ">
                              <thead>
                                  <tr>
                                      <th>SN</th>
                                      <th>Funding Name</th>
                                      <th>Webhook</th>
                                      <th>Action</th>
                                     
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach ($funding_options as $funding_option)
                                    <tr>
                                      <td>{{ $loop->index + 1 }}</td>
                                      <td>{{ $funding_option->funding_option_name }}</td>
                                      <td> Webhook url:<strong>{{ $funding_option->webhook_string == NULL ?  env('APP_URL').'api/admin/wallets/'.$funding_option->slug.'_webhook/NOT_SET' : env('APP_URL').'api/admin/wallets/'.$funding_option->slug.'_webhook/'.$funding_option->webhook_string->webhook_suffix_string  }}</strong><br>
                                        <span class="text-red-600 mt-4"><b>(This must be the same with the webhook set on your {{  $funding_option->funding_option_name }} Dashboard)</b></span></td>
                                      <td>
                                        <div class=" flex items-center justify-start">
                                          {{-- <a href="#" type="button" data-hs-overlay="#hs-vertically-centered-modal{{$funding_option->id}}"   aria-label="button" type="button" class="hs-dropdown-toggle ti-btn flex-shrink-0 h-[0.070rem] w-[0.070rem] ti-btn-primary text-sm"> 
                                           <span style="font-size: 10px">Update</span>
                                          </a> --}}

                                          <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-primary" data-hs-overlay="#hs-vertically-centered-modal{{$funding_option->id}}">
                                            Update keys & Webhook
                                          </button> 
                                          <div id="hs-vertically-centered-modal{{$funding_option->id}}" class="hs-overlay ti-modal hidden">
                                            <div class="ti-modal-box">
                                              <div class="ti-modal-content">
                                                <div class="ti-modal-header">
                                                  <h3 class="ti-modal-title">
                                                    Update Funding Option for {{ $funding_option->funding_option_name }}
                                                  </h3>
                                                  <button type="button" class="hs-dropdown-toggle ti-modal-clode-btn"
                                                    data-hs-overlay="#hs-basic-modal">
                                                    <span class="sr-only">Close</span>
                                                    <svg class="w-3.5 h-3.5" width="8" height="8" viewBox="0 0 8 8" fill="none"
                                                      xmlns="http://www.w3.org/2000/svg">
                                                      <path
                                                        d="M0.258206 1.00652C0.351976 0.912791 0.479126 0.860131 0.611706 0.860131C0.744296 0.860131 0.871447 0.912791 0.965207 1.00652L3.61171 3.65302L6.25822 1.00652C6.30432 0.958771 6.35952 0.920671 6.42052 0.894471C6.48152 0.868271 6.54712 0.854471 6.61352 0.853901C6.67992 0.853321 6.74572 0.865971 6.80722 0.891111C6.86862 0.916251 6.92442 0.953381 6.97142 1.00032C7.01832 1.04727 7.05552 1.1031 7.08062 1.16454C7.10572 1.22599 7.11842 1.29183 7.11782 1.35822C7.11722 1.42461 7.10342 1.49022 7.07722 1.55122C7.05102 1.61222 7.01292 1.6674 6.96522 1.71352L4.31871 4.36002L6.96522 7.00648C7.05632 7.10078 7.10672 7.22708 7.10552 7.35818C7.10442 7.48928 7.05182 7.61468 6.95912 7.70738C6.86642 7.80018 6.74102 7.85268 6.60992 7.85388C6.47882 7.85498 6.35252 7.80458 6.25822 7.71348L3.61171 5.06702L0.965207 7.71348C0.870907 7.80458 0.744606 7.85498 0.613506 7.85388C0.482406 7.85268 0.357007 7.80018 0.264297 7.70738C0.171597 7.61468 0.119017 7.48928 0.117877 7.35818C0.116737 7.22708 0.167126 7.10078 0.258206 7.00648L2.90471 4.36002L0.258206 1.71352C0.164476 1.61976 0.111816 1.4926 0.111816 1.36002C0.111816 1.22744 0.164476 1.10028 0.258206 1.00652Z"
                                                        fill="currentColor" />
                                                    </svg>
                                                  </button>
                                                </div>
                                                <div class="ti-modal-body">
                                                  <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.update_funding_options')  }}">
                                                    @csrf
                                                      <div>
                                                        <div class="space-y-2 mt-5">
                                                          <label class="ti-form-label mb-0">Public key: </label>
                                                          <input type="hidden" required class="my-auto ti-form-input" name="id" value="{{ $funding_option->id }}"  placeholder="">
                                                          <input type="text" required class="my-auto ti-form-input" name="api_public_key" value="{{ $funding_option->api_public_key != NULL  ? substr($funding_option->api_public_key,0,2).str_repeat('X',5).substr($funding_option->api_public_key,-3)  : '' }}"  placeholder="">
                                                        </div>
                                                        <div class="space-y-2 mt-5">
                                                          <label class="ti-form-label mb-0">Secret key: </label>
                                                          <input type="text" required class="my-auto ti-form-input" name="api_secret_key" value="{{ $funding_option->api_secret_key != NULL  ? substr($funding_option->api_secret_key,0,2).str_repeat('X',5).substr($funding_option->api_secret_key,-3)  : '' }}"  placeholder="">
                                                        </div>
                                                        <hr>
                                                        <div class="space-y-2">
                                                        <button type="submit" class="ti-btn ti-btn-primary w-full">Update Funding Option</button>

                                                        </div>
                                                    </form>
                                                    <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.update_webhook_suffix_string')  }}">
                                                      @csrf
                                                        <div>
                                                          <div class="space-y-2 mt-5">
                                                            <label class="ti-form-label mb-0">Change Webhook Suffix String </label>
                                                            <small>This string is attached to your {{ $funding_option->funding_option_name }} <br> base webhook url which forms a unique webhook url for your business</small>
                                                            <input type="hidden" required class="my-auto ti-form-input" name="funding_option_id" value="{{ $funding_option->id }}"  placeholder="">
                                                            <input type="text" required class="my-auto ti-form-input" name="webhook_suffix_string" value="{{ $funding_option->api_public_key != NULL  ? substr($funding_option->api_public_key,0,2).str_repeat('X',5).substr($funding_option->api_public_key,-3)  : '' }}"  placeholder="">
                                                          </div>
                                        
                                                          <hr>
                                                          <div class="space-y-2">
                                                            <button type="submit" class="ti-btn ti-btn-primary w-full">Update Webhook Suffix String</button>

  
                                                          </div>
                                                      </form>
                                                </div>
                                                <div class="ti-modal-footer">
                                                  
                                                  <button type="button"
                                                    class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                                    data-hs-overlay="#hs-vertically-centered-modal{{$funding_option->id}}">
                                                    Close
                                                  </button>
              
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          </div>
                                        </div>

                                          <button type="button" class="hs-dropdown-toggle ti-btn ti-btn-warning" data-hs-overlay="#hs-basic-modal{{$funding_option->id}}">
                                            Add bank codes
                                          </button> 

                                          
                                          <div id="hs-basic-modal{{$funding_option->id}}" class="hs-overlay ti-modal hidden">
                                            <div class="ti-modal-box">
                                              <div class="ti-modal-content">
                                                <div class="ti-modal-header">
                                                  <h3 class="ti-modal-title">
                                                    Add bank codes for {{ $funding_option->funding_option_name }}
                                                  </h3>
                                                 
                                                  <button type="button" class="hs-dropdown-toggle ti-modal-clode-btn"
                                                    data-hs-overlay="#hs-basic-modal">
                                                    <span class="sr-only">Close</span>
                                                    <svg class="w-3.5 h-3.5" width="8" height="8" viewBox="0 0 8 8" fill="none"
                                                      xmlns="http://www.w3.org/2000/svg">
                                                      <path
                                                        d="M0.258206 1.00652C0.351976 0.912791 0.479126 0.860131 0.611706 0.860131C0.744296 0.860131 0.871447 0.912791 0.965207 1.00652L3.61171 3.65302L6.25822 1.00652C6.30432 0.958771 6.35952 0.920671 6.42052 0.894471C6.48152 0.868271 6.54712 0.854471 6.61352 0.853901C6.67992 0.853321 6.74572 0.865971 6.80722 0.891111C6.86862 0.916251 6.92442 0.953381 6.97142 1.00032C7.01832 1.04727 7.05552 1.1031 7.08062 1.16454C7.10572 1.22599 7.11842 1.29183 7.11782 1.35822C7.11722 1.42461 7.10342 1.49022 7.07722 1.55122C7.05102 1.61222 7.01292 1.6674 6.96522 1.71352L4.31871 4.36002L6.96522 7.00648C7.05632 7.10078 7.10672 7.22708 7.10552 7.35818C7.10442 7.48928 7.05182 7.61468 6.95912 7.70738C6.86642 7.80018 6.74102 7.85268 6.60992 7.85388C6.47882 7.85498 6.35252 7.80458 6.25822 7.71348L3.61171 5.06702L0.965207 7.71348C0.870907 7.80458 0.744606 7.85498 0.613506 7.85388C0.482406 7.85268 0.357007 7.80018 0.264297 7.70738C0.171597 7.61468 0.119017 7.48928 0.117877 7.35818C0.116737 7.22708 0.167126 7.10078 0.258206 7.00648L2.90471 4.36002L0.258206 1.71352C0.164476 1.61976 0.111816 1.4926 0.111816 1.36002C0.111816 1.22744 0.164476 1.10028 0.258206 1.00652Z"
                                                        fill="currentColor" />
                                                    </svg>
                                                  </button>
                                                </div>
                                                <div class="ti-modal-body">
                                                  <h3><strong>Banks Codes Addition</strong></h3>
                                                  @if (count($funding_option->bank_codes) > 0)
                                                    <p><b>Already added bank codes:</b></p>
                                                    <ul>
                                                      @foreach ($funding_option->bank_codes as $key=>$bank_code)
                                                        SN: {{ $key + 1 }} <br> Bank Code: {{  $bank_code->bank_code }} <br> Bank Name: {{  $bank_code->bank_name }}  <hr>
                                                     
                                                    @endforeach
                                                    </ul>
                                                  @else
                                                    <p>No bank codes added yet</p>
                                                  @endif                                                 
                                                  <form enctype="multipart/form-data" method="POST" action="{{ route('admin.settings.add_funding_option_bank_code')  }}">
                                                    @csrf
                                                   <div>
                                                    <div class="space-y-2 mt-5">
                                                      <label class="ti-form-label mb-0">Bank Name: </label>
                                                      <input type="hidden" required class="my-auto ti-form-input" name="funding_option_id" value="{{ $funding_option->id }}"  placeholder="">
                                                      <input type="text" required class="my-auto ti-form-input" name="bank_name" value=""  placeholder="">
                                                    </div>

                                                    <div class="space-y-2 mt-5">
                                                      <label class="ti-form-label mb-0">Bank Code: </label>
                                                      <input type="text" required class="my-auto ti-form-input" name="bank_code" value=""  placeholder="">
                                                    </div>

                                                    <div class="space-y-2 mt-5">
                                                      <label class="ti-form-label mb-0">Charges(%), <strong>Note:</strong> Set as flat rate for WEMA: </label>
                                                      <input type="text" required class="my-auto ti-form-input" name="bank_charges" value=""  placeholder="">
                                                    </div>
                                              
                                                  </div>
                                                  <div class="ti-modal-footer">
                                                  <div class="space-y-2">
                                                    <button type="submit" class="ti-btn ti-btn-primary w-full">Add Bank Code</button>
                                                  </div>
                                                  <button type="button"
                                                    class="hs-dropdown-toggle ti-btn ti-border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:ring-offset-white focus:ring-primary dark:bg-bgdark dark:hover:bg-black/20 dark:border-white/10 dark:text-white/70 dark:hover:text-white dark:focus:ring-offset-white/10"
                                                    data-hs-overlay="#hs-basic-modal{{$funding_option->id}}">
                                                    Close
                                                  </button>
                                                  </div>
                                                  </form>

                                                 
                                                  
                                              </div>
                                            </div>
                                          </div>  
                                          </div>
                                          </div>
                                          
                                        </div>  
                                      </td>

                                     </div>
                                    
                                    </tr>     
                                  @endforeach                
                              </tbody>
                          </table>  

                              {{-- <p><b>CRYSTAL PAY</b></p>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Secret key: </label>
                                <input type="text" required class="my-auto ti-form-input" name="crystal_pay_secret_key" value="{{ $megasubplug->api_public_key != NULL  ? substr($megasubplug->api_public_key,0,2).str_repeat('X',12).substr($megasubplug->api_public_key,-3)  : '' }}" placeholder="">
                              </div>
                              <br>
                              <hr>
                              <p><b>FLUTTERWAVE</b></p>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Public key: </label>
                                <input type="text" required class="my-auto ti-form-input" name="flutterwave_public_key" value="{{ $megasubplug->api_password != NULL  ? substr($megasubplug->api_password,0,2).str_repeat('X',5).substr($megasubplug->api_password,-3)  : '' }}"  placeholder="">
                              </div>
                              <div class="space-y-2 mt-5">
                                <label class="ti-form-label mb-0">Secret key: </label>
                                <input type="text" required class="my-auto ti-form-input" name="flutterwave_secret_key" value="{{ $megasubplug->api_public_key != NULL  ? substr($megasubplug->api_public_key,0,2).str_repeat('X',12).substr($megasubplug->api_public_key,-3)  : '' }}" placeholder="">
                              </div>
                              <br>
                              <hr>      
                              <div class="space-y-2">
                                  <button type="submit" class="ti-btn ti-btn-primary w-full">Update keys</button>
                              </div>
                             --}}
                              <br>
                          </div>
                  
                        
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
                                    <option  value="ON">ON</option>
                                    <option  value="OFF">OFF</option>
                                  </select> <div class="space-y-2">
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
                              <textarea name="myInput" id="myInput" cols="35" rows="16">
                                @foreach(auth()->user()->recoveryCodes() as $code)
                                {{ $code }}
                                @endforeach
                              </textarea>
                              <br>
                              <a class="ti-btn ti-btn-info w-1/4" href="#" onclick="copyToClipboard()"><span id="copyText">Copy Codes</span></a>
                              <br>
                              <br>
                              <br>
                              @method('DELETE')
                              <div class="space-y-2">
                                <button type="submit" class="ti-btn ti-btn-danger w-1/2">Disable Two Factor Authentication</button>
                              </div>
                          @else
                              <div class="space-y-2">
                                <span class="text-red-600 mt-4 block">Two factor authentication not enabled.</span>
                                <button type="submit" class="ti-btn ti-btn-primary w-1/2">Enable Two Factor Authentication</button>
                              </div>
                          @endif
                        </form>
                        
                      </div>  
                    </div>

                    <div id="pills-with-brand-color-7" class="hidden" role="tabpanel" aria-labelledby="pills-with-brand-color-item-7">
                      <div class="overflow-auto">
                        <p>Coming soon...</p>
                        <hr>

                        
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

