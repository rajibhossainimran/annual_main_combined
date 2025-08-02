<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Imports\VendorBiddingImport;
use App\Models\Csr;
use App\Models\RequiredDocument;
use App\Models\Tender;
use App\Models\TenderNotesheet;
use App\Models\TenderPurchases;
use App\Models\TenderRequiredDocument;
use App\Models\TenderSubmittedFile;
use App\Models\User;
use App\Services\AuditService;
use App\Services\CsrService;
use App\Services\MediaService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Mockery\Exception;
use Excel;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():view
    {
        $users = User::where('is_vendor', 1)->get();
        return view('admin.vendor.index', compact('users'));
    }

    public function indexJson(Request $request)
    {
        if($request->keyword){
            $users = User::where('name', 'LIKE', '%'.$request->keyword.'%')->where('is_vendor', 1)->limit(50)->get();
        }else{
            $users = User::where('is_vendor', 1)->limit(50)->latest()->get();
        }
        
        return $users;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():view
    {
        //
        return view('admin.vendor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'max:255'],
            'password' => ['required', 'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'confirmed',
                Rules\Password::defaults()],
        ]);
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'org_id' =>  null,
            'sub_org_id' => null,
            'branch_id' => null,
            'is_vendor' => 1,
            'company_name' => $request->company_name,
            'company_email' => $request->company_email,
        ]);

        AuditService::AuditLogEntry(AuditModel::User,OperationTypes::Create,'Vendor Create',null,$user,auth()->user()->id);

        return redirect()->route('all.vendor');
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
        $vendor = User::find($id);
        return view('admin.vendor.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'max:255'],
        ]);
        try {
            $vendor = User::find($id);
            $old_data = $vendor;
            $vendor->name = $request->name;
            $vendor->phone = $request->phone;
            $vendor->company_name = $request->company_name;
            $vendor->address = $request->address;
            $vendor->save();

            AuditService::AuditLogEntry(AuditModel::User,OperationTypes::Update,'Vendor Update',$old_data,$vendor,auth()->user()->id);

            return redirect()->route('all.vendor')->with('message','Successfully Update');
        }catch (Exception $exception){
            return redirect()->back()->with('error','Fail to Update');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $user = User::find($id);
        $new_data = null;
        $old_data = $user;
        $description = 'Vendor '.$user->name.' has been deleted by '.auth()->user()->name;
        $operation = OperationTypes::Delete;

        $user->deleted_by = auth()->user()->id;
        $user->save();
        $user->delete();
        AuditService::AuditLogEntry(AuditModel::User,$operation,$description,$old_data,$new_data,$user->id);
        return redirect()->back()->with('message','Successfully Deleted.');
    }

    public function profile()
    {
        $users = User::where('id', Auth::user()->id)->first();
        return view('admin.vendor.profile', compact('users'));
    }

    public function tenderFile($id)
    {
        $files = TenderRequiredDocument::where('tender_id',$id)
//            ->leftJoin('required_documents','required_documents.id','=','tender_required_documents.required_document_id')
//            ->select('tender_required_documents.*','required_documents.name')
            ->get();
        $tender = Tender::find($id);
        $ids = array();
        foreach ($files as $file){
            array_push($ids, $file->required_document_id);
        }
        $files = RequiredDocument::whereIn('id',$ids)->get();

        return view('admin.vendor.dashboard.file_upload', compact('files','tender','id'));
    }

    public function submitFile(Request $request)
    {
        $request->validate([
            'file' => 'required|max:5000',
            'tender_id' => 'required',
            'technical_submission_file' => 'required|max:5000',
        ]);
        $files = TenderRequiredDocument::where('tender_id',$request->tender_id)->get();
        $ids = array();
        foreach ($files as $file){
            array_push($ids, $file->required_document_id);
        }
        $files_type = RequiredDocument::whereIn('id',$ids)->get();

        DB::beginTransaction();
        try {
            foreach ($request->file as $k=>$file){
                if($files_type[$k]->file_type == $file->extension()){
                    // $fileName = uniqid().time().'.'.$file->extension();
                    // $file->move(public_path('vendor/upload'), $fileName);
                    $fileName = MediaService::uploadFile(uniqid().time().'.'.$file->getClientOriginalExtension(),'vendor/upload',$file);
                    $submittedFile = new TenderSubmittedFile();
                    $submittedFile->tender_id = $request->tender_id;
                    $submittedFile->required_doc_id = $files_type[$k]->id;
                    $submittedFile->file = $fileName;
                    $submittedFile->created_by = Auth::user()->id;
                    $submittedFile->save();
                } else {
                    DB::rollback();
                    return redirect()->back()->with('error','File Type Does not Match.');
                }

            }

            if($request->hasFile('technical_submission_file')) {
                if($request->file('technical_submission_file')->extension() == 'xlsx') {
                    $array = Excel::toCollection(new VendorBiddingImport, $request->file('technical_submission_file'))->first();
                    if(count($array) > 1) {
                        $array = $array->slice(1);
                        $total_pvms = 0;
                        foreach($array as $pvms) {
                            if(empty($pvms['3'])) {
                                continue;
                            } else {
                                $csr = Csr::where('tender_id',$request->tender_id)->whereHas('PVMS', function ($query) use($pvms){
                                    $query->where('pvms_id', $pvms[0]);
                                })->first();
                                // CsrService::createVendorBidding($csr->id,$pvms[3],$pvms[4]);
                                if($csr){
                                    CsrService::createVendorBidding($csr->id,$pvms[3],str_replace(["\r", "\n"], '<br/>', $pvms[4]));
                                    $total_pvms = $total_pvms + 1;
                                }
                            }

                        }

                        if($total_pvms == 0) {
                            DB::rollback();
                            return redirect()->back()->with('error',"You don't participated into any PVMS!");
                        } else {
                            $filename = MediaService::uploadFile(uniqid().time().'.'.$request->file('technical_submission_file')->getClientOriginalExtension(),'vendor/upload',$request->file('technical_submission_file'));
                            $submittedFile = new TenderSubmittedFile();
                            $submittedFile->tender_id = $request->tender_id;
                            $submittedFile->file = $filename;
                            $submittedFile->created_by = Auth::user()->id;
                            $submittedFile->save();
                        }
                    } else {
                        //error
                        DB::rollback();
                        return redirect()->back()->with('error','Invalid File!');
                    }
                } else {
                    DB::rollback();
                    return redirect()->back()->with('error','File Type Does not Match.');
                }
            } else {
                DB::rollback();
                return redirect()->back()->with('error','Technical File Required!.');
            }

            DB::commit();
            return redirect()->route('dashboard')->with('message','Submitted Successfully.');

        } catch (Exception $exception){
            DB::rollback();
            return redirect()->back()->with('error','Fail to Submitted.');
        }
    }

    public function purchase($id)
    {
        $tender = Tender::find($id);
        return view('admin.vendor.dashboard.payment.exampleEasycheckout', compact('tender'));
    }

    public function viewTender($id)
    {
        $tenders = Tender::where('tenders.id',$id)
            ->leftJoin('tender_notesheets','tender_notesheets.tender_id','=','tenders.id')
            ->leftJoin('notesheet_demand_pvms','notesheet_demand_pvms.notesheet_id','=','tender_notesheets.notesheet_id')
            ->leftJoin('p_v_m_s','p_v_m_s.id','=','notesheet_demand_pvms.pvms_id')
            ->select('p_v_m_s.nomenclature','tenders.*','notesheet_demand_pvms.total_quantity')
            ->get();

        $tender = Tender::find($id);
        return view('admin.vendor.dashboard.show',compact('tenders','tender'));
    }

    public function PaymentSuccess()
    {
//        $tender = Tender::find($id);
        return view('admin.vendor.dashboard.payment.success');
    }
}
