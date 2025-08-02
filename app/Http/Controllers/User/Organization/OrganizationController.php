<?php

namespace App\Http\Controllers\User\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\AuditService;
use App\Services\OrganizationService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrganizationController extends Controller
{
    public function index():view
    {
        //
        $organizations = Organization::all();
        return view('admin.organization.index',compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        return view('admin.organization.create');
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
        OrganizationService::createOrUpdateOrganization($request->all());
        return redirect()->route('all.organization');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $organization = OrganizationService::getOrganizationById($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $organization = OrganizationService::getOrganizationById($id);
        return view('admin.organization.edit',compact('organization'));
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
        OrganizationService::createOrUpdateOrganization($request->all());
        return redirect()->route('all.organization');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $organization = Organization::find($id);

        $new_data = null;
        $old_data = $organization;
        $description = 'Governing Body '.$organization->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $organization->deleted_by = auth()->user()->id;
        $organization->save();
        $organization->delete();

        AuditService::AuditLogEntry(AuditModel::GoverningBody,$operation,$description,$old_data,$new_data,$organization->id);

        return redirect()->route('all.organization');
    }
}
