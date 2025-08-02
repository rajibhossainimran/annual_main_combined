@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">

            <div class="col-lg-12 app-content">
                <div class="app-content-top-title annual_demand_bg">Annual Demand</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="">
                            <div class="d-flex justify-content-between align-items-center annual_demand_bg py-1">
                                <h5 class="f-14 pl-1">
                                    Annual Demand
                                </h5>
                                {{ $annual_demands->links('vendor.pagination.custom') }}
                                <div class="text-right">
                                    <a class="nav-link" href="{{ route('annual_demand.create') }}">
                                        <button class="btn-icon btnc btn-custom">
                                            <i class="fa fa-plus btn-icon-wrapper"></i>
                                            Create Annual Demand
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sl.</th>
                                    <th>Financial year</th>
                                    <th>Department PVMS List Submitted</th>
                                    <th>Last Approval</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                                @foreach ($annual_demands as $demand)
                                    <tr>
                                        <td>{{$loop->index+1}}</td>
                                        <td>{{$demand->financialYear->name}}</td>
                                        <td>{{count($demand->departmentList )}}</td>
                                        <td>@if($demand->lastListApprovedRole)
                                                @if($demand->lastListApprovedRole->role_key == 'cmh_clark')
                                                    Afmsd Clerk
                                                @elseif($demand->lastListApprovedRole->role_key == 'mo')
                                                    Afmsd Stock Controll Officer
                                                @else
                                                    {{$demand->lastListApprovedRole->role_name}}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="d-flex justify-content-end">
                                            @if ($user_approval_role && $user_approval_role->role_key == 'cmh_clark' && auth()->user()->subOrganization->type != 'AFMSD' && isset($demand->is_list_approved) && (!isset($demand->$demand->is_unit_approved) || (isset($demand->$demand->is_unit_approved) && !$demand->$demand->is_unit_approved)) && $demand->is_list_approved && App\Utill\Approval\AnnualDemandUnitApprovalSteps::nextStepWithAnnualDemand($demand->id) && !isset(App\Utill\Approval\AnnualDemandUnitApprovalSteps::nextStepWithAnnualDemand($demand->id)['designation']))
                                                <div class="text-center mr-3 mx-2">
                                                    <a href="{{ route('annual_demand.unit', ['id' => $demand->id,'finatialYear' => $demand->financial_year_id]) }}">
                                                        <i class="fa fa-edit"></i><br />
                                                        Est. Qty.
                                                    </a>
                                                </div>
                                            @endif
                                            @if($demand->is_list_approved && auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'DGMS' || auth()->user()->subOrganization->type == 'AFMSD'))
                                            <div class="text-center mx-2">
                                                <a target="_blank" href="{{url('annual_demand_list/download/excel/'.$demand->id)}}" title="Export">
                                                    <i class="fa fa-file-excel"></i>
                                                    <br /> Export PVMS
                                                </a>
                                            </div>
                                            @endif
                                            @if (isset($demand->is_list_approved) && $demand->is_list_approved && !isset(auth()->user()->dept_id))
                                                <div class="text-center mr-3 mx-2">
                                                    <a href="{{ route('annual_demand.unit', ['id' => $demand->id,'finatialYear' => $demand->financial_year_id, 'mode' => "view"]) }}">
                                                        <i class="fa fa-eye"></i><br />
                                                        Unit Estimation View
                                                    </a>
                                                </div>
                                            @endif
                                            @if(!isset($demand->is_list_approved) || (isset($demand->is_list_approved) && !$demand->is_list_approved && !$demand->is_unit_approved))
                                                <div class="text-center mr-3 mx-2">
                                                    <a href="{{ route('annual_demand.create', ['finatialYear' => $demand->financial_year_id,'mode' => 'view']) }}">
                                                        <i class="fa fa-eye"></i><br />
                                                       List View
                                                    </a>
                                                </div>
                                            @endif
                                            {{-- @if(!$demand->is_unit_approved && $demand->is_list_approved && auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'AFMSD' || auth()->user()->subOrganization->type == 'DGMS'))
                                                <div class="text-center mr-3 mx-2">
                                                    <a href="{{ route('annual_demand_unit.list', ['id' => $demand->id ]) }}">
                                                        <i class="fa fa-eye"></i><br />
                                                        View Demand
                                                    </a>
                                                </div>
                                            @endif --}}
                                            @if(auth()->user()->dept_id &&  App\Utill\Approval\AnnualDemandListApprovalSteps::nextStep($demand->id) && isset(App\Utill\Approval\AnnualDemandListApprovalSteps::nextStep($demand->id)['step']) && App\Utill\Approval\AnnualDemandListApprovalSteps::nextStep($demand->id)['step'] == 1)
                                                <div class="text-center mr-3 mx-2">
                                                    <a href="{{ route('annual_demand.create', ['finatialYear' => $demand->financial_year_id]) }}">
                                                        <i class="fa fa-edit"></i><br />
                                                        Edit
                                                    </a>
                                                </div>
                                            @endif
                                            @if (!$demand->is_list_approved && $user_approval_role && $user_approval_role->role_key == App\Utill\Approval\AnnualDemandListApprovalSteps::nextStep($demand->id)['designation'] && (App\Utill\Approval\AnnualDemandListApprovalSteps::nextStep($demand->id)['org'] != 'afmsd' || (App\Utill\Approval\AnnualDemandListApprovalSteps::nextStep($demand->id)['org'] == 'afmsd' && auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'AFMSD' )))
                                                <div class="text-center mx-2">
                                                    <a href="{{ route('annual_demand.create', ['finatialYear' => $demand->financial_year_id]) }}"
                                                        class="approval" data-demand-id="{{ $demand->id }}" data-action="approve">
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

                                                        <br />{{ App\Utill\Approval\AnnualDemandListApprovalSteps::nextStep($demand->id)['btn_text'] }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if (!$demand->is_unit_approved && $user_approval_role && App\Utill\Approval\AnnualDemandUnitApprovalSteps::nextStepWithAnnualDemand($demand->id)['designation'] && $user_approval_role->role_key == App\Utill\Approval\AnnualDemandUnitApprovalSteps::nextStepWithAnnualDemand($demand->id)['designation'])
                                                <div class="text-center mx-2">
                                                    <a href="{{ route('annual_demand.unit', ['id' => $demand->id,'finatialYear' => $demand->financial_year_id]) }}"
                                                        class="approval" data-demand-id="{{ $demand->id }}" data-action="approve">
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

                                                        <br />{{ App\Utill\Approval\AnnualDemandUnitApprovalSteps::nextStepWithAnnualDemand($demand->id)['btn_text'] }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if($demand->is_unit_approved && auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'DGMS' || auth()->user()->subOrganization->type == 'AFMSD'))
                                            <div class="text-center mx-2">
                                                <a target="_blank" href="{{url('annual_demand/download/excel/'.$demand->id)}}" title="Export">
                                                    <i class="fa fa-file-excel"></i>
                                                    <br /> Export Demand
                                                </a>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            <tbody>
                            </tbody>
                        </table>
                        {{ $annual_demands->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
