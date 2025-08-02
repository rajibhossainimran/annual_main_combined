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
                                Querterly Demand Delivery
                            </h5>
                        </div>
                        <div class="p-2">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Demand No</th>
                                        <th>Financial Year</th>
                                        <th>Demand Type</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($querterly_demands as $querterly_demand)
                                        <tr>
                                            <td>{{$querterly_demand->demand_no}}</td>
                                            <td>{{$querterly_demand->financialYear->name}}</td>
                                            <td>{{$querterly_demand->demand_type}}</td>
                                            <td class="d-flex">
                                                <div class="text-center mx-2">
                                                    <a href="{{url('/querterly_demand/delivery/create/'.$querterly_demand->id)}}" 
                                                        class="approval" data-action="view">
                                                        <i class="fa fa-eye"></i>
                                                        <br /> Delivery
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
