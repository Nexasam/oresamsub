<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
{
    public function index(){
        $roles = Role::all();
        $data['roles'] = $roles;
        return view('admin.roles.index')->with($data);
    }

    public function permissions($role_id){
        // dd($role_id);
        $role = Role::where('id',$role_id)->first();
        $permissions  = config('permissions');
        // dd($permissions);
        $db_permission_creates = Permission::where('role_id',$role_id)->whereNotNull('permission_create')->pluck('permission_create')->toArray();
        $db_permission_reads = Permission::where('role_id',$role_id)->whereNotNull('permission_read')->pluck('permission_read')->toArray();
        $db_permission_updates = Permission::where('role_id',$role_id)->whereNotNull('permission_update')->pluck('permission_update')->toArray();
        $db_permission_deletes = Permission::where('role_id',$role_id)->whereNotNull('permission_delete')->pluck('permission_delete')->toArray();
        $data['permissions'] = $permissions;
        
        $data['db_permission_creates'] = $db_permission_creates;
        $data['db_permission_reads'] = $db_permission_reads;
        $data['db_permission_updates'] = $db_permission_updates;
        $data['db_permission_deletes'] = $db_permission_deletes;
        $data['role'] = $role;
        // dd($data);
        return view('admin.roles.permissions')->with($data);
    }

    public function update_permissions(Request $request){
        $permissions_to_update =$request->all();
        // dd($permissions_to_update);
        $role_id = $request->role_id;
        $permissions = $request->permissions;
        $role_details = Role::where('id',$role_id)->first();
        $role_name = strtolower($role_details->role_name);

        //do role cleaning first
        Permission::where('role_id',$role_id)->delete();
        //then reinsert
        foreach($permissions as $each_permission){
            $permission_name_to_remove = explode('_',$each_permission)[0];
            $permission_name_modified = explode('_',$each_permission);
            array_shift($permission_name_modified);
            $permission_key = implode('_',$permission_name_modified);
            $permission_name = config('permissions.'.$permission_key.'.name');
            $permission_slug = config('permissions.'.$permission_key.'.slug');
            // $permission_slug = config('permissions.'.$permission_key.'.slug');
            Permission::where('role_id',$role_id)->updateOrCreate([
                'role_id' => $role_id,
                'permission_slug' => $permission_slug,
                'permission_name' => $permission_name,
            ],[
                'permission_'.$permission_name_to_remove => $each_permission,
            ]);

        }
       
        Session::flash('success','Permissions was successfully updated for the role: '.$role_name);
        return redirect()->back();
    }

}
