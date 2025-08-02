@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Received Workorder</div>
                <div class="main-card card app-content-inner">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">List of Receives</h5>

                            <a class="nav-link" href="{{route('workorder.receive.create')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                    Received goods
                                </button>
                            </a>

                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="example" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Compnay Name</th>
                                            <th>Contract Number</th>
                                            <th>Contact</th>
                                            <th>CRV No</th>
                                            <th>Received Date</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($workorder_receive as $receive)
                                                <tr>
                                                    <td>{{$receive->workorder->vendor->name}}</td>
                                                    <td>{{$receive->workorder->contract_number}}</td>
                                                    <td>
                                                        @if(isset($receive->workorder->vendor->phone))
                                                        Phone: {{$receive->workorder->vendor->phone}}
                                                        @endif
                                                        <br/>
                                                        @if(isset($receive->workorder->vendor->address))
                                                        Address: {{$receive->workorder->vendor->address}}
                                                        @endif
                                                    </td>
                                                    <td>{{ $receive->crv_no }}</td>
                                                    <td>{{$receive->receiving_date ? date('d M Y', strtotime($receive->receiving_date)) : ''}}</td>
                                                    <td>
                                                        @if ($receive->approved_by=='group-incharge')
                                                            Received
                                                        @else
                                                            Not Received
                                                        @endif
                                                    </td>
                                                    <td class="d-flex justify-content-end">
                                                        @if (auth()->user()->userApprovalRole && auth()->user()->userApprovalRole->role_key==App\Utill\Approval\WorkorderReceiveApprovalSetps::nextStep($receive->approved_by))
                                                        <div class="text-center mr-3 mx-2">
                                                            <a href="{{ route('workorder.receive.edit', $receive->id) }}">
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
                                                        @else
                                                            @if (App\Utill\Approval\WorkorderReceiveApprovalSetps::nextStep($receive->approved_by) !='approved')
                                                            <a href="{{ route('workorder.receive.edit', $receive->id) }}">
                                                                <i class="fa fa-edit"></i><br />
                                                                Edit
                                                            </a>
                                                            @endif
                                                        @endif



                                                        <div class="text-center mx-2">
                                                            <a href="#" data-toggle="modal" data-target=".bd-example-modal-lg"
                                                                class="approval" data-workorder-id="{{ $receive->workorder->id }}" data-workorder-receive-id="{{ $receive->id }}" data-action="view">
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
                    <h5 class="modal-title" id="exampleModalLongTitle">Received Workorder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div id="react-workorder-receive-approval"></div>
                    @viteReactRefresh
                    @vite('resources/js/app.jsx')
                </div>
            </div>
        </div>
    </div>
@endpush
