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
                                <h5 class="f-14">
                                    Annual Demand (Unit Demand)
                                </h5>
                                {{ $annual_demand_units->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sl.</th>
                                    <th>Unit</th>
                                    <th>Financial year</th>
                                    <th>Last Approval</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                                @foreach ($annual_demand_units as $unit_demand)
                                    <tr>
                                        <td>{{$loop->index+1}}</td>
                                        <td>{{$unit_demand->subOrganization->name}}</td>
                                        <td>{{$unit_demand->annualDemand->financialYear->name}}</td>
                                        <td>
                                            @if ($unit_demand->lastUnitApprovedRole)
                                                {{$unit_demand->lastUnitApprovedRole->role_name}}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="d-flex justify-content-end">
                                            @if (!$unit_demand->is_approved && $user_approval_role && App\Utill\Approval\AnnualDemandUnitApprovalSteps::nextStep($unit_demand->id)['designation'] && $user_approval_role->role_key == App\Utill\Approval\AnnualDemandUnitApprovalSteps::nextStep($unit_demand->id)['designation'])
                                                <div class="text-center mx-2">
                                                    <a href="{{ route('annual_demand.unit', ['id' => $unit_demand->annualDemand->id,'finatialYear' => $unit_demand->annualDemand->financial_year_id, 'unit' => $unit_demand->sub_org_id]) }}"
                                                        class="approval">
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

                                                        <br />{{ App\Utill\Approval\AnnualDemandUnitApprovalSteps::nextStep($unit_demand->id)['btn_text'] }}
                                                    </a>
                                                </div>
                                            @endif
                                            <div class="text-center mr-3 mx-2">
                                                <a href="{{ route('annual_demand.unit', ['id' => $unit_demand->annualDemand->id,'finatialYear' => $unit_demand->annualDemand->financial_year_id, 'unit' => $unit_demand->sub_org_id, 'mode' => "view"]) }}">
                                                    <i class="fa fa-eye"></i><br />
                                                    View
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            <tbody>
                            </tbody>
                        </table>
                        @if($user_approval_role && $user_approval_role->role_key == 'dgms' && !$annual_demand->is_unit_approved)
                        <div class="p-2">
                            <form action="{{route('annual_demand.unit_approve',['id' => $annual_demand->id])}}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-primary mt-1">Approve Annual Demand</button>

                            </form>
                        </div>
                        @endif
                        {{ $annual_demand_units->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
