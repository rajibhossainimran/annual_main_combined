<?php

namespace App\Http\Controllers;

use App\Models\DemandUnit;
use App\Services\DemandUnitService;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery\Exception;

class DemandUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():view
    {
        $account_units = DemandUnit::all();
        return view('admin.deamnd_unit.index',compact('account_units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        return view('admin.deamnd_unit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        try {
            DemandUnitService::createOrUpdatetUnit($request->all());
            return redirect()->route('all.demand.unit')->with('message','Successfully Store.');
        }catch (Exception $exception){
            return redirect()->route('all.demand.unit')->with('error','Fail to Store.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DemandUnit $demandUnit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $account_unit = DemandUnitService::getAccountUnitById($id);
        return view('admin.deamnd_unit.edit',compact('account_unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        try {
            DemandUnitService::createOrUpdatetUnit($request->all());
            return redirect()->route('all.demand.unit')->with('message','Successfully Updated.');
        }catch (Exception $exception){
            return redirect()->route('all.demand.unit')->with('error','Fail to Updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $account_unit = DemandUnit::find($id);
        $new_data = null;
        $old_data = $account_unit;
        $description = 'Demand Unit '.$account_unit->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;
        // $account_unit->deleted_by = auth()->user()->id;
        $account_unit->save();
        $account_unit->delete();

        AuditService::AuditLogEntry(AuditModel::DemandUnit,$operation,$description,$old_data,$new_data,$account_unit->id);

        return redirect()->route('all.demand.unit')->with('message','Successfully delete.');
    }
}
