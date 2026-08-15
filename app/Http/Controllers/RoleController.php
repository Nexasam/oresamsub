<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index', ['roles' => Role::withCount(['users', 'accessPermissions'])->get()]);
    }

    public function permissions(string $role_id)
    {
        $role = Role::findOrFail($role_id);

        return view('admin.roles.permissions', [
            'role' => $role,
            'permissionGroups' => config('access_permissions'),
            'selectedPermissions' => $role->accessPermissions()->pluck('key')->all(),
            'staffUsers' => \App\Models\User::query()->where(function ($query) {
                $query->whereHas('role', fn ($role) => $role->where('role_name', '!=', 'User'))
                    ->orWhereHas('accountOfficerProfile');
            })->orderBy('first_name')->get(),
            'selectedUsers' => $role->users()->pluck('users.id')->all(),
        ]);
    }

    public function update_permissions(Request $request, string $role_id)
    {
        $catalogue = collect(config('access_permissions'))->flatten()->all();
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in($catalogue)],
        ]);

        $role = Role::findOrFail($role_id);
        $ids = collect($validated['permissions'] ?? [])->map(function (string $key) {
            return Permission::firstOrCreate(['key' => $key], [
                'name' => str($key)->after('.')->replace('_', ' ')->title(),
                'group' => str($key)->before('.')->toString(),
            ])->id;
        });
        $role->accessPermissions()->sync($ids);

        return back()->with('success', "Permissions updated for {$role->role_name}.");
    }

    public function updateUsers(Request $request, string $role_id)
    {
        $data = $request->validate(['users' => ['nullable', 'array'], 'users.*' => ['uuid', 'exists:users,id']]);
        Role::findOrFail($role_id)->users()->sync($data['users'] ?? []);
        return back()->with('success', 'Staff role assignments updated.');
    }
}
