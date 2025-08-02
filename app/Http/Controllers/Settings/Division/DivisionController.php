<?php

namespace App\Http\Controllers\Settings\Division;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Services\AuditService;
use App\Services\DivisionService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():view
    {
        //
        $divisions = Division::all();
        return view('admin.division.index',compact('divisions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        return view('admin.division.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'max:255'],
        ]);
        $check = Division::where('code', $request->code)->first();
        if(isset($check) && !empty($check)){
            return redirect()->back()->with('error','Code is already exist.');
        }
        DivisionService::createOrUpdateDivision($request->all());
        return redirect()->route('all.division');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $division = DivisionService::getDivisionById($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $division = DivisionService::getDivisionById($id);
        return view('admin.division.edit',compact('division'));
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
        $check = Division::where('id','!=',$request->id)->where('code', $request->code)->first();
        if(isset($check) && !empty($check)){
            return redirect()->back()->with('error','Code is already exist.');
        }
        DivisionService::createOrUpdateDivision($request->all());
        return redirect()->route('all.division');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $division = Division::find($id);

        $new_data = null;
        $old_data = $division;
        $description = 'Division '.$division->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $division->deleted_by = auth()->user()->id;
        $division->save();
        $division->delete();
        AuditService::AuditLogEntry(AuditModel::Division,$operation,$description,$old_data,$new_data,$division->id);
        return redirect()->route('all.division');
    }
}
