<?php

namespace App\Http\Controllers;

use App\Models\ItemGroup;
use App\Models\User;
use App\Models\UserApprovalRole;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Mockery\Exception;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function allPermission()
    {
        if (isset(Auth::user()->sub_org_id) || isset(Auth::user()->branch_id)) {
            $allUsers = User::where('sub_org_id', Auth::user()->sub_org_id)->get();
            $sub_org_id_array = array();
            foreach ($allUsers as $allUser) {
                array_push($sub_org_id_array, $allUser->id);
            }
            $roles = Role::orderBy('id', 'DESC')->whereIn('created_by', $sub_org_id_array)->get();
        } else {
            $roles = Role::orderBy('id', 'DESC')->get();
        }

        return view('admin.role.index', compact('roles'));
    }

    public function userApprovalRoles()
    {
        return UserApprovalRole::get();
    }

    public function logedUserApprovalRole()
    {
        return auth()->user()->userApprovalRole;
    }

    public function addPermission()
    {
        if (isset(Auth::user()->sub_org_id) || isset(Auth::user()->branch_id)) {
            return redirect()->route('create.permission');
        } else {
            $menus = DB::table('menu')
                ->where('menu.status', 'Active')
                ->get();
            $permissions = array();
            foreach ($menus as $k => $menu) {
                $per = permission::where('menu_id', $menu->id)->where('permissions_id', null)->select('name', 'id', 'show_name')->get();
                foreach ($per as $i => $sub) {
                    $sub_name = permission::where('permissions_id', $sub->id)->select('name', 'id')->get();
                    if (!$sub_name->isEmpty()) {
                        $per[$i]->permission_sub_menu = $sub_name;
                    } else {
                        $per[$i]->permission_sub_menu = '';
                    }
                }
                $menus[$k]->permission = $per;
            }

            return view('admin.role.create', compact('menus'));
        }
    }

    public function StorePermission(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
            'menu_id' => 'required',
        ]);

        try {
            $role = Role::create(['name' => $request->input('name'), 'created_by' => Auth::user()->id]);

            AuditService::AuditLogEntry(AuditModel::Role, OperationTypes::Create, 'Role Create', null, $role, auth()->user()->id);

            $role->syncPermissions($request->input('permission'));

            $menus = $request->input('menu_id');
            $menu_id_json = array();
            foreach ($menus as $menu) {
                array_push($menu_id_json, $menu);
                $permission = DB::table('menu_has_permission')->insert([
                    'menu_id' => $menu,
                    'role_id' => $role->id,
                    'created_by' => Auth::user()->id,
                    'created_at' => now(),
                ]);
            }

            AuditService::AuditLogEntry(AuditModel::MenuHasPermission, OperationTypes::Create, 'Menu has Permission Create', null, json_encode($menu_id_json), auth()->user()->id);

            return redirect()->route('all.permission')->with('message', 'Successfully Store.');
        } catch (Exception $exception) {
            return redirect()->back()->with('error', 'Fail to Store');
        }
    }

    public function createPermission()
    {
        $user_id = Auth::user()->id;
        $role_id = DB::table('model_has_roles')->where('model_has_roles.model_id', $user_id)
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.id')
            ->first();
        $id = $role_id->id;

        $role = Role::find($id);
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        //        $permission = Permission::get();
        $menu_has_permission = DB::table("menu_has_permission")->where("menu_has_permission.role_id", $id)
            ->pluck('menu_has_permission.menu_id', 'menu_has_permission.menu_id')
            ->all();

        $menus = DB::table('menu')
            ->where('menu.status', 'Active')
            ->get();
        $permissions = array();
        foreach ($menus as $k => $menu) {
            $per = permission::where('menu_id', $menu->id)->where('permissions_id', null)->select('name', 'id', 'show_name')->get();
            foreach ($per as $i => $sub) {
                $sub_name = permission::where('permissions_id', $sub->id)->select('name', 'id')->get();

                if (!$sub_name->isEmpty()) {
                    $per[$i]->permission_sub_menu = $sub_name;
                } else {
                    $per[$i]->permission_sub_menu = '';
                }
            }
            $menus[$k]->permission = $per;
        }

        return view('admin.role.create_2', compact('menus', 'role', 'rolePermissions', 'menu_has_permission'));
    }

    public function EditPermission($id)
    {
        if (isset(Auth::user()->sub_org_id) || isset(Auth::user()->branch_id)) {
            $user_id = Auth::user()->id;
            $role_id = DB::table('model_has_roles')->where('model_has_roles.model_id', $user_id)
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('roles.id')
                ->first();
            $role_id = $role_id->id;

            $role = Role::find($id);

            $rolePermissionsAdmin = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $role_id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->all();
            $menu_has_permission_admin = DB::table("menu_has_permission")->where("menu_has_permission.role_id", $role_id)
                ->pluck('menu_has_permission.menu_id', 'menu_has_permission.menu_id')
                ->all();

            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->all();
            $menu_has_permission = DB::table("menu_has_permission")->where("menu_has_permission.role_id", $id)
                ->pluck('menu_has_permission.menu_id', 'menu_has_permission.menu_id')
                ->all();

            $menus = DB::table('menu')
                ->where('menu.status', 'Active')
                ->get();
            $permissions = array();
            foreach ($menus as $k => $menu) {
                $per = permission::where('menu_id', $menu->id)->where('permissions_id', null)->select('name', 'id', 'show_name')->get();
                foreach ($per as $i => $sub) {
                    $sub_name = permission::where('permissions_id', $sub->id)->select('name', 'id')->get();

                    if (!$sub_name->isEmpty()) {
                        $per[$i]->permission_sub_menu = $sub_name;
                    } else {
                        $per[$i]->permission_sub_menu = '';
                    }
                }
                $menus[$k]->permission = $per;
            }
            return view('admin.role.edit_2', compact('menus', 'role', 'rolePermissions', 'menu_has_permission', 'rolePermissionsAdmin', 'menu_has_permission_admin'));
        } else {
            $role = Role::find($id);
            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->all();
            //        $permission = Permission::get();
            $menu_has_permission = DB::table("menu_has_permission")->where("menu_has_permission.role_id", $id)
                ->pluck('menu_has_permission.menu_id', 'menu_has_permission.menu_id')
                ->all();

            $menus = DB::table('menu')
                ->where('menu.status', 'Active')
                ->get();
            $permissions = array();
            foreach ($menus as $k => $menu) {
                $per = permission::where('menu_id', $menu->id)->where('permissions_id', null)->select('name', 'id', 'show_name')->get();
                foreach ($per as $i => $sub) {
                    $sub_name = permission::where('permissions_id', $sub->id)->select('name', 'id')->get();

                    if (!$sub_name->isEmpty()) {
                        $per[$i]->permission_sub_menu = $sub_name;
                    } else {
                        $per[$i]->permission_sub_menu = '';
                    }
                }
                $menus[$k]->permission = $per;
            }
            return view('admin.role.edit', compact('menus', 'role', 'rolePermissions', 'menu_has_permission'));
        }
    }

    public function editOwnPermission() {}

    public function UpdatePermission(Request $request)
    {
        $this->validate($request, [
            'permission' => 'required',
            'menu_id' => 'required',
        ]);

        $id = $request->role_id;
        $role = Role::find($id);
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        DB::beginTransaction();
        try {
            $role->syncPermissions($request->input('permission'));
            $menus = $request->input('menu_id');
            DB::table('menu_has_permission')->where('role_id', $id)->delete();
            foreach ($menus as $menu) {
                DB::table('menu_has_permission')->insert([
                    'menu_id' => $menu,
                    'role_id' => $id,
                    'created_by' => Auth::user()->id,
                    'created_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('all.permission')->with('message', 'Successfully Updated.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Fail to Update.');
        }
    }

    public function destroy(string $id)
    {
        try {
            $role = Role::find($id);

            $new_data = null;
            $old_data = $role;
            $description = 'Role ' . $role->name . ' has been deleted by ' . auth()->user()->name;
            $operation = OperationTypes::Delete;

            $role->deleted_by = auth()->user()->id;
            $role->save();
            $role->delete();

            AuditService::AuditLogEntry(AuditModel::Role, $operation, $description, $old_data, $new_data, $role->id);

            return redirect()->route('all.permission')->with('message', 'Successfully Deleted.');
        } catch (Exception $exception) {
            return redirect()->route('all.permission')->with('error', 'Fail to Deleted.');
        }
    }
}
