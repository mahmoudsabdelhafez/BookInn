<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Exports\PermissionExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PermissionImport;
use App\Models\User;
use Illuminate\Support\Facades\DB;




class RoleController extends Controller
{
    public function AllPermission()
    {
        $permissions = Permission::latest()->get();
        return view('backend.pages.permission.all_permission', compact('permissions'));
    } // End Method 


    public function AddPermission()
    {

        return view('backend.pages.permission.add_permission');
    } // End Method

    public function StorePermission(Request $request)
    {

        $request->validate([
            'name' => 'required|unique:permissions,name|max:255',
            'group_name' => 'required|max:255',
        ]);


        $permission = Permission::create([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);
        $notification = array(
            'message' => 'Permission Created Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($notification);
    } // End Method 


    public function EditPermission($id)
    {
        $permission = Permission::find($id);
        return view('backend.pages.permission.edit_permission', compact('permission'));
    } // End Method 
    public function UpdatePermission(Request $request)
    {
        $per_id = $request->id;
        Permission::find($per_id)->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);
        $notification = array(
            'message' => 'Permission Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.permission')->with($notification);
    } // End Method 
    public function DeletePermission($id)
    {
        Permission::find($id)->delete();
        $notification = array(
            'message' => 'Permission Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method 

    public function ImportPermission()
    {
        return view('backend.pages.permission.import_permission');
    } // End Method 


    public function Export()
    {

        return Excel::download(new PermissionExport, 'permission.xlsx');
    } // End Method


    public function Import(Request $request)
    {
        Excel::import(new PermissionImport, $request->file('import_file'));

        $notification = array(
            'message' => 'Permission Imported Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method


    //=========================All Roles Method ============================================
    public function AllRoles()
    {
        $roles = Role::latest()->get();
        return view('backend.pages.roles.all_roles', compact('roles'));
    } // End Method
    public function AddRoles()
    {
        return view('backend.pages.roles.add_roles');
    } // End Method
    public function StoreRoles(Request $request)
    {

        Role::create([
            'name' => $request->name,
        ]);
        $notification = array(
            'message' => 'Role Created Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification);
    } // End Method


    public function EditRoles($id)
    {
        $roles = Role::find($id);
        return view('backend.pages.roles.edit_roles', compact('roles'));
    } // End Method
    public function UpdateRoles(Request $request)
    {
        $role_id = $request->id;
        Role::find($role_id)->update([
            'name' => $request->name,
        ]);
        $notification = array(
            'message' => 'Role Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.roles')->with($notification);
    } // End Method
    public function DeleteRoles($id)
    {
        Role::find($id)->delete();
        $notification = array(
            'message' => 'Role Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method



    // =============================== Roles with Permission ================================================
    public function AddRolesPermission()
    {

        $roles = Role::all();
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('backend.pages.rolesetup.add_roles_permission', compact('roles', 'permissions', 'permission_groups'));
    } // End Method


    public function RolePermissionStore(Request $request)
    {
        // Validate the request
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,id',
        ]);
    
        $data = [];
        $permissions = $request->permission;
        $alreadyAssigned = [];
    
        foreach ($permissions as $item) {
            // Check if the permission is already assigned to the role
            $exists = DB::table('role_has_permissions')
                ->where('role_id', $request->role_id)
                ->where('permission_id', $item)
                ->exists();
    
            if ($exists) {
                // Add to already assigned list
                $alreadyAssigned[] = $item;
            } else {
                // Insert new permission assignment
                $data['role_id'] = $request->role_id;
                $data['permission_id'] = $item;
                DB::table('role_has_permissions')->insert($data);
            }
        }
    
        // Prepare notification
        if (!empty($alreadyAssigned)) {
            $notification = [
                'message' => 'Some permissions were already assigned to this role.',
                'alert-type' => 'warning'
            ];
            return redirect()->back()->with($notification);

        } else {
            $notification = [
                'message' => 'Role Permission Added Successfully',
                'alert-type' => 'success'
            ];
            return redirect()->route('all.roles.permission')->with($notification);

        }
    
        // Redirect back with notification
    }
    



    public function AllRolesPermission()
    {
        $roles = Role::all();
        return view('backend.pages.rolesetup.all_roles_permission', compact('roles'));
    } // End Method


    public function AdminEditRoles($id)
    {
        $role = Role::find($id);
        $permissions = Permission::all();
        $permission_groups = User::getpermissionGroups();
        return view('backend.pages.rolesetup.edit_roles_permission', compact('role', 'permissions', 'permission_groups'));
    } // End Method


    public function AdminRolesUpdate(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'permission' => 'nullable|array',  // Allow null for removing all permissions
            'permission.*' => 'exists:permissions,id',  // Ensure each permission exists
        ]);
    
        // Find the role
        $role = Role::findOrFail($id);
    
        // Check if permissions are provided and not null
        $permissions = $request->permission ?? [];
    
        // Sync the permissions (empty array if all permissions are removed)
        if (count($permissions) > 0) {
            $permissionNames = Permission::whereIn('id', $permissions)->pluck('name');
            $role->syncPermissions($permissionNames); // Syncs the role with the provided permission names. Removes any old permissions and replaces them with the new ones.
            
        } else {
            // If no permissions are provided, remove all associated permissions
            $role->syncPermissions([]);
        }
    
        // Return success notification
        $notification = array(
            'message' => 'Role Permission Updated Successfully',
            'alert-type' => 'success'
        );
    
        return redirect()->route('all.roles.permission')->with($notification);
    }
    



    public function AdminDeleteRoles($id)
    {
        $role = Role::find($id);
        if (!is_null($role)) {
            $role->delete();
        }
        $notification = array(
            'message' => 'Role Permission Deleted Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    } // End Method



}
