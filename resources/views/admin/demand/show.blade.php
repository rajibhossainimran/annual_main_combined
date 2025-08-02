@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            {{-- <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-drawer icon-gradient bg-happy-itmeo"></i>
                        </div>
                        <div>Demand<div class="page-title-subheading">Demand Details</div>
                        </div>
                    </div>
                    <div class="page-title-actions">
                        
                    </div>
                </div>
            </div> --}}

            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Demand Details</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5>Demand Details</h5>
                            @can('Show Demand')
                            <a class="nav-link" href="{{route('demand.index')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Demand
                                </button>
                            </a>
                            @endcan
                        </div>
                        <div class="p-2">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-3">
                                        Type
                                    </div>
                                    <div class="col-9">
                                        {{$demand->controlType->control_type}}
                                    </div>

                                    <div class="col-3">
                                        Fy
                                    </div>
                                    <div class="col-9">
                                        {{$demand->financialYear->name}}
                                    </div>

                                    <div class="col-3">
                                        Demand Date:
                                    </div>
                                    <div class="col-9">
                                        {{date('d, M Y', strtotime($demand->demand_date))}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-3">
                                        Demand Type
                                    </div>
                                    <div class="col-9">
                                        {{$demand->demandType->name}}
                                    </div>

                                    @if ($demand->demandType->name=='Signal')
                                    <div class="col-3">
                                        Signal No:
                                    </div>
                                    <div class="col-9">
                                        {{$demand->signal_no}}
                                    </div>
                                    @else
                                    <div class="col-3">
                                        Pradhikar No:
                                    </div>
                                    <div class="col-9">
                                        {{$demand->pradhikar_no}}
                                    </div>

                                    <div class="col-3">
                                        Indent No:
                                    </div>
                                    <div class="col-9">
                                        {{$demand->indent_no}}
                                    </div>
                                    @endif
                                    
                                </div>
                                
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>PVMS No</th>
                                            <th>Nomenclature</th>
                                            <th>AU</th>
                                            <th>Unit Pre Stock</th>
                                            <th>Avg Expence</th>
                                            <th>Demand Quantity</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($demand->demandPVMS as $demandPVMS)
                                        <tr>
                                            <td>{{$demandPVMS->id}}</td>
                                            <td>{{$demandPVMS->PVMS->nomenclature}}</td>
                                            <td>{{$demandPVMS->PVMS->unitName->name}}</td>
                                            <td>{{$demandPVMS->unit_pre_stock}}</td>
                                            <td>{{$demandPVMS->avg_expense}}</td>
                                            <td>{{$demandPVMS->qty}}</td>
                                            <td>{{$demandPVMS->remarks}}</td>
                                        </tr>
                                        @endforeach
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
