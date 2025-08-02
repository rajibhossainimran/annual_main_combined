@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Account Units</div>
                <div class="main-card card app-content-inner">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Account Units</h5>
{{--                            {{ $account_units->links('vendor.pagination.custom') }}--}}
                            @can('Create Units')
                            <a class="nav-link" href="{{route('add.account.unit')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                    Create Unit
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
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($account_units as $key=>$value)
                                            <tr>
                                                <th scope="row">{{$value->id}}</th>
                                                <td>{{$value->name}}</td>
                                                <td>
                                                    @if($value->status)
                                                        <div class="badge badge-pill badge-success">Active</div>
                                                    @else
                                                        <div class="badge badge-danger ml-2">Inactive</div>
                                                    @endif
                                                </td>
                                                <td class="d-flex">
                                                    @can('Edit Account Unit')
                                                        <a href="{{url('/settings/edit/account-units/'.$value->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Account Unit')
                                                        <form action="{{url('/settings/delete/account-units/'.$value->id)}}" method="post">
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
{{--                        {{ $account_units->links('vendor.pagination.custom') }}--}}
                        <!-- <div class="row">
                            <div class="col-sm-12 col-md-5">
                            {{-- {!! $account_units->appends($_GET)->links() !!} --}}
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
