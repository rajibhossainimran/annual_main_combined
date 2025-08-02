@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner ">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Tender View</div>
                <div class="tabs-animation app-content-inner">
                    {{-- <div class="d-flex justify-content-between align-items-center table-header-bg" style="padding: 7px;margin-top: 5px;margin-bottom: 7px">
                        <h5 class="f-14">Dashboard</h5>
                    </div> --}}
                    <div class="row app-content-inner">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-6 pr-0">
                                    <div class="border-right-vendor">
                                        <h6>Tender Number</h6>
                                        <h5>{{$tender->tender_no}}</h5>

                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <h6>Deadline</h6>
                                        <h5>{{date('d M Y', strtotime($tender->deadline))}}</h5>
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <a href="{{route('dashboard')}}"><button class="btn btn-outline-info">Back</button></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 vendor-dashboard-header">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="example" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nomenclature</th>
                                            {{-- <th>Quantity</th> --}}
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tender->tenderCsr as $k=>$tender_csr_pvms)
                                           <tr>
                                                <th scope="row">{{$k+1}}</th>
                                                <td>{{$tender_csr_pvms->PVMS->nomenclature}}</td>
                                                {{-- <td>{{$tender_csr_pvms->pvms_quantity}}</td> --}}
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
        </div>
    </div>
@endsection
@push('js')
@endpush
