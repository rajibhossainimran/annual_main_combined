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
                                Querterly Demand
                            </h5>

                            <div class="text-right">
                                @if ($user_approval_role && $user_approval_role->role_key == 'cmh_clark')
                                <a class="nav-link" href="{{ route('querterly_demand.create') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create Querterly Demand
                                    </button>
                                </a>
                                @endif
                            </div>
                        </div>
                        <div class="p-2">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Demand No</th>
                                        <th>Financial Year</th>
                                        @if(!auth()->user()->subOrganization || (auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'DGMS' || auth()->user()->subOrganization->type == 'AFMSD')))
                                        <th>Unit</th>
                                        @endif
                                        <th>Demand Type</th>
                                        <th>Last Approval</th>
                                        <th>Approval Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($querterly_demands as $querterly_demand)
                                        <tr>
                                            <td>{{$querterly_demand->demand_no}}</td>
                                            <td>{{$querterly_demand->financialYear->name}}</td>
                                            @if(!auth()->user()->subOrganization || (auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'DGMS' || auth()->user()->subOrganization->type == 'AFMSD')))
                                            <td>{{$querterly_demand->dmdUnit->name}}</td>
                                            @endif
                                            <td>{{$querterly_demand->demand_type}}</td>
                                            <td>{{$querterly_demand->last_approval}}</td>
                                            <td>{{$querterly_demand->is_approved ? 'Approved' : 'Not Approved'}}</td>
                                            <td class="d-flex">
                                                @php
                                                    $next_role = App\Utill\Approval\QuerterlyDemandApprovalSetps::nextStepDynamic($querterly_demand->id)['designation']
                                                @endphp

                                                @if (!$querterly_demand->is_approved && $user_approval_role && $user_approval_role->role_key==$next_role)
                                                <div class="text-center mx-2">
                                                    <a href="{{url('/querterly_demand/approval/'.$querterly_demand->id)}}" class="approval" data-demand-id="" data-action="approve">
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

                                                        <br />
                                                        @if($next_role=='head_clark' || $next_role=='mo')
                                                        Forword
                                                        @else
                                                        Approve
                                                        @endif

                                                    </a>
                                                </div>
                                                @endif
                                                <div class="text-center mx-2">
                                                    <a href="{{url('/querterly_demand/view/'.$querterly_demand->id)}}"
                                                        class="approval" data-action="view">
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
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
