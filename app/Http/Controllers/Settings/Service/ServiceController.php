<?php

namespace App\Http\Controllers\Settings\Service;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Specification;
use App\Services\AuditService;
use App\Services\ServiceSettingService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery\Exception;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('services.id','desc')->leftJoin('users','users.id','=','services.created_by')
            ->select('services.*','users.name as uname')
            ->get();
        return view('admin.service.index', compact('services'));
    }

    public function create()
    {
        return view('admin.service.create');
    }

    public function store(Request $request):RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        try {
            ServiceSettingService::StoreUpdate($request->all());
            return redirect()->route('all.service')->with('message','Successfully Store.');
        }catch (Exception $exception){
            return redirect()->route('all.service')->with('error','Fail to Store.');
        }
    }

    public function edit($id)
    {
        $service = Service::where('id',$id)->first();
        return view('admin.service.edit', compact('service'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        try {
            ServiceSettingService::StoreUpdate($request->all());
            return redirect()->route('all.service')->with('message','Successfully Updated.');
        }catch (Exception $exception){
            return redirect()->route('all.service')->with('message','Fail to Updated.');
        }
    }

    public function delete($id)
    {
        $service = Service::find($id);

        $new_data = null;
        $old_data = $service;
        $description = 'Service '.$service->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $service->deleted_by = auth()->user()->id;
        $service->save();
        $service->delete();
        AuditService::AuditLogEntry(AuditModel::Service,$operation,$description,$old_data,$new_data,$service->id);
        return redirect()->back()->with('message','Successfully Deleted.');
    }
}
