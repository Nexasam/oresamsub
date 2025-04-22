@extends('template2.layouts.app')

@section('title','Dashboard')

@section('template2_content')



<div id="max-w-3xl px-4 mx-auto default-styled-tab-content">
    {{-- <div class="max-w-3xl mx-2 md:mx-10  hidden px-2 my-6  p-4 rounded-lg shadow-lg bg-gray-50" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab"> --}}
        {{-- <div class="box w-full px-4 mb-4"> --}}
            {{-- <div class="box-header"> --}}
                {{-- <div class="sm:flex">
                    <h5 class="box-title my-auto">Recent Transactions</h5>
                </div> --}}
            {{-- </div> --}}
            <livewire:transactions-table />

            {{-- @livewire('transactions-table', ['site_primary_color' => $site_primary_color,'site_secondary_color'=>$site_secondary_color]) --}}


        {{-- </div> --}}
    {{-- </div> --}}
   
</div>
@endsection