@extends('template2.layouts.app')
@section('title','All Transactions')
@section('template2_content')
{{-- <div class="max-w-6xl mx-auto mb-4 border-b border-[{{$site_primary_color}}]">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-[{{$site_primary_color}}] hover:text-[{{$site_primary_color}}] border-[{{$site_primary_color}}]" data-tabs-inactive-classes="text-[#000000] hover:text-[{{$site_primary_color}}] border-gray-700 hover:border-[{{$site_primary_color}}]" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">All Transactions</button>
        </li>
      
    </ul>
</div> --}}
<div id="max-w-3xl px-4 mx-auto default-styled-tab-content">
    {{-- <div class="max-w-6xl md:mx-auto   hidden px-2 my-6  p-4 rounded-lg shadow-lg bg-gray-50" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab"> --}}
        {{-- <div class="box w-full px-4 mb-4"> --}}
            {{-- <div class="box-header"> --}}
                {{-- <div class="sm:flex">
                    <h5 class="box-title my-auto">Recent Transactions</h5>
                </div> --}}
            {{-- </div> --}}
            <livewire:transactions-table />

        {{-- </div> --}}
    {{-- </div> --}}
   
</div>
@endsection