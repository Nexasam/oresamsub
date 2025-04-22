<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use App\Models\Permission;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_read',config('permissions.products.read'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_read',config('permissions.products.read'))->first()->permission_read == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_read',config('permissions.products.read'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_read',config('permissions.products.read'))->first()->permission_read == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_create',config('permissions.products.create'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_create',config('permissions.products.create'))->first()->permission_create == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_update',config('permissions.products.update'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_update',config('permissions.products.update'))->first()->permission_update == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_delete',config('permissions.products.delete'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_delete',config('permissions.products.delete'))->first()->permission_delete == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        //
    }
}
