<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserPlan;
use App\Models\Permission;
use Illuminate\Auth\Access\Response;

class UserPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {

       return  (! Permission::where('role_id',$user->role)->where('permission_read',config('permissions.reseller_plans.read'))->first() || 
       Permission::where('role_id',$user->role)->where('permission_read',config('permissions.reseller_plans.read'))->first()->permission_read == NULL)
       ? false : true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserPlan $model): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_read',config('permissions.reseller_plans.read'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_read',config('permissions.reseller_plans.read'))->first()->permission_read == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_create',config('permissions.reseller_plans.create'))->first() || 
       Permission::where('role_id',$user->role)->where('permission_create',config('permissions.reseller_plans.create'))->first()->permission_create == NULL)
       ? false : true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserPlan $model): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_update',config('permissions.reseller_plans.update'))->first() || 
       Permission::where('role_id',$user->role)->where('permission_update',config('permissions.reseller_plans.update'))->first()->permission_update == NULL)
       ? false : true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserPlan $model): bool
    {
        return  (! Permission::where('role_id',$user->role)->where('permission_delete',config('permissions.reseller_plans.delete'))->first() || 
        Permission::where('role_id',$user->role)->where('permission_delete',config('permissions.reseller_plans.delete'))->first()->permission_delete == NULL)
        ? false : true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserPlan $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserPlan $model): bool
    {
        //
    }
}
