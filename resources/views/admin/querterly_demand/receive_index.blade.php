@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title annual_demand_bg">Querterly Demand</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center annual_demand_bg py-1">
                            <h5 class="f-14 pl-2">
                                Querterly Demand Receive List
                            </h5>

                            <div class="text-right">
                                
                            </div>
                        </div>
                        <div class="p-2">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Demand No</th>
                                        <th>Financial Year</th>
                                        <th>Demand Type</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($querterly_demand_receives as $querterly_demand_receive)
                                        <tr>
                                            <td>{{$querterly_demand_receive->querterlyDemand->demand_no}}</td>
                                            <td>{{$querterly_demand_receive->querterlyDemand->financialYear->name}}</td>
                                            <td>{{$querterly_demand_receive->querterlyDemand->demand_type}}</td>
                                            <td>@if($querterly_demand_receive->is_received) Received @else Not Received @endif</td>
                                            <td class="d-flex">
                                                <div class="text-center mx-2">
                                                    <a href="{{url('/querterly_demand/receive/create/'.$querterly_demand_receive->id)}}" 
                                                        class="approval" data-action="view">
                                                        <i class="fa fa-eye"></i>
                                                        <br /> @if($querterly_demand_receive->is_received) View @else Received @endif
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
