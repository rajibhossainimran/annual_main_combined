@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">{{$type}}</div>
                <div class="main-card card app-content-inner">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">{{$type}}</h5>

                        </div>
                        <div class="col-lg-12">
                            <div class="row p-2">
                                <h5>Demand Details</h5>
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Demand Number</th>
                                            <th>Demand Type</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                        </tr>
                                        </thead>
                                        @if (isset($demand) && !empty($demand))
                                        <tbody>
                                            <tr>
                                                <td>{{$demand->uuid}}</td>
                                                <td>{{$demand->demandType->name}}</td>
                                                <td>{{$demand->status}}</td>
                                                <td>{{date('d M Y', strtotime($demand->created_at))}}</td>
                                            </tr>
                                        </tbody>
                                        @endif

                                    </table>
                                </div>
                            </div>
                            <div class="row p-2">
                                <h5>Demand PVMS List</h5>
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            @if (isset($demand) && !empty($demand))
                                            <th>#</th>
                                            @if($demand->demand_type_id == 1)
                                            <th>Patient Name</th>
                                            <th>PVMS No</th>
                                            <th>Nomenclature</th>
                                            <th>Disease </th>
                                            <th>Item Type</th>
                                            <th>AU</th>
                                            <th> Quantity</th>
                                            <th>Remarks</th>
                                            @else
                                            <th>PVMS No</th>
                                            <th>Nomenclature</th>
                                            <th>Item Type</th>
                                            <th>AU</th>
                                            <th> Quantity</th>
                                            <th>Remarks</th>
                                            @endif
                                            <th>Status</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($demand) && !empty($demand))
                                            @foreach($demand->demandPVMS as $k=>$demandPVMS)
                                                <tr>
                                                    <th scope="row">{{++$k}}</th>
                                                    @if($demand->demand_type_id == 1)
                                                    <td>{{$demandPVMS->patient_name}}</td>
                                                    <td>{{$demandPVMS->PVMS->pvms_name}}</td>
                                                    <td>{{$demandPVMS->PVMS->nomenclature}}</td>
                                                    <td>{{$demandPVMS->disease}}</td>
                                                    <td>{{$demand->demandType->name}}</td>
                                                    <td>{{$demandPVMS->PVMS->unitName->name}}</td>
                                                    <td>{{$demandPVMS->qty}}</td>
                                                    <td>{{$demandPVMS->remarks}}</td>
                                                    @else
                                                    <td>{{$demandPVMS->PVMS->pvms_name}}</td>
                                                    <td>{{$demandPVMS->PVMS->nomenclature}}</td>
                                                    <td>{{$demand->demandType->name}}</td>
                                                    <td>{{$demandPVMS->PVMS->unitName->name}}</td>
                                                    <td>{{$demandPVMS->qty}}</td>
                                                    <td>{{$demandPVMS->remarks}}</td>
                                                    @endif
                                                    <td>{{App\Utill\Approval\DemandApprovalSetps::demandPvmsStatus($demand->id,$demandPVMS->PVMS->id)}}</td>
                                                </tr>
                                            @endforeach
                                            @endif
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <br/>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
