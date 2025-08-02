<?php

namespace App\Http\Controllers;

use App\Models\PVMS;
use App\Models\RateRunningPvms;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use DB;

class RateRunningPvmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = RateRunningPvms::leftJoin('users','users.id','=','rate_running_pvms.supplier_id')
        ->leftJoin('p_v_m_s','p_v_m_s.id','=','rate_running_pvms.pvms_id')
        ->select('rate_running_pvms.*','users.name','p_v_m_s.nomenclature')->orderBy('rate_running_pvms.id','desc')->get();
        return view('admin.rate_running.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $supplier = User::where('is_vendor', 1)->get();
        $pvms = PVMS::limit(5)->get();
        return view('admin.rate_running.create', compact('supplier','pvms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier' => ['required'],
            'start_date' => ['required'],
            'end_date' => ['required'],
            'price' => ['required'],
        ]);
        DB::beginTransaction();
        try{
            $pvms_id = $request->pvms_id;
            foreach($pvms_id as $k=>$pvms){
                $data = new RateRunningPvms();
                $data->supplier_id = $request->supplier;
                $data->tender_ser_no = $request->tender_ser_no;
                $data->start_date = date('Y-m-d', strtotime($request->start_date));
                $data->end_date = date('Y-m-d', strtotime($request->end_date));
                $data->pvms_id = $pvms;
                $data->price = $request->price[$k];
                $data->created_by = auth()->user()->id;
                $data->created_at = now();
                $data->save();
            }
            // return redirect()->back()->with('success','Successfully Insert Data');
        }catch(\Exception $e){

            DB::rollback();
            return response()->json('Insert Data',500);
        }
        DB::commit();
        return response()->json('Insert Data',200);


    }

    /**
     * Display the specified resource.
     */
    public function show(RateRunningPvms $rateRunningPvms)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RateRunningPvms $rateRunningPvms)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RateRunningPvms $rateRunningPvms)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RateRunningPvms $rateRunningPvms)
    {
        //
    }
}
