<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\RegisteredUserController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;


class IsExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // $routeName = \Request::route()->getName();
        // $getRolePermMenu = DB::table('menu')->where('common_url', $routeName)->first();
        // if(isset($getRolePermMenu) && !empty($getRolePermMenu)){
        //     return $next($request);
        // }
        // $getRolePerm = DB::table('permissions')->where('url', $routeName)->first();

        // if(isset($$getRolePerm) && !empty($$getRolePerm)){
        //     $role = DB::table('model_has_roles')->where('model_has_roles.model_id',Auth::user()->id)
        //     ->join('roles','roles.id','=','model_has_roles.role_id')
        //     ->select('roles.*')
        //     ->first();

        //     $getRole = DB::table('role_has_permissions')->where('role_id', $role->id)->where('permission_id',$getRolePerm->id)->first();
        //     if(isset( $getRole) && !empty( $getRole)){
        //         return $next($request);
        //     }else{
        //         return redirect()->back();
        //     }
        // }else{
        //     return redirect()->back();
        // }

        return $next($request);

    }
}
