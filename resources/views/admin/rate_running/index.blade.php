@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Rate Running PVMS</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">Rate Running PVMS</h5>
{{--                            {{ $services->links('vendor.pagination.custom') }}--}}
                            @can('Create Rate Running PVMS')
                            <a class="nav-link" href="{{route('add.rate.running')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                    Create Rate Running PVMS
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
                                            <th>Supplier Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>PVMS</th>
                                            <th>Price</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data as $k=>$datum)
                                            <tr>
                                                <th scope="row">{{$k+1}}</th>
                                                <td>{{$datum->name}}</td>
                                                <td>{{$datum->start_date}}</td>
                                                <td>{{$datum->end_date}}</td>
                                                <td>{{$datum->nomenclature}}</td>
                                                <td class="">{{$datum->price}}</td>
                                                <td class="d-flex">
                                                    {{-- @can('Edit Service')
                                                        <a href="{{url('/settings/edit/rate-running-pvms/'.$datum->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Service')
                                                        <form action="{{url('/settings/delete/rate-running-pvms/'.$datum->id)}}" method="post">
                                                            @csrf
                                                            <button type="submit" id="{{$datum->id}}" class="border-0 btn-transition btn btn-outline-danger delete-account-unit"><i class="fa fa-trash-alt"></i></button>
                                                        </form>
                                                    @endcan --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <br/>
{{--                        {{ $services->links('vendor.pagination.custom') }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
