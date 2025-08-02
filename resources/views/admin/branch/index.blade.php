@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Stores</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class=" d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">Stores</h5>
{{--                            {{ $branchs->links('vendor.pagination.custom') }}--}}
                            @can('Create Branch')
                                <a class="nav-link" href="{{ route('add.branch') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create Store
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
                                                <th>Id</th>
                                                <th>Name</th>
                                                <th>Organization</th>
                                                <th>Code</th>
                                                <th>Status</th>
                                                <th>Created By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($branchs as $k => $branch)
                                                <tr>
                                                    <th scope="row">{{ $k + 1 }}</th>
                                                    <td>{{ $branch->id }}</td>
                                                    <td>{{ $branch->name }}</td>
                                                    <td>
                                                        @if ($branch->subOrganizationFrom)
                                                            {{ $branch->subOrganizationFrom->name }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $branch->code }}</td>
                                                    <td>
                                                        @if ($branch->status)
                                                            <div class="badge badge-pill badge-success">Active</div>
                                                        @else
                                                            <div class="badge badge-danger ml-2">Inactive</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($branch->createdBy)
                                                            {{ $branch->createdBy->name }}
                                                        @endif
                                                    </td>

                                                    <td class="d-flex">
                                                        @can('Edit Branch')
                                                            <a href="{{ url('/edit/branch/' . $branch->id) }}">
                                                                <button class="btn btn-outline-info border-0">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </a>
                                                        @endcan
                                                        @can('Delete Branch')
                                                            <form action="{{ url('/delete/branch/' . $branch->id) }}" method="post">
                                                                @csrf
                                                                <button type="submit" id="{{ $branch->id }}"
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
{{--                        {{ $branchs->links('vendor.pagination.custom') }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
