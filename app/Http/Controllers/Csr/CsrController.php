<?php

namespace App\Http\Controllers\Csr;

use App\Http\Controllers\Controller;
use App\Models\Csr;
use App\Models\CsrApproval;
use App\Models\CsrCoverLetter;
use App\Models\CsrCoverLetterCoOperativeMember;
use App\Models\CsrCoverLetterMember;
use App\Models\CsrCoverLetterPresident;
use App\Models\Tender;
use App\Models\User;
use App\Utill\Approval\CsrApprovalSetps;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CsrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.csr.index');
    }

    public function csr_cover_letter()
    {
        return view('admin.csr.cover');
    }

    public function get_all_tender_of_completed_csr(Request $request)
    {
        $tenders = Tender::with([
            'tenderCsr.PVMS.unitName',
            'tenderCsr.PVMS.itemTypename',
            'tenderCsr.csrPvmsApproval.approved_by',
            'tenderCsr.vandorPerticipate.vendor',
            'tenderCsr.csrDemands',
            'vendorSubmittedFiles.requiredDocument',
            'vendorSubmittedFiles.validateBy',
            'tenderPayments' => function ($query) {
                $query->where('status', 'Success'); // Specify the ordering for children
            },
        ])->whereDoesntHave('coverLetter');
        // ->whereDoesntHave('coverLetter')->whereDoesntHave('tenderCsr', function ($query) {
        //     $query->where('status', '!=', 'approved');
        // });

        if ($request->keyword) {
            $tenders = $tenders->where('tender_no', 'like', '%' . $request->keyword . '%');
        }
        $tenders = $tenders->latest()->limit(5)->get();

        return $tenders;
    }

    public function save_cover_letter(Request $request)
    {

        $csr_cover_letter = new CsrCoverLetter();
        $csr_cover_letter->tender_id = $request->tender;
        $csr_cover_letter->details = $request->details;
        $csr_cover_letter->save();

        foreach ($request->president as $eachData) {
            $cover_leter_president = new CsrCoverLetterPresident();
            $cover_leter_president->csr_cover_letter_id = $csr_cover_letter->id;
            $cover_leter_president->user_id = $eachData["value"];
            $cover_leter_president->save();
        }

        foreach ($request->member as $eachData) {
            $cover_leter_member = new CsrCoverLetterMember();
            $cover_leter_member->csr_cover_letter_id = $csr_cover_letter->id;
            $cover_leter_member->user_id = $eachData["value"];
            $cover_leter_member->save();
        }

        foreach ($request->co_operative_member as $eachData) {
            $cover_leter_co_member = new CsrCoverLetterCoOperativeMember();
            $cover_leter_co_member->csr_cover_letter_id = $csr_cover_letter->id;
            $cover_leter_co_member->user_id = $eachData["value"];
            $cover_leter_co_member->save();
        }

        return $csr_cover_letter;
    }

    public function get_hod_users(Request $request)
    {
        if ($request->keyword) {
            return User::where('user_approval_role_id', 16)->where('name', 'LIKE', '%' . $request->keyword . '%')->orWhere('email', 'LIKE', '%' . $request->keyword)->limit(10)->get();
        }
        return User::where('user_approval_role_id', 16)->limit(10)->get();
    }

    public function get_tender_ready_for_csr(Request $request)
    {
        if ($request->keyword) {
            return Tender::where('tender_no', 'LIKE', '%' . $request->keyword . '%')
                // ->where('status','pending')
                // ->whereDate('deadline', '<', date('Y-m-d'))
                ->limit(5)->get();
        }
        return Tender::
            // where('status','pending')->
            // whereDate('deadline', '<', date('Y-m-d'))->
            limit(5)->get();
    }

    public function csrApprovalStepsApi()
    {
        return [
            'EMProcurementSteps' => CsrApprovalSetps::EMProcurementSteps,
            'Dental' => CsrApprovalSetps::Dental,
            'Medicine' => CsrApprovalSetps::Medicine,
            'Reagent' => CsrApprovalSetps::Reagent,
            'Disposable' => CsrApprovalSetps::Disposable,
        ];
    }

    public function get_tender_with_csr_pvms(Request $request)
    {
        $limit = 10;
        if ($request->limit) {
            $limit = $request->limit;
        }
        $csr_list = [];
        $pending_csrs = [];
        $pending_csr_ids = [];
        $pending_csr_for_user = [];
        if (auth()->user()->userApprovalRole && auth()->user()->userApprovalRole->role_key == 'hod') {
            $csr_list = Csr::with([
                'PVMS.unitName',
                'csrDemands.notesheet',
                'PVMS.itemTypename',
                'csrPvmsApproval.bidder.vendor',
                'vandorPerticipateWithValidDoc' => function ($query) {
                    $query->orderBy('offered_unit_price', 'asc'); // Specify the ordering for children
                },
                'vandorPerticipateWithValidDoc.vendor',
                'csrDemands',
                'tender',
                'hod',
                'selectedBidder.vendor'
                //  => function ($query) {
                //     $query->whereDate('deadline', '<', Carbon::now()); // Specify the ordering for children
                // },
            ])->whereHas('vandorPerticipateWithValidDoc')->where('hod_user', auth()->user()->id)->whereHas('tender', function ($query) {
                $query->where('deadline', '<', Carbon::now());
            })->latest()->paginate($limit);
        } else if (auth()->user()->userApprovalRole && auth()->user()->userApprovalRole->role_key == 'head_clark') {
            $csr_list = Csr::with([
                'PVMS.unitName',
                'csrDemands.notesheet',
                'PVMS.itemTypename',
                'csrPvmsApproval.bidder.vendor',
                'vandorPerticipateWithValidDoc' => function ($query) {
                    $query->orderBy('offered_unit_price', 'asc'); // Specify the ordering for children
                },
                'vandorPerticipateWithValidDoc.vendor',
                'csrDemands',
                'tender',
                'hod',
                'selectedBidder.vendor'
                //  => function ($query) {
                //     $query->whereDate('deadline', '<', Carbon::now()); // Specify the ordering for children
                // },
            ])->whereHas('vandorPerticipateWithValidDoc')
                ->whereHas('tender', function ($query) {
                    $query->where('deadline', '<', Carbon::now());
                })->latest()->paginate($limit);
        } else if (auth()->user()->userApprovalRole) {
            $csr_list = Csr::select('csr.*')->with([
                'PVMS.unitName',
                'csrDemands.notesheet',
                'PVMS.itemTypename',
                'csrPvmsApproval.bidder.vendor',
                'vandorPerticipateWithValidDoc' => function ($query) {
                    $query->orderBy('offered_unit_price', 'asc'); // Specify the ordering for children
                },
                'vandorPerticipateWithValidDoc.vendor',
                'csrDemands',
                'tender',
                'hod',
                'selectedBidder.vendor'
                //  => function ($query) {
                //     $query->whereDate('deadline', '<', Carbon::now()); // Specify the ordering for children
                // },
            ])->whereHas('vandorPerticipateWithValidDoc')->whereHas('tender', function ($query) {
                $query->where('deadline', '<', Carbon::now());
            })
                ->groupBy('csr.id')
                ->leftJoin('csr_approvals', 'csr_approvals.csr_id', '=', 'csr.id')
                ->where('csr_approvals.approved_by', auth()->user()->id)
                ->latest()->paginate($limit);
            // dd($csr_list);
            if (!$request->page || ($request->page && $request->page < 2)) {
                $pending_csrs = Csr::with([
                    'PVMS.unitName',
                    'csrDemands.notesheet',
                    'PVMS.itemTypename',
                    'csrPvmsApproval.bidder.vendor',
                    'vandorPerticipateWithValidDoc' => function ($query) {
                        $query->orderBy('offered_unit_price', 'asc'); // Specify the ordering for children
                    },
                    'vandorPerticipateWithValidDoc.vendor',
                    'csrDemands',
                    'tender',
                    'hod',
                    'selectedBidder.vendor'
                    //  => function ($query) {
                    //     $query->whereDate('deadline', '<', Carbon::now()); // Specify the ordering for children
                    // },
                ])->whereHas('vandorPerticipateWithValidDoc')->whereHas('tender', function ($query) {
                    $query->where('deadline', '<', Carbon::now());
                })->where('status', 'pending')->latest()->get();
                foreach ($pending_csrs as $pending_csr) {
                    if (auth()->user()->userApprovalRole->role_key == CsrApprovalSetps::nextStep($pending_csr->id)['designation']) {
                        $pending_csr_for_user[] = $pending_csr;
                        $pending_csr_ids[] = $pending_csr->id;
                    }
                }
            }
        } else {
            $csr_list = Csr::select('csr.*')->with([
                'PVMS.unitName',
                'csrDemands.notesheet',
                'PVMS.itemTypename',
                'csrPvmsApproval.bidder.vendor',
                'vandorPerticipateWithValidDoc' => function ($query) {
                    $query->orderBy('offered_unit_price', 'asc'); // Specify the ordering for children
                },
                'vandorPerticipateWithValidDoc.vendor',
                'csrDemands',
                'tender',
                'hod',
                'selectedBidder.vendor'
                //  => function ($query) {
                //     $query->whereDate('deadline', '<', Carbon::now()); // Specify the ordering for children
                // },
            ])->whereHas('vandorPerticipateWithValidDoc')->whereHas('tender', function ($query) {
                $query->where('deadline', '<', Carbon::now());
            })
                ->groupBy('csr.id')
                ->leftJoin('csr_approvals', 'csr_approvals.csr_id', '=', 'csr.id')
                ->where('csr_approvals.approved_by', auth()->user()->id)
                ->latest()->paginate($limit);
        }

        $tender_nextsteps = [];
        $pending_tender_nextsteps = [];
        // dd($notesheets);
        foreach ($pending_csr_for_user as $key => $csr) {
            $step = CsrApprovalSetps::nextStep($csr->id);
            $next_step = CsrApprovalSetps::nextDesignation($csr->id);
            $notesheet_step_with_id = [
                'id' => $csr->id,
                'step' => $step,
                'next_step' => $next_step
            ];
            array_push($pending_tender_nextsteps, $notesheet_step_with_id);
        }
        foreach ($csr_list as $key => $csr) {
            $step = CsrApprovalSetps::nextStep($csr->id);
            $next_step = CsrApprovalSetps::nextDesignation($csr->id);
            $notesheet_step_with_id = [
                'id' => $csr->id,
                'step' => $step,
                'next_step' => $next_step
            ];
            array_push($tender_nextsteps, $notesheet_step_with_id);
        }

        return [
            'csr_list' => $csr_list,
            'csr_next_steps' => $tender_nextsteps,
            'pending_csr_for_user' => $pending_csr_for_user,
            'pending_csr_ids' => $pending_csr_ids,
            'pending_csr_nextsteps' => $pending_tender_nextsteps
        ];
    }

    public function approve(Request $request)
    {
        $csr = Csr::find($request->csr_id);
        $next_steps = CsrApprovalSetps::nextStep($request->csr_id);

        $csr_approval = new CsrApproval();
        $csr_approval->csr_id = $request->csr_id;
        $csr_approval->selected_biddder_id = empty($request->selected_biddder_id) ? null : $request->selected_biddder_id;
        $csr_approval->approved_by = auth()->user()->id;
        $csr_approval->step_number = $next_steps['step'];
        $csr_approval->role_name = $next_steps['designation'];
        $csr_approval->last_approval_rank = $next_steps['name'];
        $csr_approval->remarks = $request->remarks;
        $csr_approval->save();

        $csr->last_approval = $next_steps['designation'];
        $csr->last_approval_rank = $next_steps['name'];
        if (!empty($request->selected_biddder_id)) {
            $csr->approved_vendor = $request->selected_biddder_id;
        }
        if (!empty($request->hod_user)) {
            $csr->hod_user = $request->hod_user;
        }

        $item_type = $csr->csrDemands[0]->notesheet->notesheet_item_type;

        if (($next_steps['designation'] == 'dgms' && $item_type == 4) || (($next_steps['designation'] == 'csg' || $next_steps['designation'] == 'cpg' || $next_steps['designation'] == 'dsg') && $item_type != 4)) {
            $csr->status = 'approved';
        }


        $csr->save();

        if (($next_steps['designation'] == 'dgms' && $item_type == 4) || (($next_steps['designation'] == 'csg' || $next_steps['designation'] == 'cpg' || $next_steps['designation'] == 'dsg') && $item_type != 4)) {
            $total_item = Csr::where('tender_id', $csr->tender_id)->count();
            $total_approved_item = Csr::where('tender_id', $csr->tender_id)->where('status', 'approved')->count();

            if ($total_item == $total_approved_item) {
                $tender = Tender::find($csr->tender_id);
                $tender->status = 'complete';
                $tender->save();
            }
        }

        return $csr_approval;
    }

    public function vendorCsrJson(Request $request)
    {
        return Csr::select(
            'csr.*',
            'p_v_m_s.nomenclature',
            'p_v_m_s.pvms_id as pvms_maked_id',
            'account_units.name as au',
            'vendor_biddings.offered_unit_price',
            'vendor_biddings.details'
        )
            ->join('p_v_m_s', 'p_v_m_s.id', 'csr.pvms_id')
            ->join('account_units', 'account_units.id', 'p_v_m_s.account_units_id')
            ->join('vendor_biddings', 'vendor_biddings.csr_id', 'csr.id')
            ->where('csr.approved_vendor', $request->vendor_id)
            ->where('vendor_biddings.vendor_id', $request->vendor_id)
            ->where('is_vendor_approved', true)
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
