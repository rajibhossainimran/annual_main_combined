@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Users</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Users</h5>
                            {{--                            {{ $users->links('vendor.pagination.custom') }} --}}
                            @can('Create User')
                                <a class="nav-link" href="{{ route('add.user') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create User
                                    </button>
                                </a>
                            @endcan
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="example"
                                        class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>User ID</th>
                                                <th>Role Name</th>
                                                <th>Rank</th>
                                                <th>Phone</th>
                                                <th>Address</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $k => $perm)
                                                <tr>
                                                    <th scope="row">{{ $k + 1 }}</th>
                                                    <td>{{ $perm->name }}</td>
                                                    <td>{{ $perm->email }}</td>
                                                    <td>{{ $perm->role_name }}</td>
                                                    <td>{{ $perm->rank }}</td>
                                                    <td>{{ $perm->phone }}</td>
                                                    <td>{{ $perm->address }}</td>
                                                    <td class="d-flex">
                                                        @can('Password User')
                                                            <a href="{{ url('/change/password/user/' . $perm->id) }}"
                                                                title="Change Password">
                                                                <button class="btn btn-outline-info border-0">
                                                                    <i class="fas fa-key"></i>
                                                                </button>
                                                            </a>
                                                        @endcan
                                                        @can('Edit User')
                                                            <a href="{{ url('/edit/user/' . $perm->id) }}">
                                                                <button class="btn btn-outline-info border-0">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </a>
                                                        @endcan
                                                        @can('Delete User')
                                                            <form action="{{ url('/delete/user/' . $perm->id) }}" method="post">
                                                                @csrf
                                                                <button type="submit" id="{{ $perm->id }}"
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
                        <br>
                        {{--                        {{ $users->links('vendor.pagination.custom-footer') }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
