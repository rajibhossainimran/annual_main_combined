<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseTypeDelivery;
use Illuminate\Support\Facades\Auth;

class IssueOrderController extends Controller
{
    public function index()
    {
        $authUser = auth()->user();
        $authUserId = $authUser->id;
        $authUserEmail = $authUser->email;
        $show = false;
        $showForwardButton = false;

        // Check if the user is admin
        if ($authUserEmail === 'dgmsadmin') {
            // Fetch all records for admin users
            $records = Purchase::with([
                'purchaseTypes' => function ($query) {
                    $query->with('pvms:id,pvms_id,nomenclature');
                },
                'subOrganization:id,name'
            ])
                ->withCount('purchaseTypes as total_item')
                ->orderBy('id', 'desc')
                ->get();
        } elseif ($authUserEmail == "DADGMS_EQUIP" || $authUserEmail == "DADGMS_CC" || $authUserEmail == "DADGMS_PP") {
            $authUserSubOrgId = $authUser->sub_org_id;

            $show = true;

            $records = Purchase::with([
                'purchaseTypes' => function ($query) {
                    $query->with('pvms:id,pvms_id,nomenclature');
                },
                'subOrganization:id,name'
            ])
                ->withCount('purchaseTypes as total_item')
                ->where('send_to', $authUserId)
                ->orderBy('id', 'desc')
                ->get();
        } elseif (is_numeric($authUser->sub_org_id) && $authUser->sub_org_id != 2) {
            $authUserSubOrgId = $authUser->sub_org_id;

            $records = Purchase::with([
                'purchaseTypes' => function ($query) {
                    $query->with('pvms:id,pvms_id,nomenclature');
                },
                'subOrganization:id,name'
            ])
                ->withCount('purchaseTypes as total_item')
                ->where('sub_org_id', $authUserSubOrgId)
                ->where('status', 'approved')
                ->orderBy('id', 'desc')
                ->get();
        } elseif ($authUser->sub_org_id == 2) {
            $showForwardButton = true;

            $records = Purchase::with([
                'purchaseTypes' => function ($query) {
                    $query->with('pvms:id,pvms_id,nomenclature');
                },
                'subOrganization:id,name'
            ])
                ->withCount('purchaseTypes as total_item')
                ->where('status', 'approved')
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('admin.issue_order.index', compact('records', 'show', 'showForwardButton'));
    }

    public function approveOrder(Request $request, $id)
    {
        $user = auth()->user();

        if (empty($user->sign)) {
            return response()->json(['error' => 'Signature (sign) is required before approving.'], 422);
        }

        if (empty($user->rank)) {
            return response()->json(['error' => 'Rank is required before approving.'], 422);
        }

        $purchaseOrder = Purchase::findOrFail($id);
        $purchaseOrder->status = 'approved';
        $purchaseOrder->rank = $user->rank;
        $purchaseOrder->name = $user->name;
        $purchaseOrder->sign = $user->sign;
        $purchaseOrder->save();

        // Update quantities
        if ($request->has('quantities')) {
            foreach ($request->quantities as $item) {
                DB::table('purchase_types')
                    ->where('id', $item['id'])
                    ->update([
                        'status' => 'approved',
                        'request_qty' => $item['qty']
                    ]);
            }
        }

        $purchaseTypes = DB::table('purchase_types')
            ->where('purchase_id', $purchaseOrder->id)
            ->get();

        foreach ($purchaseTypes as $purchaseType) {
            DB::table('issue_order_approvals')->insert([
                'purchase_id' => $purchaseOrder->id,
                'purchase_type_id' => $purchaseType->id,
                'approval_status' => 'Approved by DGMS to AFMSD',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'send_to' => 85,
                'step_number' => 2,
                'note' => 'Approved by DGMS to AFMSD',
                'action' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Order and related items forwarded successfully']);
    }


    public function UnitDeliveryApproval()
    {
        $group_incharge = 0;

        if (Auth()->user()->user_approval_role_id == 25) {
            $stage = 0;

            $purchases = Purchase::where('stage', $stage)
                ->where('purchase_item_type', 'issued')
                ->orderBy('id', 'desc')
                ->get();
        } elseif (Auth()->user()->user_approval_role_id == 1) {
            $stage = 2;

            $purchases = Purchase::where('stage', $stage)
                ->where('purchase_item_type', 'issued')
                ->orderBy('id', 'desc')
                ->get();
        } elseif (Auth()->user()->user_approval_role_id == 26) {
            $stage = 3;

            $purchases = Purchase::where('stage', $stage)
                ->where('purchase_item_type', 'issued')
                ->whereHas('purchaseTypes.pvms', function ($query) {
                    // Directly apply the filter on item_groups_id within the nested whereHas
                    $query->where('item_groups_id', auth()->user()->group_id);
                })
                ->with(['purchaseTypes' => function ($query) {
                    // Filter the purchaseTypes to only include those that match the user's group_id
                    $query->whereHas('pvms', function ($query) {
                        $query->where('item_groups_id', auth()->user()->group_id);
                    });
                }, 'purchaseTypes.pvms']) // Load the related pvms data
                ->orderBy('id', 'desc')
                ->get();

            $allPurchaseTypes = $purchases->flatMap(function ($purchase) {
                return $purchase->purchaseTypes->map(function ($purchaseType) use ($purchase) {
                    $pvmsData = \App\Models\PVMS::find($purchaseType->pvms_id);

                    $deliveryData = \App\Models\PurchaseTypeDelivery::where('purchase_type_id', $purchaseType->id)->first(); // Adjust model name if needed

                    return [
                        'purchase_type' => $purchaseType,
                        'purchase' => $purchase,
                        'pvms' => $pvmsData,
                        'delivery' => $deliveryData
                    ];
                });
            });

            $purchases = $allPurchaseTypes;

            $group_incharge = 1;
        }

        return view('admin.unit_delivery_approval.index', compact('purchases', 'group_incharge'));
    }

    public function approveUnitDelivery($id)
    {
        $purchaseOrder = Purchase::findOrFail($id);

        if ($purchaseOrder->stage == 0) {
            $purchaseOrder->stage = 2;
        } elseif ($purchaseOrder->stage == 2) {
            $purchaseOrder->stage = 3;
        } elseif ($purchaseOrder->stage == 3) {
            $purchaseOrder->stage = 1;
        }

        $purchaseOrder->save();

        return response()->json(['message' => 'Unit delivery items forwarded successfully']);
    }

    // purchase details by id 
    public function getPurchaseDetailsById($id)
    {
        $purchaseDetail = Purchase::with([
            'purchaseTypes' => function ($query) {
                $query->with('pvms:id,pvms_id,nomenclature');
            },
            'subOrganization:id,name'
        ])
        ->withCount('purchaseTypes as total_item')
        ->find($id);

        if (!$purchaseDetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'Purchase not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $purchaseDetail
        ]);
    }


    public function getPurchaseTypeDetails($id)
    {
        $purchaseType = PurchaseType::find($id);

        if ($purchaseType) {
            $deliveries = $purchaseType->purchaseDelivery;

            $purchase = $purchaseType->purchase;
            $unitName = $purchase->subOrganization ? $purchase->subOrganization->name : 'N/A';
            $status = $this->getStatusLabel($purchase->stage);

            $deliveryData = $deliveries->map(function ($delivery) {
                return [
                    'delivery_number' => $delivery->id,
                    'delivered_qty' => $delivery->delivered_qty,
                    'delivery_date' => $delivery->delivered_at, 
                ];
            });

            return response()->json([
                'purchase_type_id' => $purchaseType->id,
                'purchase_number' => $purchase->purchase_number,
                'unit_name' => $unitName,
                'status' => $status,
                'issue_qty' => $purchaseType->request_qty,
                // 'delivered_qty' => $purchaseType->delivered_qty,
                // 'group_id' => $purchaseType->group_id,
                'deliveries' => $deliveryData,
            ]);
        }

        return response()->json(['message' => 'No matching record found'], 404);
    }

    private function getStatusLabel($stage)
    {
        switch ($stage) {
            case 0:
                return 'Forwarded by AFMSD Clerk';
            case 2:
                return 'Approved by Stock Control Officer';
            case 3:
                return 'Approved by AFMSD CO';
            default:
                return 'Unknown Status';
        }
    }

    public function approvePurchaseType($id)
    {
        $deliveries = \App\Models\PurchaseTypeDelivery::where('purchase_type_id', $id)->get();

        if ($deliveries->isEmpty()) {
            return response()->json(['message' => 'No matching delivery records found for approval'], 404);
        }

        foreach ($deliveries as $delivery) {
            $delivery->purchase_type_delivered = true;
            $delivery->save();
        }

        $purchaseTypeData = \App\Models\PurchaseType::where('id', $deliveries->first()->purchase_type_id)->first();

        if ($purchaseTypeData) {
            $purchaseId = $purchaseTypeData->purchase_id;
            $purchaseTypesData = \App\Models\PurchaseType::where('purchase_id', $purchaseId)->get();

            $allDelivered = true;

            foreach ($purchaseTypesData as $purchaseType) {
                $deliveryRecord = \App\Models\PurchaseTypeDelivery::where('purchase_type_id', $purchaseType->id)->first();
                if (!$deliveryRecord || $deliveryRecord->purchase_type_delivered != 1) {
                    $allDelivered = false;
                    break;
                }
            }

            if ($allDelivered) {
                $purchaseOrder = \App\Models\Purchase::findOrFail($purchaseId);
                $purchaseOrder->stage = 1;  // Stage 1: All deliveries approved
                $purchaseOrder->save();
            }

            return response()->json(['message' => 'Marked as delivered successfully']);
        }

        return response()->json(['message' => 'No matching purchase type data found'], 404);
    }
}
