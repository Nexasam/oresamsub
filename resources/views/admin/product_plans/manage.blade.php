@extends('layouts.app')

@section('content')
    <div class="main-content">
        @include('admin.product_plans.partials.manage-form', ['modal' => false])
    </div>
@endsection
