<?php

namespace App\Http\Controllers\Settings\Financial;

use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use App\Services\FinancialYears;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Mockery\Exception;

class FinancialYearController extends Controller
{
    public function index():view
    {
        $financial_years = FinancialYear::orderBy('financial_years.id','desc')->leftJoin('users','users.id','=','financial_years.created_by')
            ->select('financial_years.*','users.name as uname')
            ->get();
        return view('admin.financial_years.index', compact('financial_years'));
    }

    public function indexApi()
    {
        $financial_years = FinancialYear::orderBy('financial_years.id','desc')->get();
        return $financial_years;
    }

    public function create():view
    {
        return view('admin.financial_years.create');
    }

    public function store(Request $request):RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        try {
            FinancialYears::StoreUpdate($request->all());
            return redirect()->route('all.financial.year')->with('message','Successfully Store.');
        }catch (Exception $exception){
            return redirect()->route('all.financial.year')->with('message','Fail to Store.');
        }
    }

    public function edit($id)
    {
        $year = FinancialYear::where('id',$id)->first();
        return view('admin.financial_years.edit', compact('year'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);
        try {
            FinancialYears::StoreUpdate($request->all());
            return redirect()->route('all.financial.year')->with('message','Successfully Updated.');
        }catch (Exception $exception){
            return redirect()->route('all.financial.year')->with('message','Fail to Updated.');
        }
    }

    public function delete($id)
    {
        $financialYear = FinancialYear::find($id);

        $new_data = null;
        $old_data = $financialYear;
        $description = 'Financial Year '.$financialYear->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $financialYear->deleted_by = auth()->user()->id;
        $financialYear->save();
        $financialYear->delete();
        AuditService::AuditLogEntry(AuditModel::FinancialYear,$operation,$description,$old_data,$new_data,$financialYear->id);
        return redirect()->back()->with('message','Successfully Deleted.');
    }
}
