<?php

namespace App\Services;

use App\Models\Csr;
use App\Models\CsrApproval;
use App\Models\CsrDemand;
use App\Models\Tender;
use App\Models\TenderNotesheet;
use App\Models\TenderPurchases;
use App\Models\TenderRequiredDocument;
use App\Models\TenderSubmittedFile;
use App\Models\VendorBidding;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Illuminate\Support\Facades\Http;

class TenderService {
    public static function createTender($data,$fileName,$termsfilename,$requiremntfilename,$tender_id=null) {

        $tender = new Tender();

        if(isset($tender_id)) {
            $tender = Tender::find($tender_id);
        } else {
            $tender->tender_no = empty($data['tender_no']) ? time() : $data['tender_no'];
        }


        $tender->start_date = $data['start_date'];
        $tender->purchase_price = $data['purchase_price'];
        $tender->deadline = $data['deadline'];
        $tender->details = $data['details'];
        $tender->performance_security_percentage = $data['performance_security_percentage'];
        $tender->published = $data['published'];
        $tender->submission_file_name = $fileName;
        $tender->terms_conditions_file = $termsfilename;
        $tender->requirements_file = $requiremntfilename;
        $tender->status = 'pending';
        $tender->created_by = auth()->user()->id;
        $tender->updated_by = auth()->user()->id;
        $tender->save();

        return $tender;
    }

    public static function createTenderRequiredDocuments($tender_id, $required_document_id)
    {

        $tenderRequiredDocument = new TenderRequiredDocument();
        $tenderRequiredDocument->tender_id = $tender_id;
        $tenderRequiredDocument->required_document_id = $required_document_id;
        $tenderRequiredDocument->created_by = auth()->user()->id;
        $tenderRequiredDocument->updated_by = auth()->user()->id;
        $tenderRequiredDocument->save();

        return $tenderRequiredDocument;
    }

    public static function createTenderNotesheet($tender_id, $notesheet_id)
    {

        $tenderNotesheet = new TenderNotesheet();
        $tenderNotesheet->tender_id = $tender_id;
        $tenderNotesheet->notesheet_id = $notesheet_id;
        $tenderNotesheet->created_by = auth()->user()->id;
        $tenderNotesheet->updated_by = auth()->user()->id;
        $tenderNotesheet->save();

        return $tenderNotesheet;
    }

    public static function uploadToWebsite($tender)
    {
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'nikosh',
        ]);

        if (empty($tender)) {
            return redirect('tender')->with('error', 'Fail to Download');
        }

        $data = [
            'title' => 'Tender',
            'tender' => $tender,
        ];

        $html = view('admin.pdf.tenderPDF', ['tender' => $tender]);
        $mpdf->WriteHTML($html);

        $fileName = $tender->created_at->format('Y-m-d').'.pdf';
        $mpdf->Output(storage_path('app/'.$fileName), 'F');

        $file = fopen(storage_path('app/'.$fileName), 'r');

        try {
            $response = Http::attach('pdf_file', $file, $fileName)->post(env('WEBSITE_URL') . '/api/upload-tender', [
                    'title' => 'Tender Title',
                    'details' => $tender->details,
                    'tender_no' => $tender->tender_no,
                    'last_submission_date' => $tender->deadline,
                    'api_key' => env('WEBSITE_API_KEY')
                ]);
        } catch (\Throwable $th) {

        }

        unlink(storage_path('app/'.$fileName));
    }

    public static function deleteTender($tender_id) {
        $tender_notesheet = TenderNotesheet::where('tender_id',$tender_id)->delete();
        $tedner_purchase = TenderPurchases::where('tender_id',$tender_id)->delete();
        $tedner_require_document = TenderRequiredDocument::where('tender_id',$tender_id)->delete();
        $tedner_submitted_file = TenderSubmittedFile::where('tender_id',$tender_id)->delete();

        $csr_list = Csr::where('tender_id',$tender_id)->get();
        $csr_list_array = [];

        foreach ($csr_list as $eachcsr) {
            array_push($csr_list_array,$eachcsr->id);
        }

        $csr_demands = CsrDemand::whereIn('csr_id',$csr_list_array)->delete();
        $csr_approvals = CsrApproval::whereIn('csr_id',$csr_list_array)->delete();
        $vendroBidding = VendorBidding::whereIn('csr_id',$csr_list_array)->delete();
        $csr_delete = Csr::where('tender_id',$tender_id)->delete();

        $tender = Tender::find($tender_id)->delete();

        AuditService::AuditLogEntry(AuditModel::Tender,OperationTypes::Delete,$tender_id.' tender deleted',$tender,null,$tender_id);

        return true;
    }
}
