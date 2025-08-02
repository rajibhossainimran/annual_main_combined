<?php

namespace App\Http\Controllers\User\Organization;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Division;
use App\Models\Service;
use App\Models\SubOrganization;
use App\Services\AuditService;
use App\Services\BranchService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BranchController extends Controller
{
    public function getBranches($sub_org_id) {
        return Branch::where('sub_org_id',$sub_org_id)->get();
    }
    public function getBranchesList(Request $request) {
        $data = Branch::where('sub_org_id',auth()->user()->sub_org_id);

        if($request->search) {
            $data = $data->where('name', 'LIKE', '%'.$request->search.'%');
        }

        return $data->limit(50)->latest()->get();
    }

    public function index():view
    {
        //
        $branchs = Branch::all();
        return view('admin.branch.index',compact('branchs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        $sub_organizations = SubOrganization::all();
        return view('admin.branch.create',compact('sub_organizations'));
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
        BranchService::createOrUpdateBranch($request->all());
        return redirect()->route('all.branch');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $branch = BranchService::getBranchById($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $sub_organizations = SubOrganization::all();
        $branch = BranchService::getBranchById($id);
        return view('admin.branch.edit',compact('branch','sub_organizations'));
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
        BranchService::createOrUpdateBranch($request->all());
        return redirect()->route('all.branch');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $branch = Branch::find($id);

        $new_data = null;
        $old_data = $branch;
        $description = 'Store '.$branch->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $branch->deleted_by = auth()->user()->id;
        $branch->save();
        $branch->delete();

        AuditService::AuditLogEntry(AuditModel::Store,$operation,$description,$old_data,$new_data,$branch->id);

        return redirect()->route('all.branch');
    }
}
