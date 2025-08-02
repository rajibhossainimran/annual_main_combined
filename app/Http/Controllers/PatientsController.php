<?php

namespace App\Http\Controllers;

use App\Models\DemandUnit;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\PatientsService;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Mockery\Exception;

class PatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::all();

        return view('admin.patient.index',compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = DemandUnit::all();
        return view('admin.patient.create',compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required'],
            'number' => ['required'],
            'relation' => ['required'],
            'unit_id' => ['required'],
        ]);
        try {
            PatientsService::createOrUpdatetUnit($request->all());
            return redirect()->route('all.patient')->with('message','Successfully Store.');
        }catch (Exception $exception){
            return redirect()->route('all.patient')->with('error','Fail to Store.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $patients = Patient::find($id);
        $units = DemandUnit::all();
        $type = explode('-',$patients->identification_no);

        return view('admin.patient.edit',compact('patients','units','type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required'],
            'number' => ['required'],
            'relation' => ['required'],
            'unit_id' => ['required'],
        ]);
        try {
            PatientsService::createOrUpdatetUnit($request->all());
            return redirect()->route('all.patient')->with('message','Successfully Updated.');
        }catch (Exception $exception){
            return redirect()->route('all.patient')->with('error','Fail to Update.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::find($id);
        $new_data = null;
        $old_data = $patient;
        $description = 'Patient '.$patient->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;
        // $account_unit->deleted_by = auth()->user()->id;
        $patient->save();
        $patient->delete();

        AuditService::AuditLogEntry(AuditModel::Patient,$operation,$description,$old_data,$new_data,$patient->id);

        return redirect()->route('all.patient')->with('message','Successfully delete.');
    }
}
