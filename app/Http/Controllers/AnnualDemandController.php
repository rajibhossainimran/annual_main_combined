<?php

namespace App\Http\Controllers;

use App\Exports\AnnualDeamndExport;
use App\Exports\AnnualDeamndListExport;
use App\Models\AnnualDemand;
use App\Models\AnnualDemandDepatment;
use App\Models\AnnualDemandListApproval;
use App\Models\AnnualDemandPvms;
use App\Models\AnnualDemandPvmsUnitDemand;
use App\Models\AnnualDemandUnit;
use App\Models\AnnualDemandUnitApproval;
use App\Services\AnnualDemandService;
use App\Services\AuditService;
use App\Services\PVMSService;
use App\Utill\Approval\AnnualDemandListApprovalSteps;
use App\Utill\Approval\AnnualDemandUnitApprovalSteps;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Excel;

class AnnualDemandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perpage = 10;
        if ($request->perpage) {
            $perpage = $request->perpage;
        }

        $annual_demands = AnnualDemand::with('financialYear')->withCount('departmentList')->latest()->paginate($perpage);
        $user_approval_role = auth()->user()->userApprovalRole;
        return view('admin.annual_demand.index', compact('annual_demands', 'user_approval_role'));
    }
    public function export($id)
    {
        $annual_demand = AnnualDemand::find($id);
        $annual_demand_units = AnnualDemandUnit::where('annual_demand_id', $annual_demand->id)->where('is_approved', 1)->get();
        $annual_demand_unit_ids = [];

        foreach ($annual_demand_units as $each_unit) {
            array_push($annual_demand_unit_ids, $each_unit->id);
        }

        return Excel::download(new AnnualDeamndExport($annual_demand_unit_ids), 'annual-demand-' . $annual_demand->financialYear->name . ' - ' . strtotime("now") . '.xlsx');
    }
    public function export_list($id)
    {
        $annual_demand = AnnualDemand::find($id);
        return Excel::download(new AnnualDeamndListExport($annual_demand->id), 'annual-demand-list' . $annual_demand->financialYear->name . ' - ' . strtotime("now") . '.xlsx');
    }
    public function annulaDemandListApi(Request $request)
    {
        $annual_demands = AnnualDemand::with('financialYear')->withCount('departmentList')->latest()->get();
        return $annual_demands;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.annual_demand.create');
    }
    public function unit_extimation()
    {
        return view('admin.annual_demand.unit_estimate');
    }
    public function annual_demand_unit($id, Request $request)
    {
        $perpage = 10;
        if ($request->perpage) {
            $perpage = $request->perpage;
        }

        $annual_demand_units = AnnualDemandUnit::where('annual_demand_id', $id)->latest()->paginate($perpage);
        $user_approval_role = auth()->user()->userApprovalRole;
        $annual_demand = AnnualDemand::find($id);
        return view('admin.annual_demand.unit_demand', compact('annual_demand_units', 'user_approval_role', 'annual_demand'));
    }

    public function annual_demand_unit_approve($id, Request $request)
    {
        $annual_demand = AnnualDemand::find($id);
        $annual_demand->last_list_approved_role = auth()->user()->user_approval_role_id;
        $annual_demand->is_unit_approved = 1;
        $annual_demand->save();
        return redirect()->route('annual_demand_unit.list', ['id' => $id])->with('message', 'Successfully Approved.');
    }
    public function unit_extimation_create(Request $request)
    {

        if (auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'AFMSD' || auth()->user()->subOrganization->type == 'DGMS')) {
            foreach ($request->annual_demand_pvms_list as $annual_demand_pvms) {
                if (isset($annual_demand_pvms['annual_demand_pvms_unit_demand_id'])) {
                    AnnualDemandService::updateAnnualDemandPvmsUnitDemand($annual_demand_pvms['annual_demand_pvms_unit_demand_id'], $annual_demand_pvms);
                }
            }
        } else {
            $annual_demand_unit = AnnualDemandService::createAnnualDemandUnit($request->all());
            foreach ($request->annual_demand_pvms_list as $annual_demand_pvms) {
                if (isset($annual_demand_pvms['annual_demand_pvms_unit_demand_id'])) {
                    AnnualDemandService::updateAnnualDemandPvmsUnitDemand($annual_demand_pvms['annual_demand_pvms_unit_demand_id'], $annual_demand_pvms);
                } else {
                    AnnualDemandService::createAnnualDemandPvmsUnitDemand($annual_demand_unit->id, $annual_demand_pvms);
                }
            }
        }


        return response()->json(['success' => 1], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $annual_demand = AnnualDemandService::createAnnualDemand($request->all());
        $annual_demand_department = AnnualDemandService::createAnnualDemandDepartment($annual_demand->id, $request->department_id);

        foreach ($request->pvms_list as $pvms) {
            AnnualDemandService::createAnnualDemandDepartmentPvms($annual_demand_department->id, $pvms["id"]);
        }

        return response()->json(['success' => 1], 200);
    }

    public function approveList(Request $request)
    {
        $annual_demand = AnnualDemand::find($request->id);

        $next_steps = AnnualDemandListApprovalSteps::nextStep($annual_demand->id);
        $annual_demand_approval = new AnnualDemandListApproval();
        $annual_demand_approval->annual_demand_id = $request->id;
        $annual_demand_approval->approved_by = auth()->user()->id;
        $annual_demand_approval->step_number = $next_steps['step'];
        $annual_demand_approval->role_name = $next_steps['designation'];
        $annual_demand_approval->note = $request->note ? $request->note : '';
        $annual_demand_approval->save();
        AuditService::AuditLogEntry(AuditModel::AnnualDemandListApproval, OperationTypes::Approval, "Annual demand pvms list approved by " . auth()->user()->name, null, $annual_demand_approval, $annual_demand_approval->id);

        $annual_demand->last_list_approved_role = auth()->user()->user_approval_role_id;
        if ($next_steps['designation'] == 'gso-1') {
            $annual_demand->is_list_approved = 1;
        }
        $annual_demand->save();

        foreach ($annual_demand->departmentList as $eachDept) {
            $eachDept->last_list_approved_role = auth()->user()->user_approval_role_id;
            if ($next_steps['designation'] == 'gso-1') {
                $eachDept->is_list_approved = 1;
            }
            $eachDept->save();
        }

        return $annual_demand_approval;
    }
    public function approveUnit(Request $request)
    {
        $annual_demand = AnnualDemand::find($request->id);
        $annual_demand_unit = AnnualDemandUnit::where('annual_demand_id', $annual_demand->id)->where('sub_org_id', auth()->user()->sub_org_id)->first();

        $next_steps = AnnualDemandUnitApprovalSteps::nextStep($annual_demand_unit->id);
        $annual_demand_unit_approval = new AnnualDemandUnitApproval();
        $annual_demand_unit_approval->annual_demand_id = $annual_demand->id;
        $annual_demand_unit_approval->annual_demand_unit_id = $annual_demand_unit->id;
        $annual_demand_unit_approval->sub_org_id = $annual_demand_unit->sub_org_id;
        $annual_demand_unit_approval->approved_by = auth()->user()->id;
        $annual_demand_unit_approval->step_number = $next_steps['step'];
        $annual_demand_unit_approval->role_name = $next_steps['designation'];
        $annual_demand_unit_approval->note = $request->note ? $request->note : '';
        $annual_demand_unit_approval->save();
        AuditService::AuditLogEntry(AuditModel::AnnualDemandUnitApproval, OperationTypes::Approval, "Annual demand pvms unit estimation approved by " . auth()->user()->name, null, $annual_demand_unit_approval, $annual_demand_unit_approval->id);
        // if($next_steps['org'] != 'CMH') {
        //     $annual_demand->last_list_approved_role = auth()->user()->user_approval_role_id;
        //     if($next_steps['designation'] == 'dgms') {
        //         $annual_demand->is_unit_approved = 1;
        //     }
        //     $annual_demand->save();
        // }

        $annual_demand_unit->last_approved_role = auth()->user()->user_approval_role_id;
        if ($next_steps['designation'] == 'dgms') {
            $annual_demand_unit->is_approved = 1;
        }
        $annual_demand_unit->save();


        return $annual_demand_unit_approval;
    }
    public function approveByDept(Request $request)
    {
        $annual_demand = AnnualDemand::find($request->id);

        $annual_demand_units = AnnualDemandUnit::where('annual_demand_id', $annual_demand->id)->get();
        foreach ($annual_demand_units as $annual_demand_unit) {
            $next_steps = AnnualDemandUnitApprovalSteps::nextStep($annual_demand_unit->id);

            if (isset($next_steps['designation']) && isset($next_steps['step'])) {

                $annual_demand_unit_approval = new AnnualDemandUnitApproval();
                $annual_demand_unit_approval->annual_demand_id = $annual_demand->id;
                $annual_demand_unit_approval->annual_demand_unit_id = $annual_demand_unit->id;
                $annual_demand_unit_approval->sub_org_id = $annual_demand_unit->sub_org_id;
                $annual_demand_unit_approval->approved_by = auth()->user()->id;
                $annual_demand_unit_approval->step_number = $next_steps['step'];
                $annual_demand_unit_approval->role_name = $next_steps['designation'];
                $annual_demand_unit_approval->note = $request->note ? $request->note : '';
                $annual_demand_unit_approval->save();
                AuditService::AuditLogEntry(AuditModel::AnnualDemandUnitApproval, OperationTypes::Approval, "Annual demand pvms unit estimation approved by " . auth()->user()->name, null, $annual_demand_unit_approval, $annual_demand_unit_approval->id);
            }
            $annual_demand_unit->last_approved_role = auth()->user()->user_approval_role_id;
            if ($next_steps['designation'] == 'dgms') {
                $annual_demand_unit->is_approved = 1;
                $annual_demand->is_unit_approved = 1;
                $annual_demand->save();
            }
            $annual_demand_unit->save();
        }
        if ($annual_demand->is_unit_approved == 1) {
            AnnualDemandUnit::where('annual_demand_id', $annual_demand->id)->update(["is_approved" => 1]);
        }

        return $annual_demand_units;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    public function showApi(Request $request)
    {
        $fy = $request->finacialYear;
        $department = $request->department;

        $annual_demand = AnnualDemand::with([
            'financialYear',
            'departmentList' => function ($query) use ($department) {
                $query->where('department_id', $department)
                    ->with(['pvmsList' => function ($query) {
                        $query->with('PVMS.unitName');
                    }]);
            }
        ])
            ->where('financial_year_id', $fy)
            ->whereHas('departmentList', function ($query) use ($department) {
                $query->where('department_id', $department);
            })->first();
        $nivpvmsList = AnnualDemand::where('financial_year_id', $fy)
            ->whereHas('departmentList.pvmsList.PVMS', function ($query) {
                $query->where('pvms_id', 'NIV');
            })
            ->with([
                'departmentList.pvmsList' => function ($query) {
                    $query->whereHas('PVMS', function ($query) {
                        $query->where('pvms_id', 'NIV');
                    })->with('PVMS.unitName');
                }
            ])
            ->get()
            ->pluck('departmentList')
            ->flatten()
            ->pluck('pvmsList')
            ->flatten()
            ->filter(function ($pvms) {
                return $pvms->PVMS->pvms_id === 'NIV';
            });
        if ($annual_demand) {
            return response()->json([
                'annual_demand' => $annual_demand,
                'current_approval' => AnnualDemandListApprovalSteps::nextStep($annual_demand->id),
                'niv' => $nivpvmsList
            ], 200);
        } else {
            return response()->json([
                'annual_demand' => null,
                'current_approval' => null,
                'niv' => $nivpvmsList
            ], 200);
        }
    }
    public function showUnitApi(Request $request)
    {
        $fy = $request->finacialYear;
        $department = $request->department;
        $item_type = $request->item;
        $page = 0;
        $limit = 50;

        $annual_demand = AnnualDemand::with('financialYear')->where('financial_year_id', $fy)->first();

        if ($annual_demand) {
            if (!auth()->user()->subOrganization || (auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'AFMSD' || auth()->user()->subOrganization->type == 'DGMS'))) {
                $annual_demand_pvms = AnnualDemandPvms::with(['PVMS' => function ($query) {
                    $query->orderBy('nomenclature', 'asc');
                }, 'PVMS.unitName', 'annualDemandDepartment'])->whereHas('annualDemandDepartment', function ($query) use ($annual_demand) {
                    $query->where('annual_demand_id', $annual_demand->id);
                });

                if (isset($item_type)) {
                    $annual_demand_pvms = $annual_demand_pvms->whereHas('PVMS', function ($query) use ($item_type) {
                        $query->where('item_types_id', $item_type);
                    });
                } else {
                    $annual_demand_pvms = $annual_demand_pvms->whereHas('PVMS');
                }

                $annual_demand_pvms = $annual_demand_pvms->groupBy('pvms_id')->paginate($limit);

                $annual_demand_unit = AnnualDemandUnit::where('annual_demand_id', $annual_demand->id)->get();
                $annual_demand_unit_ids = $annual_demand_unit->pluck('id')->toArray();
                $pvms_list = $annual_demand_pvms->pluck('pvms_id')->toArray();
                if ($annual_demand_unit) {
                    $annual_demand_unit_pvms = AnnualDemandPvmsUnitDemand::with(['annualDemandPvms.PVMS.unitName', 'annualDemandUnit.subOrganization'])->whereIn('annual_demand_unit_id', $annual_demand_unit_ids);
                    $annual_demand_unit_pvms = $annual_demand_unit_pvms->whereHas('annualDemandPvms', function ($query) use ($pvms_list) {
                        $query->whereIn('pvms_id', $pvms_list);
                    });
                    if (isset($item_type)) {
                        $annual_demand_unit_pvms = $annual_demand_unit_pvms->whereHas('annualDemandPvms.PVMS', function ($query) use ($item_type) {
                            $query->where('item_types_id', $item_type);
                        });
                    } else {
                        $annual_demand_unit_pvms = $annual_demand_unit_pvms->whereHas('annualDemandPvms.PVMS');
                    }
                    $annual_demand_unit_pvms = $annual_demand_unit_pvms->get();

                    if ($annual_demand_unit_pvms && count($annual_demand_unit_pvms) > 0) {
                        $pvms_list = [];

                        foreach ($annual_demand_unit_pvms as $each_pvms) {
                            array_push($pvms_list, $each_pvms->annualDemandPvms->pvms_id);
                        }
                        return response()->json([
                            'type' => 'update',
                            'annual_demand_unit' => $annual_demand_unit,
                            'annual_demand' => $annual_demand,
                            'annual_demand_unit_pvms' => $annual_demand_unit_pvms,
                            'current_approval' => AnnualDemandUnitApprovalSteps::nextStepWithAnnualDemand($annual_demand->id),
                            'unit_stock' => PVMSService::pvmsUnitWiseStock($pvms_list, auth()->user()->sub_org_id ? auth()->user()->sub_org_id : 2),
                            'annual_demand_unit_ids' => $annual_demand_unit_ids,
                            'annual_demand_pvms' => $annual_demand_pvms
                        ], 200);
                    }
                }

                return response()->json([
                    'type' => 'create',
                    'annual_demand' => $annual_demand,
                    'annual_demand_department_pvms' => $annual_demand_pvms->items(),
                    'current_approval' => AnnualDemandUnitApprovalSteps::nextStep($annual_demand_unit_ids[0]),
                    'unit_stock' => null,
                    'annual_demand_unit_ids' => $annual_demand_unit_ids,
                    'annual_demand_pvms' => $annual_demand_pvms
                ], 200);
            } else {
                $annual_demand_pvms = AnnualDemandPvms::with(['PVMS' => function ($query) {
                    $query->orderBy('nomenclature', 'asc');
                }, 'PVMS.unitName', 'annualDemandDepartment'])->whereHas('annualDemandDepartment', function ($query) use ($annual_demand) {
                    $query->where('annual_demand_id', $annual_demand->id);
                });

                if (isset($item_type)) {
                    $annual_demand_pvms = $annual_demand_pvms->whereHas('PVMS', function ($query) use ($item_type) {
                        $query->where('item_types_id', $item_type);
                    });
                } else {
                    $annual_demand_pvms = $annual_demand_pvms->whereHas('PVMS');
                }

                $annual_demand_pvms = $annual_demand_pvms->groupBy('pvms_id')->paginate($limit);

                $annual_demand_unit = AnnualDemandUnit::where('annual_demand_id', $annual_demand->id)->where('sub_org_id', auth()->user()->sub_org_id)->first();
                $pvms_list = $annual_demand_pvms->pluck('pvms_id')->toArray();
                if ($annual_demand_unit) {
                    $annual_demand_unit_pvms = AnnualDemandPvmsUnitDemand::with('annualDemandPvms.PVMS.unitName')->where('annual_demand_unit_id', $annual_demand_unit->id);
                    $annual_demand_unit_pvms = $annual_demand_unit_pvms->whereHas('annualDemandPvms', function ($query) use ($pvms_list) {
                        $query->whereIn('pvms_id', $pvms_list);
                    });
                    if (isset($item_type)) {
                        $annual_demand_unit_pvms = $annual_demand_unit_pvms->whereHas('annualDemandPvms.PVMS', function ($query) use ($item_type) {
                            $query->where('item_types_id', $item_type);
                        });
                    } else {
                        $annual_demand_unit_pvms = $annual_demand_unit_pvms->whereHas('annualDemandPvms.PVMS');
                    }
                    $annual_demand_unit_pvms = $annual_demand_unit_pvms->get();
                    if ($annual_demand_unit_pvms && count($annual_demand_unit_pvms) > 0) {
                        $pvms_list = [];

                        foreach ($annual_demand_unit_pvms as $each_pvms) {
                            array_push($pvms_list, $each_pvms->annualDemandPvms->pvms_id);
                        }
                        return response()->json([
                            'type' => 'update',
                            'annual_demand_unit' => $annual_demand_unit,
                            'annual_demand' => $annual_demand,
                            'annual_demand_unit_pvms' => $annual_demand_unit_pvms,
                            'current_approval' => AnnualDemandUnitApprovalSteps::nextStepWithAnnualDemand($annual_demand->id),
                            'unit_stock' => PVMSService::pvmsUnitWiseStock($pvms_list, auth()->user()->sub_org_id),
                            'annual_demand_pvms' => $annual_demand_pvms
                        ], 200);
                    }
                }


                // if(isset($annual_demand_pvms)) {
                //     foreach ($annual_demand_pvms as $each_pvms) {
                //         array_push($pvms_list,$each_pvms->pvms_id);
                //     }
                // }

                return response()->json([
                    'type' => 'create',
                    'annual_demand' => $annual_demand,
                    'annual_demand_department_pvms' => $annual_demand_pvms->items(),
                    'current_approval' => null,
                    'unit_stock' => PVMSService::pvmsUnitWiseStock($pvms_list, auth()->user()->sub_org_id),
                    'annual_demand_pvms' => $annual_demand_pvms
                ], 200);
            }
        } else {
            return response()->json([
                'annual_demand' => null,
                'current_approval' => null,
            ], 200);
        }
    }

    public function removeAnuualDemandPvms($id)
    {
        $annual_demand_pvms = AnnualDemandPvms::find($id);
        AuditService::AuditLogEntry(AuditModel::AnnualDemandPvms, OperationTypes::Delete, "Annual demand pvms deleted by " . auth()->user()->name, $annual_demand_pvms, null, $annual_demand_pvms->id);
        $annual_demand_pvms->delete();
        return $annual_demand_pvms;
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

    }
}
