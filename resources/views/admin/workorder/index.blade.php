@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Workorder</div>
                <div class="main-card card app-content-inner">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">List of Workorders</h5>

                            @if (auth()->user()->sub_org_id==4)
                            <a class="nav-link" href="{{route('workorder.create')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                    Create Workorder
                                </button>
                            </a>
                            @endif


                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="work_order_table" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Compnay Name</th>
                                            <th>Contact</th>
                                            <th>Contract Number</th>
                                            <th>Total Amount</th>
                                            <th>Last Submit Date</th>
                                            <th>Contract Date</th>
                                            <th>Is Delivered</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($workorders as $workorder)
                                                <tr>
                                                    <td>{{$workorder->vendor->name}}
                                                        @if(isset($workorder->workorderReceive) && count($workorder->workorderReceive) == 0)
                                                            <span class="badge bg-success text-white">New</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(isset($workorder->vendor->phone))
                                                        Phone: {{$workorder->vendor->phone}}
                                                        @endif
                                                        <br/>
                                                        @if(isset($workorder->vendor->address))
                                                        Address: {{$workorder->vendor->address}}
                                                        @endif
                                                    </td>
                                                    <td>{{$workorder->contract_number}}</td>
                                                    <td>
                                                        {{$workorder->total_amount}}
                                                    </td>
                                                    <td>{{date('d M Y', strtotime($workorder->last_submit_date)) }}</td>
                                                    <td>{{date('d M Y', strtotime($workorder->contract_date)) }}</td>
                                                    <td>@if($workorder->is_delivered) Delivered @else Not Fully Delivered @endif</td>
                                                    <td>@if($workorder->is_adgms_approved) Approved @else Not Approved @endif</td>
                                                    <td class="d-flex justify-content-end">
                                                        @if((auth()->user()->user_approval_role_id==12 && !$workorder->is_dadgms_approved) || ($workorder->is_dadgms_approved && auth()->user()->user_approval_role_id==4 && !$workorder->is_adgms_approved))
                                                        <div class="text-center mx-2">
                                                            <a href="#" data-toggle="modal" data-target=".bd-example-modal-lg"
                                                                class="approval" data-workorder-id="{{ $workorder->id }}" data-action="approve">
                                                                <svg xmlns="http://www.w3.org/2000/svg" height="1em"
                                                                    viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                                    <style>
                                                                        svg {
                                                                            fill: #089c14
                                                                        }
                                                                    </style>
                                                                    <path
                                                                        d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                                                </svg>

                                                                <br /> Approve
                                                            </a>
                                                        </div>
                                                        @endif
                                                        @if (!$workorder->is_adgms_approved)
                                                        <div class="text-center mr-3 mx-2">
                                                            @if (auth()->user()->sub_org_id==4)
                                                            <a href="{{ route('workorder.edit', $workorder->id) }}">
                                                                <i class="fa fa-edit"></i><br />
                                                                Edit
                                                            </a>
                                                            @endif
                                                        </div>
                                                        @endif
                                                        <div class="text-center mx-2">
                                                            <a href="{{url('workorder/download/pdf/'.$workorder->id)}}" title="Download Notice">
                                                                <i class="fa fa-file-pdf"></i>
                                                                <br /> Download
                                                            </a>
                                                        </div>
                                                        <div class="text-center mx-2">
                                                            <a href="#" data-toggle="modal" data-target=".bd-example-modal-lg"
                                                                class="approval" data-workorder-id="{{ $workorder->id }}" data-action="view">
                                                                <i class="fa fa-eye"></i>
                                                                <br /> View
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
                        <br/>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<div class="modal bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Workorder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div id="react-workorder-approval"></div>
                    @viteReactRefresh
                    @vite('resources/js/app.jsx')
                </div>
            </div>
        </div>
    </div>
@endpush
