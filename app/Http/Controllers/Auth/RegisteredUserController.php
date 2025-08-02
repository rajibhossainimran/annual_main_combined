<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Branch;
use App\Models\ItemGroup;
use Illuminate\View\View;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Services\AuditService;
use App\Models\SubOrganization;
use App\Utill\Const\AuditModel;
use App\Models\UserApprovalRole;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Utill\Const\OperationTypes;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function index(): view
    {
        $sub_org_id = Auth::user()->sub_org_id;
        $branch_id = Auth::user()->branch_id;
        if (isset($sub_org_id) && isset($branch_id)) {
            $users = User::where('branch_id', $branch_id)->get();
        } elseif (isset($sub_org_id)) {
            $users = User::where('sub_org_id', $sub_org_id)->get();
        } else {
            $users = User::all();
        }
        foreach ($users as $k => $user) {
            $role = DB::table('model_has_roles')->where('model_has_roles.model_id', $user->id)
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('roles.name')
                ->first();
            if ($role) {
                $users[$k]->role_name = $role->name;
            } else {
                $users[$k]->role_name = "";
            }
        }

        return view('admin.user.index', compact('users'));
    }

    public function create(): View
    {
        if (isset(Auth::user()->sub_org_id) || isset(Auth::user()->branch_id)) {
            $allUsers = User::where('sub_org_id', Auth::user()->sub_org_id)->get();
            $sub_org_id_array = array();
            foreach ($allUsers as $allUser) {
                array_push($sub_org_id_array, $allUser->id);
            }

            $roles = Role::orderBy('id', 'DESC')->whereIn('created_by', $sub_org_id_array)->get();
        } else {
            $roles = Role::all();
        }

        $organizations = Organization::all();
        $sub_organizations = [];
        $branches = [];
        if (isset(Auth::user()->org_id)) {
            $sub_organizations = SubOrganization::where('org_id', Auth::user()->org_id)->get();
            if (isset(Auth::user()->sub_org_id)) {
                $branches = Branch::where('sub_org_id', Auth::user()->sub_org_id)->get();
            }
        }

        $user_approval_roles = UserApprovalRole::all();

        $item_groups = ItemGroup::all();

        return view('admin.user.create', compact('roles', 'organizations', 'sub_organizations', 'branches', 'user_approval_roles', 'item_groups'));
        //        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'confirmed',
                Rules\Password::defaults()
            ],
            'roles' => ['required'],
            //            'user_approval_role_id' => ['required'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'rank' => $request->rank,
            'demand_email' => $request->demand_email,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'org_id' => isset($request->org_id) ? $request->org_id : null,
            'sub_org_id' => isset($request->sub_org_id) ? $request->sub_org_id : null,
            'branch_id' => isset($request->branch_id) ? $request->branch_id : null,
            'user_approval_role_id' => $request->user_approval_role_id,
            'wing_id' => $request->wing_id,
            'for_role' => $request->for_role ?? null,
            'group_id' => $request->group_id ?? null,
        ]);

        $insert = $user->assignRole($request->input('roles'));

        AuditService::AuditLogEntry(AuditModel::User, OperationTypes::Create, 'User Create', null, $user, auth()->user()->id);

        return redirect()->route('all.user')->with('message', 'Successfully Added');
    }

    public function edit(string $id): view
    {
        if (isset(Auth::user()->sub_org_id) || isset(Auth::user()->branch_id)) {
            $allUsers = User::where('sub_org_id', Auth::user()->sub_org_id)->get();
            $sub_org_id_array = array();
            foreach ($allUsers as $allUser) {
                array_push($sub_org_id_array, $allUser->id);
            }

            $roles = Role::orderBy('id', 'DESC')->whereIn('created_by', $sub_org_id_array)->get();
        } else {
            $roles = Role::all();
        }
        $role_id = DB::table('model_has_roles')->where('model_has_roles.model_id', $id)
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.id')
            ->first();

        $user = User::find($id);
        //        $roles = Role::all();
        $organizations = Organization::all();
        $user_approval_roles = UserApprovalRole::all();
        return view('admin.user.edit', compact('user', 'roles', 'user_approval_roles', 'role_id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        $user = User::find($id);
        $old_data = $user;
        $user->name = $request->name;
        $user->rank = $request->rank;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->demand_email = $request->demand_email;
        $user->user_approval_role_id = $request->user_approval_role_id;
        $user->for_role = $request->for_role;
        $user->save();

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));

        AuditService::AuditLogEntry(AuditModel::User, OperationTypes::Update, 'User Update', $old_data, $user, auth()->user()->id);
        return redirect()->route('all.user')->with('message', 'Successfully Updated');
    }

    public function delete($id)
    {
        $user = User::find($id);
        $new_data = null;
        $old_data = $user;
        $description = 'User ' . $user->name . ' has been deleted by ' . auth()->user()->name;
        $operation = OperationTypes::Delete;

        $user->deleted_by = auth()->user()->id;
        $user->save();
        $user->delete();
        AuditService::AuditLogEntry(AuditModel::User, $operation, $description, $old_data, $new_data, $user->id);
        return redirect()->back()->with('message', 'Successfully Deleted.');
    }

    public function ChangePassword()
    {
        return view('admin.user.change_password');
    }

    public function UpdatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'confirmed'
            ],
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        $user = User::find(Auth::user()->id);
        if ($request->old_password != $request->new_password) {
            if (Hash::check($request->old_password, $user->password)) {
                $old_data = $user;
                $user->password = $request->new_password;
                $user->change_password = date('Y-m-d');
                $user->save();
                AuditService::AuditLogEntry(AuditModel::User, OperationTypes::Update, 'User Password Update', $old_data, $user, auth()->user()->id);
                return redirect()->back()->with('message', 'Password Change Successfully.');
            } else {
                return redirect()->back()->with('error', "Old Password doesn't match");
            }
        } else {
            return redirect()->back()->with('error', "Old Password and New Password are same");
        }
    }

    public function UpdatePasswordExpired(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'confirmed'
            ],
            'new_password_confirmation' => 'required|same:new_password',
        ]);



        $user = User::find(\Session::get('expiredId'));
        if ($request->old_password != $request->new_password) {
            if (Hash::check($request->old_password, $user->password)) {
                $old_data = $user;
                $user->password = $request->new_password;
                $user->change_password = date('Y-m-d');
                $user->save();

                // AuditService::AuditLogEntry(AuditModel::User,OperationTypes::Update,'User Password Update',$old_data,$user,\Session::get('expiredId'));
                return redirect('/login')->with('message', 'Password Change Successfully.');
            } else {
                return redirect()->back()->with('error', "Old Password doesn't match");
            }
        } else {
            return redirect()->back()->with('error', "Old Password and New Password are same");
        }
    }

    public function UserPasswordChange($id)
    {
        return view('admin.user.user_password', compact('id'));
    }

    public function UpdatePasswordAll(Request $request)
    {
        if ($request->id == Auth::user()->id) {
            $request->validate([
                'old_password' => 'required',
                'new_password' => [
                    'required',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'confirmed'
                ],
                'new_password_confirmation' => 'required|same:new_password',
            ]);

            $user = User::find(Auth::user()->id);
            if ($request->old_password != $request->new_password) {
                if (Hash::check($request->old_password, $user->password)) {
                    $old_data = $user;
                    $user->password = $request->new_password;
                    $user->change_password = date('Y-m-d');
                    $user->save();
                    AuditService::AuditLogEntry(AuditModel::User, OperationTypes::Update, 'User Password Update', $old_data, $user, auth()->user()->id);
                    return redirect()->back()->with('message', 'Password Change Successfully.');
                } else {
                    return redirect()->back()->with('error', "Old Password doesn't match");
                }
            } else {
                return redirect()->back()->with('error', "Old Password and New Password are same");
            }
        } else {
            $request->validate([
                'new_password' => [
                    'required',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'confirmed'
                ],
                'new_password_confirmation' => 'required|same:new_password',
            ]);
            if ($request->old_password != $request->new_password) {
                $user = User::find($request->id);
                $old_data = $user;
                $user->password = $request->new_password;
                $user->change_password = date('Y-m-d');
                $user->save();
                AuditService::AuditLogEntry(AuditModel::User, OperationTypes::Update, 'User Password Update', $old_data, $user, auth()->user()->id);
                return redirect()->back()->with('message', 'Password Change Successfully.');
            } else {
                return redirect()->back()->with('error', "Old Password and New Password are same");
            }
        }
    }

    public function digitalSign()
    {
        return view('admin.user.user_sign');
    }

    public function digitalSignUpdate(Request $request)
    {

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        $imageName = time() . '.' . $request->image->extension();

        $request->image->move(public_path('sign'), $imageName);

        $user = User::find(Auth::user()->id);
        $old_data = $user;
        $user->sign = $imageName;
        $user->save();
        AuditService::AuditLogEntry(AuditModel::User, OperationTypes::Update, 'User Sign Update', $old_data, $user, auth()->user()->id);
        return redirect()->back()->with('message', 'User Signature Change Successfully.');
    }
}
