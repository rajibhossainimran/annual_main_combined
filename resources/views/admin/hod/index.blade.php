@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Head of The Department</div>
                <div class="main-card card app-content-inner">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">List of HOD</h5>
                            @can('Create HOD')
                                <a class="nav-link" href="{{route('add.HOD')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create HOD
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
                                            <th>CMH Unit</th>
                                            <th>Dept Name</th>
                                            <th>HOD</th>
                                            <th>Rank</th>
                                            <th>Contact No</th>
                                            <th>User ID/Email</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($users as $k=>$data)
                                            <tr>
                                                <th scope="row">{{$k+1}}</th>
                                                <td>{{$data->sub_name}}</td>
                                                <td>{{$data->dept_name}}</td>
                                                <td>{{$data->name}}</td>
                                                <td>{{$data->rank}}</td>
                                                <td>{{$data->phone}}</td>
                                                <td>{{$data->email}}</td>
                                                <td class="d-flex">
                                                    @can('Edit HOD')
                                                        <a href="{{url('/settings/edit/cmh/hod/'.$data->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete HOD')
                                                        <form action="{{url('/settings/delete/cmh/hod/'.$data->id)}}" method="post">
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
