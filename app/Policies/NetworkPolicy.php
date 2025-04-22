<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Network;
use App\Models\Permission;
use Illuminate\Auth\Access\Response;

class NetworkPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_read',config('permissions.networks.read'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_read',config('permissions.networks.read'))->first()->permission_read == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Network $network): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_read',config('permissions.networks.read'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_read',config('permissions.networks.read'))->first()->permission_read == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_create',config('permissions.networks.create'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_create',config('permissions.networks.create'))->first()->permission_create == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_update',config('permissions.networks.update'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_update',config('permissions.networks.update'))->first()->permission_update == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Network $network): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_delete',config('permissions.networks.delete'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_delete',config('permissions.networks.delete'))->first()->permission_delete == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Network $network): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Network $network): bool
    {
        //
    }
}
