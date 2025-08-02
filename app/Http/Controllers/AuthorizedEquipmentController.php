<?php

namespace App\Http\Controllers;

use App\Models\AuthorizedEquipment;
use App\Models\CMHDepartment;
use App\Models\PVMS;
use App\Models\SubOrganization;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class AuthorizedEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->sub_org_id){
            $departs = AuthorizedEquipment::where('authorized_equipment.sub_org_id',Auth::user()->sub_org_id)
                ->orderBy('authorized_equipment.id','desc')
                ->leftJoin('sub_organizations','sub_organizations.id','=','authorized_equipment.sub_org_id')
                ->leftJoin('c_m_h_departments','c_m_h_departments.id','=','authorized_equipment.dept_id')
                ->leftJoin('p_v_m_s','p_v_m_s.id','=','authorized_equipment.pvms_id')
                ->select('authorized_equipment.*','sub_organizations.name as sub_name','c_m_h_departments.name as dept_name','p_v_m_s.pvms_id','p_v_m_s.nomenclature')
                ->get();
        }else{
            $departs = AuthorizedEquipment::orderBy('authorized_equipment.id','desc')
                ->leftJoin('sub_organizations','sub_organizations.id','=','authorized_equipment.sub_org_id')
                ->leftJoin('c_m_h_departments','c_m_h_departments.id','=','authorized_equipment.dept_id')
                ->leftJoin('p_v_m_s','p_v_m_s.id','=','authorized_equipment.pvms_id')
                ->select('authorized_equipment.*','sub_organizations.name as sub_name','c_m_h_departments.name as dept_name','p_v_m_s.pvms_id','p_v_m_s.nomenclature')
                ->get();
        }
        return view('admin.authorized.index', compact('departs'));
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
        $pvms = PVMS::limit(10)->get();
        return view('admin.authorized.create', compact('subOrg','pvms','dept'));
    }

    public function GetDept(Request $request)
    {
        $dept = CMHDepartment::where('sub_org_id', $request->id)->get();
        return response()->json($dept);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sub_org_id' => ['required'],
            'available_number' => ['required'],
            'authorized_number' => ['required'],
            'svc' => ['required'],
            'unsvc' => ['required'],
            'doi' => ['required'],
            'wp' => ['required'],
            'bmo' => ['required'],
            'supplier' => ['required'],
            'pvms_id' => ['required'],
            'dept_id' => ['required'],
        ]);

        $AuthorizedEquipment = new AuthorizedEquipment();
        $AuthorizedEquipment->sub_org_id = $request->sub_org_id;
        $AuthorizedEquipment->bmo = $request->bmo;
        $AuthorizedEquipment->supplier = $request->supplier;
        $AuthorizedEquipment->available_number = $request->available_number;
        $AuthorizedEquipment->authorized_number = $request->authorized_number;
        $AuthorizedEquipment->svc = $request->svc;
        $AuthorizedEquipment->unsvc = $request->unsvc;
        $AuthorizedEquipment->doi = $request->doi;
        $AuthorizedEquipment->wp = $request->wp;
        $AuthorizedEquipment->pvms_id = $request->pvms_id;
        $AuthorizedEquipment->dept_id = $request->dept_id;
        $AuthorizedEquipment->created_by = Auth::user()->id;
        $AuthorizedEquipment->created_at = now();
        $AuthorizedEquipment->save();

        return redirect('settings/authorized/equipment')->with('message', 'Successfully Added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AuthorizedEquipment $authorizedEquipment)
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
        $pvms = PVMS::all();
        $authorized = AuthorizedEquipment::find($id);
        return view('admin.authorized.edit', compact('subOrg','pvms','dept','authorized'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'sub_org_id' => ['required'],
            'available_number' => ['required'],
            'authorized_number' => ['required'],
            'pvms_id' => ['required'],
            'dept_id' => ['required'],
        ]);

        $AuthorizedEquipment = AuthorizedEquipment::find($request->id);
        $AuthorizedEquipment->sub_org_id = $request->sub_org_id;
        $AuthorizedEquipment->available_number = $request->available_number;
        $AuthorizedEquipment->authorized_number = $request->authorized_number;
        $AuthorizedEquipment->pvms_id = $request->pvms_id;
        $AuthorizedEquipment->dept_id = $request->dept_id;
        $AuthorizedEquipment->updated_by = Auth::user()->id;
        $AuthorizedEquipment->updated_at = now();
        $AuthorizedEquipment->save();

        return redirect('settings/authorized/equipment')->with('message', 'Successfully Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        try {
            $author = AuthorizedEquipment::find($id);

            $new_data = null;
            $old_data = $author;
            $description = 'Authorize Equipment '.$author->id.' has been deleted by '.auth()->user()->name;
            $operation = OperationTypes::Delete;

            $author->deleted_by = auth()->user()->id;
            $author->save();
            $author->delete();

            AuditService::AuditLogEntry(AuditModel::AuthorizeEquipment,$operation,$description,$old_data,$new_data,$author->id);

            return redirect()->back()->with('message', 'Successfully Deleted.');
        }catch (Exception $exception){
            return redirect()->back()->with('error', 'Fail to Deleted.');
        }
    }
}
