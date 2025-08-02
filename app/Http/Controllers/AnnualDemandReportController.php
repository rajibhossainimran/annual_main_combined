<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubOrganization;
use App\Models\FinancialYear;
use App\Models\ItemType;
use App\Models\PVMS;

class AnnualDemandReportController extends Controller
{
    public function index(){
        $data = [];
        $org = SubOrganization::where('type', '!=','DGMS')
        ->where('type', '!=','AFMSD')
        ->where('type', '!=','AFIP')
        ->get();
        $years = FinancialYear::all();
        $items = ItemType::all();
        return view('admin.report.annual_demand', compact('data','org','years','items'));
    }

    public function annualDemand(Request $request){
        
        $data = PVMS::where('p_v_m_s.pvms_id',$request->pvms_no)
        ->leftJoin('annual_demand_pvms','annual_demand_pvms.pvms_id','=','p_v_m_s.id')
        ->leftJoin('annual_demand_depatments','annual_demand_depatments.id','=','annual_demand_pvms.annual_demand_depatment_id')
        ->leftJoin('annual_demands','annual_demands.id','=','annual_demand_depatments.annual_demand_id')
        ->leftJoin('annual_demand_units','annual_demand_units.annual_demand_id','=','annual_demands.id')
        ->leftJoin('annual_demand_pvms_unit_demands','annual_demand_pvms_unit_demands.annual_demand_pvms_id','=','annual_demand_pvms.id')
        ->where('annual_demands.financial_year_id',$request->year_id)
        ->leftJoin('sub_organizations','sub_organizations.id','=','annual_demand_units.sub_org_id');
        if($request->sub_org != 'All'){
            $data = $data->where('annual_demand_units.sub_org_id',$request->sub_org);
        }
        $data = $data->select('p_v_m_s.nomenclature','p_v_m_s.pvms_id','sub_organizations.name','annual_demand_pvms_unit_demands.*')
        ->get();
        
        $org = SubOrganization::where('type', '!=','DGMS')
        ->where('type', '!=','AFMSD')
        ->where('type', '!=','AFIP')
        ->get();
        $years = FinancialYear::all();
        $items = ItemType::all();
        return view('admin.report.annual_demand', compact('data','org','years','items'));
    }
}
