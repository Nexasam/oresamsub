@extends('oresamsub.layouts.app')

@section('content')
<div class="pt-6 max-w-sm mx-auto pb-24" x-data="{ isSubmitting: false }">

  <!-- Back Button -->
  <!-- Back Button -->
  <div class="mb-4">
    <a 
    href="{{ route('dashboard') }}"
    @click.prevent="showLoader = true; setTimeout(() => window.location.href = '{{ route('dashboard') }}', 1000)"
    class="inline-flex items-center px-3 py-1.5 rounded-md 
           bg-emerald-600 hover:bg-emerald-700 
           text-white 
           text-xs font-semibold 
           transition-all duration-200 shadow"
  >
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Back to Dashboard
  </a>
  </div>
  
  

  <h2 class="text-xl font-bold text-center mb-2">Buy Data</h2>

  @if(session('success'))
    <div class="mb-4 bg-green-100 text-green-800 text-sm px-4 py-2 rounded-lg">
      {{ session('success') }}
    </div>
  @elseif(session('error'))
    <div class="mb-4 bg-red-100 text-red-800 text-sm px-4 py-2 rounded-lg">
      {{ session('error') }}
    </div>
  @endif

  <!-- Wallet Balance Display -->
  <div class="mb-6 text-center" x-data="{ showBalance: true }">
    <p class="text-sm text-gray-500 dark:text-gray-400">Your Wallet Balance</p>
    <div class="flex items-center justify-center space-x-2 mt-1 text-xl font-bold">
        <!-- Balance -->
        <span x-show="showBalance" x-cloak class="text-green-600 dark:text-green-400">
            ₦{{ number_format(auth()->user()->main_balance, 2) }}
        </span>
        <span x-show="!showBalance" x-cloak class="tracking-widest text-gray-400">
            •••••
        </span>

        <!-- Toggle Button -->
        <button @click="showBalance = !showBalance" class="ml-2 hover:text-gray-600 dark:hover:text-gray-200 transition" title="Toggle Balance">
            <!-- Eye icon -->
            <svg x-show="!showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <!-- Eye-off icon -->
            <svg x-show="showBalance" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a10.06 10.06 0 013.232-4.568M6.223 6.223A10.05 10.05 0 0112 5c4.478 0 8.269 2.943 9.543 7a10.06 10.06 0 01-4.676 5.316M15 12a3 3 0 00-3-3M3 3l18 18"/>
            </svg>
        </button>
    </div>
  </div>


  <div class="pt-6 max-w-sm mx-auto pb-24" x-data="{ isSubmitting: false }">
  <form id="dataWrapper" method="POST" @submit.prevent="isSubmitting = true" action="{{ route('ore.data.submit') }}">
    @csrf

    <!-- Hidden Fields -->
    <input type="hidden" name="product_slug" id="product_slug" value="data">
    <input type="hidden" name="wallet_category" id="wallet_category" value="main_wallet">

    <!-- Network -->
    {{-- <div class="mb-4">
      <label for="network_id" class="block text-sm mb-1">Network</label>
      <select
        name="network_id"
        id="network_id"
        required
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="">Select</option>
        @foreach ($networks as $network)
          <option value="{{ $network->id }}">{{ $network->network_name }}</option>
        @endforeach
      </select>
    </div> --}}



  <!-- Network Selection -->
<div class="mb-4" x-data="{ selectedNetwork: '' }">
  <label class="block text-sm mb-2">Networks</label>

  <!-- Hidden Select (for jQuery compatibility) -->
  <select
      name="network_id"
      id="network_id"
      required
      class="hidden"
      x-model="selectedNetwork"
  >
      <option value="">Select</option>
      @foreach ($networks as $network)
          <option value="{{ $network->id }}">{{ $network->network_name }}</option>
      @endforeach
  </select>

  <!-- Visible Network Icons -->
  <div class="grid grid-cols-4 gap-3 mt-2">
      @foreach ($networks as $network)
          <div 
              class="relative border rounded-lg p-3 cursor-pointer flex items-center justify-center transition-all duration-200 hover:border-green-500 hover:scale-105 bg-white"
              :class="{ 
                  'border-green-600 bg-green-100 shadow-lg scale-110': selectedNetwork == '{{ $network->id }}' 
              }"
              @click="
                  selectedNetwork = '{{ $network->id }}';
                  document.querySelector('#network_id').value = '{{ $network->id }}';
                  $('#network_id').trigger('change'); // keep jQuery logic working
              "
          >
              <!-- Checkmark Overlay -->
              <template x-if="selectedNetwork == '{{ $network->id }}'">
                  <div class="absolute top-1 right-1 bg-green-600 text-white rounded-full p-1">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                  </div>
              </template>

              <!-- Network Icon -->
              @if(strtolower($network->network_name) === 'mtn')
                  <svg height="60" width="60" clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="145 65 270 270" xmlns="http://www.w3.org/2000/svg">
                      <path d="m145 65h270v270h-270z" fill="#fff"/>
                      <path d="m158.163 78.136h243.702v243.702h-243.702z" fill="#ffcb05"/>
                      <g fill-rule="nonzero">
                          <path d="m394.237 199.285c0 26.014-51.138 47.101-114.21 47.101-63.086 0-114.224-21.087-114.224-47.101 0-26.015 51.138-47.088 114.224-47.088 63.072 0 114.21 21.073 114.21 47.088" fill="#00678f"/>
                          <path d="m206.844 222.532 11.812-47.102h18.873v27.432l12.407-27.432h19.48l-11.799 47.101h-12.406l7.073-30.401-14.755 30.401h-10.017v-30.401l-7.695 30.401h-12.974z" fill="#fff"/>
                      </g>
                  </svg>
              @elseif(strtolower($network->network_name) === 'airtel')
                      <svg height="60" width="60" clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="150.945 65.008 258.109 269.992" xmlns="http://www.w3.org/2000/svg"><g fill="#ed1d24" fill-rule="nonzero"><g transform="matrix(.82224398 0 0 .82224398 55.633645 -111.291277)"><path d="m419.845 631.892c-19.755 0-35.855-16.801-35.855-37.477 0-21.064 15.559-37.531 35.368-37.531s35.393 16.424 35.393 37.274c.223 9.916-3.192 19.28-9.689 26.481-6.598 7.253-15.541 11.256-25.217 11.256" transform="matrix(.348134 0 0 -.348134 55.5165 646.755)"/><path d="m191.796 541.172h19.366v-81.659l-19.372 3.345z"/><path d="m280.508 347.794c-5.508-4.077-16.672-7.59-28.88-7.59-22.361 0-32.439 18.187-32.05 39.35.379 17.505 10.711 37.412 38.636 37.412h22.294zm-22.952 194.501c-26.57 0-50.455-7.935-74.999-20.902l-6.274-3.366 14.944-35.304 8.455 4.217c17.352 8.263 35.898 13.765 52.552 13.028 20.122-.863 28.415-10.659 28.415-31.093v-11.981h-33.033c-52.983 0-85.973-29.746-86.873-75.887 0-43.909 34.52-81.71 82.58-81.71 38.765 0 71.571 13.3 93.728 36.785v125.176c0 60.986-41.112 81.006-79.529 81.006m592.101-45.65c24.706 0 28.351-28.415 28.351-46.058h-60.218c.964 22.695 9.906 46.058 31.867 46.058m12.033-156c-15.246 0-25.553 5.325-32.308 13-9.976 11.495-14.457 35.001-13.235 59.983h119.958v5.875c-1.059 83.131-27.809 120.975-86.448 120.975-65.387 0-90.891-63.71-91.369-123.545-.398-37.826 13.074-75.994 37.771-97.673 15.041-13.245 35.805-20.544 59.967-20.544 13.159 0 26.881 2.01 39.478 6.172 24.054 7.872 41.834 21.514 41.834 21.514l-15.712 34.184c-2.833-2.249-26.839-19.962-59.905-19.962m-382.846 155.448-.061-192.72h56.738v181.422c7.397 7.632 23.097 14.359 38.028 14.974 13.82.594 22.769-2.448 22.769-2.448l15.699 37.293c-6.078 3.201-20.611 7.1-37.501 7.1-24.372 0-61.91-7.547-95.672-45.599m575.129-155.596c-23.12 1.882-27.97 12.63-27.97 30.542v232.101l-55.788-10.078v-218.182c0-51.698 26.545-73.885 70.908-73.885 9.49 0 21.31 2.356 21.31 2.356v37.021s-5.26-.153-8.46.122m-351.977 262.33-57.029-9.71v-219.253c0-49.088 27.096-72.361 72.524-72.361 10.931 0 21.183 2.194 21.183 2.194v36.473c-.618.061-4.804.061-8.024.196-23.962.875-28.654 14.879-28.654 31.001v123.373h36.678v43.304h-36.678z" transform="matrix(.348134 0 0 -.348134 59.9561 646.755)"/></g><path d="m419.285 1757.93c4.795 1.83 8.599 4.37 12.241 6.83l.997.66c3.887 2.59 7.682 5.62 11.584 9.23 8.636 8 13.82 15.84 16.789 25.42 1.212 3.9 2.907 11.46.826 18.57-1.53 5.17-4.499 9.44-8.802 12.65-.495.44-5.866 4.99-16.005 4.99-9.273 0-19.433-3.78-30.205-11.26l-.337-.23-.052-.04c-.325-.2-.646-.42-.961-.64-.245-.21-.514-.39-.832-.62-2.204-1.58-4.315-3.3-6.259-5.13-4.55-4.66-9.741-12.45-9.425-19.07.137-2.8 1.245-5.1 3.289-6.79 1.837-1.53 4.141-2.3 6.84-2.3 5.515 0 11.562 3.18 15.7 5.86.26.2.52.39.786.56l2.164 1.53.704.51c5.875 4.17 11.935 8.49 18.811 11.01 1.785.65 3.336.95 4.765.95.704 0 1.402-.07 2.063-.24 2.087-.5 3.611-1.76 4.52-3.72 1.582-3.44 1.206-8.88-.955-13.8-2.95-6.74-8.018-13.4-15.05-19.76-3.593-3.25-6.901-5.76-9.848-7.45l-.267-.15c-1.377-.81-2.925-1.71-4.566-2.36l-.223-.09c-.484-.19-.9-.37-1.285-.5-6.88-1.95-2.693 4.47-2.693 4.47 1.514 1.9 3.06 3.44 4.7 5.04l2.858 2.91.215.23c1.199 1.28 2.846 3.02 2.754 5.51-.144 3.33-3.314 5.4-6.313 5.49h-.215c-2.87 0-5.594-1.79-7.375-3.28-1.937-1.71-3.604-3.7-4.942-5.91-1.827-3.08-5.683-10.93-1.928-17.19 1.499-2.51 4.009-3.78 7.451-3.78 2.403 0 5.243.62 8.453 1.85" transform="matrix(1.83186246 0 0 -1.83186246 -497.581854 3428.82021)"/></g></svg>
              @elseif(strtolower($network->network_name) === 'glo')
                                        <svg height="60" width="60" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="34.39 21 937.22 937.21"><defs><linearGradient id="a" x1="484.48" y1="319.34" x2="536.14" y2="785.87" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#123214"/><stop offset=".46" stop-color="#3e7c37"/><stop offset=".91" stop-color="#5fbb46"/></linearGradient><linearGradient id="b" x1="4812.32" y1="-2121.07" x2="4812.32" y2="-2122.13" gradientTransform="matrix(115.64, 0, 0, -355.9, -555713.76, -754599.8)" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#fff" stop-opacity="0"/><stop offset=".64" stop-color="#fff" stop-opacity=".43"/><stop offset="1" stop-color="#fff" stop-opacity=".42"/></linearGradient><radialGradient id="c" cx="1772.25" cy="-341.52" fy="-409.149" r="151.52" gradientTransform="matrix(1.17, 0.59, -0.28, 0.47, -1527.13, -664.3)" gradientUnits="userSpaceOnUse"><stop offset=".13" stop-color="#fff"/><stop offset=".29" stop-color="#fff" stop-opacity=".69"/><stop offset=".45" stop-color="#fff" stop-opacity=".4"/><stop offset=".59" stop-color="#fff" stop-opacity=".18"/><stop offset=".69" stop-color="#fff" stop-opacity=".05"/><stop offset=".74" stop-color="#fff" stop-opacity="0"/></radialGradient><radialGradient id="d" cx="570.86" cy="398.43" fx="613.1" fy="841.348" r="444.93" gradientTransform="translate(410.75 -300.75) rotate(39.84) scale(1 1.09)" gradientUnits="userSpaceOnUse"><stop offset=".86" stop-opacity="0"/><stop offset=".98" stop-opacity=".47"/><stop offset="1"/></radialGradient></defs><title>Nigeria-Logo</title><circle cx="502.49" cy="490.94" r="401.79" fill="#50b651"/><path d="M903.05,489.81c0,222.6-180.45,403.05-403,403.05S97,712.41,97,489.81c0-98.47,2.76-113.11,41.52-169.17,0,0-2,63.32,44.43,101,28.87,23.41,59,6.51,114.15-18.89,59.66-27.5,108.16-45.24,146-40.56,57,7.06,232.69,112.57,324.38,91.56C840.73,437,833,264.3,833,264.3,913,342.42,903.05,395.22,903.05,489.81Z" style="isolation:isolate" fill-rule="evenodd" opacity=".6629999876022339" fill="url(#a)"/><path d="M673,785.86q218.65-205.67,72.69-471.24.74.54,18.06-62.32,77.68,94.07,82.61,232.22Q846.32,663.46,673,785.86Z" style="isolation:isolate" fill-rule="evenodd" opacity=".5860000252723694" fill="url(#b)"/><path d="M603.86,253.13C533.5,214.19,452.78,135.86,463.51,119.41s150-13.62,220.37,25.32,130.26,155.75,119.54,172.2S674.22,292.07,603.86,253.13Z" style="isolation:isolate" fill-rule="evenodd" opacity=".7440000176429749" fill="url(#c)"/><path d="M665.15,589.89c-58.37,0-105.69-48.67-105.69-108.7S606.78,372.5,665.15,372.5s105.7,48.66,105.7,108.69S723.53,589.89,665.15,589.89Zm.75-34.48c36.43,0,66-33.23,66-74.22S702.33,407,665.9,407s-66,33.23-66,74.21S629.47,555.41,665.9,555.41Z" fill="#fff" fill-rule="evenodd"/><polygon points="447.08 284.98 523.24 284.98 523.24 588.68 481.43 588.68 481.43 318.29 447.08 318.29 447.08 284.98" fill="#fff" fill-rule="evenodd"/><path d="M390.32,375.59h39.53V629.21c0,23.11-24.31,47.18-48,57.54q-16.8,7.34-62,8.72V670.24q44.21-4.93,59.68-19.67a59.29,59.29,0,0,0,10.8-28.06V548q-15.85,33.63-39.92,44.59c-16.68,9-50.63,7.52-64.49,0s-34.19-27.32-43.51-56.81c-2.58-8.14-9-30.29-8.33-53.76.6-22.35,8-46,14.15-55.72,10.56-16.6,27.21-37.39,52.43-46.08q18.48-6.37,68.5-4.58v25.09q-37.17-2.51-46.38,2.51c-9.21,5-30,11.17-36.86,56.48s.2,82.39,21.95,94c7.45,4,20.63,8.73,32.81,4.56,23.4-8,45.42-36.53,47.1-55.86Q390.32,473,390.32,375.59Z" fill="#fff" fill-rule="evenodd"/><path d="M890.42,570.48C853.09,750.23,693.81,885.29,503,885.29c-218.53,0-395.69-177.16-395.69-395.69,0-135.76,68.37-255.55,172.55-326.8C195.21,233,141.31,339.05,141.31,457.64c0,211.53,171.48,383,383,383C696.57,840.64,842.25,726.93,890.42,570.48Z" fill-rule="evenodd" fill="url(#d)"/><path d="M503,21C244.2,21,34.39,230.8,34.39,489.6S244.2,958.21,503,958.21,971.61,748.4,971.61,489.6,761.8,21,503,21Zm0,864.3c-218.53,0-395.69-177.16-395.69-395.69,0-135.76,68.37-255.55,172.55-326.8A393.82,393.82,0,0,1,503,93.91c218.53,0,395.69,177.15,395.69,395.69a397.6,397.6,0,0,1-8.27,80.88C853.09,750.23,693.81,885.29,503,885.29Z" fill="#fff" fill-rule="evenodd"/></svg>
              @elseif(strtolower($network->network_name) === '9mobile')
                  <img height="60" width="60" src="https://cdn.brandfetch.io/idyOMKCECk/w/400/h/400/theme/dark/icon.jpeg" alt="9mobile">
              @endif
          </div>
      @endforeach
  </div>

  <template x-if="!selectedNetwork">
      <p class="text-red-500 text-xs mt-2">Please select a network</p>
  </template>
</div>




    <!-- Phone Number -->
    <div class="mb-4">
      <label for="phone_number" class="block text-sm mb-1">Phone Number</label>
      <input
        type="tel"
        name="phone_number"
        id="phone_number"
        required
        placeholder="e.g. 08012345678"
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>

   <!-- Hidden field for selected product_plan_id -->
    <input type="hidden" name="product_plan_id" id="product_plan_id">

    <!-- Data Plan Grid -->
    {{-- <div class="mb-4">
      <label class="block text-sm mb-1">Data Plan</label>
      <div id="plan_grid" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      </div>
      <div id="plan_error" class="text-red-500 text-sm mt-2 hidden">Please select a data plan</div>
    </div> --}}


    <div id="size_filters" class="flex gap-2 mb-3 flex-wrap">
      <!-- Filter buttons injected via AJAX -->
    </div>
    <div class="mb-4">
      <label class="block text-sm mb-1">Data Plans</label>
    
      <!-- Scrollable container -->
      <div class="relative max-h-64 overflow-y-auto border rounded-lg p-2 pr-3 scrollbar-thin scrollbar-thumb-emerald-500 scrollbar-track-transparent">
    
        <!-- Grid with 3 columns -->
        <div id="plan_grid" class="grid grid-cols-3 gap-2 text-xs">
          {{-- Plans will be appended here dynamically --}}
        </div>
    
        <!-- Scroll hint -->
        <div class="absolute bottom-1 right-2 text-[10px] text-gray-400 dark:text-gray-500 italic pointer-events-none">
          Scroll for more ↓
        </div>
      </div>
    
      <div id="plan_error" class="text-red-500 text-sm mt-2 hidden">Please select a data plan</div>
    </div>
    
    
    
    

    

    <!-- Transaction PIN -->
    <div class="mb-6">
      <label for="pin" class="block text-sm mb-1">Transaction PIN</label>
      <input
        type="password"
        name="pin"
        id="pin"
        required
        maxlength="4"
        minlength="4"
        inputmode="numeric"
        pattern="[0-9]{4}"
        title="Enter a 4-digit PIN"
        placeholder="Enter 4-digit PIN"
        class="w-full px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
    </div>
    

    <!-- Submit Button -->
    <button
    type="submit"
    id="buy_data_btn"
    class="w-full py-2 px-4 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition disabled:opacity-50"
    :disabled="isSubmitting"
  >
    <span x-show="!isSubmitting">📶 Buy Data</span>
    <span x-show="isSubmitting" x-cloak class="flex items-center justify-center gap-2">
      <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10"
                stroke="currentColor" stroke-width="4" fill="none"/>
        <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8z"/>
      </svg>
      Processing...
    </span>
  </button>
  
  </form>
  
</div>
@endsection
