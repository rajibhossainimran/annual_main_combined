<?php

namespace App\Http\Controllers;

use App\Models\TenderPurchases;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('admin.payment.index');
    }
    public function payment_report($start_date,$end_date)
    {
        //
        $startDate = Carbon::parse($start_date);
        $endDate = Carbon::parse($end_date);

        $payment_report = TenderPurchases::with('tender','vendor')->where('status','Success')->whereBetween('created_at', [$startDate, $endDate])->get();
        return $payment_report;
    }
    public function payment_report_monthly($date)
    {
        $delimiter = "-";

        $array = explode($delimiter, $date);

        $currentMonth = $array[0];
        $currentYear = $array[1];
        $payment_report = TenderPurchases::with('tender','vendor')->where('status','Success')
                            ->whereMonth('created_at','=',$currentMonth)
                            ->whereYear('created_at','=',$currentYear)
                            ->get();
        return $payment_report;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
