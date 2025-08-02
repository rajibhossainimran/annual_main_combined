<?php

namespace App\Http\Controllers;

use App\Models\PVMS;
use App\Models\Setting;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use App\Services\SettingService;
use Mockery\Exception;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::all();
        return view('admin.config.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.config.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => ['required', 'max:255'],
            'value' => ['required', 'max:255'],
        ]);

        SettingService::CreateOrUpdate($request->all());
        return redirect()->route('all.config')->with('message', 'Successfully Created');

    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $setting = Setting::find($id);
        return view('admin.config.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'key' => ['required', 'max:255'],
            'value' => ['required', 'max:255'],
        ]);

        SettingService::CreateOrUpdate($request->all());
        return redirect()->route('all.config')->with('message', 'Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        try {
            $setting = Setting::find($id);

            $new_data = null;
            $old_data = $setting;
            $description = 'Config Setting '.$setting->key.' has been deleted by '.auth()->user()->name;
            $operation = OperationTypes::Delete;

            $setting->deleted_by = auth()->user()->id;
            $setting->save();
            $setting->delete();

            AuditService::AuditLogEntry(AuditModel::PVMS,$operation,$description,$old_data,$new_data,$setting->id);

            return redirect()->back()->with('message', 'Successfully Deleted.');
        }catch (Exception $exception){
            return redirect()->back()->with('error', 'Fail to Deleted.');
        }
    }
}
