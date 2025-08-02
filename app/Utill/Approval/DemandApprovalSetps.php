<?php

namespace App\Utill\Approval;

use App\Models\ApprovalLayer;
use App\Models\Demand;
use App\Models\DemandApproval;
use App\Models\DemandPvms;
use App\Models\NotesheetDemandPVMS;
use App\Models\TenderNotesheet;

class DemandApprovalSetps
{
    const EMProcurementSteps = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General'
            ]
        ]

    ];

    const Dental = [];
    // $demand->createdBy && $demand->createdBy->sub_org_id == 2

    const EMDental = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ]
    ];

    const Medicine = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ]
    ];

    const MedicineDental = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ]
    ];

    const Reagent = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ]
    ];

    const Disposable =
    [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'ds',
                'name' => 'Dental Surgeon'
            ],
            [
                'step' => 6,
                'designation' => 'dgms',
                'name' => 'DGMS'
            ]
        ],

    ];

    const DisposableDental =
    [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 4,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ]
    ];

    const Repair = [
        [
            'step' => 1,
            'designation' => 'head_clark',
            'name' => 'head clark'
        ],
        [
            'step' => 2,
            'designation' => 'csg',
            'name' => 'Consultant Surgeon General'
        ],
    ];

    public static function nextStep($demand_id)
    {

        $demand = Demand::find($demand_id);

        if ($demand->purchase_type) {
            $purchase_type = $demand->purchase_type;
        } else {
            $purchase_type = 'notesheet';
        }


        $demand_approval = DemandApproval::where('demand_id', $demand_id)
            ->where('need_reapproval', false)
            ->latest()
            ->first();

        if ($demand_approval) {
            $next_steps = $demand_approval->step_number + 1;
        } else {
            $next_steps = 1;
        }

        if ($demand->demand_type_id == 4) {
            $current_approval_step = DemandApprovalSetps::Repair;
        } else if ($demand->demand_item_type_id == 1 && $demand->is_dental_type) {
            if ($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::EMDentalAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::EMDental[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 1) {
            if ($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::EMProcurementAfmsdSteps[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::EMProcurementSteps[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 3 && $demand->is_dental_type) {
            if ($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::MedicineDentalAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::MedicineDental[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 3) {
            if ($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::MedicineAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::Medicine[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 4) {
            if ($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::ReagentAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::Reagent[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 5 && $demand->is_dental_type) {
            if ($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::DisposableDentalAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::DisposableDental[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 5) {
            if ($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::DisposableAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::Disposable[$purchase_type];
            }
        }


        if (!isset($current_approval_step) || ($current_approval_step) <= $next_steps - 1) {
            return [
                'designation' => null
            ];
        }

        if (!isset($current_approval_step[$next_steps - 1])) {
            return [
                'designation' => null
            ];
        }

        $step = $current_approval_step[$next_steps - 1];
        $step['last_approval'] = end($current_approval_step)['designation'];

        return $step;
    }

    public static function  nextStepDynamic($demand_id)
    {
        $demand = Demand::find($demand_id);

        $demand_approval = DemandApproval::where('demand_id', $demand_id)
            ->where('need_reapproval', false)
            ->latest()
            ->first();

        if ($demand_approval) {
            $current_layer = ApprovalLayer::select('id')
                ->where('designation', $demand_approval->role_name)
                ->where('is_repair', $demand->demand_type_id == 4 ? true : false)
                ->where('org_id', $demand->sub_org_id)
                ->first();

            if ($current_layer) {
                $approval_layer = ApprovalLayer::where('id', '>', $current_layer->id)
                    ->where('is_repair', $demand->demand_type_id == 4 ? true : false)
                    ->where('org_id', $demand->sub_org_id)
                    ->first();
            } else {
                $approval_layer = null;
            }
        } else {
            $approval_layer = ApprovalLayer::where('is_repair', $demand->demand_type_id == 4 ? true : false)
                ->where('org_id', $demand->sub_org_id)
                ->first();
        }

        if ($approval_layer) {
            return [
                "step" => 0,
                "designation" => $approval_layer->designation,
                "name" => $approval_layer->name,
                "last_approval" => "",
            ];
        } else {
            if ($demand->purchase_type) {
                $purchase_type = $demand->purchase_type;
            } else {
                $purchase_type = 'notesheet';
            }

            if ($demand_approval) {
                $next_steps = $demand_approval->step_number + 1;
            } else {
                $next_steps = 1;
            }

            if ($demand->demand_type_id == 4) {
                $current_approval_step = DemandApprovalSetps::Repair;
            } else if ($demand->demand_item_type_id == 1 && $demand->is_dental_type) {
                $current_approval_step = DemandApprovalSetps::EMDental[$purchase_type];
            } else if ($demand->demand_item_type_id == 1) {
                $current_approval_step = DemandApprovalSetps::EMProcurementSteps[$purchase_type];
            } else if ($demand->demand_item_type_id == 3 && $demand->is_dental_type) {
                $current_approval_step = DemandApprovalSetps::MedicineDental[$purchase_type];
            } else if ($demand->demand_item_type_id == 3) {
                $current_approval_step = DemandApprovalSetps::Medicine[$purchase_type];
            } else if ($demand->demand_item_type_id == 4) {
                $current_approval_step = DemandApprovalSetps::Reagent[$purchase_type];
            } else if ($demand->demand_item_type_id == 5 && $demand->is_dental_type) {
                $current_approval_step = DemandApprovalSetps::DisposableDental[$purchase_type];
            } else if ($demand->demand_item_type_id == 5) {
                $current_approval_step = DemandApprovalSetps::Disposable[$purchase_type];
            }

            if (!isset($current_approval_step) || ($current_approval_step) <= $next_steps - 1) {
                return [
                    'designation' => null
                ];
            }

            if (!isset($current_approval_step[$next_steps - 1])) {
                return [
                    'designation' => null
                ];
            }

            $step = $current_approval_step[$next_steps - 1];
            $step['last_approval'] = end($current_approval_step)['designation'];

            return $step;
        }
    }

    public static function demandPvmsStatus($demand_id, $pvms_id)
    {
        $notesheet_item = NotesheetDemandPVMS::where('demand_id', $demand_id)->where('pvms_id', $pvms_id)->first();

        if (isset($notesheet_item)) {
            $tender_item = TenderNotesheet::where('notesheet_id', $notesheet_item->notesheet_id)->first();
            if (isset($tender_item)) {
                return "Tender";
            } else {
                return "Notesheet";
            }
        } else {
            $demand_pvms = DemandPvms::where('demand_id', $demand_id)->where('p_v_m_s_id', $pvms_id)->first();

            if (isset($demand_pvms) && isset($demand_pvms->purchase_type)) {
                return "Decesion: " . ucfirst($demand_pvms->purchase_type);
            } else {
                $next_step = DemandApprovalSetps::nextStepDynamic($demand_id);

                if (isset($next_step) && isset($next_step["step"])) {
                    if ($next_step["step"] > 0) {
                        return "DGMS";
                    }
                }

                return "Unit";
            }
        }
    }
}
