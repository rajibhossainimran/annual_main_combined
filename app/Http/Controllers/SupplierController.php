<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\String_;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Services\AuditService;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::get();
        return view('admin.supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'address' => ['required'],
            'contact_no' => ['required'],
        ]);

        $data = new Supplier();
        $data->name = $request->name;
        $data->address = $request->address;
        $data->contact_no = $request->contact_no;
        $data->code = 1;
        $data->save();
        return redirect()->route('all.supplier')->with('message','Successfully Inserted.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $supplier = Supplier::find($id);
        return view('admin.supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
            'address' => ['required'],
            'contact_no' => ['required'],
        ]);

        $data = Supplier::find($id);
        $data->name = $request->name;
        $data->address = $request->address;
        $data->contact_no = $request->contact_no;
        $data->code = 1;
        $data->save();
        return redirect()->route('all.supplier')->with('message','Successfully Updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);

        $new_data = null;
        $old_data = $supplier;
        $description = 'Supplier '.$supplier->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $supplier->deleted_by = auth()->user()->id;
        $supplier->save();
        $supplier->delete();

        AuditService::AuditLogEntry(AuditModel::Supplier,$operation,$description,$old_data,$new_data,$supplier->id);

        return redirect()->route('all.supplier')->with('message','Successfully deleted.');
    }
}
