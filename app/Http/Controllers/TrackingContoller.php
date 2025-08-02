<?php

namespace App\Http\Controllers;

use App\Models\Csr;
use App\Models\Demand;
use App\Models\Notesheet;
use App\Models\Tender;
use Illuminate\Http\Request;

class TrackingContoller extends Controller
{
    //
    public function index(Request $request) {
        return view('admin.tracking.index');
    }

    public function track(Request $request) {
        $data = '';

        if($request->tracking_on == 'demand') {
            $data = Demand::with('approval')->where('uuid',$request->traking_no)->first();
        } else if($request->tracking_on == 'notesheet') {
            $data = Notesheet::with('approval')->where('notesheet_id',$request->traking_no)->first();
        } else if($request->tracking_on == 'csr') {
            $tender = Tender::where('tender_no',$request->traking_no)->first();
            if($tender) {
                $data = Csr::with(['PVMS','hod','csrPvmsApproval.bidder'])->where('tender_id',$tender->id)->get();
            }
        }

        return response()->json($data,200);
    }
}
