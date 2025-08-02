<?php

namespace App\Http\Controllers;

use App\Models\AnnualDemand;
use App\Models\AnnualDemandPvmsUnitDemand;
use App\Models\AnnualDemandUnit;
use App\Models\PvmsStore;
use App\Models\QuerterlyDemand;
use App\Models\QuerterlyDemandApproval;
use App\Models\QuerterlyDemandPvms;
use App\Models\QuerterlyDemandReceive;
use App\Models\QuerterlyDemandReceivePvms;
use App\Models\UserApprovalRole;
use App\Services\PVMSService;
use App\Utill\Approval\QuerterlyDemandApprovalSetps;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuerterlyDemandController extends Controller
{
    public function index(Request $request)
    {
        $perpage = 10;
        if($request->perpage) {
            $perpage = $request->perpage;
        }

        $annual_demands = AnnualDemand::with('financialYear')->withCount('departmentList')->latest()->paginate($perpage);
        $user_approval_role = auth()->user()->userApprovalRole;

        $querterly_demands = QuerterlyDemand::orderBy('id', 'DESC')->paginate();

        return view('admin.querterly_demand.index',compact('annual_demands','user_approval_role', 'querterly_demands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.querterly_demand.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $querterly_demand = new  QuerterlyDemand();
        $querterly_demand->annual_demand_id = $request->annual_demand_id;
        $querterly_demand->demand_no = $request->demand_no;
        $querterly_demand->financial_year = $request->fy;
        $querterly_demand->demand_date = $request->demand_date;
        $querterly_demand->demand_type = $request->demand_type;
        $querterly_demand->sub_org_id = auth()->user()->sub_org_id;
        $querterly_demand->save();

        foreach ($request->pvms as $pvms) {
            if($pvms['request_qty']!=0){
                $querterly_qemand_pvms = new QuerterlyDemandPvms();
                $querterly_qemand_pvms->querterly_demand_id = $querterly_demand->id;
                $querterly_qemand_pvms->annual_demand_pvms_unit_demand_id = $pvms['annual_demand_pvms_unit_demand_id'];
                $querterly_qemand_pvms->pvms_id = $pvms['pvms_primary_id'];
                $querterly_qemand_pvms->req_qty = $pvms['request_qty'];
                $querterly_qemand_pvms->remarks = $pvms['remarks'];
    
                $querterly_qemand_pvms->save();
            }
        }

        return $querterly_demand;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('admin.querterly_demand.show');

    }

    public function approval(string $id)
    {
        return view('admin.querterly_demand.show');

    }

    public function approve(Request $request)
    {
        $querterly_demand = QuerterlyDemand::find($request->demand_id);

        $next_steps = QuerterlyDemandApprovalSetps::nextStepDynamic($request->demand_id);

        $demand_approval = new QuerterlyDemandApproval();
        $demand_approval->demand_id = $request->demand_id;
        $demand_approval->approved_by = auth()->user()->id;
        $demand_approval->step_number = $next_steps['step'];
        $demand_approval->role_name = $next_steps['designation'];
        $demand_approval->note = '';
        $demand_approval->action = 'APPROVE';
        $demand_approval->save();

        $user_approval_role = UserApprovalRole::where('role_key', $next_steps['designation'])->first();
        $querterly_demand->last_approval = $user_approval_role->role_name;

        if($user_approval_role->role_key==$next_steps['last_approval']){
            $querterly_demand->is_approved = true;
        }

        $querterly_demand->save();

        return $demand_approval;
    }

    public function showJson($id)
    {
        $querterly_demand = QuerterlyDemand::with('financialYear')->find($id);

        $annual_demand_unit = AnnualDemandUnit::where('annual_demand_id',$querterly_demand->annual_demand_id)
                                                ->where('sub_org_id',$querterly_demand->sub_org_id)->first();

        $annual_demand_unit_pvms = AnnualDemandPvmsUnitDemand::with(['annualDemandPvms.PVMS.unitName','annualDemandUnit.subOrganization', 'querterlyDemandPvms'])
            ->where('annual_demand_unit_id',$annual_demand_unit->id)
            ->get();

        $pvms_list = [];

        foreach ($annual_demand_unit_pvms as $annual_demand_unit_pvm) {
            array_push($pvms_list,$annual_demand_unit_pvm->annualDemandPvms->pvms_id);
        }

        return [
            'querterly_demand' => $querterly_demand,
            'annual_demand_unit_pvms' => $annual_demand_unit_pvms,
            'pvms_stock' => PVMSService::pvmsUnitWiseStock($pvms_list, $querterly_demand->sub_org_id),
            'receieved_qty' => 0
        ];

    }

    public function showJsonDelivery($id)
    {
        $querterly_demand = QuerterlyDemand::with('financialYear')->find($id);

        $querterly_demand_pvms = QuerterlyDemandPvms::with([
            'pvms.unitName',
            'querterlyDemandReceivePvms.batchPvms',
            'annualDemandPvmsUnitDemand',
            'batchPvms' => function ($query) {
                $query->addSelect(DB::raw('id, pvms_id, batch_no, expire_date, qty, (SELECT CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and pvms_store.sub_org_id='.auth()->user()->sub_org_id.') as available_quantity'))
                        ->where('expire_date', '>', Carbon::now())
                        ->whereHas('unitStock', function ($query) {
                          $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                                ->where('sub_org_id',auth()->user()->sub_org_id)
                                ->where('is_received',1)
                                ->groupBy('batch_pvms_id')
                                ->havingRaw('available_quantity>0');
                        })->orderBy('expire_date');
            }
            ])
                                        ->where('querterly_demand_id', $id)->get();

        $annual_demand_unit = AnnualDemandUnit::where('annual_demand_id',$querterly_demand->annual_demand_id)
                                                ->where('sub_org_id',$querterly_demand->sub_org_id)->first();

        $annual_demand_unit_pvms = AnnualDemandPvmsUnitDemand::with(['annualDemandPvms.PVMS.unitName','annualDemandUnit.subOrganization', 'querterlyDemandPvms.querterlyDemandReceivePvms'])
            ->where('annual_demand_unit_id',$annual_demand_unit->id)
            ->get();

        $pvms_list = [];

        foreach ($querterly_demand_pvms as $querterly_demand_pvm) {
            array_push($pvms_list,$querterly_demand_pvm->pvms_id);
        }

        return [
            'querterly_demand' => $querterly_demand,
            'annual_demand_unit_pvms' => $annual_demand_unit_pvms,
            'querterly_demand_pvms' => $querterly_demand_pvms,
            'pvms_stock' => PVMSService::pvmsUnitWiseStock($pvms_list, $querterly_demand->sub_org_id),
            'receieved_qty' => 0
        ];

    }

    public function edit(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        $querterly_demand = QuerterlyDemand::find($id);
        // $querterly_demand->annual_demand_id = $request->annual_demand_id;
        // $querterly_demand->demand_no = $request->demand_no;
        // $querterly_demand->financial_year = $request->fy;
        // $querterly_demand->demand_date = $request->demand_date;
        // $querterly_demand->demand_type = $request->demand_type;
        // $querterly_demand->sub_org_id = auth()->user()->sub_org_id;
        // $querterly_demand->save();

        foreach ($request->pvms as $pvms) {
            if($pvms['request_qty']!=0){
                if($pvms['querterly_demand_pvms_id']){
                    $querterly_qemand_pvms = QuerterlyDemandPvms::find($pvms['querterly_demand_pvms_id']);
                }else{
                    $querterly_qemand_pvms = new QuerterlyDemandPvms();
                }
                
                $querterly_qemand_pvms->querterly_demand_id = $querterly_demand->id;
                $querterly_qemand_pvms->annual_demand_pvms_unit_demand_id = $pvms['annual_demand_pvms_unit_demand_id'];
                $querterly_qemand_pvms->pvms_id = $pvms['pvms_primary_id'];
                $querterly_qemand_pvms->req_qty = $pvms['request_qty'];
                $querterly_qemand_pvms->remarks = $pvms['remarks'];
    
                $querterly_qemand_pvms->save();
            }
        }

        return $querterly_demand;
    }

    public function destroy(string $id)
    {
        //

    }

    public function deliveryIndex(Request $request)
    {
        $perpage = 10;
        if($request->perpage) {
            $perpage = $request->perpage;
        }

        $annual_demands = AnnualDemand::with('financialYear')->withCount('departmentList')->latest()->paginate($perpage);
        $user_approval_role = auth()->user()->userApprovalRole;

        $querterly_demands = QuerterlyDemand::where('is_approved', true)->orderBy('id', 'DESC')->paginate();

        return view('admin.querterly_demand.delivery_index',compact('annual_demands','user_approval_role', 'querterly_demands'));
    }

    public function deliveryCreate($demand_id){
        return view('admin.querterly_demand.delivery_create');
    }

    public function deliveryStore(Request $request){

        $querterly_demand_receive = new QuerterlyDemandReceive();
        $querterly_demand_receive->querterly_demand_id = $request->querterly_demand_id;
        $querterly_demand_receive->save();

        foreach($request->demandPVMS as $demandPVMS){
            foreach($demandPVMS['delivery_data'] as $delivery_data){
                if($delivery_data['delivery_qty'] && !$delivery_data['exists']){
                    $pvms_store = new PvmsStore();
                    $pvms_store->sub_org_id = 2;
                    $pvms_store->pvms_id = $demandPVMS['pvms_id'];
                    $pvms_store->batch_pvms_id = $delivery_data['batch_id'];
                    $pvms_store->stock_out = $delivery_data['delivery_qty'];
                    $pvms_store->save();

                    $pvms_store = new PvmsStore();
                    $pvms_store->sub_org_id = $querterly_demand_receive->querterlyDemand->sub_org_id;
                    $pvms_store->from_sub_org_id = 2;
                    $pvms_store->pvms_id = $demandPVMS['pvms_id'];
                    $pvms_store->batch_pvms_id = $delivery_data['batch_id'];
                    $pvms_store->stock_in = $delivery_data['delivery_qty'];
                    $pvms_store->save();

                    $querterly_demand_receive_pvms = new QuerterlyDemandReceivePvms();
                    $querterly_demand_receive_pvms->querterly_demand_receive_id = $querterly_demand_receive->id;
                    $querterly_demand_receive_pvms->querterly_demand_id = $request->querterly_demand_id;
                    $querterly_demand_receive_pvms->querterly_demand_pvms_id = $delivery_data['querterly_demand_pvms_id'];
                    $querterly_demand_receive_pvms->issued_qty = $delivery_data['delivery_qty'];
                    $querterly_demand_receive_pvms->batch_pvms_id = $delivery_data['batch_id'];
                    $querterly_demand_receive_pvms->pvms_store_id = $pvms_store->id;
                    $querterly_demand_receive_pvms->save();
                }
            }
        }
    }

    public function receiveIndex(){
        $querterly_demand_receives = QuerterlyDemandReceive::with('querterlyDemand.financialYear')
                                        ->whereHas('querterlyDemand', function($q){
                                            $q->where('sub_org_id', auth()->user()->sub_org_id);
                                            // $q->where('sub_org_id', 1);
                                        })
                                        ->latest()
                                        ->get();

        return view('admin.querterly_demand.receive_index',compact('querterly_demand_receives'));
    }

    public function receiveCreate($receive_id){
        return view('admin.querterly_demand.receive_create');
    }

    public function receiveStore(Request $request){
        $querterly_demand_receive = QuerterlyDemandReceive::find($request->querterly_demand_receive_id);
        $querterly_demand_receive->is_received = true;
        $querterly_demand_receive->save();

        foreach($request->demandPVMS as $demandPVMS){
            $querterly_demand_receive_pvms = QuerterlyDemandReceivePvms::find($demandPVMS['querterly_demand_receive_pvms']);
            $querterly_demand_receive_pvms->received_qty = $demandPVMS['received_qty'];
            $querterly_demand_receive_pvms->wastage_qty = $demandPVMS['wastage_qty'];
            $querterly_demand_receive_pvms->remarks = $demandPVMS['remarks'];
            $querterly_demand_receive_pvms->save();

            $pvms_store = PvmsStore::find($querterly_demand_receive_pvms->pvms_store_id);
            $pvms_store->stock_in = $demandPVMS['received_qty'];
            $pvms_store->is_received = true;
            $pvms_store->save();
        }

        return $querterly_demand_receive;
    }

    public function receiveDetailsJson($receive_id){
        $querterly_demand_receives = QuerterlyDemandReceive::with(
            'querterlyDemand.financialYear',
            'querterlyDemandReceivePvms.querterlyDemandPvms.pvms.unitName'
            )
            ->find($receive_id);

        return $querterly_demand_receives;
    }
}
