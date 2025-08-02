<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psr\Log\NullLogger;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function addMenu()
    {
        $menus = DB::table('menu')->where('status', 'Active')->get();
        return view('admin.menu.create', compact('menus'));
    }

    public function storeMenu(Request $request)
    {
        $menu = DB::table('menu')
            ->insert([
                'name' => $request->name,
                'common_url' => $request->common_url,
                'status' => 'Active'
            ]);

        //        AuditService::AuditLogEntry(AuditModel::Menu,OperationTypes::Create,'Menu Create',null,$menu,auth()->user()->id);

        return redirect()->back();
    }

    public function storeSubmenu(Request $request)
    {
        if ($request->sub_menu_id) {
            $permissions_id = $request->sub_menu_id;
        } else {
            $permissions_id = null;
        }

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'menu_id' => $request->menu_id,
            'url' => $request->url,
            'is_show' => $request->is_show,
            'show_name' => $request->show_name,
            'permissions_id' => $permissions_id,
            'created_at' => now(),
        ]);

        //        AuditService::AuditLogEntry(AuditModel::Permission,OperationTypes::Create,'Permission Create',null,$permission,auth()->user()->id);

        return redirect()->back();
    }

    public static function parentMenu()
    {

        $menus = DB::table('model_has_roles')
            ->where('model_has_roles.model_id', Auth::user()->id)
            ->join('menu_has_permission', 'menu_has_permission.role_id', '=', 'model_has_roles.role_id')
            ->join('menu', 'menu.id', '=', 'menu_has_permission.menu_id')
            ->select('menu.name', 'menu.id', 'menu.common_url', 'icon')
            ->get();
        foreach ($menus as $k => $menu) {
            $sub_menu = DB::table('model_has_roles')
                ->where('model_has_roles.model_id', Auth::user()->id)
                ->join('role_has_permissions', 'role_has_permissions.role_id', '=', 'model_has_roles.role_id')
                ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->where('permissions.is_show', 'Yes')
                ->where('permissions.menu_id', $menu->id)
                ->select('permissions.*')
                ->get();
            if (!$sub_menu->isEmpty()) {
                $menus[$k]->sub_menu = $sub_menu;
            } else {
                $menus[$k]->sub_menu = '';
            }
        }
        //        dd($menus);
        return $menus;
    }

    public function getParentMenu(Request $request)
    {
        $sub_menu = DB::table('permissions')
            ->where('permissions.menu_id', '=', $request->id)
            ->where('permissions.is_show', 'Yes')
            ->get();
        return response()->json($sub_menu);
    }
}
