<?php

namespace App\Http\Controllers;

use PDF;
use NPDF;
use SPDF;
use App\Models\Csr;
use App\Models\User;
use App\Models\Demand;
use App\Models\Tender;
use App\Models\Purchase;
use App\Models\Notesheet;
use App\Models\Workorder;
use App\Models\DemandPvms;
use App\Models\AccountUnit;
use App\Models\CsrApproval;
use App\Models\PurchaseType;
use Illuminate\Http\Request;
use App\Models\FinancialYear;
use App\Models\VendorBidding;
use App\Models\DemandApproval;
use App\Models\DemandDocument;
use App\Models\SubOrganization;
use App\Models\UserApprovalRole;
use App\Models\NotesheetApproval;
use Illuminate\Support\Facades\DB;
use App\Models\NotesheetDemandPVMS;
use App\Models\CsrCoverLetterMember;
use Illuminate\Support\Facades\Auth;
use App\Models\CsrCoverLetterPresident;
use Illuminate\Support\Facades\Redirect;
use App\Models\CsrCoverLetterCoOperativeMember;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class PDFController extends Controller
{
    public function demandPDF($id)
    {
        $demand = Demand::where('id', $id)->first();
        $demandDoc = DemandDocument::where('demand_id', $id)->count();
        $createdBy = $demand->created_by;
        $sub_org_id = User::where('id', $createdBy)->first();

        if ($demand === null) {
            return redirect('demand')->with('error', 'Fail to Download');
        }
        $demands = DemandPvms::where('demand_id', $id)
            ->leftJoin('demands', 'demands.id', '=', 'demand_pvms.demand_id')
            ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'demand_pvms.p_v_m_s_id')
            ->leftJoin('account_units', 'account_units.id', '=', 'p_v_m_s.account_units_id')
            ->select('demand_pvms.*', 'demands.description1', 'demands.description', 'demands.demand_type_id', 'p_v_m_s.pvms_id', 'p_v_m_s.nomenclature', 'account_units.name as a_name')
            ->get();
        $afmsdFlag = 0;
        foreach ($demands as $d) {
            if (isset($d->proposed_reqr) && !empty($d->proposed_reqr)) {
                $afmsdFlag = 1;
                break;
            }
        }
        $demand_type = Demand::where('id', $id)->first();
        $subOrg = SubOrganization::where('id', $sub_org_id->sub_org_id)->first();

        $OIC = User::where('sub_org_id', $sub_org_id->sub_org_id)
            ->where('user_approval_role_id', 1)
            ->orderBy('id', 'desc')
            ->first();
        $clerk = User::where('sub_org_id', $sub_org_id->sub_org_id)
            ->leftJoin('user_approval_roles', 'user_approval_roles.id', '=', 'users.user_approval_role_id')
            ->where('role_key', 'cmh_clark')
            ->select('users.*')
            ->orderBy('users.id', 'desc')
            ->first();
        $approved = DemandApproval::where('demand_id', $id)
            ->leftJoin('users', 'users.id', '=', 'demand_approvals.approved_by')
            ->select('users.*', 'demand_approvals.role_name', 'demand_approvals.created_at as sign_date')
            ->get();
        $finYear = FinancialYear::latest()->first();
        // dd($approved);
        $data = [
            'title' => 'Demand',
            'demands' => $demands,
            'subOrg' => $subOrg,
            'demand' => $demand,
            'OIC' => $OIC,
            'type' => $demand_type,
            'clerk' => $clerk,
            'approved' => $approved,
            'afmsdFlag' => $afmsdFlag,
            'finYear' => $finYear,
            'demandDoc' => $demandDoc,
        ];

        // $mpdf = new \Mpdf\Mpdf([
        //     'default_font' => 'nikosh',
        //     'format' => 'Legal',
        // ]);
        // $html = view('admin.pdf.demandPDF', $data);
        // $mpdf->WriteHTML($html);

        // $mpdf->Output();

        // $pdf = PDF::loadView('admin.pdf.demandPDF', $data);

        // return $pdf->download('Demand- '.now().'.pdf');


        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'format' => 'Legal',
            'default_font' => 'nikosh',
            'fontDir' => array_merge($fontDirs, [
                resource_path('fonts'),
            ]),
            'fontdata' => $fontData + [
                'nikosh' => [
                    'R' => 'Nikosh.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
        ]);

        // $data = [
        //     'title' => 'বাংলা পিডিএফ রিপোর্ট',
        //     // add more data as needed
        // ];

        $html = view('admin.pdf.demandPDF', $data)->render();
        $mpdf->WriteHTML($html);
        return $mpdf->Output();
    }



    public function demandPDFByUUID($uuid)
    {
        try {
            $id = Demand::where('uuid', $uuid)->value('id');
            if (!$id) {
                return response()->json(['error' => 'No demand record found for the provided Demand No.'], 404);
            }

            $demand = Demand::where('id', $id)->first();
            if ($demand === null) {
                return response()->json(['error' => 'Demand not found'], 404);
            }

            // Rest of the code...
            $demandDoc = DemandDocument::where('demand_id', $id)->count();
            $createdBy = $demand->created_by;
            $sub_org_id = User::where('id', $createdBy)->first();

            $demands = DemandPvms::where('demand_id', $id)
                ->leftJoin('demands', 'demands.id', '=', 'demand_pvms.demand_id')
                ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'demand_pvms.p_v_m_s_id')
                ->leftJoin('account_units', 'account_units.id', '=', 'p_v_m_s.account_units_id')
                ->select('demand_pvms.*', 'demands.description1', 'demands.description', 'demands.demand_type_id', 'p_v_m_s.pvms_id', 'p_v_m_s.nomenclature', 'account_units.name as a_name')
                ->get();

            $afmsdFlag = 0;
            foreach ($demands as $d) {
                if (isset($d->proposed_reqr) && !empty($d->proposed_reqr)) {
                    $afmsdFlag = 1;
                    break;
                }
            }

            $demand_type = Demand::where('id', $id)->first();
            $subOrg = SubOrganization::where('id', $sub_org_id->sub_org_id)->first();

            $OIC = User::where('sub_org_id', $sub_org_id->sub_org_id)
                ->where('user_approval_role_id', 1)
                ->orderBy('id', 'desc')
                ->first();

            $clerk = User::where('sub_org_id', $sub_org_id->sub_org_id)
                ->leftJoin('user_approval_roles', 'user_approval_roles.id', '=', 'users.user_approval_role_id')
                ->where('role_key', 'cmh_clark')
                ->select('users.*')
                ->orderBy('users.id', 'desc')
                ->first();

            $approved = DemandApproval::where('demand_id', $id)
                ->leftJoin('users', 'users.id', '=', 'demand_approvals.approved_by')
                ->select('users.*', 'demand_approvals.role_name', 'demand_approvals.created_at as sign_date')
                ->get();

            $finYear = FinancialYear::latest()->first();

            $data = [
                'title' => 'Demand',
                'demands' => $demands,
                'subOrg' => $subOrg,
                'demand' => $demand,
                'OIC' => $OIC,
                'type' => $demand_type,
                'clerk' => $clerk,
                'approved' => $approved,
                'afmsdFlag' => $afmsdFlag,
                'finYear' => $finYear,
                'demandDoc' => $demandDoc,
            ];

            $mpdf = new \Mpdf\Mpdf([
                'default_font' => 'nikosh',
                'format' => 'Legal',
            ]);

            $html = view('admin.pdf.demandPDF', $data);
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function issueOrderPDF($id)
    {
        // Include the subOrganization relationship in the query
        $purchase = Purchase::with(['purchaseTypes.pvms.accountUnit', 'subOrganization'])->find($id);

        if ($purchase) {
            $purchaseDetails = $purchase->toArray();
            $purchaseTypes = $purchase->purchaseTypes;
            $count = $purchaseTypes->count();

            // Get the sub organization name
            $subOrganizationName = $purchase->subOrganization->name ?? 'N/A';  // Check if subOrganization exists
        }

        if ($purchase->status == 'approved') {
            $OIC = User::where('id', $purchase->send_to)->first();
        } else {
            $OIC = null;
        }

        $data = [
            'title' => 'Issue Order',
            'purchase' => $purchase,
            'purchaseDetails' => $purchaseDetails,
            'purchaseTypes' => $purchaseTypes->toArray(),
            'count' => $count,
            'subOrganizationName' => $subOrganizationName,
            'OIC' => isset($OIC) ? $OIC : null,
        ];




        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'nikosh',
            'format' => 'Legal',
        ]);

        $html = view('admin.pdf.issueOrderPDF', $data)->render();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    public function tenderPDF($id)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'nikosh',
            'format' => 'Legal',
        ]);


        $tender = Tender::where('id', $id)->first();

        if (empty($tender)) {
            return redirect('tender')->with('error', 'Fail to Download');
        }

        $user = User::where('email', 'DADGMS_CC')->first();
        // dd($user);

        $data = [
            'title' => 'Tender',
            'tender' => $tender,
            'user' => $user,
        ];

        $html = view('admin.pdf.tenderPDF', ['tender' => $tender, 'user' => $user]);
        $mpdf->WriteHTML($html);

        $mpdf->Output();
    }

    public function notesheetPDF($id)
    {
        // $mpdf = new \Mpdf\Mpdf([
        //     'default_font' => 'nikosh',
        //     'format' => 'Legal',
        // ]);
        // $mpdf->jSWord = 0.4;
        // $mpdf->jSmaxChar = 2;

        $notesheet = Notesheet::find($id);
        //  dd($notesheet);
        if ($notesheet->is_rate_running == 1) {
            $notesheetPVMS = DB::table('notesheet_demand_pvms')->groupBy('notesheet_demand_pvms.pvms_id')
                ->where('notesheet_demand_pvms.notesheet_id', $id)
                ->leftJoin('demands', 'demands.id', '=', 'notesheet_demand_pvms.demand_id')
                ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'notesheet_demand_pvms.pvms_id')
                ->leftJoin('sub_organizations', 'sub_organizations.id', '=', 'demands.sub_org_id')
                ->leftJoin('account_units', 'account_units.id', '=', 'p_v_m_s.account_units_id')
                ->leftJoin('rate_running_pvms', 'rate_running_pvms.pvms_id', '=', 'p_v_m_s.id')
                ->leftJoin('users', 'users.id', '=', 'rate_running_pvms.supplier_id')
                ->orderBy('demands.created_at', 'asc')
                ->selectRaw('rate_running_pvms.price,users.name as supplier_name,rate_running_pvms.tender_ser_no,notesheet_demand_pvms.*,notesheet_demand_pvms.pvms_id as p_id, sum(notesheet_demand_pvms.total_quantity) as sum,sub_organizations.name as sub_name,account_units.name as acc_name,demands.created_at as demand_date,demands.uuid,demands.sub_org_id,p_v_m_s.pvms_id,p_v_m_s.nomenclature')
                ->get();
        } else {
            $notesheetPVMS = DB::table('notesheet_demand_pvms')->groupBy('notesheet_demand_pvms.pvms_id')
                ->where('notesheet_demand_pvms.notesheet_id', $id)
                ->leftJoin('demands', 'demands.id', '=', 'notesheet_demand_pvms.demand_id')
                ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'notesheet_demand_pvms.pvms_id')
                ->leftJoin('sub_organizations', 'sub_organizations.id', '=', 'demands.sub_org_id')
                ->leftJoin('account_units', 'account_units.id', '=', 'p_v_m_s.account_units_id')
                ->orderBy('demands.demand_date', 'asc')
                ->selectRaw('notesheet_demand_pvms.*,notesheet_demand_pvms.pvms_id as p_id, sum(notesheet_demand_pvms.total_quantity) as sum,sub_organizations.name as sub_name,account_units.name as acc_name,demands.created_at as demand_date2,demands.uuid,demands.demand_date,demands.sub_org_id,p_v_m_s.pvms_id,p_v_m_s.nomenclature')
                ->get();
        }

        //  dd($notesheetPVMS);
        foreach ($notesheetPVMS as $k => $p) {
            $data = NotesheetDemandPVMS::where('notesheet_demand_pvms.pvms_id', $p->p_id)
                ->where('notesheet_demand_pvms.notesheet_id', $id)
                ->leftJoin('demands', 'demands.id', '=', 'notesheet_demand_pvms.demand_id')
                ->leftJoin('sub_organizations', 'sub_organizations.id', '=', 'demands.sub_org_id')
                ->select('sub_organizations.name as sub_name')
                ->get();
            //    dd($data);
            if (count($data) > 1) {
                $arr = [];
                foreach ($data as $d) {
                    array_push($arr, $d->sub_name);
                }
                $notesheetPVMS[$k]->dmd = $arr;
            }
        }

        // dd($notesheetPVMS);


        $notesheetApp = NotesheetApproval::where('notesheet_id', $id)
            ->leftJoin('user_approval_roles', 'user_approval_roles.role_key', '=', 'notesheet_approvals.role_name')
            ->leftJoin('users', 'users.user_approval_role_id', '=', 'user_approval_roles.id')
            ->select('notesheet_approvals.*', 'users.sign')
            ->get();

        $gso_1 = UserApprovalRole::where('role_key', 'gso-1')
            ->leftJoin('users', 'users.user_approval_role_id', '=', 'user_approval_roles.id')
            ->select('users.*')
            ->first();
        if (empty($notesheet)) {
            return redirect()->back()->with('error', 'Fail to Download');
        }



        //  dd($notesheet);

        $data = [
            'title' => 'Note Sheet',
            'notesheet' => $notesheet,
        ];
        //  return view('admin.pdf.note');
        if ($notesheet->is_rate_running == 1) {
            $html = view('admin.pdf.rate_running', ['notesheet' => $notesheet, 'notesheetPVMS' => $notesheetPVMS, 'notesheetApp' => $notesheetApp, 'gso_1' => $gso_1]);
        } else {
            $html = view('admin.pdf.note', ['notesheet' => $notesheet, 'notesheetPVMS' => $notesheetPVMS, 'notesheetApp' => $notesheetApp, 'gso_1' => $gso_1]);
        }

        // $mpdf->WriteHTML($html);

        // $mpdf->Output();
        // $mpdf->debug = true;

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'format' => 'Legal',
            'default_font' => 'nikosh',
            'fontDir' => array_merge($fontDirs, [
                resource_path('fonts'),
            ]),
            'fontdata' => $fontData + [
                'nikosh' => [
                    'R' => 'Nikosh.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
        ]);

        // $data = [
        //     'title' => 'বাংলা পিডিএফ রিপোর্ট',
        //     // add more data as needed
        // ];

        // $html = view('admin.pdf.index', $data)->render();
        $html = $html->render();

        if (ob_get_length()) {
            ob_end_clean();
        }

        // dd($html);

        $mpdf->WriteHTML($html);
        return $mpdf->Output();
    }


    // afmsd issue PDF 
    public function afmsdIssuePDF($id)
    {

        $data = Purchase::with([
            'purchaseTypes',
            'subOrganization',
            'financialYear',
            // 'purchasePvms.pvms.itemTypename',
            // 'purchasePvms.demand.demandType',
            'purchaseTypes.purchaseDelivery',
            'purchaseTypes.demand.demandType',
            'purchaseTypes.pvms.accountUnit',
            'purchaseTypes.pvms.itemTypename',
        ])->where('id', $id)
            ->orderBy('id', 'desc')
            ->get();

         
       
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'format' => 'A4',
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'nikosh' => [
                    'R' => 'Nikosh.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ]
            ],
            'default_font' => 'nikosh'
        ]);
        // dd($data);
        $html = view('admin.pdf.afmsdIssuePDF', ['data' => $data])->render();
        $mpdf->WriteHTML($html);

        // PDF content as string
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="issue-' . $id . '.pdf"');
    }


    public function csrPDF($id)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'nikosh',
        ]);
        $mpdf->shrink_tables_to_fit = 0;
        $mpdf->text_input_as_HTML = true;
        $mpdf->keep_table_proportions = true;

        $csr = Csr::where('csr.id', $id)
            ->leftJoin('tenders', 'tenders.id', '=', 'csr.tender_id')
            ->select('csr.*', 'tenders.deadline', 'tenders.tender_no')
            ->first();

        $matchedCsrs = Csr::where('tender_id', $csr->tender_id)->get();

        $position = 0;

        foreach ($matchedCsrs as $key => $matchedCsr) {
            if ($matchedCsr->pvms_id == $csr->pvms_id) {
                $position = $key + 1;
                break;
            }
        }

        $acc_unit = AccountUnit::where('id', $csr->PVMS->account_units_id)->first();

        foreach ($csr->vandorPerticipate as $k => $c) {
            $vendor = DB::table('users')->where('id', $c->vendor_id)->where('is_vendor', 1)->first();
            $csr->vandorPerticipate[$k]->v_name = $vendor->name;
        }

        $CSRApp = CsrApproval::where('csr_id', $id)
            ->leftJoin('user_approval_roles', 'user_approval_roles.role_key', '=', 'csr_approvals.role_name')
            ->leftJoin('users', function ($join) {
                $join->on('users.user_approval_role_id', '=', 'user_approval_roles.id')
                    ->whereColumn('csr_approvals.approved_by', 'users.id');
            })
            ->select('csr_approvals.*', 'users.sign', 'users.name', 'users.email', 'users.id', 'users.rank', 'users.address')
            ->groupBy('csr_approvals.id')
            ->get();

        $gso_1 = UserApprovalRole::where('role_key', 'gso-1')
            ->leftJoin('users', 'users.user_approval_role_id', '=', 'user_approval_roles.id')
            ->select('users.*')
            ->first();

        $data = [
            'title' => 'CSR',
            'position' => $position,
        ];

        $html = view('admin.pdf.csrPDF', ['csr' => $csr, 'acc_unit' => $acc_unit, 'position' => $position, 'CSRApp' => $CSRApp, 'gso_1' => $gso_1]);
        $mpdf->WriteHTML($html);

        $mpdf->Output();
    }

    public function workOrder($id)
    {

        $order = Workorder::where('id', $id)->first();
        $createdBy = $order->created_by;
        $sub_org_id = User::where('id', $createdBy)->first();

        if ($order === null) {
            return redirect()->back()->with('error', 'Fail to Download');
        }
        $workorders = Workorder::where('workorders.id', $id)
            ->leftJoin('workorder_pvms', 'workorder_pvms.workorder_id', '=', 'workorders.id')
            ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'workorder_pvms.pvms_id')
            ->leftJoin('account_units', 'account_units.id', '=', 'p_v_m_s.account_units_id')
            ->select('workorders.vendor_id', 'workorders.order_no', 'workorders.total_amount', 'workorders.contract_number', 'workorders.contract_date', 'workorders.last_submit_date', 'workorders.notesheet_details1', 'workorders.notesheet_details', 'workorder_pvms.*', 'p_v_m_s.pvms_id', 'p_v_m_s.nomenclature')
            ->get();

        dd($workorders);

        $data = [];
        // $afmsdFlag = 0;
        // foreach($demands as $d){
        //     if(isset($d->proposed_reqr) && !empty($d->proposed_reqr)){
        //         $afmsdFlag = 1;
        //         break;
        //     }
        // }
        // $demand_type = Demand::where('id',$id)->first();
        // $subOrg = SubOrganization::where('id', $sub_org_id->sub_org_id)->first();

        // $OIC = User::where('sub_org_id', $sub_org_id->sub_org_id)
        //     ->where('user_approval_role_id', 1)
        //     ->orderBy('id','desc')
        //     ->first();
        // $clerk = User::where('sub_org_id', $sub_org_id->sub_org_id)
        //     ->leftJoin('user_approval_roles','user_approval_roles.id','=','users.user_approval_role_id')
        //     ->where('role_key', 'cmh_clark')
        //     ->select('users.*')
        //     ->orderBy('users.id','desc')
        //     ->first();
        // $approved = DemandApproval::where('demand_id',$id)
        //     ->leftJoin('users','users.id','=','demand_approvals.approved_by')
        //     ->select('users.*','demand_approvals.role_name','demand_approvals.created_at as sign_date')
        //     ->get();
        // $finYear = FinancialYear::latest()->first();
        // // dd($approved);
        // $data = [
        //     'title' => 'Demand',
        //     'demands' => $demands,
        //     'subOrg' => $subOrg,
        //     'demand' => $demand,
        //     'OIC' => $OIC,
        //     'type' => $demand_type,
        //     'clerk' => $clerk,
        //     'approved' => $approved,
        //     'afmsdFlag' => $afmsdFlag,
        //     'finYear' => $finYear,
        //     'demandDoc' => $demandDoc,
        // ];

        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'nikosh',
            'format' => 'Legal',
        ]);
        $html = view('admin.pdf.demandPDF', $data);
        $mpdf->WriteHTML($html);

        $mpdf->Output();
    }

    public function issuePDF($id)
    {

        $issue = Purchase::where('purchase.id', $id)
            ->leftJoin('sub_organizations', 'sub_organizations.id', '=', 'purchase.sub_org_id')
            ->select('purchase.*', 'sub_organizations.name')
            ->first();
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'nikosh',
            'format' => 'Legal-L',
            'orientation' => 'L'
        ]);
        $data = PurchaseType::where('purchase_types.id', $id)
            ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'purchase_types.pvms_id')
            ->leftJoin('account_units', 'account_units.id', '=', 'p_v_m_s.account_units_id')
            ->select('purchase_types.*', 'p_v_m_s.pvms_name', 'p_v_m_s.pvms_name', 'p_v_m_s.nomenclature', 'account_units.name',)
            ->get();
        //  dd($data);
        $html = view('admin.pdf.issuePDF', ['data' => $data, 'issue' => $issue]);
        $mpdf->WriteHTML($html);

        $mpdf->Output();
    }

    function generate_pdf($id)
    {
        $notesheet = Notesheet::find($id);
        $notesheetPVMS = NotesheetDemandPVMS::where('notesheet_demand_pvms.notesheet_id', $id)
            ->leftJoin('demands', 'demands.id', '=', 'notesheet_demand_pvms.demand_id')
            ->leftJoin('p_v_m_s', 'p_v_m_s.id', '=', 'notesheet_demand_pvms.pvms_id')
            ->leftJoin('sub_organizations', 'sub_organizations.id', '=', 'demands.sub_org_id')
            ->leftJoin('account_units', 'account_units.id', '=', 'p_v_m_s.account_units_id')
            ->select('notesheet_demand_pvms.*', 'account_units.name as acc_name', 'demands.created_at as demand_date', 'demands.uuid', 'demands.sub_org_id', 'p_v_m_s.pvms_id', 'p_v_m_s.nomenclature', 'sub_organizations.name as sub_name')
            ->get();
        $data = [
            'notesheet' => $notesheet,
            'notesheetPVMS' => $notesheetPVMS
        ];
        // $pdf = SPDF::loadView('admin.pdf.document', $data);
        // $pdf->download('invoice.pdf');
        $pdf = SPDF::loadView('admin.pdf.document', $data);
        return $pdf->download('invoice.pdf');
    }

    public function csrCoverLetter($id)
    {

        $tender = Tender::where('tenders.id', $id)
            ->leftJoin('csr_cover_letters', 'csr_cover_letters.tender_id', '=', 'tenders.id')
            ->select('tenders.tender_no', 'tenders.start_date', 'tenders.deadline', 'csr_cover_letters.details', 'csr_cover_letters.id')
            ->first();
        if (isset($tender->details) && !empty($tender->details)) {
            // dd($tender);
        } else {
            return redirect()->back()->with('error', 'Fail to Download.');
        }
        $president = CsrCoverLetterPresident::where('csr_cover_letter_presidents.csr_cover_letter_id', $tender->id)
            ->orderBy('csr_cover_letter_presidents.id', 'desc')
            ->leftJoin('users', 'users.id', '=', 'csr_cover_letter_presidents.user_id')
            ->select('users.*')
            ->first();

        $member = CsrCoverLetterMember::where('csr_cover_letter_members.csr_cover_letter_id', $tender->id)
            ->leftJoin('users', 'users.id', '=', 'csr_cover_letter_members.user_id')
            ->select('users.*')
            ->get();

        $co_member = CsrCoverLetterCoOperativeMember::where('csr_cover_letter_co_operative_members.csr_cover_letter_id', $tender->id)
            ->leftJoin('users', 'users.id', '=', 'csr_cover_letter_co_operative_members.user_id')
            ->select('users.*')
            ->get();
        // dd($co_member);
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'nikosh',
            'format' => 'Legal',
            // 'orientation' => 'L'
        ]);

        $html = view('admin.pdf.tender_cover_letter', ['tender' => $tender, 'president' => $president, 'member' => $member, 'co_member' => $co_member]);
        $mpdf->WriteHTML($html);

        $mpdf->Output();
    }

    public function patientFile(Request $request)
    {

        $documents  = DemandDocument::where('demand_id', $request->id)->get();
        return response()->json($documents);

        // foreach($documents as $document){
        //     $path = public_path('/storage/demand_documents/'.$document->file);
        //     // return response()->file($path);
        //     if (file_exists($path)) {
        //         $headers = array(
        //             "Content-type"        => "application/pdf",
        //             "Content-Disposition" => "attachment; filename=$document->file",
        //             "Pragma"              => "no-cache",
        //             "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        //             "Expires"             => "0"
        //         );

        //         return response()->download($path,$document->file,$headers);
        //     }else{
        //         echo $path;
        //     }
        // return response()->download(public_path('demand_documents/'.$document->file));
        // }

    }

    public function patientFileOpen($id)
    {
        $documents  = DemandDocument::where('id', $id)->first();
        $path = public_path('/storage/demand_documents/' . $documents->file);
        $headers = array(
            "Content-type"        => "application/pdf",
            "Content-Disposition" => "attachment; filename=$documents->file",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        return response()->file($path);
    }
}
