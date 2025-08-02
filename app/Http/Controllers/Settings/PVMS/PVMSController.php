<?php

namespace App\Http\Controllers\Settings\PVMS;

use App\Http\Controllers\Controller;
use App\Imports\ImportPVMS;
use App\Models\PVMS;
use App\Models\AccountUnit;
use App\Models\AnnualDemand;
use App\Models\AnnualDemandPvmsUnitDemand;
use App\Models\AnnualDemandUnit;
use App\Models\BatchPvms;
use App\Models\CMHDepartmentCategory;
use App\Models\Specification;
use App\Models\ItemSections;
use App\Models\ItemDepartment;
use App\Models\ItemGroup;
use App\Models\ItemType;
use App\Models\ControlType;
use App\Models\OnLoanItem;
use App\Models\Purchase;
use App\Models\PvmsStore;
use App\Models\SubOrganization;
use App\Models\WorkorderReceivePvms;
use App\Services\AuditService;
use App\Services\PVMSService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mockery\Exception;
use Excel;
use Psy\CodeCleaner\IssetPass;
// use DB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PVMSController extends Controller
{
    public function index(Request $request): view
    {
        $limit = 20;

        if ($request->limit) {
            $limit = $request->limit;
        }

        $canAddPvmsStock = auth()->user()->can('Add PVMS Stock');

        $pvms = PVMS::orderBy('id', 'desc')->paginate($limit);

        return view('admin.PVMS.index', compact('pvms', 'canAddPvmsStock'));
    }
    public function pvms_stock(Request $request): view
    {
        $units = SubOrganization::whereIn('type', ['CMH', 'AFIP'])->get();
        $item_types = ItemType::all();
        return view('admin.PVMS.stock', compact('units', 'item_types'));
    }
    public function company_order_due(Request $request): view
    {
        return view('admin.report.company_order_due');
    }
    public function pvms_stock_transition(Request $request): view
    {
        $units = SubOrganization::whereIn('type', ['CMH', 'AFIP'])->get();
        $item_types = ItemType::all();
        return view('admin.report.unit_branch_transition', compact('units', 'item_types'));
    }
    public function pvms_stock_position(Request $request): view
    {
        $units = SubOrganization::whereIn('type', ['CMH', 'AFIP'])->get();
        $item_types = ItemType::all();
        return view('admin.PVMS.stock_position', compact('units', 'item_types'));
    }
    public function voucher_dispatch(Request $request): view
    {
        return view('admin.report.voucher_dispatch');
    }
    public function pvms_transit(Request $request): view
    {
        $units = SubOrganization::whereIn('type', ['CMH', 'AFIP'])->get();
        return view('admin.report.transit', compact('units'));
    }

    public function pvms_expire_date_wise(Request $request): view
    {
        $units = SubOrganization::whereIn('type', ['CMH', 'AFIP'])->get();
        $item_types = ItemType::all();
        return view('admin.report.expire_date_pvms', compact('units', 'item_types'));
    }
    public function pvms_on_loan(Request $request): view
    {
        return view('admin.report.on_loan');
    }
    public function pvms_on_loan_adjustment(Request $request): view
    {
        return view('admin.report.on_loan_adjust');
    }
    public function pvms_stock_batch_wise($id, Request $request): view
    {
        $sub_org_id = auth()->user()->sub_org_id;
        if (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'DGMS') {
            $sub_org_id = 2;
        }
        if (!isset(auth()->user()->subOrganization) && auth()->user()->org_id) {
            $sub_org_id = 2;
        }
        // dd($sub_org_id);
        $data = PVMS::with(['unitName', 'itemTypename', 'authorizedEquipment', 'itemGroupName', 'specificationName', 'batchList' => function ($query) use ($sub_org_id) {
            $query->addSelect(DB::raw('id, pvms_id, batch_no, expire_date, qty, (SELECT CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and pvms_store.sub_org_id=' . $sub_org_id . ') as available_quantity'))
                ->where('expire_date', '>', Carbon::now())
                ->whereHas('unitStock', function ($query) use ($sub_org_id) {
                    $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                        ->where('sub_org_id', $sub_org_id)
                        ->where('is_received', 1)
                        ->groupBy('batch_pvms_id')
                        ->havingRaw('available_quantity>0');
                });
        }])
            ->withCount(['batchList as stock_qty' => function ($query) use ($sub_org_id) {
                $query->where('expire_date', '>', Carbon::now())
                    ->select(DB::raw('SUM(CAST((SELECT SUM(stock_in) - SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id=' . $sub_org_id . ') AS SIGNED))'));
            }])
            ->where('id', $id)->first();

        $onloan_qty = PvmsStore::with('onLoanItem')->where('is_on_loan', 1)->where('pvms_id', $data->id)->whereHas('onLoanItem')->sum('stock_in');
        // $onloan_qty = PvmsStore::join('on_loan_items', 'pvms_store.on_loan_item_id', '=', 'on_loan_items.id')
        // ->where('pvms_store.is_on_loan', 1)
        // ->where('pvms_store.pvms_id', $data->id)
        // ->sum('on_loan_items.qty');
        return view('admin.PVMS.stock_batch', compact('data', 'onloan_qty'));
    }

    // public function byPvmsId(int $pvms_id)
    // {

    //     $rows = PvmsStore::with(
    //         'batch',
    //         'workorderReceivePvms.workorderReceive',
    //         'workorderReceivePvms.workorderReceive.workorder.vendor',
    //         )->where('pvms_id', $pvms_id)
    //         ->where('is_received', 1)
    //         ->where('stock_in', '>', 0)
    //         ->get();

    //     $stockOutTotal = PvmsStore::where('pvms_id', $pvms_id)
    //         ->where('is_received', 1)
    //         ->sum('stock_out');

    //     $totalStockIn = PvmsStore::where('pvms_id', $pvms_id)
    //         ->where('is_received', 1)
    //         ->sum('stock_in');

    //     return response()->json([
    //         'rows'           => $rows,
    //         'stock_out_sum'  => $stockOutTotal,
    //         'stock_in_sum'  =>  $totalStockIn,
    //     ]);
    // }

    public function byPvmsId(int $pvms_id)
    {
        $rows = PvmsStore::with(
            'batch',
            'workorderReceivePvms.workorderReceive',
            'workorderReceivePvms.workorderReceive.workorder.vendor'
        )
            ->where('pvms_id', $pvms_id)
            ->where('is_received', 1)
            ->where('stock_in', '>', 0)
            ->get();

        // Optional: filter only for batch_pvms_id not distributed
        $excludedBatchIds = DB::table('batch_pvms')
            ->where('pvms_id', $pvms_id)
            ->where('is_afmsd_distributed', 1)
            ->pluck('id')
            ->toArray();

        // Now we will calculate stock_in and stock_out grouped by batch_pvms_id
        $batchSums = DB::table('pvms_store')
            ->select(
                'batch_pvms_id',
                DB::raw('SUM(stock_in) as total_stock_in'),
                DB::raw('SUM(stock_out) as total_stock_out')
            )
            ->where('pvms_id', $pvms_id)
            ->where('is_received', 1)
            ->where('sub_org_id', 2)
            ->whereNotIn('batch_pvms_id', $excludedBatchIds)
            ->groupBy('batch_pvms_id')
            ->get()
            ->keyBy('batch_pvms_id'); // Convert to map for easy access

        // query for get contact quantity 
        $total_qty = DB::table('workorder_pvms')
            ->where('pvms_id', $pvms_id)
            ->sum('qty');

        $total_received_qty = DB::table('workorder_receive_pvms')
            ->join('workorder_pvms', 'workorder_receive_pvms.workorder_pvms_id', '=', 'workorder_pvms.id')
            ->where('workorder_pvms.pvms_id', $pvms_id)
            ->sum('workorder_receive_pvms.received_qty');

        $totals = (object)[
            'pvms_id' => $pvms_id,
            'total_qty' => $total_qty,
            'total_received_qty' => $total_received_qty,
        ];
        // Attach group summary to each row (if batch_pvms_id exists in map)
        $rows->each(function ($row) use ($batchSums, $totals) { // <-- ADD $totals here
            $batch_id = $row->batch_pvms_id;
            $row->total_stock_in = $batchSums[$batch_id]->total_stock_in ?? 0;
            $row->total_stock_out = $batchSums[$batch_id]->total_stock_out ?? 0;
            $row->available_stock = ($row->total_stock_in ?? 0) - ($row->total_stock_out ?? 0);

            $row->pvms_id = $totals->pvms_id ?? null;
            $row->contact_total_qty = $totals->total_qty ?? 0;
            $row->contact_total_received_qty = $totals->total_received_qty ?? 0;
        });

        return response()->json($rows);
    }


    // check purchasePvms item have or not in pvms_store table 
    public function checkReceivedItems(Request $request)
{
    $data = $request->input('items'); // [{ purchase_id, pvms_id }]
    $subOrgId = auth()->user()->sub_org_id;
    // dd($subOrgId);
    $received = DB::table('pvms_store')
        ->where('is_received', 1)
        ->where('sub_org_id', $subOrgId)
        ->where(function ($query) use ($data) {
            foreach ($data as $item) {
                $query->orWhere(function ($q) use ($item) {
                    $q->where('issue_voucher_id', $item['purchase_id'])
                      ->where('pvms_id', $item['pvms_id']);
                });
            }
        })
        ->select('issue_voucher_id as purchase_id', 'pvms_id')
        ->get();

    return response()->json($received);
}



    public function pvms_with_stock_of_unit(Request $request)
    {
        $pvms_list = explode(',', $request->pvms);
        $data = [];
        $subOrgId = auth()->user()->sub_org_id;

        if ($request->unit) {
            $subOrgId = $request->unit;
        }

        if (count($pvms_list) > 0) {
            $data = PVMSService::pvmsUnitWiseStock($pvms_list, $subOrgId);
        }

        return response()->json($data, 200);
    }

    public function pvms_stock_list(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;
        $sub_org_id = isset($request->sub_org_id) ? $request->sub_org_id : auth()->user()->sub_org_id;
        $item_type = $request->pvms_item_type;
        if (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'DGMS' && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }
        if (!isset(auth()->user()->subOrganization) && auth()->user()->org_id && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }
        if ($request->input('date_range')) {
            $dateRange = $request->input('date_range');
            list($startDate, $endDate) = explode(' - ', $dateRange);

            $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();

            $data = PVMS::with(['batchList', 'unitName', 'itemTypename', 'authorizedEquipment', 'itemGroupName', 'specificationName', 'batchList' => function ($query) use ($sub_org_id, $startDate, $endDate) {
                $query->addSelect(DB::raw('id, pvms_id, batch_no, expire_date, qty, (SELECT CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and pvms_store.sub_org_id=' . $sub_org_id . ' AND pvms_store.created_at BETWEEN \'' . $startDate->toDateString() . ' 00:00:00\' AND \'' . $endDate->toDateString() . ' 23:59:59\') as available_quantity'))
                    ->where('expire_date', '>', Carbon::now())
                    ->whereHas('unitStock', function ($query) use ($sub_org_id, $startDate, $endDate) {
                        $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                            ->where('sub_org_id', $sub_org_id)
                            ->where('is_received', 1)
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->groupBy('batch_pvms_id')
                            ->havingRaw('available_quantity>0');
                    });
            }])
                ->withCount(['batchList as stock_qty' => function ($query) use ($sub_org_id, $startDate, $endDate) {
                    $query->where('expire_date', '>', Carbon::now())
                        ->select(DB::raw('SUM(CAST((SELECT SUM(stock_in) - SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id=' . $sub_org_id . ' AND pvms_store.created_at BETWEEN \'' . $startDate->toDateString() . ' 00:00:00\' AND \'' . $endDate->toDateString() . ' 23:59:59\') AS SIGNED))'));
                }]);
        } else {
            $data = PVMS::with(['batchList', 'unitName', 'itemTypename', 'authorizedEquipment', 'itemGroupName', 'specificationName', 'batchList' => function ($query) use ($sub_org_id) {
                $query->addSelect(DB::raw('id, pvms_id, batch_no, expire_date, qty, (SELECT CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and pvms_store.sub_org_id=' . $sub_org_id . ') as available_quantity'))
                    ->where('expire_date', '>', Carbon::now())
                    ->whereHas('unitStock', function ($query) use ($sub_org_id) {
                        $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) as available_quantity'))
                            ->where('sub_org_id', $sub_org_id)
                            ->where('is_received', 1)
                            ->groupBy('batch_pvms_id')
                            ->havingRaw('available_quantity>0');
                    });
            }])
                ->withCount(['batchList as stock_qty' => function ($query) use ($sub_org_id) {
                    $query->where('expire_date', '>', Carbon::now())
                        ->select(DB::raw('SUM(CAST((SELECT SUM(stock_in) - SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id=' . $sub_org_id . ') AS SIGNED))'));
                }]);
        }

        if ($item_type) {
            $data = $data->where('item_types_id', $item_type);
        }

        if ($request->query('search')) {
            $data = $data->where('pvms_id', 'LIKE', '%' . $request->query('search') . '%')
                ->orWhere('nomenclature', 'LIKE', '%' . $request->query('search') . '%')
                ->orWhere('pvms_old_name', 'LIKE', '%' . $request->query('search') . '%');
        }



        $data = $data->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }

    public function pvms_stock_transition_list(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;
        $sub_org_id = isset($request->sub_org_id) ? $request->sub_org_id : auth()->user()->sub_org_id;
        $item_type = $request->pvms_item_type;
        if (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'DGMS' && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }
        if (!isset(auth()->user()->subOrganization) && auth()->user()->org_id && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }
        $data = PvmsStore::with(['ward', 'batch', 'pvms.unitName', 'pvms.itemTypename', 'pvms.itemGroupName', 'pvms.specificationName', 'unit'])->whereNotNull('branch_id');


        if ($request->input('date_range')) {
            $dateRange = $request->input('date_range');
            list($startDate, $endDate) = explode(' - ', $dateRange);
            $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();
            $data = $data->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($sub_org_id != 2) {
            $data = $data->where('sub_org_id', $sub_org_id);
        } else {
            $data = $data->where('sub_org_id', '!=', 2);
        }

        if ($item_type) {
            $data = $data->whereHas('pvms', function ($query) use ($item_type) {
                $query->where('item_types_id', $item_type);
            });
        }

        if ($request->query('search')) {
            $data = $data->whereHas('pvms', function ($query) use ($request) {
                $query->where('pvms_id', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('nomenclature', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('pvms_old_name', 'LIKE', '%' . $request->query('search') . '%');
            });
        }



        $data = $data->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }
    public function pvms_stock_position_list(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;
        $sub_org_id = $request->input('sub_org_id', auth()->user()->sub_org_id);
        $item_type = $request->input('pvms_item_type');

        if (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'DGMS' && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }
        if (!isset(auth()->user()->subOrganization) && auth()->user()->org_id && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }

        // Main query
        $data = PVMS::with([
            'batchList',
            'unitName',
            'itemTypename',
            'authorizedEquipment',
            'itemGroupName',
            'specificationName',
            'batchList' => function ($query) use ($sub_org_id) {
                $query->addSelect(DB::raw('
                    batch_pvms.id AS batch_id,
                    batch_pvms.pvms_id,
                    batch_pvms.batch_no,
                    batch_pvms.expire_date,
                    batch_pvms.qty,
                    (SELECT CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED)
                     FROM pvms_store
                     WHERE pvms_store.batch_pvms_id = batch_pvms.id
                       AND pvms_store.sub_org_id = ' . $sub_org_id . ') AS available_quantity
                '))
                    ->where('expire_date', '>', Carbon::now())
                    ->whereHas('unitStock', function ($query) use ($sub_org_id) {
                        $query->select(DB::raw('batch_pvms_id, CAST(SUM(stock_in) - SUM(stock_out) AS SIGNED) AS available_quantity'))
                            ->where('sub_org_id', $sub_org_id)
                            ->where('is_received', 1)
                            ->groupBy('batch_pvms_id')
                            ->havingRaw('available_quantity > 0');
                    });
            }
        ])
            ->withCount([
                'batchList as stock_qty' => function ($query) use ($sub_org_id) {
                    $query->where('expire_date', '>', Carbon::now())
                        ->select(DB::raw('SUM(CAST((SELECT SUM(stock_in) - SUM(stock_out)
                                                FROM pvms_store ps6
                                                WHERE ps6.batch_pvms_id = batch_pvms.id
                                                  AND is_received = 1
                                                  AND ps6.sub_org_id = ' . $sub_org_id . ') AS SIGNED))'));
                }
            ])
            ->addSelect(DB::raw('
            (SELECT MIN(batch_pvms.expire_date)
             FROM batch_pvms
             JOIN pvms_store ps7 ON batch_pvms.id = ps7.batch_pvms_id
             WHERE ps7.sub_org_id = ' . $sub_org_id . '
               AND batch_pvms.pvms_id = p_v_m_s.id
               ) AS upcoming_expire_date
        '))
            ->addSelect(DB::raw('
            (SELECT CAST(SUM(stock_in) AS SIGNED)
             FROM pvms_store ps8
             WHERE ps8.batch_pvms_id = (
                 SELECT batch_pvms.id
                 FROM batch_pvms
                 WHERE batch_pvms.pvms_id = p_v_m_s.id
                 ORDER BY ps8.created_at DESC
                 LIMIT 1
             )
               AND ps8.sub_org_id = ' . $sub_org_id . '
               AND ps8.created_at = (SELECT MAX(ps9.created_at)
                                     FROM pvms_store ps9
                                     WHERE ps9.batch_pvms_id = ps8.batch_pvms_id
                                       AND ps9.sub_org_id = ' . $sub_org_id . ')
        ) AS latest_stock_qty
        '))
            ->addSelect(DB::raw('
            (SELECT MAX(ps10.created_at)
             FROM pvms_store ps10
             WHERE ps10.batch_pvms_id = (
                 SELECT batch_pvms.id
                 FROM batch_pvms
                 WHERE batch_pvms.pvms_id = p_v_m_s.id
                 ORDER BY ps10.created_at DESC
                 LIMIT 1
             )
               AND ps10.sub_org_id = ' . $sub_org_id . '
        ) AS latest_stock_date
        '));

        // Additional logic to apply search filters and pagination
        if ($item_type) {
            $data = $data->where('item_types_id', $item_type);
        }

        if ($request->query('search')) {
            $data = $data->where('pvms_id', 'LIKE', '%' . $request->query('search') . '%')
                ->orWhere('nomenclature', 'LIKE', '%' . $request->query('search') . '%')
                ->orWhere('pvms_old_name', 'LIKE', '%' . $request->query('search') . '%');
        }

        $data = $data->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }

    public function pvms_expire_given_month_list(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;
        $sub_org_id = $request->input('sub_org_id', auth()->user()->sub_org_id);
        $item_type = $request->input('pvms_item_type');

        if (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'DGMS' && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }
        if (!isset(auth()->user()->subOrganization) && auth()->user()->org_id && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }
        $months = $request->no_of_months;

        $data = DB::table('pvms_store')
            ->join('batch_pvms', 'pvms_store.batch_pvms_id', '=', 'batch_pvms.id')
            ->join('p_v_m_s', 'pvms_store.pvms_id', '=', 'p_v_m_s.id')
            ->leftJoin('account_units', 'p_v_m_s.account_units_id', '=', 'account_units.id')
            ->leftJoin('specifications', 'p_v_m_s.specifications_id', '=', 'specifications.id')
            ->leftJoin('item_groups', 'p_v_m_s.item_groups_id', '=', 'item_groups.id')
            ->leftJoin('workorder_receives', 'pvms_store.workorder_receive_id', '=', 'workorder_receives.id')
            ->leftJoin('workorders', 'workorder_receives.workorder_id', '=', 'workorders.id')
            ->leftJoin('users', 'workorders.vendor_id', '=', 'users.id')
            ->leftJoin('financial_years', 'workorders.financial_year_id', '=', 'financial_years.id')
            ->select(
                'account_units.name as au',
                'item_groups.name as ig',
                'specifications.name as spec',
                'p_v_m_s.pvms_id as pvms_uniq_id',
                'p_v_m_s.nomenclature',
                'financial_years.name as fy',
                'users.name as vendor_name',
                'workorder_receives.crv_no',
                'pvms_store.created_at',
                'pvms_store.stock_in',
                'batch_pvms.expire_date',
                'workorders.order_no as contract_no'
            )
            ->where('pvms_store.sub_org_id', $sub_org_id)
            ->where('pvms_store.stock_in', '>', 0)
            ->where('batch_pvms.expire_date', '>', Carbon::now());

        if (isset($months)) {
            $futureDate = Carbon::now()->addMonths($months);
            $data = $data->where('batch_pvms.expire_date', '<=', $futureDate);
        }

        if (isset($item_type)) {
            $data = $data->where('p_v_m_s.item_types_id', $item_type);
        }

        if ($request->query('search')) {
            $data = $data->where('p_v_m_s.pvms_id', 'LIKE', '%' . $request->query('search') . '%')
                ->orWhere('p_v_m_s.nomenclature', 'LIKE', '%' . $request->query('search') . '%')
                ->orWhere('p_v_m_s.pvms_old_name', 'LIKE', '%' . $request->query('search') . '%');
        }


        $data = $data->orderBy('batch_pvms.expire_date')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }

    public function pvms_transit_list(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;
        $sub_org_id = isset($request->sub_org_id) ? $request->sub_org_id : auth()->user()->sub_org_id;

        if (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'DGMS' && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }

        if (!isset(auth()->user()->subOrganization) && auth()->user()->org_id && !isset($request->sub_org_id)) {
            $sub_org_id = 2;
        }


        $data = PvmsStore::with(['batch', 'pvms.unitName', 'unit'])->where('is_received', 0);

        if ($sub_org_id == 2) {
            $data = $data->where('sub_org_id', '!=', $sub_org_id);
        } else {
            $data = $data->where('sub_org_id', $sub_org_id);
        }

        if ($request->query('search')) {
            $data = $data->whereHas('pvms', function ($query) use ($request) {
                $query->where('pvms_id', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('nomenclature', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('pvms_old_name', 'LIKE', '%' . $request->query('search') . '%');
            });
        }

        $data = $data->latest()->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }
    public function pvms_on_loan_list(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;

        $data = OnLoanItem::with(['pvmsStore', 'PVMS.unitName', 'onLoan.vendor']);


        if ($request->query('search')) {
            $data = $data->whereHas('PVMS', function ($query) use ($request) {
                $query->where('pvms_id', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('nomenclature', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('pvms_old_name', 'LIKE', '%' . $request->query('search') . '%');
            });
        }

        $data = $data->latest()->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }
    public function pvms_on_loan_adjust_list(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;

        $data = WorkorderReceivePvms::with(['workorderPvms.pvms.unitName', 'onLoanItem', 'workorderPvms.workorder.vendor'])->whereNotNull('on_loan_item_id');

        if ($request->input('date_range')) {
            $dateRange = $request->input('date_range');
            list($startDate, $endDate) = explode(' - ', $dateRange);

            $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();
            $data = $data->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->query('search')) {
            $data = $data->whereHas('workorderPvms.pvms', function ($query) use ($request) {
                $query->where('pvms_id', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('nomenclature', 'LIKE', '%' . $request->query('search') . '%')
                    ->orWhere('pvms_old_name', 'LIKE', '%' . $request->query('search') . '%');
            });
        }

        $data = $data->latest()->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }
    public function voucher_dispatch_list(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;

        $data = Purchase::with(['purchasePvms', 'dmdUnit'])->where('purchase_item_type', 'issued')->withCount('purchasePvms');

        if ($request->input('date_range')) {
            $dateRange = $request->input('date_range');
            list($startDate, $endDate) = explode(' - ', $dateRange);

            $startDate = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();
            $data = $data->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->query('search')) {
            $data = $data->where('purchase_number', 'LIKE', '%' . $request->query('search') . '%');

            $data = $data->orWhereHas('dmdUnit', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->query('search') . '%');
            });
        }


        $data = $data->latest()->paginate($perPage, ['*'], 'page', $page);
        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }

    public function search(Request $request)
    {
        $pvms = PVMS::with('unitName', 'itemTypename', 'authorizedEquipment', 'itemGroupName', 'specificationName')
            ->where('pvms_id', 'LIKE', '%' . $request->keyword . '%')
            ->orWhere('nomenclature', 'LIKE', '%' . $request->keyword . '%')
            ->orWhere('pvms_old_name', 'LIKE', '%' . $request->keyword . '%')
            ->limit(50)->get();

        return $pvms;
    }
    public function search_annual_demand(Request $request)
    {
        if (isset($request->fy)) {
            $annual_demand = AnnualDemand::with('financialYear')->where('financial_year_id', $request->fy)->first();
            if (!$annual_demand) {
                return response(['message' => 'Annual demand not found'], 400);
            }

            if (!$annual_demand->is_unit_approved) {
                return response(['message' => 'Annual demand not approved yet'], 400);
            }

            $annual_demand_unit = AnnualDemandUnit::where('annual_demand_id', $annual_demand->id)->where('sub_org_id', $request->sub_org_id)->first();

            $annual_demand_unit_pvms = AnnualDemandPvmsUnitDemand::with(['annualDemandPvms.PVMS.unitName', 'annualDemandUnit.subOrganization'])
                ->where('annual_demand_unit_id', $annual_demand_unit->id)
                ->get();

            $pvms_list = [];

            foreach ($annual_demand_unit_pvms as $annual_demand_unit_pvm) {
                array_push($pvms_list, $annual_demand_unit_pvm->annualDemandPvms->pvms_id);
            }

            return [
                'annual_demand_unit_pvms' => $annual_demand_unit_pvms,
                'pvms_stock' => PVMSService::pvmsUnitWiseStock($pvms_list, $request->sub_org_id),
                'receieved_qty' => 0
            ];
        }

        return [
            'annual_demand_unit_pvms' => null,
            'pvms_stock' => null,
            'receieved_qty' => 0
        ];
    }

    public function create(): view
    {
        $units = AccountUnit::all();
        $specification = Specification::all();
        $itemSections = ItemSections::all();
        $itemDepartment = ItemDepartment::all();
        $itemGroup = ItemGroup::all();
        $itemType = ItemType::all();
        $controlType = ControlType::all();
        return view('admin.PVMS.create', compact('units', 'specification', 'itemSections', 'itemDepartment', 'itemGroup', 'itemType', 'controlType'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pvms_name' => ['required', 'string', 'max:255', 'unique:' . PVMS::class],
            'nomenclature' => ['required', 'max:255'],
            //            'specifications_id' => ['required'],
            //            'item_sections_id' => ['required'],
            'account_units_id' => ['required'],
            //            'item_groups_id' => ['required'],
            'item_types_id' => ['required'],
        ]);
        try {
            PVMSService::StoreOrUpdate($request->all());
            return redirect()->route('all.pvms')->with('message', 'Successfully Store.');
        } catch (Exception $exception) {
            return redirect()->back()->with('error', 'Fail to Store.');
        }
    }

    public function edit($id)
    {
        $pvms = PVMS::find($id);
        $units = AccountUnit::all();
        $specification = Specification::all();
        $itemSections = ItemSections::all();
        $itemDepartment = ItemDepartment::all();
        $itemGroup = ItemGroup::all();
        $itemType = ItemType::all();
        $controlType = ControlType::all();
        return view('admin.PVMS.edit', compact('pvms', 'units', 'specification', 'itemSections', 'itemDepartment', 'itemGroup', 'itemType', 'controlType'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'pvms_name' => ['required', 'string', 'max:255'],
                'nomenclature' => ['required', 'max:255'],
                //                'specifications_id' => ['required'],
                //                'item_sections_id' => ['required'],
                'account_units_id' => ['required'],
                //                'item_groups_id' => ['required'],
                'item_types_id' => ['required'],
            ]);
            $check = PVMS::where('id', '!=', $request->id)->where('pvms_name', $request->pvms_name)->first();
            if (isset($check) && !empty($check)) {
                return redirect()->back()->with('error', 'PVMS already exist.');
            }
            PVMSService::StoreOrUpdate($request->all());
            return redirect()->route('all.pvms')->with('message', 'Successfully Updated.');
        } catch (Exception $exception) {
            return redirect()->back()->with('error', 'Fail to Updated.');
        }
    }

    public function addPvmsStock($id)
    {
        $pvms = PVMS::find($id);
        return view('admin.PVMS.add-pvms-stock', compact('pvms'));
    }

    public function updatePvmsStock(Request $request)
    {
        try {
            $request->validate([
                'current_stock' => ['required', 'integer', 'min:0'],
                'batch' => ['required', 'string', 'max:255'],
                'expiry_date' => ['required', 'date'],
            ], [
                'current_stock.required' => 'The current stock is required.',
                'current_stock.integer' => 'The current stock must be a valid number.',
                'current_stock.min' => 'The current stock must be at least 0.',
                'batch.required' => 'The batch is required.',
                'batch.string' => 'The batch must be a valid string.',
                'batch.max' => 'The batch may not be greater than 255 characters.',
                'expiry_date.required' => 'The expiry date is required.',
                'expiry_date.date' => 'The expiry date must be a valid date format.',
            ]);

            $pvmsData = PVMS::where('id', $request->id)->first();

            if (!isset($pvmsData) && empty($pvmsData)) {
                return redirect()->back()->with('error', 'PVMS not found.');
            }

            $batchPVMS = new BatchPvms();

            $batchPVMS->pvms_id = $request->id;
            $batchPVMS->batch_no = $request->batch;
            $batchPVMS->expire_date = $request->expiry_date;
            $batchPVMS->qty = $request->current_stock;
            $batchPVMS->is_afmsd_distributed = 0;
            $batchPVMS->is_unit_distributed = 0;

            $batchPVMS->save();

            if ($batchPVMS) {
                $pvmsStore = new PvmsStore();

                $pvmsStore->sub_org_id = 2;
                $pvmsStore->pvms_id = $request->id;
                $pvmsStore->batch_pvms_id = $batchPVMS->id;
                $pvmsStore->stock_in = $batchPVMS->qty;
                $pvmsStore->stock_out = 0;
                $pvmsStore->is_received = 1;
                $pvmsStore->is_on_loan = 0;
                $pvmsStore->created_at = now();
                $pvmsStore->updated_at = now();

                $pvmsStore->save();
            }

            return redirect()->route('all.pvms')->with('message', 'Successfully Added.');
        } catch (Exception $exception) {
            return redirect()->back()->with('error', 'Failed to add.');
        }
    }

    public function delete($id)
    {
        try {
            $pvms = PVMS::find($id);

            $new_data = null;
            $old_data = $pvms;
            $description = 'PVMS ' . $pvms->pvms_name . ' has been deleted by ' . auth()->user()->name;
            $operation = OperationTypes::Delete;

            $pvms->deleted_by = auth()->user()->id;
            $pvms->save();
            $pvms->delete();

            AuditService::AuditLogEntry(AuditModel::PVMS, $operation, $description, $old_data, $new_data, $pvms->id);

            return redirect()->back()->with('message', 'Successfully Deleted.');
        } catch (Exception $exception) {
            return redirect()->back()->with('error', 'Fail to Deleted.');
        }
    }

    public function importView()
    {
        return view('admin.PVMS.import');
    }

    public function import(Request $request)
    {
        ini_set('max_execution_time', 5000);
        $array = Excel::toCollection(new ImportPVMS, $request->file('file'))->first();
        if (count($array) > 1) {
            $array = $array->slice(1);
            $allinterests = [];
            $notinterests = [];
            $x = 0;
            foreach ($array as $k => $pvmsData) {

                // if($k > 20000){

                // $accounts_id = AccountUnit::where('name',$pvmsData[3])->first();
                // $specification = Specification::where('name',$pvmsData[4])->first();
                // $group = ItemGroup::where('code', $pvmsData[5])->first();
                $section = ItemSections::where('code', $pvmsData[6])->first();
                // $type_id = ItemType::where('name',$pvmsData[7])->first();
                // $itemDept = ItemDepartment::where('name',$pvmsData[9])->first();
                // $control = DB::table('control_types')->where('control_type',$pvmsData[10])->first();


                // if(isset($type_id) && !empty($type_id)){
                //     $type_id = $type_id->id;
                // }else{
                //     $type_id = new ItemType();
                //     $type_id->name = $pvmsData[7];
                //     $type_id->anx = 'F';
                //     $type_id->save();
                //     $type_id = $type_id->id;
                // }

                //     $cnt = null;
                //     if(isset($control) && !empty($control)){
                //         $cnt = $control->id;
                //     }else{
                //         $cnt = DB::table('control_types')
                //         ->insertGetId([
                //             'control_type'=> $pvmsData[10],
                //         ]);
                //     }
                //     $spce = null;
                //     if(isset($specification) && !empty($specification)){
                //         $spce = $specification->id;
                //     }else{
                //         $spec = new Specification();
                //         $spec->name = $pvmsData[4];
                //         $spec->status = 1;
                //         $spec->save();
                //         $spce = $spec->id;
                //     }
                //     $dept = null;
                //     if(isset($itemDept) && !empty($itemDept)){
                //         $dept = $itemDept->id;
                //     }else{
                //         $dept = new ItemDepartment();
                //         $dept->name = $pvmsData[9];
                //         $dept->save();
                //         $dept = $dept->id;
                //     }
                //     $acc = null;
                //     if(isset($accounts_id) && !empty($accounts_id)){
                //         $acc = $accounts_id->id;
                //     }else{
                //         $acc = new AccountUnit();
                //         $acc->name = $pvmsData[3];
                //         $acc->status = 1;
                //         $acc->save();
                //         $acc = $acc->id;
                //     }

                $sec = null;
                if (isset($section->id) && !empty($section->id)) {
                    $sec = $section->id;
                } else {
                    $sec = new ItemSections();
                    $sec->name = $pvmsData[6];
                    $sec->code = $pvmsData[6];
                    $sec->status = 1;
                    $sec->save();
                    $sec = $sec->id;
                }
                // $grp = null;
                // if(isset($group->id) && !empty($group->id)){
                //     $grp = $group->id;
                // }else{
                //     $grp = new ItemGroup();
                //     $grp->name = $pvmsData[5];
                //     $grp->save();
                //     $grp = $grp->id;
                // }
                $check = PVMS::where('pvms_name', $pvmsData[11])->first();

                if (isset($check) && !empty($check)) {
                    $pvms = PVMS::find($check->id);
                    // $pvms->created_by = auth()->user()->id;
                    // $pvms->pvms_old_name = $pvmsData[1];
                    // $pvms->pvms_id = $pvmsData[11];
                    // $pvms->pvms_name = $pvmsData[11];
                    // $pvms->nomenclature = $pvmsData[2];
                    // $pvms->account_units_id = $acc;
                    // $pvms->item_types_id = $type_id;
                    // $pvms->item_departments_id = $dept;
                    // $pvms->control_types_id = $cnt;
                    // $pvms->specifications_id = $spce;
                    // $pvms->item_groups_id = $grp;
                    $pvms->item_sections_id = $sec;
                    $pvms->save();
                }
                // else{
                //     $pvms = new PVMS();
                //     $pvms->created_by = auth()->user()->id;
                //     $pvms->pvms_old_name = $pvmsData[1];
                //     $pvms->pvms_id = $pvmsData[11];
                //     $pvms->pvms_name = $pvmsData[11];
                //     $pvms->nomenclature = $pvmsData[2];
                //     $pvms->account_units_id = $acc;
                //     $pvms->item_types_id = $type_id;
                //     $pvms->item_departments_id = $dept;
                //     $pvms->control_types_id = $cnt;
                //     $pvms->specifications_id = $spce;
                //     $pvms->item_groups_id = $grp;
                //     $pvms->item_sections_id = $sec;

                //     $allinterests[] = $pvms->attributesToArray();

                // }
                // if(count($allinterests) == 900){
                //     PVMS::insert($allinterests);
                //     $allinterests = [];
                // }
                // if($k == 35114){
                //     $x = $k;
                //     PVMS::insert($allinterests);
                //     $allinterests = [];
                //     break;
                // }
                // }

            }
            dd($x);

            return redirect()->back()->with('message', 'Successfully Upload Data. Total  ' . $x);
        } else {
            return redirect()->back()->with('error', 'Fail to Upload Data');
        }
    }

    public function importViewStock()
    {
        return view('admin.PVMS.importStock');
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    public function importStock(Request $request)
    {

        ini_set('max_execution_time', 5000);
        $array = Excel::toCollection(new ImportPVMS, $request->file('file'))->first();
        if (count($array) > 1) {
            $array = $array->slice(1);
            $allinterests = [];
            $notinterests = [];
            $x = 0;
            $notInPvms = [];
            $insert_data = [];
            $stock_data = [];
            // dd($array);
            foreach ($array as $k => $arr) {

                $exp = $this->transformDate($arr[6]);

                $pvms = PVMS::where('nomenclature', $arr[1])->first();
                if (isset($pvms) && !empty($pvms)) {
                    $data = [
                        'batch_no' => rand(),
                        'pvms_id' => $pvms->id,
                        'expire_date' => $exp,
                        'qty' => $arr[4],
                    ];
                    $insert_data[] = $data;
                } else {
                    $notInPvms[] = $arr[0];
                }
            }

            $insert_data = collect($insert_data);
            $chunks = $insert_data->chunk(500);

            foreach ($chunks as $chunk) {
                BatchPvms::insert($chunk->toArray());
            }

            // stock
            foreach ($array as $k => $arr) {

                $exp = $this->transformDate($arr[6]);

                $pvms = PVMS::where('nomenclature', $arr[1])->first();
                if (isset($pvms) && !empty($pvms)) {

                    $batch_pvms_id = BatchPvms::where('pvms_id', $pvms->id)->first();
                    $sub_org_id = auth()->user()->sub_org_id;
                    $stock = [
                        'sub_org_id' => $sub_org_id,
                        'pvms_id' => $pvms->id,
                        'batch_pvms_id' => $batch_pvms_id->id,
                        'stock_in' => $arr[4],
                        'is_received' => 1,
                    ];
                    $stock_data[] = $stock;
                }
            }
            $insert = collect($stock_data);
            $chunks_data = $insert->chunk(500);

            foreach ($chunks_data as $chunk) {
                PvmsStore::insert($chunk->toArray());
            }

            echo "Not Insert PVMS List:";

            dd($notInPvms);

            //return redirect()->back()->with('message','Successfully Upload Data. Total  '.$x);
        } else {
            return redirect()->back()->with('error', 'Fail to Upload Data');
        }
    }

    public function checkDuplicate()
    {
        $arr = [];
        $check = PVMS::all();
        foreach ($check  as $c) {
            $d = PVMS::where('pvms_name', $c->pvms_name)
                ->first();
            if (isset($d) && !empty($d)) {
                array_push($arr, $d->pvms_name);
            }
        }
        echo count($arr);
        var_dump($arr);
    }

    public function createNIV()
    {

        $units = AccountUnit::all();
        $specification = Specification::all();
        $itemSections = ItemSections::all();
        $itemDepartment = ItemDepartment::all();
        $itemGroup = ItemGroup::all();
        $itemType = ItemType::all();
        $controlType = ControlType::all();
        return view('admin.PVMS.createNIV', compact('units', 'specification', 'itemSections', 'itemDepartment', 'itemGroup', 'itemType', 'controlType'));
    }

    public function storeNIV(Request $request): RedirectResponse
    {
        $request->validate([
            'pvms_name' => ['required', 'string', 'max:255'],
            'nomenclature' => ['required', 'max:255'],
            //            'specifications_id' => ['required'],
            //            'item_sections_id' => ['required'],
            'account_units_id' => ['required'],
            //            'item_groups_id' => ['required'],
            'item_types_id' => ['required'],
        ]);
        try {
            PVMSService::StoreOrUpdate($request->all());
            return redirect()->back()->with('message', 'Successfully Store.');
        } catch (Exception $exception) {
            return redirect()->back()->with('error', 'Fail to Store.');
        }
    }
}
