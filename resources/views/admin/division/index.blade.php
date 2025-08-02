@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Division</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        {{-- <h5 class="card-title">All Division</h5> --}}
                        <div class=" d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">All Division</h5>
{{--                            {{ $divisions->links('vendor.pagination.custom') }}--}}
                            @can('Create Division')
                                <a class="nav-link" href="{{ route('add.division') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create Division
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
                                            <th>Code</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($divisions as $key => $value)
                                            <tr>
                                                <th scope="row">{{ $key+1 }}</th>
                                                <td>{{ $value->name }}</td>
                                                <td>{{ $value->code }}</td>
                                                <td>
                                                    @if ($value->status)
                                                        <div class="badge badge-pill badge-success">Active</div>
                                                    @else
                                                        <div class="badge badge-danger ml-2">Inactive</div>
                                                    @endif
                                                </td>
                                                <td class="d-flex">
                                                    @can('Edit Division')
                                                        <a href="{{ url('/settings/edit/division/' . $value->id) }}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Division')
                                                        <form action="{{ url('/settings/delete/division/' . $value->id) }}"
                                                              method="post">
                                                            @csrf
                                                            <button type="submit" id="{{ $value->id }}"
                                                                    class="border-0 btn-transition btn btn-outline-danger delete-account-unit"><i
                                                                    class="fa fa-trash-alt"></i></button>
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
{{--                        {{ $divisions->links('vendor.pagination.custom') }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
