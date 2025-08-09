<?php

namespace App\Http\Controllers;

use App\Models\AfmsdIssueApproval;
use App\Models\AfmsdIssueApprovalBatch;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AfmsdIssueApprovalController extends Controller
{
   
// $item = Purchase::with('purchasePvms.batchPvms', 'dmdUnit', 'purchasePvms.pvms.itemTypename',
//  'purchasePvms.demand', 'purchasePvms.purchaseDelivery.store.batch')


    // public function IssueApprovals()
    // {
    //    $approvals = Purchase::with([                  
    //     'purchaseTypes',
    //     'subOrganization',
    //     'financialYear',
    //     // 'purchasePvms.pvms.itemTypename',
    //     // 'purchasePvms.demand.demandType',
    //      'purchaseTypes.purchaseDelivery',
    //      'purchaseTypes.demand.demandType',
    //      'purchaseTypes.pvms.itemTypename',
    // ])->where('afmsd_approval', 1)
    // ->orderBy('id', 'desc')
    // ->get();



    //     return response()->json($approvals);
    // }

    public function IssueApprovalsItemForGroupIng (){
        $approvals = AfmsdIssueApproval::with([
        'batchInfo',
        'purchaseItem.subOrganization',
        'purchaseItem.purchasePvms',          
        'purchaseItem.dmdUnit',                   
        'purchaseItem.purchasePvms.purchaseDelivery.store.batch',                   
        'purchaseType.pvms.itemTypename',
        'purchaseType.demand',
        'purchaseType.batchPvms',
        'user'
            ])
            ->where('status', 2)
            ->orderBy('id', 'desc')
            ->get();

        $grouped = $approvals->groupBy('purchase_id')->map(function ($items, $purchaseId) {
            return [
                'purchase_id' => $purchaseId,
                'items' => $items->values(),
            ];
        })->values();

        return response()->json($grouped);

    }


    public function storeIssueApprovl(Request $request)
        {
            DB::beginTransaction();

            try {
                $request->validate([
                    'id' => 'required|integer',
                    'purchase_pvms' => 'required|array',
                    'purchase_pvms.*.id' => 'required|integer',
                ]);

                $purchase_id = $request->input('id');
                $pvmsItems = $request->input('purchase_pvms');
                $roleId = auth()->user()->id;

                foreach ($pvmsItems as $item) {
                    $issueApproval = AfmsdIssueApproval::create([
                        'purchase_id' => $purchase_id,
                        'purchase_type_id' => $item['id'],
                        'asign_qty' => $item['deliver_today'] ?? 0,
                        'afmsd_clerk' => $roleId,
                        'delivery_at' => Carbon::now()->toDateString(),
                        'status' => 1,
                    ]);

                    if (!empty($item['batchPvmsList'])) {
                        foreach ($item['batchPvmsList'] as $batch) {
                            if (!empty($batch['batchPvms']) && !empty($batch['qty'])) {
                                AfmsdIssueApprovalBatch::create([
                                    'afmsd_issue_approval_id' => $issueApproval->id,
                                    'batchPvms_id' => $batch['batchPvms'],
                                    'qty' => $batch['qty'],
                                ]);
                            }
                        }
                    }
                }

                DB::commit();
                return response()->json(['message' => 'Data inserted successfully.']);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => $e->getMessage()], 500);
            }
    }
    
    public function updateDeliveryApproval(Request $request, $id)
    {
        $request->validate([
            'purchase_id' => 'required|integer',
            'updates' => 'required|array',
            'updates.*.id' => 'required|integer',
        ]);

        // Update status 
        AfmsdIssueApproval::where('purchase_id', $request->purchase_id)
            ->update(['status' => 2]);

        // Update each Batch
        foreach ($request->updates as $update) {
            AfmsdIssueApprovalBatch::where('id', $update['id'])->update([
                'batchPvms_id' => $update['batchPvms_id'],
                'qty' => $update['qty'],
            ]);
        }

        return response()->json(['message' => 'Data updated successfully.']);
    }


}
