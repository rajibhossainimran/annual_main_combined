<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PVMS;
use App\Models\User;
use App\Models\Batch;
use App\Models\Demand;
use App\Models\Purchase;
use App\Models\BatchPvms;
use App\Models\Notesheet;
use App\Models\PvmsStore;
use App\Models\Workorder;
use App\Models\DemandPvms;
use App\Models\OnLoanItem;
use App\Models\PurchaseType;
use App\Models\UnitStockOut;
use Illuminate\Http\Request;
use App\Models\FinancialYear;
use App\Models\WorkorderPvms;
use App\Services\PVMSService;
use App\Services\StockService;
use App\Models\SubOrganization;
use App\Models\UnitStockOutPvms;
use App\Models\WorkorderReceive;
use App\Models\IssueOrderApproval;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseTypeDelivery;
use App\Models\WorkorderReceivePvms;
use Illuminate\Support\Facades\Auth;
use App\Models\AnnualDemandPvmsUnitDemand;
use Illuminate\Validation\Rules\Can;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function index()
    {
        $data = [];
        $org = SubOrganization::all();
        $org_name = SubOrganization::where('id', Auth::user()->sub_org_id)->first();
        return view('admin.report.search_by_date', compact('data', 'org', 'org_name'));
    }

    public function unitWiseItemIssue()
    {
        return view('admin.report.unit_wise_item_issue');
    }

    public function unitWiseItemIssueApi(Request $request)
    {
        $fy = $request->fy;
        $pvms_id = $request->pvms_id;
        $unit = $request->unit;

        $annual_demand_pvms = AnnualDemandPvmsUnitDemand::with(['annualDemandPvms.PVMS', 'annualDemandUnit.annualDemand']);

        if (isset($unit)) {
            $annual_demand_pvms = $annual_demand_pvms->whereHas('annualDemandUnit', function ($query) use ($unit) {
                $query->where('sub_org_id', $unit);
            });
        }

        $annual_demand_pvms = $annual_demand_pvms->whereHas('annualDemandPvms.PVMS', function ($query) use ($pvms_id) {
            $query->where('id', $pvms_id);
        })
            ->whereHas('annualDemandUnit.annualDemand', function ($query) use ($fy) {
                $query->where('financial_year_id', $fy);
            })
            ->get()
            ->groupBy(fn($item) => optional($item->annualDemandPvms->PVMS)->id)
            ->map(function ($group) {
                return [
                    'pvms_id' => $group->first()->annualDemandPvms->PVMS->pvms_id ?? null,
                    'nomenclature' => $group->first()->annualDemandPvms->PVMS->nomenclature ?? null,
                    'unitName' => $group->first()->annualDemandPvms->PVMS->unitName->name ?? null,
                    'total_qty' => $group->sum('dg_qty'),
                ];
            });

        $item_issue = PurchaseType::with(['pvms.unitName', 'purchase.dmdUnit', 'demand'])->where('pvms_id', $pvms_id)->where('status', 'approved')->where('purchase_type', 'issued');

        if (isset($unit)) {
            $item_issue = $item_issue->whereHas('purchase', function ($query) use ($fy, $unit) {
                $query->where('financial_year_id', $fy)->where('sub_org_id', $unit);
            });
        } else {
            $item_issue = $item_issue->whereHas('purchase', function ($query) use ($fy) {
                $query->where('financial_year_id', $fy);
            });
        }

        $item_issue = $item_issue->get();

        $contract_item = WorkorderPvms::with('workorder')->whereHas('workorder', function ($query) use ($fy) {
            $query->where('financial_year_id', $fy);
        })->where('pvms_id', $pvms_id)->get();

        $contract_item_receive = WorkorderReceivePvms::with(['workorderPvms.pvms', 'workorderPvms.workorder.vendor', 'pvmsStore.batch', 'onLoanItem.pvmsStore.batch', 'workOrderReceive'])
            ->whereHas('workorderPvms.workorder', function ($query) use ($fy) {
                $query->where('financial_year_id', $fy);
            })
            ->whereHas('workorderPvms', function ($query) use ($pvms_id) {
                $query->where('pvms_id', $pvms_id);
            })
            ->whereHas('workOrderReceive', function ($query) {
                $query->where('approved_by', 'group-incharge');
            })->get();

        $on_loan_issue_qty = OnLoanItem::where('pvms_id', $pvms_id)->sum('qty');

        return response()->json([
            "contract_item" => $contract_item,
            "contract_item_receive" => $contract_item_receive,
            "annual_demand_pvms" => isset($annual_demand_pvms[$pvms_id]) ? $annual_demand_pvms[$pvms_id] : "",
            "stock_data" => PVMSService::pvmsUnitWiseStock([$pvms_id], 2),
            "item_issue" => $item_issue,
            "on_loan_issue_qty" => $on_loan_issue_qty
        ]);
    }

    public function supply_source()
    {
        $financial_years = FinancialYear::orderBy('financial_years.id', 'desc')->get();
        return view('admin.report.supply_source', compact('financial_years'));
    }

    public function supply_source_list(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;

        $data = DB::table('workorder_pvms')
            ->join('workorders', 'workorder_pvms.workorder_id', '=', 'workorders.id')
            ->join('p_v_m_s', 'workorder_pvms.pvms_id', '=', 'p_v_m_s.id')
            ->leftJoin('account_units', 'p_v_m_s.account_units_id', '=', 'account_units.id')
            ->leftJoin('specifications', 'p_v_m_s.specifications_id', '=', 'specifications.id')
            ->leftJoin('workorder_receive_pvms', 'workorder_pvms.id', '=', 'workorder_receive_pvms.workorder_pvms_id')
            ->leftJoin('workorder_receives', 'workorder_receive_pvms.workorder_receive_id', '=', 'workorder_receives.id')
            ->leftJoin('users', 'workorders.vendor_id', 'users.id')
            ->select(
                'workorder_pvms.workorder_id',
                'workorder_pvms.pvms_id',
                'users.name as user_name',
                'p_v_m_s.pvms_id as pvms_name',
                'p_v_m_s.nomenclature',
                'account_units.name as au',
                'specifications.name as spec',
                DB::raw('SUM(workorder_pvms.qty) as total_qty'),
                DB::raw('IFNULL(SUM(workorder_receive_pvms.received_qty), 0) as total_received_qty')
            )
            ->where('workorders.is_adgms_approved', 1)
            ->where(function ($query) {
                $query->whereNotNull('workorder_receive_pvms.on_loan_item_id')
                    ->orWhere('workorder_receives.approved_by', 'group-incharge');
            });

        if ($request->fy) {
            $data = $data->where('workorders.financial_year_id', $request->fy);
        }

        if ($request->query('search')) {
            $data = $data->where(function ($query) use ($request) {
                $query->where('p_v_m_s.pvms_id', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('p_v_m_s.nomenclature', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('p_v_m_s.pvms_old_name', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $request->query('search') . '%');
            });
        }


        $data = $data->groupBy('workorder_pvms.workorder_id', 'workorder_pvms.pvms_id')->latest('workorder_pvms.created_at')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }

    public function getPvmsWithAvilableStockList(Request $request)
    {
        $search = $request->search;
        $items = PVMS::with('batchList', 'itemTypename', 'authorizedEquipment', 'itemGroupName', 'specificationName', 'unitName')
            ->with(['batchList' => function ($query) {
                $query->addSelect(DB::raw('id, pvms_id, batch_no, expire_date, qty, (SELECT CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and pvms_store.is_received=1 and pvms_store.sub_org_id=' . auth()->user()->sub_org_id . ') as available_quantity'))
                    ->where('expire_date', '>', Carbon::now())
                    ->whereHas('unitStock', function ($query) {
                        $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                            ->where('is_received', 1)
                            ->where('sub_org_id', auth()->user()->sub_org_id)
                            ->groupBy('batch_pvms_id')
                            ->havingRaw('available_quantity>0');
                    });
            }])
            ->where(function ($query) use ($search) {
                $query->where('pvms_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('nomenclature', 'LIKE', '%' . $search . '%')
                    ->orWhere('pvms_old_name', 'LIKE', '%' . $search . '%');
            });

        if ($request->item_type) {
            $items = $items->where('item_types_id', $request->item_type);
        }

        return $items->limit(50)->get();;
    }

    public function search(Request $request)
    {
        $data = [];
        $type = $request->type;
        $sub = $request->sub_org;
        $org = SubOrganization::all();
        if ($type == 'Demand') {
            $data = Demand::wheredate('created_at', date('Y-m-d', strtotime($request->date)))
                ->where('sub_org_id', $sub)
                ->get();
        } elseif ($type == 'Notesheet') {
            $user = Auth::user()->sub_org_id;
            $org_name = SubOrganization::where('id', Auth::user()->sub_org_id)->first();
            if (isset($org_name) && !empty($org_name)) {
                $allUser = User::where('sub_org_id', $org_name->id)->select('id')->get();
                $userId = [];
                foreach ($allUser as $user) {
                    array_push($userId, $user->id);
                }

                $data = Notesheet::wheredate('created_at', date('Y-m-d', strtotime($request->date)))
                    ->whereIn('created_by', $userId)
                    ->get();
            } else {
                $allUser = User::where('sub_org_id', $sub)->select('id')->get();
                $userId = [];
                foreach ($allUser as $user) {
                    array_push($userId, $user->id);
                }

                $data = Notesheet::wheredate('created_at', date('Y-m-d', strtotime($request->date)))
                    ->whereIn('created_by', $userId)
                    ->get();
            }
        }
        return view('admin.report.search_by_date', compact('data', 'type', 'org'));
    }

    public function purchase_pvms(Request $request)
    {
        return view('admin.purchase_pvms.index');
    }

    public function unit_delivery(Request $request)
    {
        return view('admin.purchase_pvms_delivery.delivery');
    }

    public function purchase_pvms_receive(Request $request)
    {
        return view('admin.purchase_pvms_receive.index');
    }

    public function lp_item_received(Request $request)
    {
        foreach ($request->purchase_pvms as $each_purchase_pvms) {
            $purchase_type = PurchaseType::find($each_purchase_pvms["id"]);
            if ($purchase_type && $each_purchase_pvms["received_deliver_qty"] && (int)$each_purchase_pvms["received_deliver_qty"] > 0) {

                $batch_pvms = new BatchPvms();
                $batch_pvms->batch_no = $each_purchase_pvms["batch_no"];
                $batch_pvms->pvms_id = $purchase_type->pvms_id;
                $batch_pvms->lp_pvms_id = $purchase_type->id;
                $batch_pvms->expire_date = $each_purchase_pvms["expire_date"];
                $batch_pvms->qty = (int) $each_purchase_pvms["received_deliver_qty"];
                $batch_pvms->save();

                $uni_stock_transit = StockService::stockEntry(
                    $purchase_type->sub_org_id,
                    null,
                    $request->id,
                    $purchase_type->pvms_id,
                    $batch_pvms->id,
                    (int)$each_purchase_pvms["received_deliver_qty"],
                    0,
                    1
                );

                $received_qty = (int)$purchase_type->received_qty ?? 0;
                $purchase_type->received_qty = $received_qty + (int) $each_purchase_pvms["received_deliver_qty"];
                $purchase_type->save();
                //CMH stock in
            }
        }
        return response()->json(['success' => 1], 200);
    }

    public function purchase_pvms_delivery_received(Request $request)
    {
        foreach ($request->purchase_pvms as $each_purchase_pvms) {
            $purchase_type = PurchaseType::find($each_purchase_pvms["id"]);
            $received_qty = (int)$purchase_type->received_qty ?? 0;
            foreach ($each_purchase_pvms["purchase_delivery"] as $each_delivery) {
                if (((int)$each_delivery["waste_qty"] > 0 || (int)$each_delivery["received_qty"] > 0) && $each_delivery["is_received"] == 0) {
                    $purchase_type_delivery = PurchaseTypeDelivery::find($each_delivery["id"]);
                    if ($purchase_type_delivery) {

                        $unit_stock_transit_update = PvmsStore::where('id', $purchase_type_delivery->pvms_store_id)->first();
                        $unit_stock_transit_update->stock_in = (int)$each_delivery["received_qty"];
                        $unit_stock_transit_update->is_received = 1;
                        $unit_stock_transit_update->save();

                        $received_qty += (int)$each_delivery["received_qty"] + (int)$each_delivery["waste_qty"];
                        $purchase_type_delivery->recieved_by = auth()->user()->id;
                        $purchase_type_delivery->recieved_at = Carbon::now();
                        $purchase_type_delivery->received_qty = (int)$each_delivery["received_qty"];
                        $purchase_type_delivery->waste_qty = (int)$each_delivery["waste_qty"];
                        $purchase_type_delivery->received_remarks = $each_delivery["received_remarks"];
                        $purchase_type_delivery->is_received = 1;
                        $purchase_type_delivery->save();
                        //CMH stock in
                    }
                }
            }

            $purchase_type->received_qty = $received_qty;
            $purchase_type->save();
        }
        return response()->json(['success' => 1], 200);
    }

    public function unitStockOut(Request $request)
    {
        $data = $request->all();
        $max_voucher_number = UnitStockOut::max('voucher_no');

        $unit_stock_out = new UnitStockOut();
        $unit_stock_out->sub_org_id = auth()->user()->sub_org_id;
        $unit_stock_out->branch_id = $data["branch"];
        // $unit_stock_out->patient_id =
        $unit_stock_out->voucher_no = $max_voucher_number ? $max_voucher_number + 1 : 10001;
        $unit_stock_out->save();

        foreach ($data["pvmsList"] as $eachData) {
            foreach ($eachData["deliveryBatchList"] as $eachBatch) {
                if (isset($eachBatch["batchPvms"]) && isset($eachBatch["qty"])) {
                    $batchPvms = BatchPvms::find($eachBatch["batchPvms"]);
                    $stock_data = PvmsStore::select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                        ->where('is_received', 1)
                        ->where('sub_org_id', auth()->user()->sub_org_id)
                        ->where('batch_pvms_id', $batchPvms->id)
                        ->groupBy('batch_pvms_id')->first();

                    if ($stock_data && $stock_data->available_quantity > 0 &&  $stock_data->available_quantity >= (int)$eachBatch["qty"]) {
                        StockService::stockEntry(
                            auth()->user()->sub_org_id,
                            null,
                            null,
                            $eachData["id"],
                            $batchPvms->id,
                            0,
                            (int)$eachBatch["qty"],
                            1,
                            $data["branch"]
                        );

                        $branch_store = StockService::stockEntry(
                            null,
                            auth()->user()->sub_org_id,
                            null,
                            $eachData["id"],
                            $batchPvms->id,
                            (int)$eachBatch["qty"],
                            0,
                            1,
                            $data["branch"]
                        );

                        $unit_stock_out_pvms = new UnitStockOutPvms();
                        $unit_stock_out_pvms->unit_stock_out_id = $unit_stock_out->id;
                        $unit_stock_out_pvms->pvms_id = $batchPvms->pvms_id;
                        $unit_stock_out_pvms->btach_pvms_id = $batchPvms->id;
                        $unit_stock_out_pvms->store_id = $branch_store->id;
                        $unit_stock_out_pvms->qty = (int)$eachBatch["qty"];
                        $unit_stock_out_pvms->save();

                        if ((int) $stock_data->available_quantity - (int)$eachBatch["qty"] == 0) {
                            $batchPvms->is_unit_distributed = 1;
                            $batchPvms->save();
                        }
                    }
                }
            }
        }
        return response()->json(['success' => 1], 200);
    }

    public function  purchase_pvms_unit_stock($id, Request $request)
    {
        $purchase_types = PurchaseType::where('purchase_id', $id)->select('pvms_id')
            ->selectRaw('(SELECT SUM(stock_in-stock_out) FROM pvms_store WHERE sub_org_id = purchase_types.sub_org_id AND pvms_id=purchase_types.pvms_id AND is_received=1) AS stock')
            ->selectRaw('(SELECT SUM(stock_out) FROM pvms_store WHERE sub_org_id = purchase_types.sub_org_id AND pvms_id=purchase_types.pvms_id AND is_received=1) AS stock_three_month')->get();
        return $purchase_types;
    }



    // issue page function 
    public function showPurchase($id)
    {
        return Purchase::with(['purchase_pvms', 'financial_year', 'dmd_unit'])->findOrFail($id);
    }


    public function pvms_receive_voucher_no_search(Request $request)
    {
        $item = [];

        if ($request->type && $request->type == 'issued') {
            if (isset($request->unit) && auth()->user()->subOrganization->type == 'AFMSD') {
                $item = Purchase::with('purchasePvms.batchPvms', 'dmdUnit', 'purchasePvms.pvms.itemTypename', 'purchasePvms.demand', 'purchasePvms.purchaseDelivery.store.batch')
                    ->where('purchase_number', 'LIKE', '%' . $request->keyword . '%')
                    ->where('sub_org_id', $request->unit)
                    ->where('purchase_item_type', $request->type)
                    ->where('status', 'approved')
                    ->whereHas('purchasePvms.batchPvms', function ($query) {
                        $query->where('expire_date', '>', Carbon::now())
                            ->whereHas('unitStock', function ($query) {
                                $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                                    ->where('is_received', 1)
                                    ->where('sub_org_id', auth()->user()->sub_org_id)
                                    ->groupBy('batch_pvms_id');
                            });
                    })
                    ->with(['purchasePvms.batchPvms' => function ($query) {
                        $query->addSelect(DB::raw('id, pvms_id, batch_no, expire_date, qty, (SELECT CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and pvms_store.sub_org_id=' . auth()->user()->sub_org_id . ') as available_quantity'))
                            ->where('expire_date', '>', Carbon::now())
                            ->whereHas('unitStock', function ($query) {
                                $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                                    ->where('sub_org_id', auth()->user()->sub_org_id)
                                    ->where('is_received', 1)
                                    ->groupBy('batch_pvms_id')
                                    ->havingRaw('available_quantity>0');
                            })->orderBy('expire_date');
                    }])
                    ->limit(5)->get();
            } else {
                $item = Purchase::with('purchasePvms.batchPvms', 'dmdUnit', 'purchasePvms.pvms.itemTypename', 'purchasePvms.demand', 'purchasePvms.purchaseDelivery.store.batch')
                    ->where('purchase_number', 'LIKE', '%' . $request->keyword . '%')
                    ->where('status', 'approved')
                    ->where('stage', 1)
                    ->where('sub_org_id', auth()->user()->sub_org_id)
                    ->where('purchase_item_type', $request->type)
                    // ->whereHas('purchasePvms.batchPvms', function ($query) {
                    //     $query->where('expire_date', '>', Carbon::now())
                    //     ->whereHas('unitStock', function ($query) {
                    //         $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                    //         ->where('is_received',1)
                    //         ->where('sub_org_id',auth()->user()->sub_org_id)
                    //         ->groupBy('batch_pvms_id');
                    //     });
                    // })
                    ->with(['purchasePvms.batchPvms' => function ($query) {
                        $query->addSelect(DB::raw('id, pvms_id, batch_no, expire_date, qty, (SELECT CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and pvms_store.sub_org_id=' . auth()->user()->sub_org_id . ') as available_quantity'))
                            ->where('expire_date', '>', Carbon::now())
                            ->whereHas('unitStock', function ($query) {
                                $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                                    ->where('sub_org_id', auth()->user()->sub_org_id)
                                    ->where('is_received', 1)
                                    ->groupBy('batch_pvms_id')
                                    ->havingRaw('available_quantity>0');
                            })->orderBy('expire_date');
                    }])
                    ->limit(5)->get();
            }
        }
        if ($request->type && $request->type == 'lp') {
            $item = Purchase::with('dmdUnit', 'purchasePvms.pvms.itemTypename', 'purchasePvms.demand')
                ->where('purchase_number', 'LIKE', '%' . $request->keyword . '%')
                ->where('status', 'approved')
                ->where('sub_org_id', auth()->user()->sub_org_id)
                ->where('purchase_item_type', $request->type)
                ->limit(5)->get();
        }

        return $item;
    }


    // get afmsd data in a table 
    // public function getAllIssuedPurchases(Request $request)
    // {
    //     $user = auth()->user();
    //     $perPage = $request->per_page ?? 10;
    //     $search = $request->search;

    //     $query = Purchase::with('purchasePvms.batchPvms','financialYear', 'dmdUnit', 'purchasePvms.pvms.itemTypename', 'purchasePvms.demand','purchasePvms.demand.demandType', 'purchasePvms.purchaseDelivery.store.batch')
    //         ->where('status', 'approved')
    //         ->where('purchase_item_type', 'issued');

    //     // 🔍 Search by purchase number
    //     if ($search) {
    //         $query->where('purchase_number', 'LIKE', '%' . $search . '%');
    //     }

    //     // Paginate results
    //     $purchases = $query->orderBy('id', 'desc')->paginate($perPage);

    //     return response()->json($purchases);
    // }


    public function getPurchaseData($id)
    {
        $purchase = Purchase::with([
            'purchasePvms.pvms',
            'purchasePvms.pvms.accountUnit',
            'financialYear',
            'purchasePvms.pvms.itemTypename',
            'purchasePvms.demand',
            'purchasePvms.demand.demandType',
            'purchasePvms.purchaseDelivery.store.batch',
            'dmdUnit',
            'purchasePvms.batchPvms' => function ($query) {
                $query->where('expire_date', '>', Carbon::now())
                    ->select([
                        'id',
                        'pvms_id',
                        'batch_no',
                        'expire_date',
                        'qty',
                        DB::raw("(
                                SELECT CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED)
                                FROM pvms_store
                                WHERE pvms_store.batch_pvms_id = batch_pvms.id
                                AND pvms_store.sub_org_id = " . auth()->user()->sub_org_id . "
                                AND pvms_store.is_received = 1
                            ) as available_quantity")
                    ])
                    ->orderBy('expire_date');
            }
        ])
            ->where('id', $id)
            ->where('purchase_item_type', 'issued')
            ->where('status', 'approved')
            ->whereHas('purchasePvms.batchPvms', function ($query) {
                $query->where('expire_date', '>', Carbon::now())
                    ->whereHas('unitStock', function ($q) {
                        $q->where('is_received', 1)
                            ->where('sub_org_id', auth()->user()->sub_org_id)
                            ->groupBy('batch_pvms_id')
                            ->havingRaw('SUM(stock_in) - SUM(stock_out) > 0');
                    });
            })
            ->first();

        if (!$purchase) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        return response()->json($purchase);
    }
    public function getAllIssuedPurchases(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->per_page ?? 10;
        $search = $request->search;

        $query = Purchase::with([
            'purchasePvms.batchPvms',
            'financialYear',
            'dmdUnit',
            'purchasePvms.pvms.itemTypename',
            'purchasePvms.demand.demandType',
            'purchasePvms.purchaseDelivery.store.batch'
        ])
            ->where('status', 'approved')
            ->where('purchase_item_type', 'issued');

        // 🔍 Extended Search across multiple columns
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%$search%")
                    ->orWhereHas('financialYear', function ($q1) use ($search) {
                        $q1->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('dmdUnit', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('purchasePvms', function ($q3) use ($search) {
                        $q3->whereHas('pvms', function ($q4) use ($search) {
                            $q4->where('pvms_id', 'like', "%$search%")
                                ->orWhere('nomenclature', 'like', "%$search%");
                        })
                            ->orWhereHas('demand.demandType', function ($q5) use ($search) {
                                $q5->where('name', 'like', "%$search%");
                            });
                    });
            });
        }

        // Paginate results
        $purchases = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json($purchases);
    }

    // afmsd approval data for stockcontrolofficer 
    public function IssueApprovals()
    {
        $approvals = Purchase::with([
            'purchaseTypes',
            'subOrganization',
            'financialYear',
            // 'purchasePvms.pvms.itemTypename',
            // 'purchasePvms.demand.demandType',
            'purchaseTypes.purchaseDelivery',
            'purchaseTypes.demand.demandType',
            'purchaseTypes.pvms.accountUnit',
            'purchaseTypes.pvms.itemTypename',
        ])->where('afmsd_approval', 1)
            ->orderBy('id', 'desc')
            ->get();

        //         ->whereIn('afmsd_approval', [1, 2, 3]) 
        // ->orderBy('id', 'desc')
        // ->get();


        return response()->json($approvals);
    }




    // afmsd approval data for stockcontrolofficer 
    public function IssueApprovalsAfmsdCo()
    {
        $approvals = Purchase::with([
            'purchaseTypes',
            'subOrganization',
            'financialYear',
            // 'purchasePvms.pvms.itemTypename',
            // 'purchasePvms.demand.demandType',
            'purchaseTypes.purchaseDelivery',
            'purchaseTypes.demand.demandType',
            'purchaseTypes.pvms.accountUnit',
            'purchaseTypes.pvms.itemTypename',
        ])->where('afmsd_approval', 2)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($approvals);
    }

    // get data for Group incharge approval

    public function IssueApprovalsAfmsdGroupIncharge()
    {
        $approvals = Purchase::with([
            'purchaseTypes',
            'subOrganization',
            'financialYear',
            // 'purchasePvms.pvms.itemTypename',
            // 'purchasePvms.demand.demandType',
            'purchaseTypes.purchaseDelivery',
            'purchaseTypes.demand.demandType',
            'purchaseTypes.pvms.accountUnit',
            'purchaseTypes.pvms.itemTypename',
        ])->where('afmsd_approval', 3)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($approvals);
    }


    // afmsd approval stockControOfficer update purcahse table data 1 to 2 
    public function updateAfmsdApprovalStockControlOfficer($id, Request $request)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->afmsd_approval >= 1 && $purchase->afmsd_approval <= 3) {
            $purchase->afmsd_approval += 1; // increment by 1
            $purchase->save();

            return response()->json([
                'message' => "afmsd_approval updated to {$purchase->afmsd_approval}"
            ], 200);
        }

        return response()->json(['message' => 'Update not allowed'], 400);
    }

    public function updateAfmsdApprovalGroupIncharge($id, Request $request)
    {
        $purchase = Purchase::findOrFail($id);

        if ($purchase->afmsd_approval == 3) {
            $purchase->afmsd_approval = 4;
            $purchase->stage = 1;
            $purchase->save();

            return response()->json([
                'message' => "afmsd_approval updated to {$purchase->afmsd_approval}"
            ], 200);
        }

        return response()->json(['message' => 'Update not allowed'], 400);
    }

    public function purchase_pvms_delivery(Request $request)
    {
        foreach ($request->purchase_pvms as $each_purchase_pvms) {
            if (isset($each_purchase_pvms["deliver_today"]) && (int)$each_purchase_pvms["deliver_today"] > 0) {
                $purchase_type = PurchaseType::find($each_purchase_pvms["id"]);
                if ($purchase_type) {
                    $purchase = Purchase::find($purchase_type->purchase_id);
                    //Afmsd stock out
                    //Cmh stock in transition state
                    foreach ($each_purchase_pvms["batchPvmsList"] as $eachBatch) {
                        if (isset($eachBatch["batchPvms"]) && isset($eachBatch["qty"])) {
                            $batchPvms = BatchPvms::find($eachBatch["batchPvms"]);
                            $data = PvmsStore::select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                                ->where('sub_org_id', auth()->user()->sub_org_id)
                                ->where('is_received', 1)
                                ->where('batch_pvms_id', $batchPvms->id)
                                ->groupBy('batch_pvms_id')->first();

                            if ($data && $data->available_quantity > 0 &&  $data->available_quantity >= (int)$eachBatch["qty"]) {
                                $afmsd_stock_out = StockService::stockEntry(
                                    auth()->user()->sub_org_id,
                                    $purchase->sub_org_id,
                                    $purchase->id,
                                    $purchase_type->pvms_id,
                                    $batchPvms->id,
                                    0,
                                    (int)$eachBatch["qty"],
                                    1
                                );

                                $uni_stock_transit = StockService::stockEntry(
                                    auth()->user()->sub_org_id,
                                    $purchase->sub_org_id,
                                    $purchase->id,
                                    $purchase_type->pvms_id,
                                    $batchPvms->id,
                                    1,
                                    (int)$eachBatch["qty"],
                                    0
                                );

                                $purchase_type_delivery = new PurchaseTypeDelivery();
                                $purchase_type_delivery->purchase_type_id = $purchase_type->id;
                                $purchase_type_delivery->delivered_by = auth()->user()->id;
                                $purchase_type_delivery->delivered_at = Carbon::now();
                                $purchase_type_delivery->delivered_qty = (int)$eachBatch["qty"];
                                $purchase_type_delivery->pvms_store_id = $uni_stock_transit->id;
                                $purchase_type_delivery->afmsd_stock_out_id = $afmsd_stock_out->id;
                                $purchase_type_delivery->save();

                                if ((int) $data->available_quantity - (int)$eachBatch["qty"] == 0) {
                                    $batchPvms->is_afmsd_distributed = 1;
                                    $batchPvms->save();
                                }
                            }
                        }
                    }
                }
            }
        }
        return response()->json(['success' => 1], 200);
    }



    //     public function purchase_pvms_issue_delivery(Request $request)
    //     {
    //         $data = json_decode($request->getContent(), true);
    // return response()->json($data);

    //     }


    // return response()->json($data);
    public function purchase_pvms_issue_delivery(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        $qtyIssue = (int) $data['qty_issue'];
        $batchList = $data['batchList'];
        $purchaseId = $data['purchase_id'];
        $subOrgId = $data['sub_org_id'];
        $pvmsId = $data['pvms_id'];
        $remarks = $data['remarks'] ?? '';



        $purchase = Purchase::findOrFail($purchaseId);

        $purchase_type = PurchaseType::where('purchase_id', $purchaseId)
            ->where('pvms_id', $pvmsId)
            ->first();
        // dd($purchase_type->id);

        foreach ($batchList as $eachBatch) {
            if ($qtyIssue <= 0) break;

            $batchQty = (int) $eachBatch['batch']['qty'];
            // $workorder_receive_id = $eachBatch['workorder_receive_id'];

            if ($batchQty <= 0) continue;

            // Determine how much to take from this batch
            $deliverQty = min($qtyIssue, $batchQty);


            $batchPvms = BatchPvms::findOrFail($eachBatch['batch_pvms_id']);

            // Insert to stock out
            $afmsd_stock_out = StockService::stockEntry(
                auth()->user()->sub_org_id,
                $purchase->sub_org_id,
                $purchase->id,
                $pvmsId,
                $batchPvms->id,
                0, // outgoing
                $deliverQty,
                1
            );

            // Insert to transit store
            $uni_stock_transit = StockService::stockEntry(
                $purchase->sub_org_id,
                auth()->user()->sub_org_id,
                $purchase->id,
                $pvmsId,
                $batchPvms->id,
                $deliverQty,
                0,
                0
            );

            // Save delivery record
            $purchase_type_delivery = new PurchaseTypeDelivery();
            $purchase_type_delivery->purchase_type_id = $purchase_type->id;
            $purchase_type_delivery->delivered_by = auth()->user()->id;
            $purchase_type_delivery->pvms_store_id = $uni_stock_transit->id;
            $purchase_type_delivery->delivered_at = now();
            $purchase_type_delivery->delivered_qty = $deliverQty;
            $purchase_type_delivery->send_remarks = $remarks;
            $purchase_type_delivery->save();
            // Update batch if fully distributed
            if (($batchQty - $deliverQty) <= 0) {

                $batchPvms->is_afmsd_distributed = 1;
                $batchPvms->save();
            }

            // Reduce the remaining qtyIssue
            $qtyIssue -= $deliverQty;
        }
        $purchase->afmsd_approval = 1;
        $purchase->save();
        return response()->json(['message' => 'PVMS delivery completed successfully.']);
    }

    public function purchase_order_list_approval(Request $request)
    {
        foreach ($request->purchase_pvms as $each_item) {
            $purchase_pvms = PurchaseType::find($each_item["id"]);
            $purchase_pvms->status = $each_item["status"];
            $purchase_pvms->save();
        }

        $purchase = Purchase::find($request->id);
        $purchase->status = 'approved';
        $purchase->approved_by = Auth::user()->id;
        $purchase->save();

        return $purchase;
    }

    public function purchase_order_list(Request $request)
    {
        // $test = Purchase::with('dmdUnit', 'sendTo')->latest();
        // $products = Purchase::with('dmdUnit')->first();

        // dd($products);
        $purchase_order_list = Purchase::with('purchasePvms.batchPvms', 'dmdUnit', 'sendTo', 'purchasePvms.pvms.itemTypename', 'purchasePvms.demand', 'purchasePvms.purchaseDelivery')->latest();

        $perpage = 10;

        if ($request->perpage) {
            $perpage = $request->perpage;
        }

        if (auth()->user()->email == "clerk_dgms") {
            $purchase_order_list = $purchase_order_list;
        } else if (auth()->user()->subOrganization && auth()->user()->subOrganization->type) {
            if (auth()->user()->subOrganization->type == 'AFMSD' || auth()->user()->subOrganization->type == 'DGMS') {
                $purchase_order_list = $purchase_order_list->where('status', 'approved')->where('purchase_item_type', 'issued')->whereDoesntHave('purchasePvmsDeliveryComplete');
            } else {
                if ($request->type) {
                    $purchase_order_list = $purchase_order_list->where('purchase_item_type', $request->type);
                }
                $purchase_order_list = $purchase_order_list->where('sub_org_id', auth()->user()->sub_org_id);
            }
        } else {
            if ($request->type) {
                $purchase_order_list = $purchase_order_list->where('purchase_item_type', $request->type);
            }
            $purchase_order_list = $purchase_order_list->where('status', 'pending');
        }

        $purchase_order_list = $purchase_order_list->paginate($perpage);

        return $purchase_order_list;
    }

    public function purchase(Request $request)
    {
        $data = [];
        $org_name = SubOrganization::find(Auth::user()->sub_org_id);
        $org = SubOrganization::select('id', 'name')->get();

        $type = $request->type;
        $sub_org = $request->sub_org;

        // Check if the user has the 'Get Sub Organization' permission
        if (auth()->user()->can('Get Sub Organization')) {
            $sub_id = $request->sub_org;
        } else {
            $sub_id = auth()->user()->sub_org_id;
        }

        $fYear = FinancialYear::all();

        $send_to = User::whereIn('email', ['DADGMS_EQUIP', 'DADGMS_CC', 'DADGMS_PP'])->get();

        if (isset($type)) {
            $data = DemandPvms::where('demand_pvms.purchase_type', $type)
                ->leftJoin('demands', 'demands.id', '=', 'demand_pvms.demand_id')
                ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'demand_pvms.p_v_m_s_id')
                ->where('demands.sub_org_id', $sub_id)
                ->whereDoesntHave('purchaseOrderRequest')
                ->with('itemType')
                ->select('demand_pvms.*', 'demands.uuid', 'p_v_m_s.pvms_id', 'p_v_m_s.nomenclature', 'p_v_m_s.item_types_id', 'p_v_m_s.id as p_id', 'demands.id as d_id')
                ->get();
        }

        $prefix = $this->suggestedPrefix();

        return view('admin.report.purchase', compact('data', 'org_name', 'org', 'type', 'sub_org', 'sub_id', 'fYear', 'send_to', 'prefix'));
    }

    public function suggestedPrefix()
    {
        $suborg_id = Auth::user()->suborganization ? Auth::user()->suborganization->code : '';
        $suborg_id_2 = $suborg_id . Auth::user()->suborganization && Auth::user()->suborganization->divisiomFrom ? Auth::user()->suborganization->divisiomFrom->code : '';

        return '23.01.' . $suborg_id . '.' . $suborg_id_2 . '.';
    }

    public function store(Request $request)
    {
        $request->validate([
            'pvms_selected' => ['required'],
            'voucher' => ['required'],
            'fyear' => ['required'],
            'top_details' => ['required'],
            'bottom_details' => ['required'],
            'send_to' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $purchase = new Purchase();
            $purchase->sub_org_id = $request->sub_org;
            $purchase->created_at = now();
            $purchase->purchase_number = $request->voucher;
            $purchase->top_details = $request->top_details;
            $purchase->bottom_details = $request->bottom_details;
            $purchase->send_to = $request->send_to;
            $purchase->is_munir_keyboard = $request->is_munir_keyboard;
            $purchase->financial_year_id = $request->fyear;
            $purchase->purchase_item_type = $request->purchase_type;
            $purchase->save();

            $last_id = $purchase->id;
            $check_id = $request->demand_pvms_id;

            foreach ($check_id as $k => $demand_pvms_id) {
                if (in_array($demand_pvms_id, $request->pvms_selected)) {
                    $insert = new PurchaseType();
                    $insert->purchase_id = $last_id;
                    $insert->sub_org_id = $request->sub_org;
                    $insert->demand_pvms_id = $demand_pvms_id;
                    $insert->demand_id = $request->demand_id[$k];
                    $insert->pvms_id = $request->pvms_id[$k];
                    $insert->request_qty = $request->qty[$k];
                    $insert->purchase_type = $request->type[$k];
                    $insert->created_by = Auth::user()->id;
                    $insert->created_at = now();
                    $insert->save();

                    $issueOrderApproval = new IssueOrderApproval();
                    $issueOrderApproval->purchase_id = $last_id;
                    $issueOrderApproval->purchase_type_id = $insert->id;
                    $issueOrderApproval->approval_status = 'pending';
                    $issueOrderApproval->approved_by = Auth::user()->id;
                    $issueOrderApproval->approved_at = now();
                    $issueOrderApproval->send_to = $request->send_to;
                    $issueOrderApproval->step_number = 1;
                    $issueOrderApproval->note = 'Issued by Clerk DGMS';
                    $issueOrderApproval->action = 'issued';
                    $issueOrderApproval->created_at = now();
                    $issueOrderApproval->save();
                }
            }

            DB::commit();

            return redirect()->route('report.purchase')->with('message', 'Successfully Updated');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors('An error occurred: ' . $e->getMessage());
        }
    }

    public function billProcess()
    {
        $data = [];
        $years = FinancialYear::all();
        $workoderNumbers = Workorder::select('contract_number')->get();
        $crv = WorkorderReceive::select('crv_no')->whereNotNull('crv_no')->get();
        return view('admin.report.bill_process', compact('data', 'years', 'workoderNumbers', 'crv'));
    }

    public function billProcessResult(Request $request)
    {
        $data = Workorder::where('financial_year_id', $request->year_id)
            ->where('workorders.contract_number', $request->contract_number);
        if (isset($request->crv_no) && !empty($request->crv_no)) {
            $data = $data->leftJoin('workorder_receives', 'workorder_receives.workorder_id', '=', 'workorders.id')
                ->where('workorder_receives.crv_no', $request->crv_no)
                ->select('workorders.*', 'workorder_receives.crv_no', 'p_v_m_s.pvms_id as pvms', 'p_v_m_s.nomenclature', 'account_units.name', 'workorder_pvms.qty', 'workorder_pvms.total_price', 'workorder_pvms.unit_price');
        }
        $data = $data->leftJoin('workorder_pvms', 'workorder_pvms.workorder_id', '=', 'workorders.id')
            ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'workorder_pvms.pvms_id')
            ->leftJoin('account_units', 'account_units.id', '=', 'p_v_m_s.account_units_id');
        if (!isset($request->crv_no) && empty($request->crv_no)) {
            $data = $data->select('workorders.*', 'p_v_m_s.pvms_id as pvms', 'p_v_m_s.nomenclature', 'account_units.name', 'workorder_pvms.qty', 'workorder_pvms.total_price', 'workorder_pvms.unit_price');
        }
        $data = $data->get();

        $years = FinancialYear::all();
        $workoderNumbers = Workorder::select('contract_number')->get();
        $crv = WorkorderReceive::select('crv_no')->whereNotNull('crv_no')->get();
        return view('admin.report.bill_process', compact('data', 'years', 'workoderNumbers', 'crv'));
    }

    public function annualUnitDemand(Request $request)
    {
        $years = FinancialYear::get();
        $sub_orgs = SubOrganization::get();
        return view('admin.report.annual_demand_unit', compact('years', 'sub_orgs'));
    }

    public function annualUnitDemandJson(Request $request)
    {
        return $request->all();
    }
}
