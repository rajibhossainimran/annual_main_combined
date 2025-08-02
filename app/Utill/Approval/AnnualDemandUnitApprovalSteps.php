<?php

namespace App\Utill\Approval;

use App\Models\AnnualDemandUnit;
use App\Models\AnnualDemandUnitApproval;
use App\Models\ApprovalLayer;
use Exception;

class AnnualDemandUnitApprovalSteps {
    const ApprovalLayer = [
        [
            'step' => 1,
            'designation' => 'cmh_clark',
            'name' => 'Afmsd Clerk',
            'org'=> 'afmsd',
            'btn_text'=> 'Initiate',
        ],
        [
            'step' => 2,
            'designation' => 'mo',
            'name' => 'Afmsd Stock Controll Officer',
            'org'=> 'afmsd',
            'btn_text'=> 'Forward',
        ],
        [
            'step' => 3,
            'designation' => 'oic',
            'name' => 'Oic',
            'org'=> 'afmsd',
            'btn_text'=> 'Approve',
        ],
        [
            'step' => 4,
            'designation' => 'head_clark',
            'name' => 'head clark',
            'org'=> 'dgms',
            'btn_text'=> 'Forward',
        ],
        [
            'step' => 5,
            'designation' => 'p&p',
            'name' => 'DADGMS',
            'org'=> 'dgms',
            'btn_text'=> 'Approve',
        ],
        [
            'step' => 6,
            'designation' => 'gso-1',
            'name' => 'Adgms Store',
            'org'=> 'dgms',
            'btn_text'=> 'Approve',
        ],
        [
            'step' => 7,
            'designation' => 'ddgms',
            'name' => 'DyDgms',
            'org'=> 'dgms',
            'btn_text'=> 'Seen',
        ],
        [
            'step' => 8,
            'designation' => 'cpg',
            'name' => 'Consultant Physician General',
            'btn_text'=> 'Approve',
        ],
        [
            'step' => 9,
            'designation' => 'dgms',
            'name' => 'Dgms',
            'btn_text'=> 'Approve',
        ],
    ];

    public static function nextStepWithAnnualDemand($annual_demand_id) {
        if(auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'AFMSD' || auth()->user()->subOrganization->type == 'DGMS')) {
            $annual_demand_unit = AnnualDemandUnit::where('annual_demand_id',$annual_demand_id)->latest()->first();
        } else {
            $annual_demand_unit = AnnualDemandUnit::where('annual_demand_id',$annual_demand_id)->where('sub_org_id',auth()->user()->sub_org_id)->first();
        }

        if($annual_demand_unit) {
            return AnnualDemandUnitApprovalSteps::nextStep($annual_demand_unit->id);
        } else {
            return [
                'designation' => null
            ];
        }
    }

    public static function nextStep($demand_unit_id) {
        $current_approval_step = null;
        $annual_demand_unit = AnnualDemandUnit::find($demand_unit_id);
        $annual_unit_demand_approval = AnnualDemandUnitApproval::where('annual_demand_unit_id', $demand_unit_id)->latest()->first();
        if($annual_demand_unit) {
            if(!$annual_demand_unit->is_ready) {
                return [
                    'designation' => null
                ];
            }
        }

        if ($annual_unit_demand_approval) {
            if($annual_unit_demand_approval->step_number == 0) {
                $current_layer = ApprovalLayer::select('id')
                        ->where('designation', $annual_unit_demand_approval->role_name)
                        ->where('is_repair', false)
                        ->where('org_id', $annual_demand_unit->sub_org_id)
                        ->first();
                if($current_layer){
                    $approval_layer = ApprovalLayer::where('id', '>',$current_layer->id)
                    ->where('is_repair', false)
                    ->where('org_id', $annual_demand_unit->sub_org_id)
                    ->first();
                } else{
                    $approval_layer = null;
                }
            } else {
                $approval_layer = null;
            }
        } else {
            $approval_layer = ApprovalLayer::where('is_repair', false)
                                            ->where('org_id', $annual_demand_unit->sub_org_id)
                                            ->first();
        }

        try {
            if($approval_layer) {
                return [
                    "step" => 0,
                    "designation" => $approval_layer->designation,
                    "name" => $approval_layer->name,
                    "last_approval" => "",
                    "org" => "CMH",
                    'btn_text'=> 'Approve',
                ];
            } else {
                if ($annual_unit_demand_approval) {
                    if($annual_unit_demand_approval->role_name=='dgms'){
                        return [
                            'designation' => null
                        ];
                    }
                    $next_steps = $annual_unit_demand_approval->step_number + 1;
                } else {
                    $next_steps = 1;
                }
                $current_approval_step = AnnualDemandUnitApprovalSteps::ApprovalLayer[$next_steps - 1];
                return $current_approval_step;
            }

        } catch(Exception $e) {
            return [
                'designation' => null
            ];
        }
    }
}
