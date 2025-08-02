<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\Csr;
use App\Models\PVMS;
use App\Models\Demand;
use App\Models\Tender;
use App\Models\Purchase;
use App\Models\BatchPvms;
use App\Models\Notesheet;
use App\Models\PvmsStore;
use App\Models\Workorder;
use App\Models\DemandPvms;
use Illuminate\Http\Request;
use App\Models\DemandApproval;
use App\Models\SubOrganization;
use App\Models\TenderPurchases;
use App\Models\TenderSubmittedFile;
use Illuminate\Support\Facades\Auth;
use App\Utill\Approval\CsrApprovalSetps;
use App\Utill\Approval\DemandApprovalSetps;
use App\Utill\Approval\NotesheetApprovalSetps;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function cdn_count()
    {

        $demand_count = 0;
        $notesheet_count = 0;
        $csr_count = 0;

        if (Auth::user()->is_vendor != 1) {
            $pending_demands = Demand::where('status', 'pending')->latest()->get();
            $pending_demand_ids = [];
            foreach ($pending_demands as $pending_demand) {
                $demand_approval_setps = DemandApprovalSetps::nextStepDynamic($pending_demand->id)['designation'];
                if ((auth()->user()->userApprovalRole->role_key == $demand_approval_setps)
                    &&
                    (!(auth()->user()->userApprovalRole->role_key == 'mo' || auth()->user()->userApprovalRole->role_key == 'oic' || auth()->user()->userApprovalRole->role_key == 'cmdt' || auth()->user()->userApprovalRole->role_key == 'deputy_commandend') || (((auth()->user()->userApprovalRole->role_key == 'mo' || auth()->user()->userApprovalRole->role_key == 'oic' || auth()->user()->userApprovalRole->role_key == 'cmdt' || auth()->user()->userApprovalRole->role_key == 'deputy_commandend')) && (isset(auth()->user()->sub_org_id) && auth()->user()->sub_org_id == $pending_demand->sub_org_id)))
                ) {
                    if ($pending_demand->demand_type_id == 4) {
                        if ($demand_approval_setps == 'hod') {
                            if (auth()->user()->id == $pending_demand->hod_user_id) {
                                $pending_demand_ids[] = $pending_demand->id;
                            }
                        } else if ($demand_approval_setps == 'wing-head') {
                            if (auth()->user()->id == $pending_demand->wing_user_id) {
                                $pending_demand_ids[] = $pending_demand->id;
                            }
                        } else {
                            $pending_demand_ids[] = $pending_demand->id;
                        }
                    } else {
                        $pending_demand_ids[] = $pending_demand->id;
                    }
                }
            }
            $pending_notesheet_ids = [];

            $pending_notesheets = Notesheet::with(
                'notesheetDemandPVMS.demandRepairPVMS.PVMS.unitName',
                'notesheetDemandPVMS.demandRepairPVMS.PVMS.itemTypename',
                'notesheetDemandPVMS.demandPvms.PVMS.unitName',
                'notesheetDemandPVMS.demandPvms.PVMS.itemTypename',
                'notesheetDemandPVMS.demand.dmdUnit',
                'notesheetDemandPVMS.demand.demandType',
                'notesheetType',
                'approval'
            )->where('status', 'pending')->latest()->get();

            foreach ($pending_notesheets as $pending_notesheet) {
                if (auth()->user()->userApprovalRole->role_key == NotesheetApprovalSetps::nextStep($pending_notesheet->id)['designation']) {
                    $pending_notesheet_ids[] = $pending_notesheet->id;
                }
            }

            $pending_csr_ids = [];

            $pending_csrs = Csr::with([
                'PVMS.unitName',
                'csrDemands.notesheet',
                'PVMS.itemTypename',
                'csrPvmsApproval.bidder',
                'vandorPerticipateWithValidDoc' => function ($query) {
                    $query->orderBy('offered_unit_price', 'asc'); // Specify the ordering for children
                },
                'vandorPerticipateWithValidDoc.vendor',
                'csrDemands',
                'tender',
                'hod',
                'selectedBidder.vendor'
                //  => function ($query) {
                //     $query->whereDate('deadline', '<', date('Y-m-d')); // Specify the ordering for children
                // },
            ])->whereHas('vandorPerticipateWithValidDoc')->whereHas('tender', function ($query) {
                $query->whereDate('deadline', '<', date('Y-m-d'));
            });
            if (auth()->user()->userApprovalRole && auth()->user()->userApprovalRole->role_key == 'hod') {
                $pending_csrs = $pending_csrs->where('hod_user', auth()->user()->id);
            }
            $pending_csrs = $pending_csrs->where('status', 'pending')->latest()->get();
            foreach ($pending_csrs as $pending_csr) {
                if (auth()->user()->userApprovalRole->role_key == CsrApprovalSetps::nextStep($pending_csr->id)['designation']) {
                    $pending_csr_ids[] = $pending_csr->id;
                }
            }

            $demand_count = count($pending_demand_ids);
            $notesheet_count = count($pending_notesheet_ids);
            $csr_count = count($pending_csr_ids);
        }

        return response()->json([
            'demand_count' => $demand_count,
            'notesheet_count' => $notesheet_count,
            'csr_count' => $csr_count,
        ], 200);
    }

    public function index()
    {
        if (Auth::user()->is_vendor == 1) {
            $tenders = Tender::orderBy('tenders.id', 'DESC')->where('published', 1)
                ->select('tenders.*')
                ->get();

            foreach ($tenders as $k => $tender) {
                $tenderPur = TenderPurchases::where('tender_id', $tender->id)->where('vendor_id', Auth::user()->id)->where('status', 'Success')->orderBy('id', 'desc')->first();
                $tender_submitted_files = TenderSubmittedFile::where('tender_id', $tender->id)->first();

                if (isset($tenderPur) && !empty($tenderPur)) {
                    $tenders[$k]->purchase = $tenderPur->id;
                    $tenders[$k]->status = $tenderPur->status;
                    $tenders[$k]->vendor = $tenderPur->vendor_id;
                }
                if (isset($tender_submitted_files) && !empty($tender_submitted_files)) {
                    $tenders[$k]->files_id = $tender_submitted_files->id;
                }
                $isSubmitted = TenderSubmittedFile::where('created_by', Auth::user()->id)->where('tender_id', $tender->id)->first();
                if (isset($isSubmitted) && !empty($isSubmitted)) {
                    $tenders[$k]->isSubmitted = $isSubmitted->id;
                }
            }

            $applied = TenderSubmittedFile::where('created_by', Auth::user()->id)->count('id');
            $active = Tender::whereDate('deadline', '>', date('Y-m-d'))->count('id');
            $purchase = TenderPurchases::where('vendor_id', Auth::user()->id)->count('id');

            return view('admin.vendor.dashboard.dashboard', compact('tenders', 'applied', 'active', 'purchase'));
        } else {

            $totalWorkOrder = Workorder::count();
            $appWorkorder = Workorder::where('is_adgms_approved', 1)->count();
            $DemandPendding = Demand::where('status', '!=', 'Approved')->count();
            $totalDemand = Demand::count();
            $issuePending = Purchase::where('status', '=', 'pending')->count();
            $totalIssue = Purchase::count();

            $low_stock = DB::table('pvms_store')
                ->select(\DB::raw('SUM(stock_in) - SUM(stock_out) as stock'), \DB::raw('pvms_id'))
                ->groupBy('pvms_id')
                ->limit(10)
                ->orderBy('stock', 'asc')->get();
            // dd($low_stock);
            foreach ($low_stock as $k => $q) {
                $name = PVMS::where('id', $q->pvms_id)->first();
                $low_stock[$k]->name = $name->nomenclature;
            }

            $today_date = Carbon::now()->addDays(30)->toDateTimeString();
            $expires = BatchPvms::where('batch_pvms.expire_date', '<=', $today_date)
                ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'batch_pvms.pvms_id')
                ->limit(10)
                ->orderBy('batch_pvms.id', 'asc')->select('batch_pvms.*', 'p_v_m_s.nomenclature')->get();
            foreach ($expires as $k => $ex) {
                $stock = PvmsStore::where('batch_pvms_id', $ex->id)
                    ->selectRaw('stock_in - stock_out AS stock')->first();
                $expires[$k]->stock = $stock->stock;
            }
            $subOrg = SubOrganization::where('status', 1)->pluck('id');
            // DB::table('sub_organizations')->select('id')->orderBy('id', 'asc')->get()->toArray();

            // dd($subOrg);
            $cmhLP = Purchase::whereIn('sub_org_id', $subOrg)->where('purchase_item_type', 'lp')->get();
            // dd($cmhLP);

            $notes = Notesheet::count();
            $demand = Demand::count();
            $tender = Tender::count();
            $csr = Csr::count();
            // $totalAmount = TenderPurchases::where('created_at', '>', now()->subDays(30)->endOfDay())->where('status','Success')->sum('amount');
            $totalSSL = TenderPurchases::where('created_at', '>', now()->subDays(30)->endOfDay())->where('status', 'Success')->sum('ssl_fee');
            $totalDGMS = TenderPurchases::where('created_at', '>', now()->subDays(30)->endOfDay())->where('status', 'Success')->sum('dgms_fee');
            $totalAmount = TenderPurchases::whereBetween('created_at', [now()->subYear(), now()])
                ->where('status', 'Success')
                ->select(\DB::raw('SUM(tender_fee) as amount'), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                ->groupby('year', 'month')
                ->orderBy('id', 'desc')
                ->limit(12)
                ->get();

            $sslAmount = TenderPurchases::whereBetween('created_at', [now()->subYear(), now()])
                ->where('status', 'Success')
                ->select(\DB::raw('SUM(ssl_fee) as ssl_fee'), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                ->groupby('year', 'month')
                ->orderBy('id', 'desc')
                ->limit(12)
                ->get();
            $dgmsAmount = TenderPurchases::whereBetween('created_at', [now()->subYear(), now()])
                ->where('status', 'Success')
                ->select(\DB::raw('SUM(dgms_fee) as dgms_fee'), DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                ->groupby('year', 'month')
                ->orderBy('id', 'desc')
                ->limit(12)
                ->get();


            $lpCountCMH = DemandPvms::where('demand_pvms.purchase_type', 'lp')
                ->leftJoin('demands', 'demands.id', '=', 'demand_pvms.demand_id')
                ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'demand_pvms.p_v_m_s_id')
                ->leftJoin('sub_organizations', 'sub_organizations.id', '=', 'demands.sub_org_id')
                ->groupBy('demands.sub_org_id')
                ->select('sub_organizations.name', 'demands.sub_org_id', \DB::raw('COUNT(demand_pvms.id) as t'))
                ->get();

            // dd($lpCountCMH);
            return view('admin.dashboard.index', compact('lpCountCMH', 'totalWorkOrder', 'expires', 'low_stock', 'totalIssue', 'totalDemand', 'DemandPendding', 'issuePending', 'appWorkorder', 'notes', 'demand', 'tender', 'csr', 'totalAmount', 'sslAmount', 'dgmsAmount'));
        }
    }

    public function dgmsActivity()
    {
        // dd(auth()->user()->userApprovalRole->role_key);

        $pending_demands = Demand::where('status', 'pending')->latest()->get();

        $dgmsClerkCount = Demand::where('last_approved_role', 'CMDT')->count();

        $dgmsG2CCCount = $dgmsG2PPCount = $dgmsG2EquipmentCount = $dgmsG1Count = $dgmsDyDGMSCount = 0;

        foreach ($pending_demands as $pending_demand) {
            $demand_approval_setps = DemandApprovalSetps::nextStepDynamic($pending_demand->id)['designation'];

            if ($demand_approval_setps == 'c&c') {
                $dgmsG2CCCount++;
            } elseif ($demand_approval_setps == 'p&p') {
                $dgmsG2PPCount++;
            } elseif ($demand_approval_setps == 'cgo-1') {
                $dgmsG2EquipmentCount++;
            } elseif ($demand_approval_setps == 'gso-1') {
                $dgmsG1Count++;
            } elseif ($demand_approval_setps == 'ddgms') {
                $dgmsDyDGMSCount++;
            }
        }

        $pending_notesheets = Notesheet::where('status', 'pending')->latest()->get();

        $notesheetClerkCount = 0;

        $notesheetG2CCCount = $notesheetG2PPCount = $notesheetG2EquipmentCount = $notesheetG1Count = $notesheetDyDGMSCount = $notesheetConsPhyGenCount = $notesheetConsSurGenCount = $notesheetDgmsCount = 0;

        foreach ($pending_notesheets as $pending_notesheet) {

            $next_approval_role = '';

            if ($pending_notesheet->last_approved_role == '') {
                switch ($pending_notesheet->notesheet_item_type) {
                    case 1:
                        $next_approval_role = 'cgo-1';
                        break;
                    case 3:
                    case 5:
                        $next_approval_role = 'c&c';
                        break;
                    case 4:
                        $next_approval_role = 'p&p';
                        break;
                }
            } elseif (in_array($pending_notesheet->last_approved_role, ['cgo-1', 'c&c', 'p&p'])) {
                $next_approval_role = 'gso-1';
            } elseif ($pending_notesheet->last_approved_role == 'gso-1') {
                $next_approval_role = 'ddgms';
            } elseif ($pending_notesheet->last_approved_role == 'ddgms') {
                switch ($pending_notesheet->notesheet_item_type) {
                    case 1:
                        $next_approval_role = 'csg';
                        break;
                    case 3:
                    case 4:
                        $next_approval_role = 'cpg';
                        break;
                    case 5:
                        $next_approval_role = 'csg';
                        break;
                }
            } elseif (in_array($pending_notesheet->last_approved_role, ['csg', 'cpg'])) {
                $next_approval_role = 'dgms';
            }

            if ($next_approval_role == 'c&c') {
                $notesheetG2CCCount++;
            } elseif ($next_approval_role == 'p&p') {
                $notesheetG2PPCount++;
            } elseif ($next_approval_role == 'cgo-1') {
                $notesheetG2EquipmentCount++;
            } elseif ($next_approval_role == 'gso-1') {
                $notesheetG1Count++;
            } elseif ($next_approval_role == 'ddgms') {
                $notesheetDyDGMSCount++;
            } elseif ($next_approval_role == 'cpg') {
                $notesheetConsPhyGenCount++;
            } elseif ($next_approval_role == 'csg') {
                $notesheetConsSurGenCount++;
            } elseif ($next_approval_role == 'dgms') {
                $notesheetDgmsCount++;
            }
        }


        // Tender Pie Chart
        $runningCount = Tender::all()->filter(function ($tender) {
            return $tender->start_date <= Carbon::today() && $tender->deadline >= Carbon::today();
        })->count();

        $finishedCount = Tender::withCount(['csrs' => function ($query) {
            $query->where('status', 'approved');
        }])->get()->sum('csrs_count');

        $crsCount = Tender::withCount(['csrs' => function ($query) {
            $query->where('status', '!=', 'approved');
        }])->get()->sum('csrs_count');


        $csrG2CCCount = $csrG2PPCount = $csrG2EquipmentCount = $csrG1Count = $csrHodCount = $csrDyDGMSCount = $csrConsPhyGenCount = $csrConsSurGenCount = 0;

        $pending_csrs = Csr::with(['tender.tenderNotesheet'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        foreach ($pending_csrs as $pending_csr) {
            $notesheetId = $pending_csr->tender->tenderNotesheet->first()->notesheet_id;

            $notesheetData = Notesheet::where('id', $notesheetId)->first();

            if ($notesheetData) {
                $notesheet_item_type = $notesheetData->notesheet_item_type;
            }

            $next_approval_role = '';

            if ($pending_csr->last_approval == 'head_clark') {
                switch ($notesheet_item_type) {
                    case 1:
                        $next_approval_role = 'cgo-1';
                        break;
                    case 3:
                    case 5:
                        $next_approval_role = 'c&c';
                        break;
                    case 4:
                        $next_approval_role = 'p&p';
                        break;
                }
            } elseif (in_array($pending_csr->last_approval, ['cgo-1', 'c&c', 'p&p'])) {
                $next_approval_role = 'gso-1';
            } elseif ($pending_csr->last_approval == 'gso-1') {
                $next_approval_role = 'hod';
            } elseif ($pending_csr->last_approval == 'hod') {
                $next_approval_role = 'ddgms';
            } elseif ($pending_csr->last_approval == 'ddgms') {
                switch ($notesheet_item_type) {
                    case 1:
                        $next_approval_role = 'csg';
                        break;
                    case 3:
                    case 4:
                        $next_approval_role = 'cpg';
                        break;
                    case 5:
                        $next_approval_role = 'csg';
                        break;
                }
            }

            if ($next_approval_role == 'c&c') {
                $csrG2CCCount++;
            } elseif ($next_approval_role == 'p&p') {
                $csrG2PPCount++;
            } elseif ($next_approval_role == 'cgo-1') {
                $csrG2EquipmentCount++;
            } elseif ($next_approval_role == 'gso-1') {
                $csrG1Count++;
            } elseif ($next_approval_role == 'hod') {
                $csrHodCount++;
            } elseif ($next_approval_role == 'ddgms') {
                $csrDyDGMSCount++;
            } elseif ($next_approval_role == 'cpg') {
                $csrConsPhyGenCount++;
            } elseif ($next_approval_role == 'csg') {
                $csrConsSurGenCount++;
            }
        }

        $demands_not_in_notesheet = Demand::with('subOrganization')
            ->whereNotIn('id', function ($query) {
                $query->select('demand_id')
                    ->from('notesheet_demand_pvms');
            })
            ->where('purchase_type', 'notesheet')
            ->where('last_approved_role', 'DyDGMS')
            ->get();

        $demands_not_in_notesheet_data = [];

        foreach ($demands_not_in_notesheet as $item) {
            $approval = DemandApproval::where('demand_id', $item->id)
                ->where('role_name', 'ddgms')
                ->first();

            if ($item->subOrganization && $approval) {
                $demands_not_in_notesheet_data[] = $item->uuid . ' - ' . $item->subOrganization->name . ' - App Dt: ' . $approval->created_at->format('d-m-Y');
            }
        }

        return view('admin.dashboard.dgms_activity', compact(
            'dgmsClerkCount',
            'dgmsG2CCCount',
            'dgmsG2PPCount',
            'dgmsG2EquipmentCount',
            'dgmsG1Count',
            'dgmsDyDGMSCount',

            'notesheetClerkCount',
            'notesheetG2CCCount',
            'notesheetG2PPCount',
            'notesheetG2EquipmentCount',
            'notesheetG1Count',
            'notesheetDyDGMSCount',
            'notesheetConsPhyGenCount',
            'notesheetConsSurGenCount',
            'notesheetDgmsCount',

            'crsCount',
            'runningCount',
            'finishedCount',

            'csrG2CCCount',
            'csrG2PPCount',
            'csrG2EquipmentCount',
            'csrG1Count',
            'csrHodCount',
            'csrDyDGMSCount',
            'csrConsPhyGenCount',
            'csrConsSurGenCount',

            'demands_not_in_notesheet_data',
        ));
    }

    public function pvms_with_stock_of_unit($pvms_list)
    {
        // $pvms_list = explode(',', $request->pvms);
        $data = [];
        if (count($pvms_list) > 0) {
            $data = PVMS::
                // select('id','pvms_id')->
                withCount(
                    [
                        'batchList as stock_qty' => function ($query) {
                            $query->where('expire_date', '>', Carbon::now())
                                ->select(DB::raw('SUM(CAST((SELECT SUM(stock_in) - SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id=' . auth()->user()->sub_org_id . ') AS SIGNED))'));
                        },
                        // 'batchList as afmsd_stock_qty' => function ($query) {
                        //         $query->where('expire_date', '>', Carbon::now())
                        //         ->select(DB::raw('SUM(CAST((SELECT SUM(stock_in) - SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id=2) AS SIGNED))'));
                        // },
                        // // 'batchList as last_3_month_unit_consume_qty' => function ($query) {
                        // //         $query->where('expire_date', '>', Carbon::now())
                        // //         ->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])
                        // //         ->select(DB::raw('SUM(CAST((SELECT SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id='.auth()->user()->sub_org_id.') AS SIGNED))'));
                        // // },
                        // // 'batchList as last_3_month_afmsd_consume_qty' => function ($query) {
                        // //         $query->where('expire_date', '>', Carbon::now())
                        // //         ->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])
                        // //         ->select(DB::raw('SUM(CAST((SELECT SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id=2) AS SIGNED))'));
                        // // }
                    ]
                )->whereIn('id', $pvms_list)->get();
        }

        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function search(Request $request)
    {
        $request->validate([
            'type' => ['required'],
            'search' => ['required'],
        ]);
        $type = $request->type;
        if ($request->type == 'Demand') {
            $demand = Demand::where('uuid', $request->search)->first();

            return view('admin.search.demand', compact('demand', 'type'));
        } elseif ($request->type == 'Notesheet') {
            return redirect()->back();
        } elseif ($request->type == 'CSR') {
            return redirect()->back();
        } elseif ($request->type == 'Tender') {
            return redirect()->back();
        } elseif ($request->type == 'PVMS') {
            $keyword = $request->search;
            $pvms = PVMS::where(function ($query) use ($keyword) {
                $query->where('pvms_name', 'like', '%' . $keyword . '%')
                    ->orWhere('nomenclature', 'like', '%' . $keyword . '%')
                    ->orWhere('pvms_old_name', 'like', '%' . $keyword . '%');
            })
                ->leftJoin('item_types', 'item_types.id', '=', 'p_v_m_s.item_types_id')
                ->select('p_v_m_s.*', 'item_types.name')
                ->get();

            return view('admin.search.pvms', compact('pvms', 'type'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
