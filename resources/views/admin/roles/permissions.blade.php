@extends('layouts.app')
@section('content')
<div class="main-content">
    <div class="page-header"><h1 class="text-2xl font-semibold">Permissions for {{ $role->role_name }}</h1></div>
    @if(session('success'))<div class="alert bg-success/10 text-success mb-4">{{ session('success') }}</div>@endif
    <form method="POST" action="{{ route('admin.roles.permissions.update', $role->id) }}" class="box">
        @csrf
        <div class="box-body grid grid-cols-1 gap-5 lg:grid-cols-2">
            @foreach($permissionGroups as $group => $permissions)
                <fieldset class="rounded-sm border p-4 dark:border-white/10">
                    <legend class="px-2 font-semibold">{{ $group }}</legend>
                    <div class="space-y-2">
                        @foreach($permissions as $permission)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="permissions[]" value="{{ $permission }}" class="ti-form-checkbox" @checked(in_array($permission, $selectedPermissions))>
                                <span>{{ str($permission)->after('.')->replace('_', ' ')->title() }}</span>
                                <code class="ms-auto text-xs text-gray-400">{{ $permission }}</code>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
        </div>
        <div class="box-footer text-right"><button class="ti-btn ti-btn-primary">Save permissions</button></div>
    </form>
    <form method="POST" action="{{ route('admin.roles.users.update', $role->id) }}" class="box mt-5">
        @csrf
        <div class="box-header"><h2 class="box-title">Assign staff to this role</h2></div>
        <div class="box-body grid grid-cols-1 gap-2 md:grid-cols-2">
            @forelse($staffUsers as $staff)
                <label class="flex items-center gap-2"><input type="checkbox" name="users[]" value="{{ $staff->id }}" @checked(in_array($staff->id, $selectedUsers))><span>{{ $staff->first_name }} {{ $staff->last_name }} — {{ $staff->email }}</span></label>
            @empty <p class="text-gray-500">No staff accounts are available.</p> @endforelse
        </div>
        <div class="box-footer text-right"><button class="ti-btn ti-btn-primary">Save role members</button></div>
    </form>
</div>
@endsection
