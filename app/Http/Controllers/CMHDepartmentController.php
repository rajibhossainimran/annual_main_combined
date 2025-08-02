<?php

namespace App\Http\Controllers;

use App\Models\CMHDepartment;
use App\Models\Setting;
use App\Models\SubOrganization;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class CMHDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->sub_org_id){
            $departs = CMHDepartment::where('c_m_h_departments.sub_org_id',Auth::user()->sub_org_id)
                ->orderBy('c_m_h_departments.id','desc')
                ->leftJoin('sub_organizations','sub_organizations.id','=','c_m_h_departments.sub_org_id')
                ->select('c_m_h_departments.*','sub_organizations.name as sub_name')
                ->get();
        }else{
            $departs = CMHDepartment::orderBy('c_m_h_departments.id','desc')
                ->leftJoin('sub_organizations','sub_organizations.id','=','c_m_h_departments.sub_org_id')
                ->select('c_m_h_departments.*','sub_organizations.name as sub_name')
                ->get();
        }
        return view('admin.department.index', compact('departs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subOrg = '';
        if(Auth::user()->sub_org_id){
            $subOrg = SubOrganization::where('id', Auth::user()->sub_org_id)->first();
        }else{
            $subOrg = SubOrganization::all();
        }

        return view('admin.department.create', compact('subOrg'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sub_org_id' => ['required'],
            'name' => ['required', 'max:255'],
        ]);
        $dept = CMHDepartment::where('sub_org_id',$request->sub_org_id)->where('name', $request->name)->first();
        if (isset($dept) && !empty($dept)){
            return redirect()->back()->with('error', 'Department Name already exist.');
        }

        $depart = new CMHDepartment();
        $depart->sub_org_id = $request->sub_org_id;
        $depart->name = $request->name;
        $depart->created_by = Auth::user()->id;
        $depart->created_at = now();
        $depart->save();

        return redirect('settings/cmh/department')->with('message', 'Successfully Department Name Added.');

    }

    /**
     * Display the specified resource.
     */
    public function show(CMHDepartment $cMHDepartment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $depart = CMHDepartment::find($id);
        if(Auth::user()->sub_org_id){
            $subOrg = SubOrganization::where('id', Auth::user()->sub_org_id)->first();
        }else{
            $subOrg = SubOrganization::all();
        }
        return view('admin.department.edit', compact('id','depart','subOrg'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'sub_org_id' => ['required'],
            'name' => ['required', 'max:255'],
        ]);
        $dept = CMHDepartment::where('sub_org_id',$request->sub_org_id)->where('id','!=',$request->id)->where('name', $request->name)->first();
        if (isset($dept) && !empty($dept)){
            return redirect()->back()->with('error', 'Department Name already exist.');
        }

        $depart = CMHDepartment::find($request->id);
        $depart->sub_org_id = $request->sub_org_id;
        $depart->name = $request->name;
        $depart->updated_by = Auth::user()->id;
        $depart->updated_at = now();
        $depart->save();

        return redirect('settings/cmh/department')->with('message', 'Successfully Department Name Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        try {
            $cmh = CMHDepartment::find($id);

            $new_data = null;
            $old_data = $cmh;
            $description = 'CMH Department '.$cmh->id.' has been deleted by '.auth()->user()->name;
            $operation = OperationTypes::Delete;

            $cmh->deleted_by = auth()->user()->id;
            $cmh->save();
            $cmh->delete();

            AuditService::AuditLogEntry(AuditModel::CMHDepartment,$operation,$description,$old_data,$new_data,$cmh->id);

            return redirect()->back()->with('message', 'Successfully Deleted.');
        }catch (Exception $exception){
            return redirect()->back()->with('error', 'Fail to Deleted.');
        }
    }
}
