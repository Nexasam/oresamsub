@extends('layouts.app')
@section('content')

  <!-- Start::main-content -->
  <div class="main-content">

    <!-- Page Header -->
    <div class="block justify-between page-header md:flex">
      {{-- Your page header content --}}
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
        <div class="box" x-data="{ transactionStatus: '', showTransactionDetails: false }" @change="
          showTransactionDetails = transactionStatus === 'atleast_one_transaction';
        ">
          <div class="box-header">
            <h5 class="box-title font-bold">Daily Follow Up Filter</h5>
            {{-- <small class="text-red-800 text-base">It is required for you to set your pin to make sure all your transactions are treated securely</small> --}}
          </div>

          <div class="box-body">

            {{-- <form method="POST" action="{{ route('admin.daily_customer_followup.filter') }}" 
            x-data="{ transactionStatus: '', showTransactionDetails: false }" 
            @change="showTransactionDetails = transactionStatus === 'atleast_one_transaction'">
                @csrf
            
                <div class="flex flex-wrap items-center gap-6 max-w-full">
            
                <!-- Type select -->
                <div class="flex flex-col">
                    <label for="type" class="ti-form-label mb-1">Type</label>
                    <select name="type" id="type" class="ti-form-input w-48">
                    <option value="generic">Generic</option>
                    <option value="pos">POS</option>
                    <option value="both">Both</option>
                    </select>
                </div>
            
                <!-- Transaction Status select -->
                <div class="flex flex-col">
                    <label for="transaction_status" class="ti-form-label mb-1">Transaction Status</label>
                    <select name="transaction_status" id="transaction_status" x-model="transactionStatus" class="ti-form-input w-56">
                    <option value="" disabled selected>Select status</option>
                    <option value="atleast_one_transaction">At least one transaction</option>
                    <option value="no_transaction">No transaction</option>
                    </select>
                </div>
            
                <!-- Conditional metric + days input -->
                <template x-if="showTransactionDetails">
                    <div class="flex items-center space-x-4 border rounded p-3 bg-gray-50 dark:bg-gray-800">
                    <div class="flex flex-col">
                        <label for="transaction_metric" class="ti-form-label mb-1">Metric</label>
                        <select name="transaction_metric" id="transaction_metric" class="ti-form-input w-44">
                        <option value="atleast_x_days">At least X days</option>
                        <option value="x_days">X days</option>
                        </select>
                    </div>
            
                    <div class="flex flex-col">
                        <label for="days" class="ti-form-label mb-1">Days</label>
                        <input type="number" min="1" name="days" id="days" class="ti-form-input w-28" placeholder="Days">
                    </div>
                    </div>
                </template>
            
                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" class="ti-btn ti-btn-primary px-8 py-3">Apply Filter</button>
                </div>
            
                </div>
            </form> --}}

            <form method="POST" action="{{ route('admin.daily_customer_followup.filter') }}">
                @csrf
            
                <div class="flex flex-wrap items-center gap-6 max-w-full">
            
                    <!-- Type select -->
                    <div class="flex flex-col">
                        <label for="type" class="ti-form-label mb-1">Type</label>
                        <select name="type" id="type" class="ti-form-input w-48">
                            <option value="generic">Generic</option>
                            <option value="pos">POS</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
            
                    <!-- Days since last transaction input -->
                    <div class="flex flex-col">
                        <label for="days_since_last_txn" class="ti-form-label mb-1">Days Since Last Transaction</label>
                        <input type="number" min="0" name="days_since_last_txn" id="days_since_last_txn" class="ti-form-input w-28" placeholder="Enter days">
                    </div>
            
                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit" class="ti-btn ti-btn-primary px-8 py-3">Apply Filter</button>
                    </div>
            
                </div>
            </form>
            
      
              

            <!-- Display results if set -->
            @if(isset($users) && $users->count())
            <ul>
              @foreach($users as $item)
                <li>
                    Name: {{ $item->first_name.' '.$item->last_name }} <br>
                    Call Phone: {{ $item->phone_number }} <br>
                    Whatsapp Phone: {{ $item->phone_number }} <br>
                    No of Days : {{ $item->latestTransaction->created_at  }} <br>
                 </li>
              @endforeach
            </ul>
            @else
                <p>No results found.</p>
            @endif
          


          </div>
        </div>
      </div>

    </div>
    <!-- End::row-1 -->

  </div>
  <!-- End::main-content -->

@endsection
