@extends('layouts.app')
@section('content')
<div class="main-content">
 <div class="page-header"><h1 class="text-2xl font-semibold">Account Officers</h1><p class="text-sm text-gray-500">Manage weighted assignment for {{ number_format($unassignedCount) }} unassigned customers.</p></div>
 @if(session('success'))<div class="alert bg-success/10 text-success mb-4">{{ session('success') }}</div>@endif
 @if($errors->any())<div class="alert bg-danger/10 text-danger mb-4">{{ $errors->first() }}</div>@endif
 <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
  <form method="POST" action="{{ route('admin.account_officers.store') }}" class="box"><div class="box-header"><h2 class="box-title">Add existing staff</h2></div><div class="box-body space-y-3">@csrf
   <select name="user_id" class="ti-form-input" required><option value="">Select staff</option>@foreach($eligibleUsers as $user)<option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} — {{ $user->email }}</option>@endforeach</select>
   <input type="number" step="0.01" min="0" max="100" name="allocation_weight" class="ti-form-input" placeholder="Allocation percentage" required>
   <button class="ti-btn ti-btn-primary">Add account officer</button>
  </div></form>
  <div class="box xl:col-span-2"><form method="POST" action="{{ route('admin.account_officers.update') }}">@csrf @method('PUT')<div class="box-header"><h2 class="box-title">Officer weights</h2></div><div class="box-body space-y-3">
   @forelse($profiles as $profile)<div class="grid grid-cols-12 items-center gap-3 border-b pb-3 dark:border-white/10"><div class="col-span-5"><b>{{ $profile->user->first_name }} {{ $profile->user->last_name }}</b><div class="text-xs text-gray-500">{{ $profile->user->email }}</div></div><label class="col-span-2 text-sm"><input type="checkbox" name="officers[{{ $profile->id }}][active]" value="1" @checked($profile->is_active)> Active</label><div class="col-span-2"><input type="number" step="0.01" min="0" max="100" name="officers[{{ $profile->id }}][weight]" value="{{ $profile->allocation_weight }}" class="ti-form-input" required><div class="text-xs text-gray-500">Preview: ~{{ (int) round($unassignedCount * (float) $profile->allocation_weight / 100) }} new</div></div><div class="col-span-1 text-right">{{ $profile->user->assigned_customers_count }}</div><div class="col-span-2">@unless($profile->is_active)<button type="submit" form="redistribute-{{ $profile->id }}" class="text-xs text-danger">Redistribute</button>@endunless</div></div>@empty<p>No officers configured.</p>@endforelse
   <button class="ti-btn ti-btn-primary">Save weights</button>
  </div></form><div class="box-footer flex justify-between"><span>Active percentages must total 100% before allocation.</span><form method="POST" action="{{ route('admin.account_officers.allocate') }}">@csrf<button class="ti-btn ti-btn-success" onclick="return confirm('Assign all currently unassigned customers?')">Assign unassigned customers</button></form></div></div>
 </div>
 @foreach($profiles as $profile)@unless($profile->is_active)<form id="redistribute-{{ $profile->id }}" method="POST" action="{{ route('admin.account_officers.redistribute', $profile) }}" class="hidden">@csrf</form>@endunless @endforeach
</div>
@endsection
