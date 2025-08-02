<?php

namespace App\Http\Controllers\Settings\Item;

use App\Http\Controllers\Controller;
use App\Models\ItemGroup;
use App\Services\AuditService;
use App\Services\ItemGroupService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery\Exception;

class ItemGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():view
    {
        //
        $item_groups = ItemGroup::all();
        return view('admin.item_group.index',compact('item_groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        return view('admin.item_group.create');
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
            ItemGroupService::createOrUpdateItemGroup($request->all());
            return redirect()->route('all.group.management')->with('message', 'Successfully Store.');
        }catch (Exception $exception){
            return redirect()->route('all.group.management')->with('error', 'Fail to Store.');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $item_group = ItemGroupService::getItemGroupById($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $item_group = ItemGroupService::getItemGroupById($id);
        return view('admin.item_group.edit',compact('item_group'));
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
            ItemGroupService::createOrUpdateItemGroup($request->all());
            return redirect()->route('all.group.management')->with('message', 'Successfully Updated.');
        }catch (Exception $exception){
            return redirect()->route('all.group.management')->with('error', 'Fail to Update.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $item_group = ItemGroup::find($id);

        $new_data = null;
        $old_data = $item_group;
        $description = 'Item Group '.$item_group->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $item_group->deleted_by = auth()->user()->id;
        $item_group->save();
        $item_group->delete();

        AuditService::AuditLogEntry(AuditModel::ItemGroup,$operation,$description,$old_data,$new_data,$item_group->id);

        return redirect()->route('all.group.management')->with('message', 'Successfully Deleted.');;
    }
}
