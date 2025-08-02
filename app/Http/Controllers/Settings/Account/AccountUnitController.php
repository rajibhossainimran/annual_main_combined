<?php

namespace App\Http\Controllers\Settings\Account;

use App\Http\Controllers\Controller;
use App\Models\AccountUnit;
use App\Services\AccountUnitService;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery\Exception;


class AccountUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():view
    {
        //
        $account_units = AccountUnit::all();
        return view('admin.account_unit.index',compact('account_units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        return view('admin.account_unit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request):RedirectResponse
    {
        //
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        try {
            AccountUnitService::createOrUpdateAccountUnit($request->all());
            return redirect()->route('all.account.units')->with('message','Successfully Store.');
        }catch (Exception $exception){
            return redirect()->route('all.account.units')->with('error','Fail to Store.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $account_unit = AccountUnitService::getAccountUnitById($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id):view
    {
        //
        $account_unit = AccountUnitService::getAccountUnitById($id);
        return view('admin.account_unit.edit',compact('account_unit'));
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
            AccountUnitService::createOrUpdateAccountUnit($request->all());
            return redirect()->route('all.account.units')->with('message','Successfully Updated.');
        }catch (Exception $exception){
            return redirect()->route('all.account.units')->with('error','Fail to Updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

        $account_unit = AccountUnit::find($id);
        $new_data = null;
        $old_data = $account_unit;
        $description = 'Account Unit '.$account_unit->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;
        $account_unit->deleted_by = auth()->user()->id;
        $account_unit->save();
        $account_unit->delete();

        AuditService::AuditLogEntry(AuditModel::AccountUnit,$operation,$description,$old_data,$new_data,$account_unit->id);

        return redirect()->route('all.account.units')->with('message','Successfully delete.');
    }
}
