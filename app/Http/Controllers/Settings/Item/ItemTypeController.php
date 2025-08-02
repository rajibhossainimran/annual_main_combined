<?php

namespace App\Http\Controllers\Settings\Item;

use App\Http\Controllers\Controller;
use App\Models\ItemType;
use App\Services\AuditService;
use App\Services\ItemTypeService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery\Exception;

class ItemTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getItemTypeListApi(Request $request) {
        return ItemType::all();
    }

    public function index():view
    {
        //
        $item_types = ItemType::all();
        return view('admin.item_type.index',compact('item_types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        return view('admin.item_type.create');
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

        try {
            ItemTypeService::createOrUpdateItemType($request->all());
            return redirect()->route('all.item.types')->with('message','Successfully Store.');
        }catch (Exception $exception){
            return redirect()->route('all.item.types')->with('error','Fail to Store.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $item_type = ItemTypeService::getItemTypeById($id);
    }
    public function get_all_types()
    {
        //
        return ItemType::all();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $item_type = ItemTypeService::getItemTypeById($id);
        return view('admin.item_type.edit',compact('item_type'));
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
        try {
            ItemTypeService::createOrUpdateItemType($request->all());
            return redirect()->route('all.item.types')->with('message','Successfully Updated.');
        }catch (Exception $exception){
            return redirect()->route('all.item.types')->with('error','Fail to Updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $item_type = ItemType::find($id);

        $new_data = null;
        $old_data = $item_type;
        $description = 'Item Type '.$item_type->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $item_type->deleted_by = auth()->user()->id;
        $item_type->save();
        $item_type->delete();

        AuditService::AuditLogEntry(AuditModel::ItemType,$operation,$description,$old_data,$new_data,$item_type->id);

        return redirect()->route('all.item.types')->with('message','Successfully deleted.');
    }
}
