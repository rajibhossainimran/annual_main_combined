<?php

namespace App\Http\Controllers;

use App\Models\CMHDepartmentCategory;
use App\Models\AuthorizedEquipment;
use App\Models\CMHDepartment;
use App\Models\PVMS;
use App\Models\SubOrganization;
use App\Services\AuditService;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use App\Utill\Const\AuditModel;

class CMHDepartmentCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->sub_org_id){
            $departs = CMHDepartmentCategory::where('c_m_h_department_categories.sub_org_id',Auth::user()->sub_org_id)
                ->orderBy('c_m_h_department_categories.id','desc')
                ->leftJoin('sub_organizations','sub_organizations.id','=','c_m_h_department_categories.sub_org_id')
                ->leftJoin('c_m_h_departments','c_m_h_departments.id','=','c_m_h_department_categories.dept_id')
                ->select('c_m_h_department_categories.*','sub_organizations.name as sub_name','c_m_h_departments.name as dept_name')
                ->get();
        }else{
            $departs = CMHDepartmentCategory::orderBy('c_m_h_department_categories.id','desc')
                ->leftJoin('sub_organizations','sub_organizations.id','=','c_m_h_department_categories.sub_org_id')
                ->leftJoin('c_m_h_departments','c_m_h_departments.id','=','c_m_h_department_categories.dept_id')
                ->select('c_m_h_department_categories.*','sub_organizations.name as sub_name','c_m_h_departments.name as dept_name')
                ->get();
        }
        return view('admin.category.index', compact('departs'));
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

        return view('admin.category.create', compact('subOrg','dept'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sub_org_id' => ['required'],
            'name' => ['required'],
            'dept_id' => ['required'],
            'code' => ['required'],
        ]);

        $check = CMHDepartmentCategory::where('code', $request->code)->where('dept_id', $request->dept_id)->first();
        if(isset($check) && !empty($check)){
            return redirect()->back()->with('error','Code is already exist.');
        }
        $CMHDepartmentCategory = new CMHDepartmentCategory();
        $CMHDepartmentCategory->sub_org_id = $request->sub_org_id;
        $CMHDepartmentCategory->name = $request->name;
        $CMHDepartmentCategory->code = $request->code;
        $CMHDepartmentCategory->dept_id = $request->dept_id;
        $CMHDepartmentCategory->created_by = Auth::user()->id;
        $CMHDepartmentCategory->created_at = now();
        $CMHDepartmentCategory->save();

        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        $operation = OperationTypes::Create;
        $description = 'Department Category '.$CMHDepartmentCategory->name.', Code- '.$CMHDepartmentCategory->code.' created by '.auth()->user()->name;
        $new_data = $CMHDepartmentCategory;
        AuditService::AuditLogEntry(AuditModel::DepartmentCategory,$operation,$description,$old_data,$new_data,$CMHDepartmentCategory->id);

        return redirect('settings/cmh/department/category')->with('message', 'Successfully Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CMHDepartmentCategory $cMHDepartmentCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if(Auth::user()->sub_org_id){
            $subOrg = SubOrganization::where('id', Auth::user()->sub_org_id)->first();
            $dept = CMHDepartment::where('sub_org_id', Auth::user()->sub_org_id)->get();
        }else{
            $subOrg = SubOrganization::all();
            $dept = CMHDepartment::all();
        }

        $category = CMHDepartmentCategory::find($id);

        return view('admin.category.edit', compact('subOrg','dept','category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'sub_org_id' => ['required'],
            'name' => ['required'],
            'dept_id' => ['required'],
            'code' => ['required'],
        ]);

        $check = CMHDepartmentCategory::where('code', $request->code)->where('dept_id', $request->dept_id)->where('id','!=',$request->id)->first();
        if(isset($check) && !empty($check)){
            return redirect()->back()->with('error','Code is already exist.');
        }
        $CMHDepartmentCategory = CMHDepartmentCategory::find($request->id);
        $old_data = $CMHDepartmentCategory;
        $CMHDepartmentCategory->sub_org_id = $request->sub_org_id;
        $CMHDepartmentCategory->name = $request->name;
        $CMHDepartmentCategory->code = $request->code;
        $CMHDepartmentCategory->dept_id = $request->dept_id;
        $CMHDepartmentCategory->created_by = Auth::user()->id;
        $CMHDepartmentCategory->created_at = now();
        $CMHDepartmentCategory->save();

        $operation = OperationTypes::Update;
        $description = 'Department Category '.$CMHDepartmentCategory->name.', Code- '.$CMHDepartmentCategory->code.' updated by '.auth()->user()->name;

        $new_data = $CMHDepartmentCategory;
        AuditService::AuditLogEntry(AuditModel::DepartmentCategory,$operation,$description,$old_data,$new_data,$CMHDepartmentCategory->id);

        return redirect('settings/cmh/department/category')->with('message', 'Successfully Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        try {
            $CMHDepartmentCategory = CMHDepartmentCategory::find($id);

            $new_data = null;
            $old_data = $CMHDepartmentCategory;
            $description = 'Department Category '.$CMHDepartmentCategory->id.' has been deleted by '.auth()->user()->name;
            $operation = OperationTypes::Delete;

            $CMHDepartmentCategory->deleted_by = auth()->user()->id;
            $CMHDepartmentCategory->save();
            $CMHDepartmentCategory->delete();

            AuditService::AuditLogEntry(AuditModel::DepartmentCategory,$operation,$description,$old_data,$new_data,$CMHDepartmentCategory->id);

            return redirect()->back()->with('message', 'Successfully Deleted.');
        }catch (Exception $exception){
            return redirect()->back()->with('error', 'Fail to Deleted.');
        }
    }
}
