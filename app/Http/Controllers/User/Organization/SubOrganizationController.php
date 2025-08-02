<?php

namespace App\Http\Controllers\User\Organization;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Organization;
use App\Models\Service;
use App\Models\SubOrganization;
use App\Services\ApprovalLayerService;
use App\Services\AuditService;
use App\Services\SubOrganizationService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\SubOrganizationTypes;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SubOrganizationController extends Controller
{
    public function getSubOrganizations($org_id) {
        return SubOrganization::where('org_id',$org_id)->get();
    }

    public function getSubOrganizationsList(Request $request) {
        $data = SubOrganization::whereIn('type',['CMH','AFIP']);

        if($request->search) {
            $data = $data->where('name', 'LIKE', '%'.$request->search.'%');
        }

        return $data->limit(50)->latest()->get();
    }

    public function index():view
    {
        //
        $sub_organizations = SubOrganization::all();
        return view('admin.sub_organization.index',compact('sub_organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        $organizations = Organization::all();
        $divisions = Division::all();
        $services = Service::all();
        $types = SubOrganizationTypes::AllSubOrganizationTypes;
        $approval_layer = ApprovalLayerService::getDefaultLayer();
        return view('admin.sub_organization.create',compact('organizations','divisions','services','types','approval_layer'));
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
        $suborganization =  SubOrganizationService::createOrUpdateSubOrganization($request->all());
        ApprovalLayerService::setOrganizationLayer($suborganization->id, $request->approval_layers_all, $request->approval_layers_repair);
        return redirect()->route('all.sub.organization');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $sub_organization = SubOrganizationService::getSubOrganizationById($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $organizations = Organization::all();
        $divisions = Division::all();
        $services = Service::all();
        $sub_organization = SubOrganizationService::getSubOrganizationById($id);
        $types = SubOrganizationTypes::AllSubOrganizationTypes;
        $approval_layer = ApprovalLayerService::setOrganizationDefaultLayer($id);
        $approval_layer = ApprovalLayerService::getOrganizationLayer($id);
        return view('admin.sub_organization.edit',compact('sub_organization','organizations','divisions','services','types','approval_layer'));
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
        SubOrganizationService::createOrUpdateSubOrganization($request->all());
        ApprovalLayerService::setOrganizationLayer($request->id, $request->approval_layers_all, $request->approval_layers_repair);
        return redirect()->route('all.sub.organization');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $sub_organization = SubOrganization::find($id);

        $new_data = null;
        $old_data = $sub_organization;
        $description = 'Organization '.$sub_organization->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $sub_organization->deleted_by = auth()->user()->id;
        $sub_organization->save();
        $sub_organization->delete();

        AuditService::AuditLogEntry(AuditModel::Organization,$operation,$description,$old_data,$new_data,$sub_organization->id);

        return redirect()->route('all.sub.organization');
    }
}
