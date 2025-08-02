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
use App\Models\ItemType;
use App\Models\NotesheetDemandPVMS;
use App\Models\RemarksTemplate;
use App\Models\OnLoan;
use App\Models\OnLoanItem;
use App\Models\OnLoanItemReceive;
use App\Models\PvmsStore;
use App\Models\SubOrganization;
use App\Models\TenderNotesheet;
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
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Foreach_;

class DemandController extends Controller
{
    public function getSubOrganization() {}
    /**
     * Display a listing of the resource.
     */
    public function searchUserRemarks(Request $request)
    {
        $remakrs_template = RemarksTemplate::where('remarks_type', $request->type)
            ->where('user_id', auth()->user()->id)
            ->where('remarks_details', 'LIKE', '%' . $request->text . '%')
            ->limit(50)->get();

        return $remakrs_template;
    }

    public function uniq_demand_no($demand_no)
    {
        $demand_info = Demand::where('uuid', $demand_no)->first();
        return  $demand_info;
    }


    public function index(Request $request)
{
    $returnDemand = Demand::with([
        'demandPVMS',
        'demandPVMS.PVMS',
        'demandType',
        'approval',
        'demandItemType'
    ])
    ->where('sub_org_id', auth()->user()->sub_org_id)
    ->whereIn('status', ['Return', 'Return&Seen'])
    ->orderByRaw("FIELD(status, 'Return', 'Return&Seen')") 
    ->latest() 
    ->get();
    $demandStatus = Demand::where('status', 'Return')->get();




    $demands = Demand::latest()->paginate();
    $item_types = ItemType::all();
    $subOrg = SubOrganization::all();

    $is_filter_applied = false;

    if (auth()->user()->userApprovalRole) {
        if (auth()->user()->userApprovalRole->role_key == 'cmh_clark') {
            $demands = Demand::latest()->where('is_published', 0);
        } else if (auth()->user()->userApprovalRole->role_key == 'mo') {
            $demands = Demand::latest();
        } else if (auth()->user()->userApprovalRole->role_key == 'oic' || auth()->user()->userApprovalRole->role_key == 'deputy_commandend') {
            $demands = Demand::latest()->where('is_published', 1);
        } else {
            $demands = Demand::select('demands.*')
                ->where('demand_approvals.approved_by', auth()->user()->id)
                ->where('is_published', 1)
                ->groupBy('demands.id')
                ->leftJoin('demand_approvals', 'demand_approvals.demand_id', '=', 'demands.id')
                ->latest();

            $is_filter_applied = true;
        }
    } else {
        $demands = Demand::latest();
    }

    // ✅ Add this block here — before checking $is_filter_applied below
    if ($request->sub_org_id || $request->type || $request->perpage) {
        $is_filter_applied = true;
    }

    $pending_demand_ids = [];
    $pending_demands_for_user = [];

    if ($is_filter_applied) {
        $pending_demands = Demand::where('status', 'pending')->latest()->get();

        if ($request->sub_org_id) {
            $pending_demands = $pending_demands->where('sub_org_id', $request->sub_org_id);
        }

        if ($request->type) {
            $pending_demands = $pending_demands->where('demand_item_type_id', $request->type);
        }

        foreach ($pending_demands as $pending_demand) {
            $demand_approval_setps = DemandApprovalSetps::nextStepDynamic($pending_demand->id)['designation'];

            if (auth()->user()->userApprovalRole->role_key == $demand_approval_setps) {
                if ($pending_demand->demand_type_id == 4) {
                    if ($demand_approval_setps == 'hod' && auth()->user()->id == $pending_demand->hod_user_id) {
                        $pending_demands_for_user[] = $pending_demand;
                        $pending_demand_ids[] = $pending_demand->id;
                    } elseif ($demand_approval_setps == 'wing-head' && auth()->user()->id == $pending_demand->wing_user_id) {
                        $pending_demands_for_user[] = $pending_demand;
                        $pending_demand_ids[] = $pending_demand->id;
                    } else {
                        $pending_demands_for_user[] = $pending_demand;
                        $pending_demand_ids[] = $pending_demand->id;
                    }
                } else {
                    $pending_demands_for_user[] = $pending_demand;
                    $pending_demand_ids[] = $pending_demand->id;
                }
            }
        }
    } else {
        $pending_demands_for_user = [];
    }

    if (auth()->user()->subOrganization && auth()->user()->subOrganization->type) {
        if (in_array(auth()->user()->subOrganization->type, ['CMH', 'AFIP', 'AFMSD'])) {
            $demands = $demands->where('sub_org_id', auth()->user()->sub_org_id);
        }
    }

    $perpage = 10;
    if ($request->perpage) {
        $perpage = $request->perpage;
    }

    if ($request->type) {
        $demands = $demands->where('demand_item_type_id', $request->type);
    }

    if ($request->sub_org_id) {
        $demands = $demands->where('sub_org_id', $request->sub_org_id);
    }

    $demands = $demands->paginate($perpage);
    $demands = $demands->appends($request->query());

    $user_approval_role = auth()->user()->userApprovalRole;

    return view('admin.demand.index', compact(
        'demands',
        'user_approval_role',
        'item_types',
        'subOrg',
        'pending_demands_for_user',
        'is_filter_applied',
        'pending_demand_ids',
        'returnDemand',
        'demandStatus'
    ));
}

    // public function index(Request $request)
    // {
    //     $demands = Demand::latest()->paginate();
    //     $item_types = ItemType::all();
    //     $subOrg = SubOrganization::all();

    //     $is_filter_applied = false;

    //     if (auth()->user()->userApprovalRole) {
    //         if (auth()->user()->userApprovalRole->role_key == 'cmh_clark') {
    //             $demands = Demand::latest()->where('is_published', 0);
    //         } else if (auth()->user()->userApprovalRole->role_key == 'mo') {
    //             $demands = Demand::latest();
    //         } else if (auth()->user()->userApprovalRole->role_key == 'oic' || auth()->user()->userApprovalRole->role_key == 'deputy_commandend') {
    //             $demands = Demand::latest()->where('is_published', 1);
    //         } else {
    //             $demands = Demand::select('demands.*')
    //                 ->where('demand_approvals.approved_by', auth()->user()->id)
    //                 ->where('is_published', 1)
    //                 ->groupBy('demands.id')
    //                 ->leftJoin('demand_approvals', 'demand_approvals.demand_id', '=', 'demands.id')
    //                 ->latest();

    //             $is_filter_applied = true;
    //         }
    //     } else {
    //         $demands = Demand::latest();
    //     }

    //     $pending_demand_ids = [];
    //     $pending_demands_for_user = [];
    //     if ($is_filter_applied) {
    //         $pending_demands = Demand::where('status', 'pending')->latest()->get();
    //         if ($request->sub_org_id) {
    //             $pending_demands = $pending_demands->where('sub_org_id', $request->sub_org_id);
    //         }
    //         if ($request->type) {
    //             // demand_item_type_id
    //             $pending_demands = $pending_demands->where('demand_item_type_id', $request->type);
    //         }
            

    //         foreach ($pending_demands as $pending_demand) {
    //             $demand_approval_setps = DemandApprovalSetps::nextStepDynamic($pending_demand->id)['designation'];
    //             if (auth()->user()->userApprovalRole->role_key == $demand_approval_setps) {
    //                 if ($pending_demand->demand_type_id == 4) {
    //                     if ($demand_approval_setps == 'hod') {
    //                         if (auth()->user()->id == $pending_demand->hod_user_id) {
    //                             $pending_demands_for_user[] = $pending_demand;
    //                             $pending_demand_ids[] = $pending_demand->id;
    //                         }
    //                     } else if ($demand_approval_setps == 'wing-head') {
    //                         if (auth()->user()->id == $pending_demand->wing_user_id) {
    //                             $pending_demands_for_user[] = $pending_demand;
    //                             $pending_demand_ids[] = $pending_demand->id;
    //                         }
    //                     } else {
    //                         $pending_demands_for_user[] = $pending_demand;
    //                         $pending_demand_ids[] = $pending_demand->id;
    //                     }
    //                 } else {
    //                     $pending_demands_for_user[] = $pending_demand;
    //                     $pending_demand_ids[] = $pending_demand->id;
    //                 }
    //             }
    //         }
    //     } else {
    //         $pending_demands_for_user = [];
    //     }

    //     if (auth()->user()->subOrganization && auth()->user()->subOrganization->type) {
    //         if (auth()->user()->subOrganization->type == 'CMH' || auth()->user()->subOrganization->type == 'AFIP' || auth()->user()->subOrganization->type == 'AFMSD') {
    //             $demands = $demands->where('sub_org_id', auth()->user()->sub_org_id);
    //         }
    //     }
    //     $perpage = 10;
    //     if ($request->perpage) {
    //         $perpage = $request->perpage;
    //     }

    //     if ($request->type) {
    //         // demand_item_type_id
    //         $demands = $demands->where('demand_item_type_id', $request->type);
    //     }
    //    if ($request->sub_org_id) {
    //         $demands = $demands->where('sub_org_id', $request->sub_org_id);
    //     }



    //     $demands = $demands->paginate($perpage);
    //     $demands = $demands->appends($request->query());
    //     // dd($demands);


    //     $user_approval_role = auth()->user()->userApprovalRole;

    //     return view('admin.demand.index', compact('demands', 'user_approval_role', 'item_types', 'subOrg','pending_demands_for_user', 'is_filter_applied', 'pending_demand_ids'));
    // }

    public function demandTypes()
    {
        return DemandType::get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.demand.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = json_decode($request->data);

        $demandDate = \Carbon\Carbon::parse($data->demandDate)->format('Y-m-d');

        $uniqueDemandNo = $data->DemandNo . auth()->user()->sub_org_id . $demandDate;

        $existingDemand = Demand::where('uuid', $data->DemandNo)
            ->whereRaw("CONCAT(uuid, sub_org_id, demand_date) = ?", [$uniqueDemandNo])
            ->first();

        if ($existingDemand) {
            return response(['message' => 'Demand no. already exists!'], 400);
        }

        $document_name = '';

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $document_name = time() . '_' . $file->getClientOriginalName();
            MediaService::uploadFile(
                $document_name,
                'demand_documents',
                $file
            );
        }

        $demand = null;

        if (isset($data->demadType) && $data->demadType == 4) {
            $demand = DemandService::createDemand($data, $data->demand_item_type_id, $document_name);

            foreach ($data->repairPVMS as $repairPVMS) {

                $demand_repair_pvms = DemandService::createDemandRepairPvms($repairPVMS, $demand->id);
            }
        } else {
            $demand_pvms_types_set = [];

            foreach ($data->demandPVMS as $demandPVMS) {
                $demand_pvms_types_set[$demandPVMS->item_id][] = $demandPVMS;
            }

            foreach ($demand_pvms_types_set as $key => $demand_pvms_types_set) {
                $demand = DemandService::createDemand($data, $key, $document_name);

                foreach ($demand_pvms_types_set as $demandPVMS) {
                    if (!empty($demandPVMS->disease)) {
                        DemandService::createDisease($demandPVMS->disease);
                    }
                    DemandService::createDemandPvms($demandPVMS, $demand->id);
                }
            }
        }

        if (isset($demand) && $request->hasFile('document_files')) {
            $files = $request->file('document_files');
            foreach ($files as $file) {
                $document_name = time() . '_' . $file->getClientOriginalName();
                MediaService::uploadFile(
                    $document_name,
                    'demand_documents',
                    $file
                );

                $store_file = new DemandDocument();
                $store_file->demand_id = $demand->id;
                $store_file->file = $document_name;
                $store_file->created_by = auth()->user()->id;
                $store_file->save();
            }
        }
        return $data->demandDate;
    }

    // change uuid 
   public function approveChangeUuid(Request $request)
{
    // Validate input
    $request->validate([
        'demand_name' => 'required|string',
        'demand_id' => 'required|integer|exists:demands,id',
    ], [
        'demand_name.required' => 'UUID cannot be empty.',
    ]);

    $demand = Demand::find($request->demand_id);

    // Check if new UUID is different
    if ($request->demand_name !== $demand->uuid) {
        $existing = Demand::where('uuid', $request->demand_name)
            ->where('id', '!=', $request->demand_id)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'UUID already exists. Please enter a unique one.'], 422);
        }

        $demand->uuid = $request->demand_name;
        $demand->demand_date = Carbon::today()->toDateString();
        $demand->save();
    }

    return response()->json(['message' => 'UUID updated successfully.']);
}



    public function approve(Request $request)
    {
        $demand = Demand::find($request->demand['id']);
        
        $user = auth()->user();

        // OIC Check: Validate required profile fields
        if ($user && $user->userApprovalRole && $user->userApprovalRole->role_key === 'oic') {
            if (empty($user->rank) || empty($user->sign)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your Rank, or Sign is missing. Please update your profile before approving.'
                ], 422);
            }

            // Set to demand
            $demand->name = $user->name;
            $demand->rank = $user->rank;
            $demand->sign = $user->sign;
            $demand->save();
        }

        $has_notesheet = false;
        $has_issue = false;
        $has_lp = false;
        $has_on_loan = false;

        if ($request->demand['demand_type_id'] == 4) {
            foreach ($request->repairPVMS as $repairPVMS) {
                $demand_pvms = DemandRepairPvms::find($repairPVMS['id']);

                $demand_pvms->co_note = $repairPVMS['co_note'];
                $demand_pvms->co_selected = $repairPVMS['co_selected'];
                $demand_pvms->purchase_type = $repairPVMS['purchase_type'];

                $demand_pvms->save();

                if ($repairPVMS['purchase_type'] == 'notesheet') {
                    $has_notesheet = true;
                }
                if ($repairPVMS['purchase_type'] == 'issued') {
                    $has_issue = true;
                }
                if ($repairPVMS['purchase_type'] == 'lp') {
                    $has_lp = true;
                }
                if ($repairPVMS['purchase_type'] == 'on-loan') {
                    $has_on_loan = true;
                }
            }
        } else {
            foreach ($request->demandPVMS as $demandPVMS) {
                $demand_pvms = DemandPvms::find($demandPVMS['id']);
                if (auth()->user() && auth()->user()->userApprovalRole && auth()->user()->userApprovalRole->role_key && auth()->user()->userApprovalRole->role_key == 'oic') {
                    $demand_pvms->qty = $demandPVMS['qty'];
                    $demand_pvms->reviewd_qty = $demandPVMS['qty'];
                } else {
                    $demand_pvms->qty = $demandPVMS['qty'];
                    $demand_pvms->reviewd_qty = $demandPVMS['reviewd_qty'];
                }

                $demand_pvms->co_note = $demandPVMS['co_note'];
                $demand_pvms->co_selected = $demandPVMS['co_selected'];
                $demand_pvms->purchase_type = $demandPVMS['purchase_type'];

                $demand_pvms->save();

                if ($demandPVMS['purchase_type'] == 'notesheet') {
                    $has_notesheet = true;
                }
                if ($demandPVMS['purchase_type'] == 'issued') {
                    $has_issue = true;
                }
                if ($demandPVMS['purchase_type'] == 'lp') {
                    $has_lp = true;
                }
                if ($demandPVMS['purchase_type'] == 'on-loan') {
                    $has_on_loan = true;
                }
            }
        }

        $demand_purchse_type = '';
        if ($has_notesheet) {
            // notesheet
            $demand_purchse_type = 'notesheet';
        } elseif ($has_on_loan) {
            // on loan
            $demand_purchse_type = 'on-loan';
        } elseif ($has_lp) {
            // local purchase
            $demand_purchse_type = 'lp';
        } elseif ($has_issue) {
            // issued
            $demand_purchse_type = 'issued';
        } else {
            // notesheet
            $demand_purchse_type = 'notesheet';
        }

        $demand->purchase_type = $demand_purchse_type;
        $demand->save();

        $next_steps = DemandApprovalSetps::nextStepDynamic($request->demand['id']);

        $demand_approval = new DemandApproval();
        $demand_approval->demand_id = $request->demand['id'];
        $demand_approval->approved_by = auth()->user()->id;
        $demand_approval->step_number = $next_steps['step'];
        $demand_approval->role_name = $next_steps['designation'];
        $demand_approval->note = $request->approvalRemark ? $request->approvalRemark : '';
        $demand_approval->action = 'APPROVE';
        $demand_approval->save();

        $demand = Demand::find($request->demand['id']);
        $demand->total_pvmc_selected_for_notesheet = DemandPvms::where([
            'demand_id' => $request->demand['id'],
            'co_selected' => true
        ])->count();
        $user_approval_role = UserApprovalRole::where('role_key', $next_steps['designation'])->first();
        $demand->last_approved_role = $user_approval_role->role_name;

        if ($next_steps['designation'] == $next_steps['last_approval']) {
            $demand->status = 'Approved';
        }

        if ($next_steps['designation'] == 'oic-repair') {
            $demand->hod_user_id = $request->selectedHod;
        }

        if ($next_steps['designation'] == 'hod') {
            $demand->wing_user_id = $request->selectedWingUser;
        }

        $demand->save();

        return $request->demand['id'];
    }

    public function demandSendForReapprove(Request $request)
    {
        $next_steps = DemandApprovalSetps::nextStepDynamic($request->demand['id']);

        $demand_approval = new DemandApproval();
        $demand_approval->demand_id = $request->demand['id'];
        $demand_approval->approved_by = auth()->user()->id;
        $demand_approval->step_number = $next_steps['step'];
        $demand_approval->role_name = $next_steps['designation'];
        $demand_approval->note = $request->approvalRemark ? $request->approvalRemark : '';
        $demand_approval->action = 'BACK';
        $demand_approval->save();

        $demand = Demand::find($request->demand['id']);
        $demand->status = 'Reapproval';
        $demand->save();

        DemandApproval::where('demand_id', $request->demand['id'])
            ->whereNot('role_name', 'oic')
            ->update([
                'need_reapproval' => true
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $demand = Demand::find($id);

        return view('admin.demand.show', compact('demand'));
    }

    public function showApi(string $id)
    {
        $demand = Demand::with(
            'demandPVMS.PVMS.unitName',
            'demandRepairPVMS.PVMS.unitName',
            'demandRepairPVMS.PVMS.authorizedEquipment',
            'demandPVMS.centralStock',
            'demandPVMS.orgStock',
            'demandPVMS.PVMS.itemTypename',
            'approval',
            'demandItemType',
            'demandDocuments',
            'createdBy'
        )
            ->find($id);

        return $demand;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $demand = Demand::find($id);

        return view('admin.demand.edit', compact('demand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $demand = Demand::find($id);

        if ($request->demadType == 4) {
            foreach ($request->repairPVMS as $repairPVMS) {

                if ($repairPVMS) {
                    if (isset($repairPVMS['demand_pvms_id'])) {

                        $demand_repair_pvms = DemandRepairPvms::find($repairPVMS['demand_pvms_id']);
                        $demand_repair_pvms->issue_date = date('Y-m-d', strtotime($repairPVMS['issue_date']));
                        $demand_repair_pvms->installation_date = date('Y-m-d', strtotime($repairPVMS['installation_date']));
                        $demand_repair_pvms->warranty_date = date('Y-m-d', strtotime($repairPVMS['warranty_date']));
                        $demand_repair_pvms->authorized_machine = $repairPVMS['authorized_machine'];
                        $demand_repair_pvms->existing_machine = $repairPVMS['existing_machine'];
                        $demand_repair_pvms->running_machine = $repairPVMS['running_machine'];
                        $demand_repair_pvms->disabled_machine = $repairPVMS['disabled_machine'];
                        $demand_repair_pvms->approved_qty = $repairPVMS['disabled_machine'];
                        $demand_repair_pvms->supplier = $repairPVMS['supplier'];
                        $demand_repair_pvms->remarks = $repairPVMS['remarks'];

                        $demand_repair_pvms->save();
                    } else {

                        $demand_repair_pvms = new DemandRepairPvms();
                        $demand_repair_pvms->demand_id = $demand->id;
                        $demand_repair_pvms->p_v_m_s_id = $repairPVMS['id'];
                        $demand_repair_pvms->issue_date = date('Y-m-d', strtotime($repairPVMS['issue_date']));
                        $demand_repair_pvms->installation_date = date('Y-m-d', strtotime($repairPVMS['installation_date']));
                        $demand_repair_pvms->warranty_date = date('Y-m-d', strtotime($repairPVMS['warranty_date']));
                        $demand_repair_pvms->authorized_machine = $repairPVMS['authorized_machine'];
                        $demand_repair_pvms->existing_machine = $repairPVMS['existing_machine'];
                        $demand_repair_pvms->running_machine = $repairPVMS['running_machine'];
                        $demand_repair_pvms->disabled_machine = $repairPVMS['disabled_machine'];
                        $demand_repair_pvms->supplier = $repairPVMS['supplier'];
                        $demand_repair_pvms->remarks = $repairPVMS['remarks'];

                        $demand_repair_pvms->save();
                    }
                }
            }
        } else {
            foreach ($request->demandPVMS as $demandPVMS) {

                if ($demandPVMS) {
                    if (isset($demandPVMS['demand_pvms_id'])) {
                        $demand_pvms = DemandPvms::find($demandPVMS['demand_pvms_id']);
                        $demand_pvms->qty = $demandPVMS['qty'];
                        $demand_pvms->reviewd_qty = $demandPVMS['qty'];
                        $demand_pvms->patient_name = $demandPVMS['patient_name'];
                        $demand_pvms->patient_id = $demandPVMS['patient_id'];
                        $demand_pvms->disease = $demandPVMS['disease'];
                        $demand_pvms->remarks = $demandPVMS['remarks'];
                        $demand_pvms->authorized_machine = $demandPVMS['authorized_machine'];
                        $demand_pvms->existing_machine = $demandPVMS['existing_machine'];
                        $demand_pvms->running_machine = $demandPVMS['running_machine'];
                        $demand_pvms->disabled_machine = $demandPVMS['disabled_machine'];
                        $demand_pvms->ward = $demandPVMS['ward'];
                        $demand_pvms->prev_purchase = !isset($demandPVMS['prev_purchase']) ? null : $demandPVMS['prev_purchase'];
                        $demand_pvms->present_stock = !isset($demandPVMS['present_stock']) ? null : $demandPVMS['present_stock'];
                        $demand_pvms->proposed_reqr = !isset($demandPVMS['proposed_reqr']) ? null : $demandPVMS['proposed_reqr'];
                        $demand_pvms->save();
                    } else {

                        $demand_pvms = new DemandPvms();
                        $demand_pvms->demand_id = $demand->id;
                        $demand_pvms->p_v_m_s_id = $demandPVMS['id'];
                        $demand_pvms->qty = $demandPVMS['qty'];
                        $demand_pvms->reviewd_qty = $demandPVMS['qty'];
                        $demand_pvms->patient_name = $demandPVMS['patient_name'];
                        $demand_pvms->patient_id = $demandPVMS['patient_id'];
                        $demand_pvms->disease = $demandPVMS['disease'];
                        $demand_pvms->remarks = $demandPVMS['remarks'];
                        $demand_pvms->authorized_machine = $demandPVMS['authorized_machine'];
                        $demand_pvms->existing_machine = $demandPVMS['existing_machine'];
                        $demand_pvms->running_machine = $demandPVMS['running_machine'];
                        $demand_pvms->disabled_machine = $demandPVMS['disabled_machine'];
                        $demand_pvms->ward = $demandPVMS['ward'];
                        $demand_pvms->prev_purchase = empty($demandPVMS['prev_purchase']) ? null : $demandPVMS['prev_purchase'];
                        $demand_pvms->present_stock = empty($demandPVMS['present_stock']) ? null : $demandPVMS['present_stock'];
                        $demand_pvms->proposed_reqr = empty($demandPVMS['proposed_reqr']) ? null : $demandPVMS['proposed_reqr'];
                        $demand_pvms->save();
                    }
                }
            }
        }


        $demand->demand_type_id = $request->demadType;
        $demand->updated_by = auth()->user()->id;
        $demand->is_published = $request->is_published;
        $demand->demand_item_type_id = $request->demand_item_type_id;
        $demand->description = $request->description;
        $demand->description1 = isset($request->description1) ? $request->description1 : null;
        $demand->demand_category = isset($request->demand_category) ? $request->demand_category : null;
        $demand->financialYear = isset($request->fy) ? $request->fy : null;
        $demand->is_dental_type = $request->isDentalType;
        $demand->uuid = $request->DemandNo;
        $demand->save();

        if ($request->is_published) {
            if (ApprovalLayerService::isMoExists($demand->sub_org_id)) {
                $demand_approval = new DemandApproval();
                $demand_approval->demand_id = $demand->id;
                $demand_approval->approved_by = auth()->user()->id;
                $demand_approval->step_number = 1;
                $demand_approval->role_name = 'mo';
                $demand_approval->note = '';
                $demand_approval->action = 'APPROVE';
                $demand_approval->save();
            }
        }

        $demand = Demand::where('id', $id)->with('demandPVMS.PVMS.unitName', 'demandPVMS.PVMS.itemTypename', 'demandType', 'dmdUnit', 'demandItemType')->first();
        return $demand;
    }

    public function updateDocumentFile(Request $request, $demand_id)
    {
        // $file = $request->file('document_file');
        // $document_name = time().'_'.$file->getClientOriginalName();

        // MediaService::uploadFile(
        //     $document_name,
        //     'demand_documents',
        //     $file
        // );

        // $demand = Demand::find($demand_id);
        // $demand->document_file = $document_name;
        // $demand->save();

        if ($request->hasFile('document_files')) {
            DemandDocument::where('demand_id', $demand_id)->delete();
            $files = $request->file('document_files');
            foreach ($files as $file) {
                $document_name = time() . '_' . $file->getClientOriginalName();
                MediaService::uploadFile(
                    $document_name,
                    'demand_documents',
                    $file
                );

                $store_file = new DemandDocument();
                $store_file->demand_id = $demand_id;
                $store_file->file = $document_name;
                $store_file->created_by = auth()->user()->id;
                $store_file->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

        $notesheet_demand_pvms_list = NotesheetDemandPVMS::where('demand_id', $id)->get();

        foreach ($notesheet_demand_pvms_list as $each_notesheet_demand_pvms) {
            $tender_notesheet = TenderNotesheet::where('notesheet_id', $each_notesheet_demand_pvms->notesheet_id)->first();

            if ($tender_notesheet) {
                TenderService::deleteTender($tender_notesheet->tender_id);
            }

            $notesheet = NotesheetService::deleteNotesheet($each_notesheet_demand_pvms->notesheet_id);
        }

        $demand = DemandService::deleteDemand($id);

        return redirect()->route('demand.index')->with('message', 'Successfully delete.');
    }

    public function deleteDemandPVMS(string $id)
    {
        //
        $demand_pvms = DemandPvms::find($id);
        $demand_pvms->delete();

        return $demand_pvms;
    }

    public function demandStepsApi()
    {
        return [
            'EMProcurementSteps' => DemandApprovalSetps::EMProcurementSteps,
            'Dental' => DemandApprovalSetps::Dental,
            'Medicine' => DemandApprovalSetps::Medicine,
            'Reagent' => DemandApprovalSetps::Reagent,
            'Disposable' => DemandApprovalSetps::Disposable,
        ];
    }

    public function suggestedSemandNoPrefixJs()
    {
        $suborg_id = Auth::user()->suborganization ? Auth::user()->suborganization->code : '';
        $suborg_id_2 = $suborg_id . Auth::user()->suborganization && Auth::user()->suborganization->divisiomFrom ? Auth::user()->suborganization->divisiomFrom->code : '';

        return response('window.suggested_demand_no_prefix = "23.01.' . $suborg_id . $suborg_id_2 . '"')->header('Content-Type', 'application/javascript');
    }

    public function demandEditJs(Request $request)
    {
        return response('window.demand_id = ' . $request->demand_id)->header('Content-Type', 'application/javascript');
    }

    public function on_loan(Request $request)
    {
        $perpage = 10;
        if ($request->perpage) {
            $perpage = $request->perpage;
        }

        $on_loans = OnLoan::latest();
        $on_loans = $on_loans->paginate($perpage);
        $on_loans = $on_loans->appends($request->query());

        return view('admin.demand.on_loan', compact('on_loans'));
    }

    public function create_on_loan()
    {
        return view('admin.demand.create_on_loan');
    }

    public function on_loan_receive($id)
    {
        $on_loan = OnLoan::find($id);
        return view('admin.demand.on_loan_receive', compact('on_loan'));
    }

    public function on_loan_receive_into_stock(Request $request)
    {
        foreach ($request->on_loan_item_list as $eachItem) {
            if (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'AFMSD') {
                $on_loan_item = OnLoanItem::where('id', $eachItem["id"])->first();
                foreach ($eachItem["receive_today"] as $eachItemReceive) {
                    if (isset($eachItemReceive["receieved_qty"]) && $eachItemReceive["receieved_qty"] > 0 && isset($eachItemReceive["batch_no"]) && isset($eachItemReceive["expire_date"])) {
                        $batch_pvms = new BatchPvms();
                        $batch_pvms->batch_no = $eachItemReceive['batch_no'];
                        $batch_pvms->pvms_id = $eachItem['pvms_id'];
                        $batch_pvms->expire_date = $eachItemReceive['expire_date'];
                        $batch_pvms->qty = $eachItemReceive['receieved_qty'];
                        $batch_pvms->save();

                        $on_loan_item_receive = new OnLoanItemReceive();
                        $on_loan_item_receive->on_loan_item_id = $on_loan_item->id;
                        $on_loan_item_receive->receieved_qty = $eachItemReceive["receieved_qty"];
                        $on_loan_item_receive->batch_pvms_id = $batch_pvms->id;
                        $on_loan_item_receive->date = Carbon::now();
                        $on_loan_item_receive->created_by = auth()->user()->id;
                        $on_loan_item_receive->updated_by = auth()->user()->id;
                        $on_loan_item_receive->save();

                        if (isset($on_loan_item->receieved_qty)) {
                            $on_loan_item->receieved_qty = $on_loan_item->receieved_qty + $eachItemReceive["receieved_qty"];
                        } else {
                            $on_loan_item->receieved_qty = $eachItemReceive["receieved_qty"];
                        }
                        $on_loan_item->save();
                        StockService::stockEntry(
                            auth()->user()->sub_org_id,
                            null,
                            null,
                            $eachItem['pvms_id'],
                            $batch_pvms->id,
                            (int)$eachItemReceive["receieved_qty"],
                            0,
                            1,
                            null,
                            $eachItem["id"],
                            1
                        );
                    }
                }
            }
        }
    }

    public function on_loan_item_api($id)
    {
        $on_loan = OnLoan::where('id', $id)->with([
            'vendor',
            'onLoanItemList.itemReceieve',
            'onLoanItemList.PVMS',
            'onLoanItemList.pvmsStore.batch'
        ])->first();
        return $on_loan;
    }

    public function store_on_loan(Request $request)
    {

        $on_loan = new OnLoan();
        $on_loan->reference_no = $request->reference_no;
        $on_loan->vendor_id = $request->vendor_id;
        $on_loan->reference_date = $request->reference_date;
        $on_loan->created_by = auth()->user()->id;
        $on_loan->updated_by = auth()->user()->id;
        $on_loan->save();

        foreach ($request->on_loan_items as $eachItem) {
            $on_loan_item = new OnLoanItem();
            $on_loan_item->on_loan_id = $on_loan->id;
            $on_loan_item->pvms_id = $eachItem["pvms_id"];
            $on_loan_item->qty = $eachItem["qty"];
            $on_loan_item->note = $eachItem["remarks"];
            $on_loan_item->created_by = auth()->user()->id;
            $on_loan_item->updated_by = auth()->user()->id;
            $on_loan_item->save();
        }

        return response()->json($on_loan, 200);
    }

    public function onLoanStockAdjust()
    {
        return view('admin.demand.on_loan_stock_adjust');
    }

    public function onLoanStockAdjustStore(Request $request)
    {

        foreach ($request->adjustablePvms as $eachData) {
            if (count($eachData['adjusted_pvms_store_id_list']) > 0 && $eachData['today_received'] > 0) {
                $workorder_receive_pvms = new WorkorderReceivePvms();
                $workorder_receive_pvms->workorder_pvms_id = $eachData['id'];
                $workorder_receive_pvms->received_qty = $eachData['today_received'];
                $workorder_receive_pvms->on_loan_item_id = $eachData['on_loan_item_id'];
                $workorder_receive_pvms->save();

                foreach ($eachData['adjusted_pvms_store_id_list'] as $value) {
                    # code...
                    $pvms_store = PvmsStore::find($value);
                    $pvms_store->is_on_loan = 0;
                    $pvms_store->save();
                }


                return $workorder_receive_pvms;
            }
        }

        return "";
    }

    public function onLoanListJson()
    {
        return OnLoan::latest()->get();
    }


    // demand return to cmd   
    public function returnToCmd(Request $request)
    {
        $demandId = $request->input('demand_id');
        $approvedBy = $request->input('approved_by');
        $roleName = $request->input('role_name');
        $note = $request->input('note');

        $demand = Demand::find($demandId);

        if (!$demand) {
            return response()->json(['message' => 'Demand not found'], 404);
        }

        // Step 1: Update demand status
        $demand->status = 'Return';
        $demand->save();

        // Step 2: Insert into demand_approvals table
        DB::table('demand_approvals')->insert([
            'demand_id' => $demandId,
            'approved_by' => $approvedBy,
            'step_number' => 0,
            'role_name' => $roleName,
            'note' => $note,
            'need_reapproval' => 0,
            'action' => 'BACK',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Demand returned successfully']);
    }

    // update status when seen the return demand 
    public function markAsSeen(Request $request)
{
    $demand = Demand::findOrFail($request->id);

    if ($demand->status === 'Return') {
        $demand->status = 'Return&Seen';
        $demand->save();
    }

    return response()->json(['message' => 'Marked as seen']);
}



}
