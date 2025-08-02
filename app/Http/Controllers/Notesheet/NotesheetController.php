<?php

namespace App\Http\Controllers\Notesheet;

use App\Http\Controllers\Controller;
use App\Models\Demand;
use App\Models\Notesheet;
use App\Models\NotesheetApproval;
use App\Models\TenderNotesheet;
use App\Models\User;
use App\Services\NotesheetService;
use App\Services\TenderService;
use App\Utill\Approval\NotesheetApprovalSetps;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Auth;

class NotesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('admin.note_sheet.index');
    }

    public function getLoogedUserApproval()
    {
        return User::with('userApprovalRole', 'subOrganization')->where('id', auth()->user()->id)->first();
    }

    public function notesheetApprovalStepsApi()
    {
        return [
            'EMProcurementSteps' => NotesheetApprovalSetps::EMProcurementSteps,
            'Dental' => NotesheetApprovalSetps::Dental,
            'Medicine' => NotesheetApprovalSetps::Medicine,
            'Reagent' => NotesheetApprovalSetps::Reagent,
            'Disposable' => NotesheetApprovalSetps::Disposable,
        ];
    }

    public function get_all_notesheet(Request $request)
    {
        //
        $limit = 10;
        if ($request->limit) {
            $limit = $request->limit;
        }
        $notesheets = [];
        $pending_notesheets = [];
        $pending_notesheet_ids = [];
        $pending_notesheet_for_user = [];
        if (auth()->user()->userApprovalRole && auth()->user()->userApprovalRole->role_key == 'head_clark') {
            $notesheets = Notesheet::with(
                'notesheetDemandPVMS.demandPvms.PVMS.unitName',
                'notesheetDemandPVMS.demandPvms.PVMS.itemTypename',
                'notesheetDemandPVMS.demandRepairPVMS.PVMS.unitName',
                'notesheetDemandPVMS.demandRepairPVMS.PVMS.itemTypename',
                'notesheetDemandPVMS.demand.dmdUnit',
                'notesheetDemandPVMS.demand.demandType',
                'notesheetType',
                'approval'
            );
        } else if (auth()->user()->userApprovalRole) {
            $notesheets = Notesheet::select('notesheets.*')->with(
                'notesheetDemandPVMS.demandPvms.PVMS.unitName',
                'notesheetDemandPVMS.demandPvms.PVMS.itemTypename',
                'notesheetDemandPVMS.demandRepairPVMS.PVMS.unitName',
                'notesheetDemandPVMS.demandRepairPVMS.PVMS.itemTypename',
                'notesheetDemandPVMS.demand.dmdUnit',
                'notesheetDemandPVMS.demand.demandType',
                'notesheetType',
                'approval'
            )->where('notesheet_approvals.approved_by', auth()->user()->id)
                ->groupBy('notesheets.id')
                ->where('notesheet_approvals.approved_by', auth()->user()->id)
                ->leftJoin('notesheet_approvals', 'notesheet_approvals.notesheet_id', '=', 'notesheets.id');
            if (!$request->page || ($request->page && $request->page < 2)) {
                $pending_notesheets = Notesheet::with(
                    'notesheetDemandPVMS.demandRepairPVMS.PVMS.unitName',
                    'notesheetDemandPVMS.demandRepairPVMS.PVMS.itemTypename',
                    'notesheetDemandPVMS.demandPvms.PVMS.unitName',
                    'notesheetDemandPVMS.demandPvms.PVMS.itemTypename',
                    'notesheetDemandPVMS.demand.dmdUnit',
                    'notesheetDemandPVMS.demand.demandType',
                    'notesheetType',
                    'approval'
                )->where('status', 'pending')->orderBy('notesheet_date', 'DESC')->get();
                foreach ($pending_notesheets as $pending_notesheet) {
                    if (auth()->user()->userApprovalRole->role_key == NotesheetApprovalSetps::nextStep($pending_notesheet->id)['designation']) {
                        $pending_notesheet_for_user[] = $pending_notesheet;
                        $pending_notesheet_ids[] = $pending_notesheet->id;
                    }
                }
            }
        } else {
            $notesheets = Notesheet::select('notesheets.*')->with(
                'notesheetDemandPVMS.demandRepairPVMS.PVMS.unitName',
                'notesheetDemandPVMS.demandRepairPVMS.PVMS.itemTypename',
                'notesheetDemandPVMS.demandPvms.PVMS.unitName',
                'notesheetDemandPVMS.demandPvms.PVMS.itemTypename',
                'notesheetDemandPVMS.demand.dmdUnit',
                'notesheetDemandPVMS.demand.demandType',
                'notesheetType',
                'approval'
            )->where('notesheet_approvals.approved_by', auth()->user()->id)
                ->groupBy('notesheets.id')
                ->where('notesheet_approvals.approved_by', auth()->user()->id)
                ->leftJoin('notesheet_approvals', 'notesheet_approvals.notesheet_id', '=', 'notesheets.id');
        }
        $notesheets = $notesheets->orderBy('notesheet_date', 'DESC')->paginate($limit);

        $notesheet_nextsteps = [];
        $pending_notesheet_nextsteps = [];
        // dd($notesheets);

        foreach ($notesheets as $key => $notesheet) {
            $step = NotesheetApprovalSetps::nextStep($notesheet->id);
            $next_step = NotesheetApprovalSetps::nextDesignation($notesheet->id);
            $notesheet_step_with_id = [
                'id' => $notesheet->id,
                'step' => $step,
                'next_step' => $next_step
            ];
            array_push($notesheet_nextsteps, $notesheet_step_with_id);
        }

        foreach ($pending_notesheet_for_user as $key => $notesheet) {
            $step = NotesheetApprovalSetps::nextStep($notesheet->id);
            $next_step = NotesheetApprovalSetps::nextDesignation($notesheet->id);
            $notesheet_step_with_id = [
                'id' => $notesheet->id,
                'step' => $step,
                'next_step' => $next_step
            ];
            array_push($pending_notesheet_nextsteps, $notesheet_step_with_id);
        }

        return [
            'notesheets' => $notesheets,
            'notesheet_next_steps' => $notesheet_nextsteps,
            'pending_notesheet_for_user' => $pending_notesheet_for_user,
            'pending_notesheet_ids' => $pending_notesheet_ids,
            'pending_notesheet_nextsteps' => $pending_notesheet_nextsteps
        ];
    }

    public function demand_ready_for_noteshet(Request $request)
    {
        if ($request->is_repair == 1 && isset($request->item_type) && $request->item_type == 1) {
            if ($request->is_rate_running == 1) {
                return Demand::with(
                    'demandRepairPVMSRateRunningOnlyNotesheet.PVMS.unitName',
                    'demandRepairPVMSRateRunningOnlyNotesheet.PVMS.rateRunningContract',
                    'demandRepairPVMSRateRunningOnlyNotesheet.notesheet',
                    'demandRepairPVMSRateRunningOnlyNotesheet.PVMS.itemTypename',
                    'demandType',
                    'dmdUnit',
                    'demandItemType'
                )
                    ->where('demand_item_type_id', $request->item_type)
                    ->where('is_dental_type', $request->is_dental)
                    ->whereHas('demandRepairPVMSRateRunningOnlyNotesheet', function (Builder $query) {
                        $query->doesntHave('notesheet');
                    })
                    ->latest()->get();
            } else {
                return Demand::with(
                    'demandRepairPVMSOnlyNotesheetWithoutRateRunnning.PVMS.unitName',
                    'demandRepairPVMSOnlyNotesheetWithoutRateRunnning.notesheet',
                    'demandRepairPVMSOnlyNotesheetWithoutRateRunnning.PVMS.itemTypename',
                    'demandType',
                    'dmdUnit',
                    'demandItemType'
                )
                    ->where('demand_item_type_id', $request->item_type)
                    ->where('is_dental_type', $request->is_dental)
                    ->whereHas('demandRepairPVMSOnlyNotesheetWithoutRateRunnning', function (Builder $query) {
                        $query->doesntHave('notesheet');
                    })
                    ->latest()->get();
            }
        } else {
            if (isset($request->item_type)) {
                if ($request->is_rate_running == 1) {
                    return Demand::with(
                        'demandPVMSRateRunningOnlyNotesheet.PVMS.unitName',
                        'demandPVMSRateRunningOnlyNotesheet.PVMS.rateRunningContract',
                        'demandPVMSRateRunningOnlyNotesheet.notesheet',
                        'demandPVMSRateRunningOnlyNotesheet.PVMS.itemTypename',
                        'demandType',
                        'dmdUnit',
                        'demandItemType'
                    )
                        ->where('demand_item_type_id', $request->item_type)
                        ->where('is_dental_type', $request->is_dental)
                        ->whereHas('demandPVMSRateRunningOnlyNotesheet', function (Builder $query) {
                            $query->doesntHave('notesheet');
                        })->latest()->get();
                } else {
                    return Demand::with(
                        'demandPVMSOnlyNotesheet.PVMS.unitName',
                        'demandPVMSOnlyNotesheet.notesheet',
                        'demandPVMSOnlyNotesheet.PVMS.itemTypename',
                        'demandType',
                        'dmdUnit',
                        'demandItemType'
                    )
                        ->where('demand_item_type_id', $request->item_type)
                        ->where('is_dental_type', $request->is_dental)
                        ->whereHas('demandPVMSOnlyNotesheet', function (Builder $query) {
                            $query->doesntHave('notesheet');
                        })->latest()->get();
                }
            } else {
                return Demand::with('demandPVMSOnlyNotesheet.PVMS.unitName', 'demandPVMSOnlyNotesheet.PVMS.itemTypename', 'demandType', 'dmdUnit', 'demandItemType')->whereHas('demandPVMSOnlyNotesheet')->latest()->get();
            }
        }
    }
    public function demand_ready_for_noteshet_re_tender(Request $request)
    {
        if ($request->is_repair == 1 && isset($request->item_type) && $request->item_type == 1) {
            if ($request->is_rate_running == 1) {
                return Demand::with(
                    'demandRepairPVMSRateRunningOnlyNotesheet.PVMS.unitName',
                    'demandRepairPVMSRateRunningOnlyNotesheet.PVMS.rateRunningContract',
                    'demandRepairPVMSRateRunningOnlyNotesheet.notesheet',
                    'demandRepairPVMSRateRunningOnlyNotesheet.PVMS.itemTypename',
                    'demandType',
                    'dmdUnit',
                    'demandItemType'
                )
                    ->where('demand_item_type_id', $request->item_type)
                    ->where('is_dental_type', $request->is_dental)
                    ->whereHas('demandRepairPVMSRateRunningOnlyNotesheet', function (Builder $query) {
                        $query->has('notesheet');
                    })
                    ->latest()->get();
            } else {
                return Demand::with(
                    'demandRepairPVMSOnlyNotesheetWithoutRateRunnning.PVMS.unitName',
                    'demandRepairPVMSOnlyNotesheetWithoutRateRunnning.notesheet',
                    'demandRepairPVMSOnlyNotesheetWithoutRateRunnning.PVMS.itemTypename',
                    'demandType',
                    'dmdUnit',
                    'demandItemType'
                )
                    ->where('demand_item_type_id', $request->item_type)
                    ->where('is_dental_type', $request->is_dental)
                    ->whereHas('demandRepairPVMSOnlyNotesheetWithoutRateRunnning', function (Builder $query) {
                        $query->has('notesheet');
                    })
                    ->latest()->get();
            }
        } else {
            if (isset($request->item_type)) {
                if ($request->is_rate_running == 1) {
                    return Demand::with(
                        'demandPVMSRateRunningOnlyNotesheet.PVMS.unitName',
                        'demandPVMSRateRunningOnlyNotesheet.PVMS.rateRunningContract',
                        'demandPVMSRateRunningOnlyNotesheet.notesheet',
                        'demandPVMSRateRunningOnlyNotesheet.PVMS.itemTypename',
                        'demandType',
                        'dmdUnit',
                        'demandItemType'
                    )
                        ->where('demand_item_type_id', $request->item_type)
                        ->where('is_dental_type', $request->is_dental)
                        ->whereHas('demandPVMSRateRunningOnlyNotesheet', function (Builder $query) {
                            $query->has('notesheet');
                        })->latest()->get();
                } else {
                    return Demand::with(
                        'demandPVMSOnlyNotesheet.PVMS.unitName',
                        'demandPVMSOnlyNotesheet.notesheet',
                        'demandPVMSOnlyNotesheet.PVMS.itemTypename',
                        'demandType',
                        'dmdUnit',
                        'demandItemType'
                    )
                        ->where('demand_item_type_id', $request->item_type)
                        ->where('is_dental_type', $request->is_dental)
                        ->whereHas('demandPVMSOnlyNotesheet', function (Builder $query) {
                            $query->has('notesheet');
                        })->latest()->get();
                }
            } else {
                return Demand::with('demandPVMSOnlyNotesheet.PVMS.unitName', 'demandPVMSOnlyNotesheet.PVMS.itemTypename', 'demandType', 'dmdUnit', 'demandItemType')->whereHas('demandPVMSOnlyNotesheet')->latest()->get();
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.note_sheet.create');
    }

    public function re_tender()
    {
        //
        return view('admin.note_sheet.re_tender');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function uniq_notesheet_no($notesheet_no)
    {
        $notesheet_info = Notesheet::where('notesheet_id', $notesheet_no)->first();
        return  $notesheet_info;
    }
    public function store(Request $request)
    {
        $notesheet = NotesheetService::createNotesheet($request->all());

        foreach ($request->notesheetDemandList as $notesheetPVMSDemandList) {
            foreach ($notesheetPVMSDemandList["demands"] as $notesheetPvmsDemand) {
                NotesheetService::createNotesheetDemandPVMS($notesheet->id, $notesheetPvmsDemand["id"], $notesheetPvmsDemand["demand_id"], $notesheetPvmsDemand["p_v_m_s_id"], $request->is_repair == 1 ? $notesheetPvmsDemand["approved_qty"] : $notesheetPvmsDemand["qty"], $request->is_repair, $notesheetPVMSDemandList["unit_price"]);
            }
        }

        return $notesheet;
    }

    public function approve(Request $request)
    {

        $notesheet = Notesheet::find($request->notesheet['id']);
        $next_steps = NotesheetApprovalSetps::nextStep($request->notesheet['id']);

        $notesheet_approval = new NotesheetApproval();
        $notesheet_approval->notesheet_id = $request->notesheet['id'];
        $notesheet_approval->approved_by = auth()->user()->id;
        $notesheet_approval->step_number = $next_steps['step'];
        $notesheet_approval->role_name = $next_steps['designation'];
        $notesheet_approval->note = $request->remark;
        $notesheet_approval->save();

        $notesheet = Notesheet::find($request->notesheet['id']);
        $notesheet->last_approved_role = $next_steps['designation'];
        if ($next_steps['designation'] == 'dgms') {
            $notesheet->status = 'approved';
        }
        $notesheet->save();

        return $request->notesheet['id'];
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
        $tender_notesheet = TenderNotesheet::where('notesheet_id', $id)->first();

        if ($tender_notesheet) {
            TenderService::deleteTender($tender_notesheet->tender_id);
        }
        $notesheet = NotesheetService::deleteNotesheet($id);
        return response()->json($notesheet, 200);
    }

    public function suggested_notesheet_no_prefix_js(Request $request)
    {
        $s1 = Auth::user()->suborganization ? Auth::user()->suborganization->code : '';
        $s2 = Auth::user()->suborganization && Auth::user()->suborganization->divisiomFrom ? Auth::user()->suborganization->divisiomFrom->code : '';

        if ($request->tender_id) {
            return response('window.suggested_notesheet_no_prefix = "23.01.' . $s1 . $s2 . '";window.tender_id = ' . $request->tender_id . ';window.suggested_tender_no_prefix = "23.01.' . $s1 . $s2 . '"')->header('Content-Type', 'application/javascript');
        }

        if ($request->on_loan_id) {
            return response('window.suggested_notesheet_no_prefix = "23.01.' . $s1 . $s2 . '";window.on_loan_id = ' . $request->on_loan_id . ';window.suggested_tender_no_prefix = "23.01.' . $s1 . $s2 . '"')->header('Content-Type', 'application/javascript');
        }

        return response('window.suggested_notesheet_no_prefix = "23.01.' . $s1 . $s2 . '";window.suggested_tender_no_prefix = "23.01.' . $s1 . $s2 . '"')->header('Content-Type', 'application/javascript');
    }
}
