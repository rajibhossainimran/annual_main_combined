<?php

namespace App\Http\Controllers;

use App\Models\RequiredDocument;
use App\Models\Service;
use App\Services\AuditService;
use App\Services\RequiredDocumentService;
use App\Services\ServiceSettingService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Mockery\Exception;

class RequiredDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requirements = RequiredDocument::all();
        return view('admin.tender_documents.index', compact('requirements'));
    }

    public function get_required_documents(Request $request) {
        if($request->keyword) {
            return RequiredDocument::where('name','LIKE', '%'.$request->keyword.'%')->limit(5)->get();
        }
        return RequiredDocument::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tender_documents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file_type' => ['required', 'string', 'max:255']
        ]);
        try {
            RequiredDocumentService::StoreUpdate($request->all());
            return redirect()->route('all.tender.documents')->with('message','Successfully Store.');
        }catch (Exception $exception){
            return redirect()->route('all.tender.documents')->with('error','Fail to Store.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $document = RequiredDocument::find($id);
        return view('admin.tender_documents.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file_type' => ['required', 'string', 'max:255']
        ]);
        try {
            RequiredDocumentService::StoreUpdate($request->all());
            return redirect()->route('all.tender.documents')->with('message','Successfully Updated.');
        }catch (Exception $exception){
            return redirect()->route('all.tender.documents')->with('error','Fail to Updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $req = RequiredDocument::find($id);

        $new_data = null;
        $old_data = $req;
        $description = 'Service '.$req->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $req->deleted_by = auth()->user()->id;
        $req->save();
        $req->delete();
        AuditService::AuditLogEntry(AuditModel::Service,$operation,$description,$old_data,$new_data,$req->id);
        return redirect()->back()->with('message','Successfully Deleted.');
    }
}
