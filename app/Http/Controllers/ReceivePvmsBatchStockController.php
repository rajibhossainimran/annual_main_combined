<?php

namespace App\Http\Controllers;

use App\Models\BatchPvms;
use App\Models\Demand;
use App\Models\DemandApproval;
use App\Models\DemandDocument;
use App\Models\DemandPvms;
use App\Models\DemandRepairPvms;
use App\Models\DemandType;
use App\Models\Disease;
use App\Models\FinancialYear;
use App\Models\IssueOrderApproval;
use App\Models\ItemType;
use App\Models\NotesheetDemandPVMS;
use App\Models\Purchase;
use App\Models\PurchaseType;
use App\Models\RemarksTemplate;
use App\Models\OnLoan;
use App\Models\OnLoanItem;
use App\Models\OnLoanItemReceive;
use App\Models\PvmsStore;
use App\Models\SubOrganization;
use App\Models\TenderNotesheet;
use App\Models\User;
use App\Models\UserApprovalRole;
use App\Models\WorkorderReceivePvms;
use App\Services\ApprovalLayerService;
use App\Services\DemandService;
use App\Services\MediaService;
use App\Services\NotesheetService;
use App\Services\StockService;
use App\Services\TenderService;
use App\Utill\Approval\DemandApprovalSetps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Foreach_;

class ReceivePvmsBatchStockController extends Controller
{
    public function getSubOrganization()
    {
        $result['units'] = SubOrganization::whereIn('type', ['CMH', 'AFIP'])->get();
        $result['org_name'] = SubOrganization::find(Auth::user()->sub_org_id);
        $result['org'] = SubOrganization::select('id', 'name')->get();

        $result['fYear'] = FinancialYear::orderBy('id','ASC')->get();

        $result['send_to'] = User::whereIn('email', ['DADGMS_EQUIP', 'DADGMS_CC', 'DADGMS_PP'])->get();



        return $result;
    }


    public function subtract()
    {
        return view('admin.receive_pvms_batch_stock.delete');
    }

    public function stockdel(Request $request)
    {

        // return $request ; 

        $data = json_decode($request->data, true);

        DB::beginTransaction();


        $issueItems = $data['demandPVMS'];

        try {

            /************ START Receive PVMS Batch Stock Table *************/

            foreach ($issueItems as $k => $item) {

                $rec  = BatchPvms::select('id')->where('pvms_id', $item['id'])->where('batch_no',$item['batch_no'])->orderBy('id', 'desc')->first() ;
                // $insert = new BatchPvms();
                // $insert->batch_no = $item['batch_no'];
                // $insert->pvms_id = $item['id'];
                // $insert->lp_pvms_id = null;
                // $insert->workorder_pvms_id = null;
                // $insert->expire_date = $item['expire_date'];
                // $insert->qty = $item['qty'];
                // $insert->is_afmsd_distributed = 0;
                // $insert->is_unit_distributed = 0;
                // $insert->created_at = now();
                // $insert->save();
                if($rec){
                    $issueOrderApproval = new PvmsStore();
                    $issueOrderApproval->sub_org_id = Auth::user()->sub_org_id;    //null;
                    $issueOrderApproval->from_sub_org_id  = null;
                    $issueOrderApproval->issue_voucher_id = null;
                    $issueOrderApproval->workorder_receive_id = null;
                    $issueOrderApproval->branch_id = null;
                    $issueOrderApproval->pvms_id = $item['id'];
                    $issueOrderApproval->batch_pvms_id = $rec->id ;
                    $issueOrderApproval->stock_in = 0;
                    $issueOrderApproval->stock_out = $item['qty'];
                    $issueOrderApproval->is_received = 1;
                    $issueOrderApproval->is_on_loan = 0;
                    $issueOrderApproval->on_loan_item_id = 0;
                    $issueOrderApproval->created_at = now();
                    $issueOrderApproval->save();
                }


            }
        /************ START Purschase Table *************/


            DB::commit();

            return response()->json(['status'=>true,'success' => 'Item added successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'errors' => $e->getMessage()]);
        }

    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.receive_pvms_batch_stock.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {

        // return $request ;

        $data = json_decode($request->data, true);

        DB::beginTransaction();


        $issueItems = $data['demandPVMS'];

        try {

            /************ START Receive PVMS Batch Stock Table *************/

            foreach ($issueItems as $k => $item) {

                $insert = new BatchPvms();
                $insert->batch_no = $item['batch_no'];
                $insert->pvms_id = $item['id'];
                $insert->lp_pvms_id = null;
                $insert->workorder_pvms_id = null;
                $insert->expire_date = $item['expire_date'];
                $insert->qty = $item['qty'];
                $insert->is_afmsd_distributed = 0;
                $insert->is_unit_distributed = 0;
                $insert->created_at = now();
                $insert->save();


                $issueOrderApproval = new PvmsStore();
                $issueOrderApproval->sub_org_id = Auth::user()->sub_org_id;
                $issueOrderApproval->from_sub_org_id = null;
                $issueOrderApproval->issue_voucher_id = null;
                $issueOrderApproval->workorder_receive_id = null;
                $issueOrderApproval->branch_id = null;
                $issueOrderApproval->pvms_id = $item['id'];
                $issueOrderApproval->batch_pvms_id = $insert->id;
                $issueOrderApproval->stock_in = $item['qty'];
                $issueOrderApproval->stock_out = 0;
                $issueOrderApproval->is_received = 1;
                $issueOrderApproval->is_on_loan = 0;
                $issueOrderApproval->on_loan_item_id = 0;
                $issueOrderApproval->created_at = now();
                $issueOrderApproval->save();
            }
        /************ START Purschase Table *************/


            DB::commit();

            return response()->json(['status'=>true,'success' => 'Item added successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status'=>false,'errors' => $e->getMessage()]);
        }

    }

}
