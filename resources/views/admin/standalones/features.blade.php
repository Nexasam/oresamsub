@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="block justify-between page-header md:flex"><div><h3 class="text-gray-700 text-2xl font-semibold">Standalone features</h3><p class="mt-1 text-sm text-gray-500">Manage feature billing and global price-level amounts.</p></div><a class="ti-btn ti-btn-light" href="{{ route('admin.standalones.index') }}">Back to standalones</a></div>
    @if(session('success'))<div class="bg-success/10 border border-success/20 alert text-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="bg-danger/10 border border-danger/20 alert text-danger">{{ $errors->first() }}</div>@endif

    <div class="box"><div class="box-header"><h5 class="box-title">Add feature</h5></div><div class="box-body">
        <form method="POST" action="{{ route('admin.standalones.features.store') }}" class="grid grid-cols-12 gap-4">@csrf
            <div class="col-span-12 md:col-span-4"><label class="ti-form-label">Name</label><input class="ti-form-input" name="name" required></div>
            <div class="col-span-12 md:col-span-4"><label class="ti-form-label">Slug</label><input class="ti-form-input" name="slug" required></div>
            <div class="col-span-12 md:col-span-4"><label class="ti-form-label">Billing</label><select class="ti-form-select" name="billing_type"><option value="one_time">One-time</option><option value="monthly">Monthly</option><option value="one_time_plus_monthly">One-time + monthly</option><option value="free">Free</option></select></div>
            @foreach(['default_price'=>'Default price','level_1_price'=>'Level 1','level_2_price'=>'Level 2','level_3_price'=>'Level 3','level_4_price'=>'Level 4'] as $field=>$label)<div class="col-span-6 md:col-span-2"><label class="ti-form-label">{{ $label }}</label><input class="ti-form-input" type="number" min="0" step="0.01" name="{{ $field }}" required></div>@endforeach
            <div class="col-span-12 text-sm font-semibold">Monthly prices (required for recurring features)</div>
            @foreach(['default_monthly_price'=>'Default monthly','level_1_monthly_price'=>'Monthly L1','level_2_monthly_price'=>'Monthly L2','level_3_monthly_price'=>'Monthly L3','level_4_monthly_price'=>'Monthly L4'] as $field=>$label)<div class="col-span-6 md:col-span-2"><label class="ti-form-label">{{ $label }}</label><input class="ti-form-input" type="number" min="0" step="0.01" name="{{ $field }}"></div>@endforeach
            <div class="col-span-12"><label class="inline-flex gap-2"><input type="checkbox" name="is_active" value="1" checked> Available to standalones</label></div>
            <div class="col-span-12"><button class="ti-btn ti-btn-primary">Create feature</button></div>
        </form>
    </div></div>

    @foreach($features as $feature)<div class="box"><div class="box-header"><h5 class="box-title">{{ $feature->name }}</h5></div><div class="box-body">
        <form method="POST" action="{{ route('admin.standalones.features.update', $feature) }}" class="grid grid-cols-12 gap-4">@csrf @method('PUT')
            <div class="col-span-12 md:col-span-3"><label class="ti-form-label">Name</label><input class="ti-form-input" name="name" value="{{ $feature->name }}" required></div>
            <div class="col-span-12 md:col-span-3"><label class="ti-form-label">Slug</label><input class="ti-form-input" name="slug" value="{{ $feature->slug }}" required></div>
            <div class="col-span-12 md:col-span-3"><label class="ti-form-label">Billing</label><select class="ti-form-select" name="billing_type">@foreach(['one_time'=>'One-time','monthly'=>'Monthly','one_time_plus_monthly'=>'One-time + monthly','free'=>'Free'] as $value=>$label)<option value="{{ $value }}" @selected($feature->billing_type===$value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-span-12 md:col-span-3"><label class="ti-form-label">Order</label><input class="ti-form-input" type="number" min="0" name="sort_order" value="{{ $feature->sort_order }}"></div>
            @foreach(['default_price'=>'Default price','level_1_price'=>'Level 1','level_2_price'=>'Level 2','level_3_price'=>'Level 3','level_4_price'=>'Level 4'] as $field=>$label)<div class="col-span-6 md:col-span-2"><label class="ti-form-label">{{ $label }}</label><input class="ti-form-input" type="number" min="0" step="0.01" name="{{ $field }}" value="{{ $feature->$field }}" required></div>@endforeach
            <div class="col-span-12 text-sm font-semibold">Monthly prices</div>
            @foreach(['default_monthly_price'=>'Default monthly','level_1_monthly_price'=>'Monthly L1','level_2_monthly_price'=>'Monthly L2','level_3_monthly_price'=>'Monthly L3','level_4_monthly_price'=>'Monthly L4'] as $field=>$label)<div class="col-span-6 md:col-span-2"><label class="ti-form-label">{{ $label }}</label><input class="ti-form-input" type="number" min="0" step="0.01" name="{{ $field }}" value="{{ $feature->$field }}"></div>@endforeach
            <div class="col-span-12"><label class="inline-flex gap-2"><input type="checkbox" name="is_active" value="1" @checked($feature->is_active)> Available</label></div>
            <div class="col-span-12"><button class="ti-btn ti-btn-primary">Save feature</button></div>
        </form>
    </div></div>@endforeach
</div>
@endsection
