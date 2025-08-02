<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\BatchPvms;
use App\Models\PvmsStore;
use App\Models\User;
use App\Models\Workorder;
use App\Models\WorkorderDocument;
use App\Models\WorkorderPvms;
use App\Models\WorkorderReceive;
use App\Models\WorkorderReceiveDocument;
use App\Services\MediaService;
use App\Models\WorkorderReceivePvms;
use App\Services\AuditService;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkorderController extends Controller
{
    public function company_order_due_api(Request $request)
    {
        $perPage = $request->input('length', 100);
        $page = $request->input('start', 0) / $perPage + 1;

        $stockQtySubquery = DB::table('batch_pvms')
            ->join('pvms_store', 'batch_pvms.id', '=', 'pvms_store.batch_pvms_id')
            ->select('batch_pvms.pvms_id as batch_table_pvms_id')
            ->selectRaw('SUM(CAST(pvms_store.stock_in AS SIGNED) - CAST(pvms_store.stock_out AS SIGNED)) as stock_qty')
            ->where('pvms_store.is_received', 1)
            ->where('pvms_store.sub_org_id', 2)
            ->where('batch_pvms.expire_date', '>', Carbon::now())
            ->groupBy('batch_pvms.pvms_id');

        $data = WorkorderPvms::with([
            'pvms.unitName',
            'workorder.vendor',
            'workorderReceivePvms' => function ($query) {
                $query->select('workorder_pvms_id') // Select foreign key
                    ->selectRaw('SUM(received_qty) as total_received_qty')
                    ->selectRaw('MAX(created_at) as last_received_date')
                    ->groupBy('workorder_pvms_id');
            }
        ])
            ->leftJoinSub($stockQtySubquery, 'stock_qty_subquery', function ($join) {
                $join->on('workorder_pvms.pvms_id', '=', 'stock_qty_subquery.batch_table_pvms_id');
            });
        if ($request->fy) {
            $data = $data->whereHas('workorder', function ($query) use ($request) {
                $query->where('financial_year_id', $request->fy);
            });
        }

        if ($request->vendor) {
            $data = $data->whereHas('workorder', function ($query) use ($request) {
                $query->where('vendor_id', $request->vendor);
            });
        }

        if ($request->query('search')) {
            $data = $data->whereHas('workorder', function ($query) use ($request) {
                $query->where('contract_number', 'LIKE', '%' . $request->query('search') . '%');
            });
        }

        $data = $data->latest()->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'draw' => $request->input('draw'),
            'data' => $data->items(),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
        ]);
    }
    public function index()
    {
        $workorders = Workorder::latest()->paginate();

        return view('admin.workorder.index', compact('workorders'));
    }

    public function nextCrv()
    {
        $max_crv = WorkorderReceive::max('crv_no');
        return $max_crv;
    }

    public function create()
    {
        $vendors = User::where('is_vendor', true)->get();

        return view('admin.workorder.create', compact('vendors'));
    }

    public function store(Request $request)
    {

        $workorder = new Workorder();
        $workorder->vendor_id = $request->vandor_id;
        $workorder->contract_date = $request->contact_date;
        $workorder->contract_number = $request->contract_number;
        $workorder->last_submit_date = $request->last_submit_date;
        $workorder->financial_year_id = $request->financial_year;
        $workorder->notesheet_details = $request->notesheet_details;
        $workorder->notesheet_details1 = $request->notesheet_details1;
        $workorder->is_munir_keyboard = $request->is_munir_keyboard;
        $workorder->order_no = uniqid();
        $workorder->total_amount = 0;
        $workorder->save();

        $total_amount = 0;
        foreach ($request->workorder_csr_pvms as $workorder_csr_pvms) {
            $pvms_total = $workorder_csr_pvms['unit_price'] * $workorder_csr_pvms['qty'];

            $workorder_csr = new WorkorderPvms();
            $workorder_csr->workorder_id = $workorder->id;
            $workorder_csr->csr_id = 0;
            $workorder_csr->pvms_id = $workorder_csr_pvms['pvms_id'];
            $workorder_csr->qty = $workorder_csr_pvms['qty'];
            $workorder_csr->unit_price = $workorder_csr_pvms['unit_price'];
            $workorder_csr->total_price = $pvms_total;
            $workorder_csr->delivery_mood = $workorder_csr_pvms['delivery_mood'];
            $workorder_csr->remarks = $workorder_csr_pvms['remarks'];
            $workorder_csr->save();

            $total_amount += $pvms_total;
        }

        $workorder->total_amount = $total_amount;
        $workorder->save();

        return $workorder;
    }

    public function edit($id)
    {
        return view('admin.workorder.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        $workorder = Workorder::find($id);
        $workorder->vendor_id = $request->vandor_id;
        $workorder->contract_date = $request->contact_date;
        $workorder->contract_number = $request->contract_number;
        $workorder->last_submit_date = $request->last_submit_date;
        $workorder->total_amount = 0;
        $workorder->save();

        $total_amount = 0;
        $pvms_changes = [];
        foreach ($request->workorder_csr_pvms as $workorder_csr_pvms) {
            $pvms_total = $workorder_csr_pvms['unit_price'] * $workorder_csr_pvms['qty'];

            $workorder_csr = WorkorderPvms::find($workorder_csr_pvms['id']);
            if (!$workorder_csr) {
                $workorder_csr = new WorkorderPvms();
                $workorder_csr->workorder_id = $workorder->id;
                $workorder_csr->pvms_id = $workorder_csr_pvms['pvms_id'];
            }
            $workorder_csr->qty = $workorder_csr_pvms['qty'];
            $workorder_csr->unit_price = $workorder_csr_pvms['unit_price'];
            $workorder_csr->total_price = number_format($pvms_total, 2);
            $workorder_csr->delivery_mood = $workorder_csr_pvms['delivery_mood'];
            $workorder_csr->remarks = $workorder_csr_pvms['remarks'];
            $workorder_csr->save();

            if ($workorder_csr->getChanges()) {
                $pvms_changes[] = $workorder_csr->getChanges();
            }

            $total_amount += $pvms_total;
        }

        $workorder->total_amount = $total_amount;
        $workorder->save();

        $changes_data = [
            'workorder' => $workorder,
            'pvms' => $pvms_changes
        ];

        AuditService::AuditLogEntry(
            AuditModel::Workorder,
            OperationTypes::Update,
            'Workorder ' . $workorder->id . ' has been updated by ' . auth()->user()->name,
            $workorder,
            json_encode($changes_data),
            $workorder->id
        );
    }

    public function showJson($id)
    {
        return Workorder::where('id', $id)
            ->with('workorderPvms.pvms.unitName', 'workorderPvms.workorderReceivePvms', 'vendor', 'financialYear', 'documents')
            ->first();
    }

    public function newWorkorderCount()
    {
        return Workorder::leftJoin('workorder_receives', 'workorders.id', 'workorder_receives.workorder_id')
            ->whereNull('workorder_receives.workorder_id')
            ->count();
    }

    public function showReceiveJson($id)
    {
        $workorder_receive = WorkorderReceive::where('id', $id)
            ->with('workorder.vendor', 'workorder.financialYear', 'pvmsStore.batch', 'pvmsStore.workorderReceivePvms', 'documents')
            ->first();

        // $workorder_store = PvmsStore::select(
        //                         'pvms_store.*',
        //                         'p_v_m_s.id as pvms_primary_id',
        //                         'p_v_m_s.pvms_id',
        //                         'p_v_m_s.nomenclature',
        //                         'account_units.name as au',
        //                         'workorder_pvms.qty as contract_qty',
        //                         'workorder_pvms.unit_price',
        //                         'workorder_pvms.id as workorder_pvms_id',
        //                         'batch_pvms.batch_no',
        //                         'batch_pvms.expire_date',
        //                         'batch_pvms.id as batch_pvms_id',
        //                         )
        //                     ->join('batch_pvms', 'batch_pvms.id', 'pvms_store.batch_pvms_id')
        //                     ->join('p_v_m_s', 'p_v_m_s.id', 'batch_pvms.pvms_id')
        //                     ->join('account_units', 'account_units.id', 'p_v_m_s.account_units_id')
        //                     ->join('workorder_pvms', 'workorder_pvms.id', 'batch_pvms.workorder_pvms_id')
        //                     ->where('workorder_receive_id', $id)->get();

        return [
            'workorder_receive' => $workorder_receive,
            // 'workorder_store' => $workorder_store,
        ];
    }

    public function approval(Request $request)
    {
        $workorder = Workorder::find($request->workorder_id);
        if (auth()->user()->user_approval_role_id == 12) {
            $workorder->is_dadgms_approved = true;
            $workorder->save();

            return response(['success' => true, 'message' => 'Approved']);
        } elseif (($workorder->is_dadgms_approved && auth()->user()->user_approval_role_id == 4)) {
            $workorder->is_adgms_approved = true;
            $workorder->save();

            return response(['success' => true, 'message' => 'Approved']);
            $workorder->save();
        } else {
            return response(['success' => true, 'message' => 'Access Denied'], 403);
        }
    }

    public function workorderReceived()
    {
        $workorder_receive = WorkorderReceive::latest()->paginate();

        return view('admin.workorder.received-index', compact('workorder_receive'));
    }

    public function workorderReceivedCreate()
    {
        return view('admin.workorder.received-create');
    }

    public function workordersJson(Request $request)
    {
        if ($request->contract_number) {
            return Workorder::with('vendor', 'financialYear')->where('contract_number', 'LIKE', '%' . $request->contract_number . '%')->limit(10)->latest()->get();
        } else {
            return Workorder::with('vendor', 'financialYear')->latest()->limit(10)->get();
        }
    }

    public function workorderCsrJson(Request $request)
    {
        return WorkorderCsr::select(
            'workorder_csrs.*',
            'p_v_m_s.nomenclature',
            'p_v_m_s.pvms_id as pvms_maked_id',
            'account_units.name as au',
        )
            ->selectRaw('(SELECT SUM(stock_in-stock_out) FROM `pvms_store` WHERE sub_org_id IS NULL AND pvms_id=p_v_m_s.id) AS stock')
            ->join('p_v_m_s', 'p_v_m_s.id', 'workorder_csrs.pvms_id')
            ->join('account_units', 'account_units.id', 'p_v_m_s.account_units_id')
            ->join('csr', 'csr.id', 'workorder_csrs.csr_id')
            ->join('vendor_biddings', 'vendor_biddings.csr_id', 'csr.id')
            ->where('workorder_id', $request->workorder_id)
            ->where('is_vendor_approved', true)
            ->get();
    }

    public function workorderReceiveStore(Request $request)
    {
        $workorder_receive = new WorkorderReceive();
        $workorder_receive->workorder_id = $request->workorder_id;
        $workorder_receive->crv_no = $request->contractNumber;
        $workorder_receive->received_by = auth()->user()->id;
        $workorder_receive->created_by = auth()->user()->id;
        $workorder_receive->receiving_date = $request->ReceiveDate;
        $workorder_receive->save();

        foreach ($request->store_pvms as $store_pvms) {
            foreach ($store_pvms['delivery_data'] as $delivery_data) {
                if ($delivery_data['delivery_qty']) {
                    $batch_pvms = BatchPvms::where('batch_no', $delivery_data['batch_no'])
                        ->whereDate('expire_date', $delivery_data['expire_date'])->where('pvms_id', $store_pvms['pvms_primary_id'])->first();

                    if ($batch_pvms === null) {
                        $batch_pvms = new BatchPvms();
                        $batch_pvms->batch_no = $delivery_data['batch_no'];
                        $batch_pvms->pvms_id = $store_pvms['pvms_primary_id'];
                        $batch_pvms->workorder_pvms_id = $store_pvms['id'];
                        $batch_pvms->expire_date = $delivery_data['expire_date'];
                        $batch_pvms->mfg_date = $delivery_data['mfg_date'];
                        $batch_pvms->qty = $delivery_data['delivery_qty'];
                    } else {
                        $batch_pvms->qty += $delivery_data['delivery_qty'];
                    }

                    $batch_pvms->save();

                    $pvms_store = new PvmsStore();
                    $pvms_store->sub_org_id = 2;
                    $pvms_store->workorder_receive_id = $workorder_receive->id;
                    $pvms_store->pvms_id = $store_pvms['pvms_primary_id'];
                    $pvms_store->batch_pvms_id = $batch_pvms->id;
                    $pvms_store->stock_in = $delivery_data['delivery_qty'];
                    $pvms_store->save();

                    $workorder_receive_pvms = new WorkorderReceivePvms();
                    $workorder_receive_pvms->workorder_pvms_id = $store_pvms['id'];
                    $workorder_receive_pvms->workorder_receive_id = $workorder_receive->id;
                    $workorder_receive_pvms->received_qty = $delivery_data['delivery_qty'];
                    $workorder_receive_pvms->pvms_store_id = $pvms_store->id;
                    $workorder_receive_pvms->receiver_remarks = $store_pvms['receiver_remarks'] ?? null;
                    $workorder_receive_pvms->save();
                }
            }
        }

        return response([
            "workorder_receive" => $workorder_receive,
            "success" => true,
            "message" => "Store Updated"
        ]);
    }

    public function workorderReceivedEdit($id)
    {
        return view('admin.workorder.received-edit', compact('id'));
    }

    public function workorderReceivedUpdate(Request $request, $id)
    {

        $workorder_receive = WorkorderReceive::find($request->workorder_receive_id);
        $workorder_receive->crv_no = $request->contractNumber;
        $workorder_receive->received_by = auth()->user()->id;
        $workorder_receive->receiving_date = $request->ReceiveDate;

        if (auth()->user()->userApprovalRole) {
            if (auth()->user()->userApprovalRole->role_key == 'stock-control-officer' && !$workorder_receive->approved_by) {
                $workorder_receive->approved_by = 'stock-control-officer';
            } else if (auth()->user()->userApprovalRole->role_key == 'oic' && $workorder_receive->approved_by == 'stock-control-officer') {
                $workorder_receive->approved_by = 'oic';
            } else if (auth()->user()->userApprovalRole->role_key == 'group-incharge' && $workorder_receive->approved_by == 'oic') {
                $workorder_receive->approved_by = 'group-incharge';
                PvmsStore::where('workorder_receive_id', $request->workorder_receive_id)->update([
                    'is_received' => true
                ]);
            }
        }
        $workorder_receive->save();

        foreach ($request->store_pvms as $store_pvms) {
            foreach ($store_pvms['delivery_data'] as $delivery_data) {
                if ($delivery_data['delivery_qty']) {
                    $batch_pvms = BatchPvms::find($delivery_data['batch_pvms_id']);
                    $batch_pvms->batch_no = $delivery_data['batch_no'];
                    $batch_pvms->expire_date = $delivery_data['expire_date'];
                    $batch_pvms->mfg_date = $delivery_data['mfg_date'];
                    $batch_pvms->qty = $delivery_data['delivery_qty'];
                    $batch_pvms->save();

                    $pvms_store = PvmsStore::find($delivery_data['pvms_store_id']);
                    $pvms_store->stock_in = $delivery_data['delivery_qty'];
                    $pvms_store->save();

                    $workorder_receive_pvms = WorkorderReceivePvms::find($delivery_data['workorder_receive_pvms_id']);
                    $workorder_receive_pvms->received_qty = $delivery_data['delivery_qty'];
                    $workorder_receive_pvms->receiver_remarks = $store_pvms['receiver_remarks'] ?? null;
                    $workorder_receive_pvms->save();
                }
            }
        }

        return response([
            "workorder_receive" => $workorder_receive,
            "success" => true,
            "message" => "Store Receive Updated"
        ]);
    }

    public function workorderReceiveApprove(Request $request) {}

    public function workorderUpdateDocument(Request $request)
    {
        if ($request->hasFile('document_files')) {
            $workorder_documents = WorkorderDocument::where('workorder_id', $request->workorder_id)->get();

            foreach ($workorder_documents as $workorder_document) {
                try {
                    unlink(storage_path('app/public/workorder_documents/' . $workorder_document->file));
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }

            WorkorderDocument::where('workorder_id', $request->workorder_id)->delete();

            $files = $request->file('document_files');
            foreach ($files as $file) {
                $document_name = time() . '_' . $file->getClientOriginalName();
                MediaService::uploadFile(
                    $document_name,
                    'workorder_documents',
                    $file
                );

                $store_file = new WorkorderDocument();
                $store_file->workorder_id = $request->workorder_id;
                $store_file->file = $document_name;
                $store_file->created_by = auth()->user()->id;
                $store_file->save();
            }
        }
        return $request->all();
    }

    public function workorderReceiveUpdateDocument(Request $request)
    {
        if ($request->hasFile('document_files')) {
            $workorder_receive_documents = WorkorderReceiveDocument::where('workorder_receive_id', $request->workorder_receive_id)->get();

            foreach ($workorder_receive_documents as $workorder_receive_document) {
                try {
                    unlink(storage_path('app/public/workorder_receive_documents/' . $workorder_receive_document->file));
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }

            WorkorderReceiveDocument::where('workorder_receive_id', $request->workorder_receive_id)->delete();

            $files = $request->file('document_files');
            foreach ($files as $file) {
                $document_name = time() . '_' . $file->getClientOriginalName();
                MediaService::uploadFile(
                    $document_name,
                    'workorder_receive_documents',
                    $file
                );

                $store_file = new WorkorderReceiveDocument();
                $store_file->workorder_receive_id = $request->workorder_receive_id;
                $store_file->file = $document_name;
                $store_file->created_by = auth()->user()->id;
                $store_file->save();
            }
        }
        return $request->all();
    }

    public function workorderPvmsRemove(Request $request)
    {
        WorkorderPvms::find($request->workorder_pvms_id)->delete();
    }
}
