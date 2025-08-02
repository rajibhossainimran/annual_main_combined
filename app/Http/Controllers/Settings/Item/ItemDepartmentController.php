<?php

namespace App\Http\Controllers\Settings\Item;

use App\Http\Controllers\Controller;
use App\Models\ItemDepartment;
use App\Services\AuditService;
use App\Services\ItemDepartmentService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getDepartmentListApi(Request $request) {
        $data = ItemDepartment::orderBy('name');

        if($request->search) {
            $data = $data->where('name', 'LIKE', '%'.$request->search.'%');
        }

        return $data->limit(50)->latest()->get();
    }

    public function index():view
    {
        //
        $item_departments = ItemDepartment::all();
        return view('admin.item_department.index',compact('item_departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        return view('admin.item_department.create');
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
        ItemDepartmentService::createOrUpdateItemDepartment($request->all());
        return redirect()->route('all.item.department');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $item_department = ItemDepartmentService::getItemDepartmentById($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $item_department = ItemDepartmentService::getItemDepartmentById($id);
        return view('admin.item_department.edit',compact('item_department'));
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
        ItemDepartmentService::createOrUpdateItemDepartment($request->all());
        return redirect()->route('all.item.department');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $item_department = ItemDepartment::find($id);

        $new_data = null;
        $old_data = $item_department;
        $description = 'Item Department '.$item_department->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $item_department->deleted_by = auth()->user()->id;
        $item_department->save();
        $item_department->delete();
        AuditService::AuditLogEntry(AuditModel::ItemDepartment,$operation,$description,$old_data,$new_data,$item_department->id);
        return redirect()->route('all.item.department');
    }
}
