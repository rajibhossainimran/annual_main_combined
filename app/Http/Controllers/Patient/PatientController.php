<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Unit;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function search(Request $request)
    {
        return Patient::with('unitFrom')->where('name', 'LIKE', '%'.$request->keyword.'%')
                    ->orWhere('identification_no', 'LIKE', '%'.$request->keyword.'%')->limit(5)->get();
    }
    public function checkIdentificationNo(Request $request)
    {
        return Patient::where('identification_no', $request->identification_no)->where('relation',$request->relation)->first();
    }

    public function patientUnits()
    {
        return Unit::get();
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
        $patient = new Patient();
        $patient->name = $request->name;
        $patient->identification_no = $request->identification_no;
        $patient->relation = $request->relation;
        $patient->unit_id = $request->unit_id;
        $patient->created_by = auth()->user()->id;
        $patient->save();

        return $patient;
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
