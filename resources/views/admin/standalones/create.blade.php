@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="block justify-between page-header md:flex">
        <div><h3 class="text-gray-700 text-2xl font-semibold">Add standalone website</h3><p class="mt-1 text-sm text-gray-500">Create a protected integration and its 20-minute bootstrap token.</p></div>
        <a class="ti-btn ti-btn-light mt-3 md:mt-0" href="{{ route('admin.standalones.index') }}">Back to standalones</a>
    </div>

    @if ($errors->any())
        <div class="bg-danger/10 border border-danger/20 alert text-danger"><ul class="list-disc ps-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="box">
        <div class="box-header"><h5 class="box-title">Business and SecureWave details</h5></div>
        <div class="box-body">
            <form method="POST" action="{{ route('admin.standalones.store') }}">@csrf
                <div class="grid lg:grid-cols-2 gap-6">
                    @foreach(['business_name'=>'Business name','contact_first_name'=>'Contact first name','contact_last_name'=>'Contact last name','email'=>'Email address','phone'=>'Phone number','website_url'=>'Website URL','bvn'=>'BVN'] as $field=>$label)
                        <div class="space-y-2 {{ in_array($field, ['business_name','website_url']) ? 'lg:col-span-2' : '' }}">
                            <label class="ti-form-label mb-0" for="{{ $field }}">{{ $label }}</label>
                            <input class="ti-form-input" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" type="{{ $field === 'email' ? 'email' : ($field === 'website_url' ? 'url' : 'text') }}" required @if($field === 'bvn') inputmode="numeric" maxlength="11" @endif>
                            @error($field)<p class="text-xs text-danger">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-end gap-2"><a class="ti-btn ti-btn-light" href="{{ route('admin.standalones.index') }}">Cancel</a><button class="ti-btn ti-btn-primary" type="submit">Create standalone</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
