<?php

namespace App\Http\Controllers;

use App\Models\CMHDepartment;
use App\Models\SubOrganization;
use App\Models\User;
use App\Models\Wing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class WingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wings = Wing::paginate();
        return view('admin.wing.index', compact('wings'));
    }

    public function indexJson()
    {
        $wings = Wing::where('sub_organization_id', auth()->user()->sub_org_id)->get();
        return $wings;
    }

    public function indexByOrgJson($org_id)
    {
        $wings = Wing::where('sub_organization_id', $org_id)->get();
        return $wings;
    }

    public function wingUsers($wing_id)
    {
        $users = User::where('wing_id', $wing_id)->get();
        return $users;
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
        return view('admin.wing.create', compact('subOrg','dept','roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $wing = new Wing();
        $wing->name = $request->wing_name;
        $wing->sub_organization_id = $request->sub_org_id;
        $wing->save();

        return redirect()->route('wing.index');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
