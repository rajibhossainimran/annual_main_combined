<?php

namespace App\Http\Controllers\Settings\Specification;

use App\Http\Controllers\Controller;
use App\Models\Specification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\SpecificationService;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\AuditModel;
use App\Services\AuditService;
use Mockery\Exception;

class SpecificationController extends Controller
{
    public function index()
    {
        $specifications = Specification::orderBy('specifications.id','desc')->leftJoin('users','users.id','=','specifications.created_by')
            ->select('specifications.*','users.name as uname')
            ->get();
        return view('admin.specification.index', compact('specifications'));
    }

    public function create()
    {
        return view('admin.specification.create');
    }

    public function store(Request $request):RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        try {
            SpecificationService::StoreUpdate($request->all());
            return redirect()->route('all.specification')->with('message','Successfully Store.');
        }catch (Exception $exception){
            return redirect()->route('all.specification')->with('message','Fail to Store.');
        }
    }

    public function edit($id)
    {
        $specification = Specification::where('id',$id)->first();
        return view('admin.specification.edit', compact('specification'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        try {
            SpecificationService::StoreUpdate($request->all());
            return redirect()->route('all.specification')->with('message','Successfully Updated.');
        }catch (Exception $exception){
            return redirect()->route('all.specification')->with('error','Fail to Updated.');
        }

    }

    public function delete($id)
    {
        $specification = Specification::find($id);

        $new_data = null;
        $old_data = $specification;
        $description = 'Specification '.$specification->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $specification->deleted_by = auth()->user()->id;
        $specification->save();
        $specification->delete();
        AuditService::AuditLogEntry(AuditModel::Specification,$operation,$description,$old_data,$new_data,$specification->id);
        return redirect()->back()->with('message','Successfully Deleted.');
    }

}
