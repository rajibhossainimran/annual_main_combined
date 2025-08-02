@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Vendors</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">Vendors</h5>
{{--                            {{ $users->links('vendor.pagination.custom') }}--}}
                            @can('Create Vendor')
                            <a class="nav-link" href="{{route('add.vendor')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                   Create Vendor
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
                                            <th>User ID</th>
                                            <th>Email</th>
                                            <th>Company Name</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($users as $k=>$perm)
                                            <tr>
                                                <th scope="row">{{$k+1}}</th>
                                                <td>{{$perm->name}}</td>
                                                <td>{{$perm->email}}</td>
                                                <td>{{$perm->company_email}}</td>
                                                <td>{{$perm->company_name}}</td>
                                                <td>{{$perm->phone}}</td>
                                                <td>{{$perm->address}}</td>
                                                <td class="d-flex">
                                                    @can('Edit Vendor')
                                                        <a href="{{url('/edit/vendor/'.$perm->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Vendor')
                                                        <form action="{{url('/delete/vendor/'.$perm->id)}}" method="post">
                                                            @csrf
                                                            <button type="submit" id="{{$perm->id}}" class="border-0 btn-transition btn btn-outline-danger delete-account-unit"><i class="fa fa-trash-alt"></i></button>
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
                        <br>
{{--                        {{ $users->links('vendor.pagination.custom-footer') }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
