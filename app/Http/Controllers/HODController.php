<?php

namespace App\Http\Controllers;

use App\Models\CMHDepartment;
use App\Models\Organization;
use App\Models\SubOrganization;
use App\Models\User;
use App\Models\UserApprovalRole;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery\Exception;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;

class HODController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sub_org_id = Auth::user()->sub_org_id;
        $branch_id = Auth::user()->branch_id;
        if(isset($sub_org_id) && isset($branch_id)) {
            $users = User::where('users.branch_id', $branch_id)->where('users.dept_id','!=',null)
                ->leftJoin('c_m_h_departments','c_m_h_departments.id','=','users.dept_id')
                ->leftJoin('sub_organizations','sub_organizations.id','=','users.sub_org_id')
                ->select('users.*','c_m_h_departments.name as dept_name','sub_organizations.name as sub_name')
                ->get();
        }elseif (isset($sub_org_id)){
            $users = User::where('users.sub_org_id', $sub_org_id)->where('users.dept_id','!=',null)
                ->leftJoin('c_m_h_departments','c_m_h_departments.id','=','users.dept_id')
                ->leftJoin('sub_organizations','sub_organizations.id','=','users.sub_org_id')
                ->select('users.*','c_m_h_departments.name as dept_name','sub_organizations.name as sub_name')
                ->get();
        }else{
            $users = User::orderBy('id','desc')->where('users.dept_id','!=',null)
                ->leftJoin('c_m_h_departments','c_m_h_departments.id','=','users.dept_id')
                ->leftJoin('sub_organizations','sub_organizations.id','=','users.sub_org_id')
                ->select('users.*','c_m_h_departments.name as dept_name','sub_organizations.name as sub_name')
                ->get();
        }
        foreach ($users as $k=>$user){
            $role = DB::table('model_has_roles')->where('model_has_roles.model_id',$user->id)
                ->join('roles','roles.id','=','model_has_roles.role_id')
                ->select('roles.name')
                ->first();
            if ($role){
                $users[$k]->role_name = $role->name;
            }else{
                $users[$k]->role_name = "";
            }
        }

        return view('admin.hod.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(Auth::user()->sub_org_id){
            $subOrg = SubOrganization::where('id', Auth::user()->sub_org_id)->first();
            $dept = CMHDepartment::where('sub_org_id', Auth::user()->sub_org_id)->get();
        }else{
            $subOrg = SubOrganization::all();
            $dept = CMHDepartment::all();
        }
        if(isset(Auth::user()->sub_org_id) || isset(Auth::user()->branch_id)){
            $allUsers = User::where('sub_org_id', Auth::user()->sub_org_id)->get();
            $sub_org_id_array = array();
            foreach ($allUsers as $allUser){
                array_push($sub_org_id_array,$allUser->id);
            }

            $roles = Role::orderBy('id','DESC')->whereIn('created_by',$sub_org_id_array)->get();
        }else{
            $roles = Role::all();
        }
        return view('admin.hod.create', compact('subOrg','dept','roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sub_org_id' => ['required'],
            'dept_id' => ['required'],
            'rank' => ['required'],
            'phone' => ['required'],
            'roles' => ['required'],
            'email' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'confirmed',
                Rules\Password::defaults()],
        ]);
        $org_id = Organization::orderBy('id','desc')->first();
        $hod = UserApprovalRole::where('role_name','HOD')->first();

        try {
            $user = User::create([
                'name' => $request->name,
                'rank' => $request->rank,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'org_id' => isset($org_id->id) ? $org_id->id : null,
                'sub_org_id' => $request->sub_org_id,
                'dept_id' => $request->dept_id,
                'user_approval_role_id' => isset($hod) ? $hod->id : 0,
            ]);
            $insert = $user->assignRole($request->input('roles'));
            AuditService::AuditLogEntry(AuditModel::User,OperationTypes::Create,'HOD User Create',null,$user,auth()->user()->id);
            return redirect()->route('all.HOD')->with('message','Successfully Added');
        }catch (Exception $exception){
            return redirect()->back()->with('error','Fail to Added');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $hod = User::find($id);
        if(Auth::user()->sub_org_id){
            $subOrg = SubOrganization::where('id', Auth::user()->sub_org_id)->first();
            $dept = CMHDepartment::where('sub_org_id', Auth::user()->sub_org_id)->get();
        }else{
            $subOrg = SubOrganization::all();
            $dept = CMHDepartment::all();
        }
        if(isset(Auth::user()->sub_org_id) || isset(Auth::user()->branch_id)){
            $allUsers = User::where('sub_org_id', Auth::user()->sub_org_id)->get();
            $sub_org_id_array = array();
            foreach ($allUsers as $allUser){
                array_push($sub_org_id_array,$allUser->id);
            }

            $roles = Role::orderBy('id','DESC')->whereIn('created_by',$sub_org_id_array)->get();
        }else{
            $roles = Role::all();
        }
        $role_id = DB::table('model_has_roles')->where('model_has_roles.model_id',$id)
            ->join('roles','roles.id','=','model_has_roles.role_id')
            ->select('roles.id')
            ->first();

        return view('admin.hod.edit', compact('subOrg','dept','roles','hod','role_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sub_org_id' => ['required'],
            'dept_id' => ['required'],
            'rank' => ['required'],
            'phone' => ['required'],
            'roles' => ['required'],
        ]);

        $user = User::find($request->id);
        $old_data = $user;
        $user->name = $request->name;
        $user->rank = $request->rank;
        $user->phone = $request->phone;
        $user->sub_org_id = $request->sub_org_id;
        $user->dept_id = $request->dept_id;

        $user->save();

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        DB::table('model_has_roles')->where('model_id',$request->id)->delete();
        $user->assignRole($request->input('roles'));

        AuditService::AuditLogEntry(AuditModel::User,OperationTypes::Update,'HOD User Update',$old_data,$user,auth()->user()->id);
        return redirect()->route('all.HOD')->with('message', 'Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $user = User::find($id);
        $new_data = null;
        $old_data = $user;
        $description = 'HOD User '.$user->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $user->deleted_by = auth()->user()->id;
        $user->save();
        $user->delete();
        AuditService::AuditLogEntry(AuditModel::User,$operation,$description,$old_data,$new_data,$user->id);
        return redirect()->back()->with('message','Successfully Deleted.');
    }
}
