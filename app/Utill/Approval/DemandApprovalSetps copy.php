<?php

namespace App\Utill\Approval;

use App\Models\ApprovalLayer;
use App\Models\Demand;
use App\Models\DemandApproval;

class DemandApprovalSetps
{
    const EMProcurementAfmsdSteps = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General'
            ]
        ]

    ];

    const EMProcurementSteps = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'csg',
                'name' => 'Consultant Surgeon General'
            ]
        ]

    ];

    const Dental = [];
    // $demand->createdBy && $demand->createdBy->sub_org_id == 2
    const EMDentalAfmsd = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ]
    ];
    const EMDental = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ]
    ];

    const MedicineAfmsd = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ],
            [
                'step' => 5,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ]
    ];
    const Medicine = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ],
            [
                'step' => 7,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ]
    ];

    const MedicineDentalAfmsd = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ]
    ];
    const MedicineDental = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ],
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'cgo-1',
                'name' => 'CGO-1'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ]
    ];

    const ReagentAfmsd =
    [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ]
    ];

    const Reagent = [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'p&p',
                'name' => 'Planning & Purchase'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'cpg',
                'name' => 'Consultant Physician General'
            ]
        ]
    ];

    const DisposableAfmsd =
    [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'ds',
                'name' => 'Dental Surgeon'
            ],
            [
                'step' => 7,
                'designation' => 'dgms',
                'name' => 'DGMS'
            ]
        ],

    ];
    const Disposable =
    [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'ds',
                'name' => 'Dental Surgeon'
            ],
            [
                'step' => 9,
                'designation' => 'dgms',
                'name' => 'DGMS'
            ]
        ],

    ];

    const DisposableDentalAfmsd =
    [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 3,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 4,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 5,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 6,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ]
    ];
    const DisposableDental =
    [
        'issued' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'lp' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ]
        ],
        'on-loan' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ],
        'notesheet' => [
            [
                'step' => 1,
                'designation' => 'oic',
                'name' => 'Oic'
            ],
            [
                'step' => 2,
                'designation' => 'deputy_commandend',
                'name' => 'Deputy Commandend'
            ],
            [
                'step' => 3,
                'designation' => 'cmdt',
                'name' => 'CMDT'
            ],
            [
                'step' => 4,
                'designation' => 'head_clark',
                'name' => 'head clark'
            ],
            [
                'step' => 5,
                'designation' => 'c&c',
                'name' => 'Correspondance & Control'
            ],
            [
                'step' => 6,
                'designation' => 'gso-1',
                'name' => 'GSO-1'
            ],
            [
                'step' => 7,
                'designation' => 'ddgms',
                'name' => 'DDGMS'
            ],
            [
                'step' => 8,
                'designation' => 'dsg',
                'name' => 'Dental Surgeon General'
            ]
        ]
    ];

    const Repair = [
        [
            'step' => 1,
            'designation' => 'jco',
            'name' => 'JCO'
        ],
        [
            'step' => 2,
            'designation' => 'go',
            'name' => 'GO'
        ],
        [
            'step' => 3,
            'designation' => 'oic-repair',
            'name' => 'OIC Repair Cell'
        ],
        [
            'step' => 4,
            'designation' => 'hod',
            'name' => 'Department Head'
        ],
        [
            'step' => 5,
            'designation' => 'wing-head',
            'name' => 'Wing Head'
        ],
        [
            'step' => 6,
            'designation' => 'oic',
            'name' => 'OIC'
        ],
        [
            'step' => 7,
            'designation' => 'deputy_commandend',
            'name' => 'Deputy Commandend'
        ],
        [
            'step' => 3,
            'designation' => 'cmdt',
            'name' => 'CMDT'
        ],
        [
            'step' => 4,
            'designation' => 'head_clark',
            'name' => 'head clark'
        ],
        [
            'step' => 8,
            'designation' => 'csg',
            'name' => 'Consultant Surgeon General'
        ],
    ];

    public static function nextStep($demand_id)
    {

        $demand = Demand::find($demand_id);

        if($demand->purchase_type){
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

        if($demand->demand_type_id == 4){
            $current_approval_step = DemandApprovalSetps::Repair;
        } else if ($demand->demand_item_type_id == 1 && $demand->is_dental_type) {
            if($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::EMDentalAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::EMDental[$purchase_type];
            }

        } elseif ($demand->demand_item_type_id == 1) {
            if($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::EMProcurementAfmsdSteps[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::EMProcurementSteps[$purchase_type];
            }

        } elseif ($demand->demand_item_type_id == 3 && $demand->is_dental_type) {
            if($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::MedicineDentalAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::MedicineDental[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 3) {
            if($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::MedicineAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::Medicine[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 4) {
            if($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::ReagentAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::Reagent[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 5 && $demand->is_dental_type) {
            if($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::DisposableDentalAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::DisposableDental[$purchase_type];
            }
        } elseif ($demand->demand_item_type_id == 5) {
            if($demand->createdBy && $demand->createdBy->sub_org_id == 2) {
                $current_approval_step = DemandApprovalSetps::DisposableAfmsd[$purchase_type];
            } else {
                $current_approval_step = DemandApprovalSetps::Disposable[$purchase_type];
            }
        }


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
