<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function audit_log(Request $request)
    {
        // $pvms = PVMS::with('unitName')->where('pvms_id', 'LIKE', '%'.$request->keyword.'%')->limit(5)->get();

        // return $pvms;
        $limit = 10;
        if($request->limit) {
            $limit = $request->limit;
        }

        $audit_log = AuditTrail::with('performedBy')->latest();

        if($request->search) {
            $audit_log = $audit_log->whereRaw('match (description) against (? in boolean mode)', [$request->search])->orWhere('description','Like', '%'.$request->search.'%');
        }

        if($request->model) {
            $audit_log = $audit_log->where('model',$request->model);
        }

        if($request->operation) {
            $audit_log = $audit_log->where('operation',$request->operation);
        }

        $audit_log = $audit_log->paginate($limit);

        return $audit_log;
    }

    public function index():view
    {
        //
        return view('admin.audit.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
