@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner ">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Dashboard</div>
                <div class="tabs-animation app-content-inner">
                    {{-- <div class="d-flex justify-content-between align-items-center table-header-bg" style="padding: 7px;margin-top: 5px;margin-bottom: 7px">
                        <h5 class="f-14">Dashboard</h5>
                    </div> --}}
                    <div class="row app-content-inner">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-3 pr-0">
                                    <div class="border-right-vendor">
                                        <h5>{{$applied}}</h5>
                                        <h6>Applied Tender</h6>
                                    </div>
                                </div>
                                <div class="col-lg-3 pr-0">
                                    <div class="border-right-vendor">
                                        <h5>{{$active}}</h5>
                                        <h6>Active Application</h6>
                                    </div>
                                </div>
                                <div class="col-lg-3 pr-0">
                                    <div class="border-right-vendor">
                                        <h5>{{$purchase}}</h5>
                                        <h6>Purchase Tender Til {{date('d M Y')}}</h6>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div>
                                        <h5>100%</h5>
                                        <h6>Complete Profile</h6>
                                    </div>
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
                                            <th>Tender No</th>
                                            <th>Tender Publishing Date</th>
                                            <th>Deadline</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                    // var_dump($tender->deadline);
                                                    $presentTime = now();
                                                    // echo $presentTime;
                                                    ?>
                                        @foreach($tenders as $k=>$tender)
                                            <tr>
                                                <th scope="row">{{$k+1}}</th>
                                                <td>{{$tender->tender_no}}</td>
                                                <td>{{$tender->start_date}}</td>
                                                <td>{{$tender->deadline}} </td>
                                                <td>
                                                    @if(isset($tender->purchase) && $tender->vendor == Auth::user()->id && strtotime($tender->deadline) > strtotime($presentTime))
                                                        <div class="badge badge-pill badge-info">Purchased</div>
                                                    @elseif(strtotime($tender->deadline) > strtotime($presentTime))
                                                        <div class="badge badge-pill badge-success">New</div>
                                                    @else
                                                        <div class="badge badge-pill badge-danger">Expired</div>
                                                    @endif
                                                </td>
                                                <td class="d-flex align-item-center">

                                                    @if(strtotime($tender->deadline) > strtotime($presentTime))
                                                        <a href="{{url('/vendor/tender/view/'.$tender->id)}}">
                                                            <button class="btn btn-outline-info border-0" title="View Tender">
                                                                <i class="fas fa-eye font-size-20"></i>
                                                            </button>
                                                        </a>
                                                        @if(isset($tender->purchase) && $tender->vendor == Auth::user()->id && $tender->status == 'Success')
                                                            @if(!isset($tender->isSubmitted) && empty($tender->isSubmitted))
                                                                <a href="{{url('/vendor/tender-file/'.$tender->id)}}">
                                                                    <button class="btn btn-outline-success border-0" title="Tender Submit">
                                                                        <i class="fas fa-upload font-size-20"></i>
                                                                    </button>
                                                                </a>
                                                                <a href="{{route('download_tender_files',[$tender->id])}}">
                                                                    <button class="btn btn-outline-success border-0" title="Download Tender Files">
                                                                        <i class="fas fa-cloud-download-alt font-size-20"></i>
                                                                    </button>
                                                                </a>
                                                            @else
                                                            <a href="#" class="cursor-none">
                                                                <div class="badge badge-pill badge-success">Submitted</div>
                                                            </a>

                                                                {{-- <div class="badge badge-pill badge-success">Submitted</div> --}}
                                                            @endif
                                                        @else
                                                            <a href="{{url('vendor/tender/purchase/'.$tender->id)}}">
                                                                <button class="btn btn-outline-info border-0" title="Purchase Tender">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 0 448 512">
                                                                        <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                                        <style>svg{fill:#00664e}</style>
                                                                        <path d="M160 112c0-35.3 28.7-64 64-64s64 28.7 64 64v48H160V112zm-48 48H48c-26.5 0-48 21.5-48 48V416c0 53 43 96 96 96H352c53 0 96-43 96-96V208c0-26.5-21.5-48-48-48H336V112C336 50.1 285.9 0 224 0S112 50.1 112 112v48zm24 48a24 24 0 1 1 0 48 24 24 0 1 1 0-48zm152 24a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z"/></svg>
                                                                </button>
                                                            </a>
                                                        @endif
                                                    @endif

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
        </div>
    </div>
@endsection
@push('js')
@endpush
