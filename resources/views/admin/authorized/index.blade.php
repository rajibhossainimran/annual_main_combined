@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">TO & E</div>
                <div class="main-card card app-content-inner">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">List of TO & E</h5>
                            @can('Create TO & E')
                                <a class="nav-link" href="{{route('add.Authorized')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create TO & E
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
                                            <th>PVMS</th>
                                            <th>Nomenclature</th>
                                            <th>CMH Unit</th>
                                            <th>Department</th>
                                            <th>Brand & Specification</th>
                                            <th>supplier</th>
                                            <th>No. Machines Authorized</th>
                                            <th>Available Machines</th>
                                            <th>SVC</th>
                                            <th>UnSVC</th>
                                            <th>Date of Installation</th>
                                            <th>Warranty Period</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($departs as $k=>$data)
                                            <tr>
                                                <th scope="row">{{$k+1}}</th>
                                                <td>{{$data->pvms_id}}</td>
                                                <td>{{$data->nomenclature}}</td>
                                                <td>{{$data->sub_name}}</td>
                                                <td>{{$data->dept_name}}</td>
                                                <td>{{$data->bmo}}</td>
                                                <td>{{$data->supplier}}</td>
                                                <td>{{$data->authorized_number}}</td>
                                                <td>{{$data->available_number}}</td>
                                                <td>{{$data->svc}}</td>
                                                <td>{{$data->unsvc}}</td>
                                                <td>{{$data->doi}}</td>
                                                <td>{{$data->wp}}</td>
                                                <td class="d-flex">
                                                    @can('Edit Authorized Equipment')
                                                        <a href="{{url('/settings/edit/authorized/equipment/'.$data->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Authorized Equipment')
                                                        <form action="{{url('/settings/delete/authorized/equipment/'.$data->id)}}" method="post">
                                                            @csrf
                                                            <button type="submit" id="{{$data->id}}" class="border-0 btn-transition btn btn-outline-danger delete-account-unit"><i class="fa fa-trash-alt"></i></button>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
