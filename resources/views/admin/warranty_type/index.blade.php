@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Warranty Types</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">Warranty Types</h5>
{{--                            {{ $warranty_types->links('vendor.pagination.custom') }}--}}
                            @can('Create Warranty Types')
                            <a class="nav-link" href="{{route('add.warranty.type')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                    Create Warranty Type
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
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($warranty_types as $key=>$value)
                                            <tr>
                                                <th scope="row">{{$value->id}}</th>
                                                <td>{{$value->name}}</td>
                                                <td class="d-flex">
                                                    @can('Edit Warranty Types')
                                                        <a href="{{url('/settings/edit/warranty-type/'.$value->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Warranty Types')
                                                        <form action="{{url('/settings/delete/warranty-type/'.$value->id)}}" method="post">
                                                            @csrf
                                                            <button type="submit" id="{{$value->id}}" class="border-0 btn-transition btn btn-outline-danger delete-account-unit"><i class="fa fa-trash-alt"></i></button>
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
{{--                        {{ $warranty_types->links('vendor.pagination.custom') }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
