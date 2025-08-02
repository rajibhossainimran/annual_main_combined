<?php

namespace App\Utill\Approval;

use App\Models\Csr;
use App\Models\CsrApproval;
use Exception;

class CsrApprovalSetps
{
    const EMProcurementSteps = [
        [
            'step' => 1,
            'designation' => 'head_clark',
            'name' => 'head clark',
            'btnText' => 'Create & Forward'
        ],
        [
            'step' => 2,
            'designation' => 'cgo-1',
            'name' => 'CGO-1',
            'btnText' => 'Forward'
        ],
        [
            'step' => 3,
            'designation' => 'gso-1',
            'name' => 'GSO-1',
            'btnText' => 'Validate'
        ],
        [
            'step' => 4,
            'designation' => 'hod',
            'name' => 'HOD',
            'btnText' => 'Select Supplier'
        ],
        [
            'step' => 5,
            'designation' => 'ddgms',
            'name' => 'DDGMS',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 6,
            'designation' => 'csg',
            'name' => 'Consultant Surgeon General',
            'btnText' => 'Countersign'
        ]
    ];

    const Dental = [
        'EMProcurement' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark',
                'btnText' => 'Create & Forward'
            ],
            [

                'step' => 2,
                'designation' => 'cgo-1',
                'name' => 'CGO-1',
                'btnText' => 'Validate'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 4,
                'designation' => 'hod',
                'name' => 'HOD',
                'btnText' => 'Select Supplier'
            ],
            [
                'step' => 5,
                'designation' => 'gso-2',
                'name' => 'GSO-1 (Dental)',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 6,
                'designation' => 'ddgms',
                'name' => 'DDGMS',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 7,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General',
                'btnText' => 'Countersign'
            ]
        ],
        'Medicine' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark',
                'btnText' => 'Create & Forward'
            ],
            [

                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control',
                'btnText' => 'Validate'
            ],
            [
                'step' => 3,
                'designation' => 'gso-1',
                'name' => 'GSO-1',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 4,
                'designation' => 'hod',
                'name' => 'HOD',
                'btnText' => 'Select Supplier'
            ],
            [
                'step' => 5,
                'designation' => 'gso-2',
                'name' => 'GSO-1 (Dental)',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 6,
                'designation' => 'ddgms',
                'name' => 'DDGMS',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 7,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General',
                'btnText' => 'Countersign'
            ]
        ],
        'Disposable' => [
            [
                'step' => 1,
                'designation' => 'head_clark',
                'name' => 'head clark',
                'btnText' => 'Create & Forward'
            ],
            [
                'step' => 2,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control',
                'btnText' => 'Validate'
            ],
            [
                'step' => 3,
                'designation' => 'gso-2',
                'name' => 'GSO-1 (Dental)',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 4,
                'designation' => 'hod',
                'name' => 'HOD',
                'btnText' => 'Select Supplier'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS',
                'btnText' => 'Countersign'
            ],
            [
                'step' => 6,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General',
                'btnText' => 'Countersign'
            ]
        ]
    ];

    const Medicine = [
        [
            'step' => 1,
            'designation' => 'head_clark',
            'name' => 'head clark',
            'btnText' => 'Create & Forward'
        ],
        [
            'step' => 2,
            'designation' => 'c&c',
            'name' => 'Correspondance & Control',
            'btnText' => 'Validate'
        ],
        [
            'step' => 3,
            'designation' => 'gso-1',
            'name' => 'GSO-1',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 4,
            'designation' => 'hod',
            'name' => 'HOD',
            'btnText' => 'Select Supplier'
        ],
        [
            'step' => 5,
            'designation' => 'ddgms',
            'name' => 'DDGMS',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 6,
            'designation' => 'cpg',
            'name' => 'Consultant Physician General',
            'btnText' => 'Countersign'
        ]
    ];

    const Reagent = [
        [
            'step' => 1,
            'designation' => 'head_clark',
            'name' => 'head clark',
            'btnText' => 'Create & Forward'
        ],
        [
            'step' => 2,
            'designation' => 'p&p',
            'name' => 'Planning & Purchase',
            'btnText' => 'Validate'
        ],
        [
            'step' => 3,
            'designation' => 'gso-1',
            'name' => 'GSO-1',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 4,
            'designation' => 'hod',
            'name' => 'HOD',
            'btnText' => 'Select Supplier'
        ],
        [
            'step' => 5,
            'designation' => 'ddgms',
            'name' => 'DDGMS',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 6,
            'designation' => 'csg',
            'name' => 'Consultant Surgeon General',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 7,
            'designation' => 'dgms',
            'name' => 'DGMS',
            'btnText' => 'Countersign and Approval'
        ],
    ];

    const Disposable = [
        [
            'step' => 1,
            'designation' => 'head_clark',
            'name' => 'head clark',
            'btnText' => 'Create & Forward'
        ],
        [
            'step' => 2,
            'designation' => 'c&c',
            'name' => 'Correspondance & Control',
            'btnText' => 'Forward'
        ],
        [
            'step' => 3,
            'designation' => 'gso-1',
            'name' => 'GSO-1',
            'btnText' => 'Validate'
        ],
        [
            'step' => 4,
            'designation' => 'hod',
            'name' => 'HOD',
            'btnText' => 'Select Supplier'
        ],
        [
            'step' => 5,
            'designation' => 'ddgms',
            'name' => 'DDGMS',
            'btnText' => 'Countersign'
        ],
        [
            'step' => 6,
            'designation' => 'csg',
            'name' => 'Consultant Surgeon General',
            'btnText' => 'Countersign'
        ]
    ];

    public static function nextStep($csr_id)
    {

        $csr = Csr::find($csr_id);
        $csr_approval = CsrApproval::where('csr_id', $csr_id)->latest()->first();
        $item_type = $csr->csrDemands[0]->notesheet->notesheet_item_type;
        if ($csr_approval) {
            if (($csr_approval->role_name == 'dgms' && $item_type == 4) || (($csr_approval->role_name == 'csg' || $csr_approval->role_name == 'cpg' || $csr_approval->role_name == 'dsg') && $item_type != 4)) {
                return [
                    'designation' => null
                ];
            }
            $next_steps = $csr_approval->step_number + 1;
        } else {
            $next_steps = 1;
        }


        $is_dental = $csr->csrDemands[0]->notesheet->is_dental;
        $current_approval_step = '';

        try {

            if (($item_type == 3 && $is_dental) || ($item_type == 1 && $is_dental) || ($item_type == 5 && $is_dental)) {
                // $current_approval_step = CsrApprovalSetps::Dental[$next_steps - 1];
                if ($item_type == 1) {
                    $current_approval_step = CsrApprovalSetps::Dental['EMProcurement'][$next_steps - 1];
                } elseif ($item_type == 3) {
                    $current_approval_step = CsrApprovalSetps::Dental['Medicine'][$next_steps - 1];
                } elseif ($item_type == 5) {
                    $current_approval_step = CsrApprovalSetps::Dental['Disposable'][$next_steps - 1];
                }
            } elseif ($item_type == 1) {
                $current_approval_step = CsrApprovalSetps::EMProcurementSteps[$next_steps - 1];
            } elseif ($item_type == 3) {
                $current_approval_step = CsrApprovalSetps::Medicine[$next_steps - 1];
            } elseif ($item_type == 4) {
                $current_approval_step = CsrApprovalSetps::Reagent[$next_steps - 1];
            } elseif ($item_type == 5) {
                $current_approval_step = CsrApprovalSetps::Disposable[$next_steps - 1];
            }
        } catch (Exception $e) {
            return [
                'designation' => null
            ];
        }

        return $current_approval_step;
    }
    public static function nextDesignation($csr_id)
    {

        $csr = Csr::find($csr_id);

        $csr_approval = CsrApproval::where('csr_id', $csr_id)->latest()->first();
        $item_type = $csr->csrDemands[0]->notesheet->notesheet_item_type;
        if ($csr_approval) {
            if (($csr_approval->role_name == 'dgms' && $item_type == 4) || (($csr_approval->role_name == 'csg' || $csr_approval->role_name == 'cpg' || $csr_approval->role_name == 'dsg') && $item_type != 4)) {
                return [
                    'designation' => null
                ];
            }
            $next_steps = $csr_approval->step_number + 1;
        } else {
            $next_steps = 1;
        }

        $is_dental = $csr->csrDemands[0]->notesheet->is_dental;
        $next_approval_step = "";

        if (($item_type == 3 && $is_dental == 1) || ($item_type == 1 && $is_dental == 1) || ($item_type == 5 && $is_dental == 1)) {
            if ($item_type == 1) {
                $next_approval_step = count(CsrApprovalSetps::Dental['EMProcurement']) > $next_steps ? CsrApprovalSetps::Dental['EMProcurement'][$next_steps] : '';
            } elseif ($item_type == 3) {
                $next_approval_step = count(CsrApprovalSetps::Dental['Medicine']) > $next_steps ? CsrApprovalSetps::Dental['Medicine'][$next_steps] : '';
            } elseif ($item_type == 5) {
                $next_approval_step = count(CsrApprovalSetps::Dental['Disposable']) > $next_steps ? CsrApprovalSetps::Dental['Disposable'][$next_steps] : '';
            }
        } elseif ($item_type == 1) {
            $next_approval_step = count(CsrApprovalSetps::EMProcurementSteps) > $next_steps ? CsrApprovalSetps::EMProcurementSteps[$next_steps] : '';
        } elseif ($item_type == 3) {
            $next_approval_step = count(CsrApprovalSetps::Medicine) > $next_steps ? CsrApprovalSetps::Medicine[$next_steps] : '';
        } elseif ($item_type == 4) {
            $next_approval_step = count(CsrApprovalSetps::Reagent) > $next_steps ? CsrApprovalSetps::Reagent[$next_steps] : '';
        } elseif ($item_type == 5) {
            $next_approval_step = count(CsrApprovalSetps::Disposable) > $next_steps ? CsrApprovalSetps::Disposable[$next_steps] : '';
        }
        return $next_approval_step;
    }
}
