<?php

namespace App\Utill\Approval;

use App\Models\Notesheet;
use App\Models\NotesheetApproval;

class NotesheetApprovalSetps{
    const Repair = [
        [
            'step' => 1,
            'designation' => 'oic-eme-repair',
            'name' => 'OIC, EME Repair Section',
            'btnText' => 'Approve'
        ],
        [
            'step' => 2,
            'designation' => 'gso-1',
            'name' => 'ADGMS (Store)',
            'btnText' => 'Approve'
        ],
        [
            'step' => 3,
            'designation' => 'ddgms',
            'name' => 'Dy DGMS',
            'btnText' => 'Approve'
        ],
        [
            'step' => 4,
            'designation' => 'csg',
            'name' => 'Consultant Surgeon General',
            'btnText' => 'Approve'
        ],
        [
            'step' => 5,
            'designation' => 'dgms',
            'name' => 'DGMS',
            'btnText' => 'Approve'
        ],
    ];
    const EMProcurementSteps = [
        [
            'step' => 1,
            'designation' => 'cgo-1',
            'name' => 'CGO-1',
            'btnText' => 'Forward'
        ],
        [
            'step' => 2,
            'designation' => 'gso-1',
            'name' => 'GSO-1',
            'btnText' => 'Validate'
        ],
        [
            'step' => 3,
            'designation' => 'ddgms',
            'name' => 'DDGMS',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 4,
            'designation' => 'csg',
            'name' => 'Consultant Surgeon General',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 5,
            'designation' => 'dgms',
            'name' => 'DGMS',
            'btnText' => 'Countersign'
        ],
    ];

    const Dental = [
        'EMProcurement' => [
            [

                'step' => 1,
                'designation' => 'cgo-1',
                'name' => 'CGO-1',
                'btnText' => 'Validate'
            ],
            [
                'step' => 2,
                'designation' => 'gso-1',
                'name' => 'GSO-1',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 3,
                'designation' => 'gso-2',
                'name' => 'GSO-1 (Dental)',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 6,
                'designation' => 'dgms',
                'name' => 'DGMS',
                'btnText' => 'Countersign and Approval'
            ],
        ],
        'Medicine' => [
            [

                'step' => 1,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control',
                'btnText' => 'Validate'
            ],
            [
                'step' => 2,
                'designation' => 'gso-1',
                'name' => 'GSO-1',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 3,
                'designation' => 'gso-2',
                'name' => 'GSO-1 (Dental)',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 6,
                'designation' => 'dgms',
                'name' => 'DGMS',
                'btnText' => 'Countersign'
            ],
        ],
        'Disposable' => [
            [
                'step' => 1,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control',
                'btnText' => 'Validate'
            ],
            [
                'step' => 2,
                'designation' => 'gso-2',
                'name' => 'GSO-1 (Dental)',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 3,
                'designation' => 'ddgms',
                'name' => 'DDGMS',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 4,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 5,
                'designation' => 'dgms',
                'name' => 'DGMS',
                'btnText' => 'Countersign and Approval'
            ],
        ]
    ];

    const Medicine = [
        [
            'step' => 1,
            'designation' => 'c&c',
            'name' => 'Correspondance & Control',
            'btnText' => 'Validate'
        ],
        [
            'step' => 2,
            'designation' => 'gso-1',
            'name' => 'GSO-1',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 3,
            'designation' => 'ddgms',
            'name' => 'DDGMS',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 4,
            'designation' => 'cpg',
            'name' => 'Consultant Physician General',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 5,
            'designation' => 'dgms',
            'name' => 'DGMS',
            'btnText' => 'Countersign'
        ],
    ];

    const Reagent = [
        [
            'step' => 1,
            'designation' => 'p&p',
            'name' => 'Planning & Purchase',
            'btnText' => 'Validate'
        ],
        [
            'step' => 2,
            'designation' => 'gso-1',
            'name' => 'GSO-1',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 3,
            'designation' => 'ddgms',
            'name' => 'DDGMS',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 4,
            'designation' => 'csg',
            'name' => 'Consultant Surgeon General',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 5,
            'designation' => 'dgms',
            'name' => 'DGMS',
            'btnText' => 'Countersign and Approval'
        ],
    ];

    const Disposable = [

        [
            'step' => 1,
            'designation' => 'c&c',
            'name' => 'Correspondance & Control',
            'btnText' => 'Forward'
        ],
        [
            'step' => 2,
            'designation' => 'gso-1',
            'name' => 'GSO-1',
            'btnText' => 'Validate'
        ],
        [
            'step' => 3,
            'designation' => 'ddgms',
            'name' => 'DDGMS',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 4,
            'designation' => 'csg',
            'name' => 'Consultant Surgeon General',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 5,
            'designation' => 'dgms',
            'name' => 'DGMS',
            'btnText' => 'Countersign and Approval'
        ],
    ];

    public static function nextStep($notesheet_id) {

        $notesheet = Notesheet::find($notesheet_id);

        $notesheet_approval = NotesheetApproval::where('notesheet_id', $notesheet_id)->latest()->first();

        if ($notesheet_approval) {
            if($notesheet_approval->role_name=='dgms'){
                return [
                    'designation' => null
                ];
            }
            $next_steps = $notesheet_approval->step_number + 1;
        } else {
            $next_steps = 1;
        }

        $current_approval_step = '';
        if($notesheet->is_repair == 1) {
            $current_approval_step = NotesheetApprovalSetps::Repair[$next_steps - 1];
        } else {

            if(($notesheet->notesheet_item_type == 3 && $notesheet->is_dental == 1) || ($notesheet->notesheet_item_type == 1 && $notesheet->is_dental == 1) || ($notesheet->notesheet_item_type == 5 && $notesheet->is_dental == 1)){
                // $current_approval_step = NotesheetApprovalSetps::Dental[$next_steps - 1];
                if($notesheet->notesheet_item_type == 1) {
                    $current_approval_step = NotesheetApprovalSetps::Dental['EMProcurement'][$next_steps - 1];
                } elseif($notesheet->notesheet_item_type == 3) {
                    $current_approval_step = NotesheetApprovalSetps::Dental['Medicine'][$next_steps - 1];
                } elseif($notesheet->notesheet_item_type == 5) {
                    $current_approval_step = NotesheetApprovalSetps::Dental['Disposable'][$next_steps - 1];
                }
            } elseif($notesheet->notesheet_item_type == 1) {
                $current_approval_step = NotesheetApprovalSetps::EMProcurementSteps[$next_steps - 1];
            } elseif($notesheet->notesheet_item_type == 3) {
                $current_approval_step = NotesheetApprovalSetps::Medicine[$next_steps - 1];
            } elseif($notesheet->notesheet_item_type == 4) {
                $current_approval_step = NotesheetApprovalSetps::Reagent[$next_steps - 1];
            } elseif($notesheet->notesheet_item_type == 5) {
                $current_approval_step = NotesheetApprovalSetps::Disposable[$next_steps - 1];
            }
        }

        return $current_approval_step;
    }
    public static function nextDesignation($notesheet_id) {

        $notesheet = Notesheet::find($notesheet_id);

        $notesheet_approval = NotesheetApproval::where('notesheet_id', $notesheet_id)->latest()->first();

        if ($notesheet_approval) {
            if($notesheet_approval->role_name=='dgms'){
                return [
                    'designation' => null
                ];
            }
            $next_steps = $notesheet_approval->step_number + 1;
        } else {
            $next_steps = 1;
        }

        $next_approval_step = '';
        if($notesheet->is_repair == 1) {
            $next_approval_step = count(NotesheetApprovalSetps::Repair) > $next_steps ? NotesheetApprovalSetps::Repair[$next_steps]: '';
        } else {
            if(($notesheet->notesheet_item_type == 3 && $notesheet->is_dental == 1) || ($notesheet->notesheet_item_type == 1 && $notesheet->is_dental == 1) || ($notesheet->notesheet_item_type == 5 && $notesheet->is_dental == 1)){
                if($notesheet->notesheet_item_type == 1) {
                    $next_approval_step = count(NotesheetApprovalSetps::Dental['EMProcurement']) > $next_steps ? NotesheetApprovalSetps::Dental['EMProcurement'][$next_steps]: '';
                } elseif($notesheet->notesheet_item_type == 3) {
                    $next_approval_step = count(NotesheetApprovalSetps::Dental['Medicine']) > $next_steps ? NotesheetApprovalSetps::Dental['Medicine'][$next_steps]: '';
                } elseif($notesheet->notesheet_item_type == 5) {
                    $next_approval_step = count(NotesheetApprovalSetps::Dental['Disposable']) > $next_steps ? NotesheetApprovalSetps::Dental['Disposable'][$next_steps]: '';
                }
            } elseif($notesheet->notesheet_item_type == 1) {
                $next_approval_step = count(NotesheetApprovalSetps::EMProcurementSteps) > $next_steps ? NotesheetApprovalSetps::EMProcurementSteps[$next_steps] : '';
            } elseif($notesheet->notesheet_item_type == 3) {
                $next_approval_step = count(NotesheetApprovalSetps::Medicine) > $next_steps ? NotesheetApprovalSetps::Medicine[$next_steps]: '';
            } elseif($notesheet->notesheet_item_type == 4) {
                $next_approval_step = count(NotesheetApprovalSetps::Reagent) > $next_steps ? NotesheetApprovalSetps::Reagent[$next_steps]: '';
            } elseif($notesheet->notesheet_item_type == 5) {
                $next_approval_step = count(NotesheetApprovalSetps::Disposable) > $next_steps ? NotesheetApprovalSetps::Disposable[$next_steps]: '';
            }
        }

        return $next_approval_step;
    }
}
