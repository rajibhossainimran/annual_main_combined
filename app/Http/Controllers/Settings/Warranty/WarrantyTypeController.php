<?php

namespace App\Http\Controllers\Settings\Warranty;

use App\Http\Controllers\Controller;
use App\Models\WarrantyType;
use App\Services\AuditService;
use App\Services\WarrantyTypeService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WarrantyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():view
    {
        //
        $warranty_types = WarrantyType::paginate(10);
        return view('admin.warranty_type.index',compact('warranty_types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        return view('admin.warranty_type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        WarrantyTypeService::createOrUpdateWarrantyType($request->all());
        return redirect()->route('all.warranty.type');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $warranty_type = WarrantyTypeService::getWarrantyTypeById($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $warranty_type = WarrantyTypeService::getWarrantyTypeById($id);
        return view('admin.warranty_type.edit',compact('warranty_type'));
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
        WarrantyTypeService::createOrUpdateWarrantyType($request->all());
        return redirect()->route('all.warranty.type');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $warranty_type = WarrantyType::find($id);

        $new_data = null;
        $old_data = $warranty_type;
        $description = 'Warranty Type '.$warranty_type->name.' has been deleted by '.auth()->user()->name;;
        $operation = OperationTypes::Delete;
        
        $warranty_type->deleted_by = auth()->user()->id;
        $warranty_type->save();
        $warranty_type->delete();
        
        AuditService::AuditLogEntry(AuditModel::WarrantyType,$operation,$description,$old_data,$new_data,$warranty_type->id);

        return redirect()->route('all.warranty.type');
    }
}
