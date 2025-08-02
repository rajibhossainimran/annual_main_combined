<?php

namespace App\Utill\Approval;

use App\Models\ApprovalLayer;
use App\Models\DemandPvms;
use App\Models\NotesheetDemandPVMS;
use App\Models\QuerterlyDemand;
use App\Models\QuerterlyDemandApproval;
use App\Models\TenderNotesheet;

class QuerterlyDemandApprovalSetps
{
    const EMProcurementSteps = [
        [
            'step' => 1,
            'designation' => 'adms',
            'name' => 'ADMS'
        ],
        [
            'step' => 2,
            'designation' => 'dms',
            'name' => 'DMS'
        ],
        [
            'step' => 3,
            'designation' => 'head_clark',
            'name' => 'head clark'
        ],
        [
            'step' => 4,
            'designation' => 'c&c',
            'name' => 'CGO-1'
        ],
        [
            'step' => 5,
            'designation' => 'gso-1',
            'name' => 'GSO-1'
        ],
    ];

    public static function nextStepDynamic($demand_id){

        $demand = QuerterlyDemand::find($demand_id);

        $demand_approval = QuerterlyDemandApproval::where('demand_id', $demand_id)
            ->where('need_reapproval', false)
            ->latest()
            ->first();

        if($demand_approval){
            $current_layer = ApprovalLayer::select('id')
                    ->where('designation', $demand_approval->role_name)
                    ->where('is_repair', $demand->demand_type_id == 4 ? true : false)
                    ->where('org_id', $demand->sub_org_id)
                    ->first();

            if($current_layer){
                $approval_layer = ApprovalLayer::where('id', '>',$current_layer->id)
                ->where('is_repair', $demand->demand_type_id == 4 ? true : false)
                ->where('org_id', $demand->sub_org_id)
                ->first();
            }else{
                $approval_layer = null;
            }

        }else{
            $approval_layer = ApprovalLayer::where('is_repair', $demand->demand_type_id == 4 ? true : false)
                                            ->where('org_id', $demand->sub_org_id)
                                            ->first();
        }

        if($approval_layer){
            return [
                "step" => 0,
                "designation" => $approval_layer->designation,
                "name" => $approval_layer->name,
                "last_approval" => "",
            ];
        }else{

            $purchase_type = $demand->purchase_type;

            if ($demand_approval) {
                $next_steps = $demand_approval->step_number + 1;
            } else {
                $next_steps = 1;
            }

            $current_approval_step = QuerterlyDemandApprovalSetps::EMProcurementSteps;
            

            if(!isset($current_approval_step) || ($current_approval_step) <= $next_steps - 1){
                return [
                    'designation' => null
                ];
            }

            if(!isset($current_approval_step[$next_steps - 1])) {
                return [
                    'designation' => null
                ];
            }

            $step = $current_approval_step[$next_steps - 1];
            $step['last_approval'] = end($current_approval_step)['designation'];

            return $step;
        }

    }

    public static function demandPvmsStatus($demand_id,$pvms_id) {
        $notesheet_item = NotesheetDemandPVMS::where('demand_id',$demand_id)->where('pvms_id',$pvms_id)->first();

        if(isset($notesheet_item)) {
            $tender_item = TenderNotesheet::where('notesheet_id',$notesheet_item->notesheet_id)->first();
            if(isset($tender_item)) {
                return "Tender";
            } else {
                return "Notesheet";
            }
        } else {
            $demand_pvms = DemandPvms::where('demand_id',$demand_id)->where('p_v_m_s_id',$pvms_id)->first();

            if(isset($demand_pvms) && isset($demand_pvms->purchase_type)) {
                return "Decesion: ".ucfirst($demand_pvms->purchase_type);
            } else {
                $next_step = DemandApprovalSetps::nextStepDynamic($demand_id);

                if(isset($next_step) && isset($next_step["step"])) {
                    if($next_step["step"] > 0) {
                        return "DGMS";
                    }
                }

                return "Unit";

            }
        }
    }
}
