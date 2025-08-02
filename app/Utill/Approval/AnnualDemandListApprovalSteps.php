<?php

namespace App\Utill\Approval;

use App\Models\AnnualDemand;
use App\Models\AnnualDemandListApproval;
use Exception;

class AnnualDemandListApprovalSteps {
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
        ]
    ];

    public static function nextStep($demand_id) {
        $current_approval_step = null;
        $annual_demand = AnnualDemand::find($demand_id);
        $annual_demand_approval = AnnualDemandListApproval::where('annual_demand_id', $demand_id)->latest()->first();

        if ($annual_demand_approval) {
            if($annual_demand_approval->role_name=='gso-1'){
                return [
                    'designation' => null
                ];
            }
            $next_steps = $annual_demand_approval->step_number + 1;
        } else {
            $next_steps = 1;
        }

        try {
            $current_approval_step = AnnualDemandListApprovalSteps::ApprovalLayer[$next_steps - 1];
        } catch(Exception $e) {
            return [
                'designation' => null
            ];
        }

        return $current_approval_step;
    }
}
