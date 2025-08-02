<?php

namespace App\Http\Controllers\Tender;

use App\Exports\PvmsExport;
use App\Http\Controllers\Controller;
use App\Models\Csr;
use App\Models\CsrDemand;
use App\Models\Notesheet;
use App\Models\NotesheetDemandPVMS;
use App\Models\Tender;
use App\Models\TenderNotesheet;
use App\Models\TenderRequiredDocument;
use App\Models\TenderSubmittedFile;
use App\Models\VendorBidding;
use App\Services\CsrService;
use App\Services\MediaService;
use App\Services\TenderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Excel;
use ZipArchive;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('admin.tender.index');
    }

    public function get_user_submitted_docs($tender_id,$user_id) {
        return TenderSubmittedFile::with('requiredDocument')->where('tender_id',$tender_id)->where('created_by',$user_id)->get();
    }
    public function download_files($id) {
        $tender = Tender::find($id);
        $files = [];

        if(!empty($tender)) {
            if(!empty($tender->submission_file_name)) {
                array_push($files,storage_path('app/public/tender-submission/'.$tender->submission_file_name));
            }
            if(!empty($tender->terms_conditions_file)) {
                array_push($files,storage_path('app/public/tender-submission/'.$tender->terms_conditions_file));
            }
            if(!empty($tender->requirements_file)) {
                array_push($files,storage_path('app/public/tender-submission/'.$tender->requirements_file));
            }
        }
        $zipFileName = $tender->tender_no.'_'.uniqid().'.zip';
        $zip = new ZipArchive;

        if ($zip->open(storage_path($zipFileName), ZipArchive::CREATE) === TRUE) {
            // Add each file to the zip archive
            foreach ($files as $file) {
                // Get the relative path to preserve the directory structure
                $relativePath = basename($file);
                $zip->addFile($file, $relativePath);
            }

            // Close the zip archive
            $zip->close();

            // Set the headers for the response
            $headers = [
                'Content-Type' => 'application/zip',
            ];

            // Return the response with the zip file
            return response()->download(storage_path($zipFileName), $zipFileName, $headers)->deleteFileAfterSend(true);
        } else {
            // Handle the case where the zip archive couldn't be created
            return response()->json(['error' => 'Failed to create zip archive'], 500);
        }
    }

    public function get_all_tender(Request $request) {
        $limit = 10;
        if($request->limit) {
            $limit = $request->limit;
        }
        // return Tender::with('tenderNotesheet.notesheet.notesheetDemandPVMS.demandPVMS.PVMS','tenderNotesheet.notesheet.notesheetDemandPVMS.demandPVMS.PVMS.unitName')->latest()->paginate(10);
        if(auth()->user()->user_approval_role_id == 2) {
            return Tender::with(['tenderCsr.PVMS.unitName','tenderCsr.PVMS.itemTypename','tenderCsr.csrPvmsApproval','tenderCsr.vandorPerticipate.vendor','tenderCsr.csrDemands','vendorSubmittedFiles.requiredDocument','vendorSubmittedFiles.validateBy','tenderNotesheet.notesheet',
            'tenderPayments' => function ($query) {
                $query->where('status', 'Success'); // Specify the ordering for children
            },])->latest()->paginate($limit);
        } else {
            return Tender::with(['tenderCsr.PVMS.unitName','tenderCsr.PVMS.itemTypename','tenderCsr.csrPvmsApproval','tenderCsr.vandorPerticipate.vendor','tenderCsr.csrDemands','vendorSubmittedFiles.requiredDocument','vendorSubmittedFiles.validateBy','tenderNotesheet.notesheet',
            'tenderPayments' => function ($query) {
                $query->where('status', 'Success'); // Specify the ordering for children
            },])->where('published',1)->latest()->paginate($limit);
        }

    }

    public function get_notesheet_readyfor_tender(Request $request) {
        if($request->keyword) {
            return Notesheet::with('notesheetDemandPVMS.PVMS.unitName')->where('notesheet_id','LIKE', '%'.$request->keyword.'%')->where('status','approved')->where('is_rate_running',0)->where('is_repair',0)->doesntHave('tenderNotesheet')->limit(5)->get();
        }
        return Notesheet::with('notesheetDemandPVMS.PVMS.unitName')->where('status','approved')->where('is_rate_running',0)->where('is_repair',0)->doesntHave('tenderNotesheet')->get();
    }

    public function uniq_tender_no($tender_no) {
        $tender_info = Tender::where('tender_no',$tender_no)->first();
        return  $tender_info;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.tender.create');
    }

    public function tender_demo_xls(Request $request) {
        if($request->note_sheets && count($request->note_sheets) > 0) {
            // $data = NotesheetDemandPVMS::with('demandPvms.PVMS')->join('demand_pvms','notesheet_demand_pvms.demand_pvms_id', '=', 'demand_pvms.id')
            //     ->select('demand_pvms.p_v_m_s_id', \DB::raw('SUM(qty) as total_qty'))
            //     ->groupBy('demand_pvms.p_v_m_s_id')->whereIn('notesheet_demand_pvms.notesheet_id', $request->note_sheets)->get();
            // dd($data);
            return Excel::download(new PvmsExport($request->note_sheets), 'tender-submit-' . strtotime("now") . '.xlsx');
        }
        return;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $filename = '';
        $termsfilename = '';
        $requirementfilename = '';
        $notesheet_list = json_decode($request->notesheets,true);

        if($notesheet_list && count($notesheet_list) > 0) {
            $uniq_id = $request->tender_no ? $request->tender_no : uniqid();
            $filename = $uniq_id. '.xlsx';

            Excel::store(new PvmsExport($notesheet_list), "public/tender-submission/".$filename);
        }

        if($request->hasFile('terms_conditions_file')) {
            $uniq_id = $request->tender_no ? $request->tender_no : uniqid();
            $termsfilename = MediaService::uploadFile($uniq_id.'termsAndConditions.'.$request->file('terms_conditions_file')->getClientOriginalExtension(),'tender-submission',$request->file('terms_conditions_file'));
        }
        if($request->hasFile('requirements_file')) {
            $uniq_id = $request->tender_no ? $request->tender_no : uniqid();
            $requirementfilename = MediaService::uploadFile($uniq_id.'specification.'.$request->file('requirements_file')->getClientOriginalExtension(),'tender-submission',$request->file('requirements_file'));
        }
        $tender = TenderService::createTender($request->all(),$filename,$termsfilename,$requirementfilename);

        $required_document_list = json_decode($request->required_documents,true);

        foreach($notesheet_list as $notesheet) {
            TenderService::createTenderNotesheet($tender->id,$notesheet);
        }

        $uniq_pvms = NotesheetDemandPVMS::select('pvms_id', \DB::raw('SUM(total_quantity) as total_quantity'))->whereIn('notesheet_id',$notesheet_list)->groupBy('pvms_id')->get();

        foreach($uniq_pvms as $each_pvms) {
            // for one uniq pvms one csr
            $csr = CsrService::createCsr($tender->id,$each_pvms->pvms_id,$each_pvms->total_quantity);
            $notesheet_pvms = NotesheetDemandPVMS::where('pvms_id',$each_pvms->pvms_id)->whereIn('notesheet_id',$notesheet_list)->get();
            foreach($notesheet_pvms as $each_notesheet_pvms) {
                CsrService::createCsrDemand($csr->id,$each_notesheet_pvms->notesheet_id,$each_notesheet_pvms->id,$each_notesheet_pvms->demand_id,$each_notesheet_pvms->demand_pvms_id,$csr->pvms_id,$each_notesheet_pvms->total_quantity);
            }
        }

        foreach($required_document_list as $required_document) {
            TenderService::createTenderRequiredDocuments($tender->id,$required_document);
        }

        if($request->published){
            TenderService::uploadToWebsite($tender);
        }

        return $tender;
    }

    public function tender_verify_vendor_doc(Request $request) {
        foreach ($request->vendor_uploaded_file as $eachFile) {
            $vendor_file = TenderSubmittedFile::find($eachFile["id"]);
            $vendor_file->is_valid = $eachFile["is_valid"];
            $vendor_file->file_checked_by = auth()->user()->id;
            $vendor_file->file_checked_at = Carbon::now();
            $vendor_file->save();
        }

        $csrs = Csr::select('id')->where('tender_id',$request->tender_id)->get();

        $csrs_id = [];

        foreach($csrs as $eachcsr) {
            array_push($csrs_id,$eachcsr["id"]);
        }

        $vendor_biddings = VendorBidding::where('vendor_id',$request->vendor)->whereIn('csr_id',$csrs_id)->get();

        foreach($vendor_biddings as $eachBid) {
            $eachBid->is_valid = $request->valid_application;
            $eachBid->is_uploaded_file_checked = 1;
            $eachBid->file_checked_by = auth()->user()->id;
            $eachBid->file_checked_at = Carbon::now();
            $eachBid->save();
        }

        return response()->json('Successfully Validated!',200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $tender = Tender::find($id);
        return view('admin.tender.edit', compact('tender'));
    }

    public function showApi(string $id)
    {
        $tender = Tender::with('tenderNotesheet.notesheet','requiredFiles.requiredDocument')->find($id);
        return $tender;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        //
        $tender_info = Tender::find($id);
        $filename = '';
        $termsfilename = '';
        $requirementfilename = '';
        $notesheet_list = json_decode($request->notesheets,true);

        if($notesheet_list && count($notesheet_list) > 0) {
            $filename = $tender_info->submission_file_name;
            Excel::store(new PvmsExport($notesheet_list), "public/tender-submission/".$filename);
        }

        if($request->hasFile('terms_conditions_file')) {
            $uniq_id = $request->tender_no ? $request->tender_no : uniqid();
            $termsfilename = MediaService::uploadFile($uniq_id.'termsAndConditions.'.$request->file('terms_conditions_file')->getClientOriginalExtension(),'tender-submission',$request->file('terms_conditions_file'));
        } else {
            $termsfilename = $tender_info->terms_conditions_file;
        }
        if($request->hasFile('requirements_file')) {
            $uniq_id = $request->tender_no ? $request->tender_no : uniqid();
            $requirementfilename = MediaService::uploadFile($uniq_id.'specification.'.$request->file('requirements_file')->getClientOriginalExtension(),'tender-submission',$request->file('requirements_file'));
        } else {
            $requirementfilename = $tender_info->requirements_file;
        }
        $tender = TenderService::createTender($request->all(),$filename,$termsfilename,$requirementfilename,$id);

        $required_document_list = json_decode($request->required_documents,true);

        TenderNotesheet::where('tender_id',$tender->id)->delete();
        TenderRequiredDocument::where('tender_id',$tender->id)->delete();

        $csr_list = Csr::where('tender_id',$tender->id)->get();

        foreach($csr_list as $csr) {
            CsrDemand::where('csr_id',$csr->id)->delete();
            Csr::where('id',$csr->id)->delete();
        }

        foreach($notesheet_list as $notesheet) {
            TenderService::createTenderNotesheet($tender->id,$notesheet);
        }

        $uniq_pvms = NotesheetDemandPVMS::select('pvms_id', \DB::raw('SUM(total_quantity) as total_quantity'))->whereIn('notesheet_id',$notesheet_list)->groupBy('pvms_id')->get();

        foreach($uniq_pvms as $each_pvms) {

            // for one uniq pvms one csr
            $csr = CsrService::createCsr($tender->id,$each_pvms->pvms_id,$each_pvms->total_quantity);

            $notesheet_pvms = NotesheetDemandPVMS::where('pvms_id',$each_pvms->pvms_id)->whereIn('notesheet_id',$notesheet_list)->get();

            foreach($notesheet_pvms as $each_notesheet_pvms) {
                CsrService::createCsrDemand($csr->id,$each_notesheet_pvms->notesheet_id,$each_notesheet_pvms->id,$each_notesheet_pvms->demand_id,$each_notesheet_pvms->demand_pvms_id,$csr->pvms_id,$each_notesheet_pvms->total_quantity);
            }
        }


        foreach($required_document_list as $required_document) {
            TenderService::createTenderRequiredDocuments($tender->id,$required_document);
        }

        if($request->published){
            TenderService::uploadToWebsite($tender);
        }

        return $tender;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $tender = TenderService::deleteTender($id);
        return response()->json($tender,200);
    }
}
