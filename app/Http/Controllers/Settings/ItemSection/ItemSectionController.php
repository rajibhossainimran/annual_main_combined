<?php

namespace App\Http\Controllers\Settings\ItemSection;

use App\Http\Controllers\Controller;
use App\Models\ItemSections;
use App\Models\Service;
use App\Services\AuditService;
use App\Services\ItemSectionService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mockery\Exception;

class ItemSectionController extends Controller
{
    public function index():view
    {
        $item_sections = ItemSections::orderBy('item_sections.id','desc')->leftJoin('users','users.id','=','item_sections.created_by')
            ->select('item_sections.*','users.name as uname')
            ->get();
        return view('admin.ItemSection.index', compact('item_sections'));
    }

    public function create():view
    {
        return view('admin.ItemSection.create');
    }

    public function store(Request $request):RedirectResponse
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255']
            ]);
            ItemSectionService::StoreUpdate($request->all());
            return redirect()->route('all.item.section')->with('message','Successfully Store');
        }catch (Exception $exception){
            return redirect()->route('all.item.section')->with('error','Fail to Store');
        }
    }

    public function edit($id)
    {
        $item_section = ItemSections::find($id);
        return view('admin.ItemSection.edit', compact('item_section'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255']
            ]);
            ItemSectionService::StoreUpdate($request->all());
            return redirect()->route('all.item.section')->with('message','Successfully Updated.');
        }catch (\Exception $exception){
            return redirect()->back()->with('error','Fail to Updated.');
        }
    }

    public function delete($id)
    {
        try {
            $itemSections = ItemSections::find($id);

            $new_data = null;
            $old_data = $itemSections;
            $description = 'Item Section '.$itemSections->name.' has been deleted by '.auth()->user()->name;
            $operation = OperationTypes::Delete;

            $itemSections->deleted_by = auth()->user()->id;
            $itemSections->save();
            $itemSections->delete();
            AuditService::AuditLogEntry(AuditModel::ItemSection,$operation,$description,$old_data,$new_data,$itemSections->id);
            return redirect()->back()->with('message','Successfully Deleted.');
        }catch (Exception $exception){

        }
    }
}
