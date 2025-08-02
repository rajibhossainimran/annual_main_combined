@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Financial Year</div>
{{--                {{ $financial_years->links('vendor.pagination.custom') }}--}}
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        {{--                        <h5 class="card-title">Table striped</h5>--}}
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">Financial Years</h5>
                            @can('Create Financial Year')
                            <a class="nav-link" href="{{route('add.financial.year')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                    Create Year
                                </button>
                            </a>
                            @endcan
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="example" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($financial_years as $k=>$financial_year)
                                            <tr>
                                                <th scope="row">{{$k+1}}</th>
                                                <td>{{$financial_year->name}}</td>
                                                <td>
                                                    @if($financial_year->status)
                                                        <div class="badge badge-pill badge-success">Active</div>
                                                    @else
                                                        <div class="badge badge-danger ml-2">Inactive</div>
                                                    @endif
                                                </td>
                                                <td>{{$financial_year->uname}}</td>

                                                <td class="d-flex">
                                                    @can('Edit Service')
                                                        <a href="{{url('/settings/edit/financial-year/'.$financial_year->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Service')
                                                        <form action="{{url('/settings/delete/financial-year/'.$financial_year->id)}}" method="post">
                                                            @csrf
                                                            <button type="submit" id="{{$financial_year->id}}" class="border-0 btn-transition btn btn-outline-danger delete-account-unit"><i class="fa fa-trash-alt"></i></button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <br/>
{{--                        {{ $financial_years->links('vendor.pagination.custom') }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
