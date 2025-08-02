@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Department Category</div>
                <div class="main-card card app-content-inner">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">List of Department Category</h5>
                            @can('Create TO & E')
                                <a class="nav-link" href="{{route('add.CMHDepartmentCategory')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create Category
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
                                            <th>Department</th>
                                            <th>Category Name</th>
                                            <th>Code</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($departs as $k=>$data)
                                            <tr>
                                                <th scope="row">{{$k+1}}</th>
                                                <td>{{$data->sub_name}}</td>
                                                <td>{{$data->dept_name}}</td>
                                                <td>{{$data->name}}</td>
                                                <td>{{$data->code}}</td>
                                                <td class="d-flex">
                                                    @can('Edit Department Category')
                                                        <a href="{{url('/settings/edit/cmh/department/category/'.$data->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Department Category')
                                                        <form action="{{url('/settings/delete/cmh/department/category/'.$data->id)}}" method="post">
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
