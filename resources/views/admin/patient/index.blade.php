@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Patients</div>
                <div class="main-card card app-content-inner">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Patients</h5>
{{--                            {{ $account_units->links('vendor.pagination.custom') }}--}}
                            @can('Create Demand Unit')
                            <a class="nav-link" href="{{route('add.patient')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                    Create Patients
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
                                            <th>Indentification No</th>
                                            <th>Relation</th>
                                            <th>Unit</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($patients as $key=>$value)
                                            <tr>
                                                <th scope="row">{{$value->id}}</th>
                                                <td>{{$value->name}}</td>
                                                <td>{{$value->identification_no}}</td>
                                                <td>{{$value->relation}}</td>
                                                <td>@if(isset($value->unitFrom->name) && !empty($value->unitFrom->name)){{$value->unitFrom->name}}@endif</td>
                                                <td class="d-flex">
                                                    @can('Edit Patient')
                                                        <a href="{{url('/settings/edit/patient/'.$value->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Patient')
                                                        <form action="{{url('/settings/delete/patient/'.$value->id)}}" method="post">
                                                            @csrf
                                                            <button type="submit" id="{{$value->id}}" class="border-0 btn-transition btn btn-outline-danger delete-patient"><i class="fa fa-trash-alt"></i></button>
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
