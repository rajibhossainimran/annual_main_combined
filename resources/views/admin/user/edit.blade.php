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
                            <h5 class="f-14">Edit User</h5>
                            @can('Show Users')
                                <a class="nav-link" href="{{ route('all.user') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        Users
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-4">
                            <form action="{{ url('/update/user/' . $user->id) }}" method="post">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Name <span class="requiredStar">*</span></label>
                                                <input type="text" required class="form-control" name="name"
                                                    value="{{ $user->name }}">
                                            </div>
                                            <div class="form-group">
                                                <label> Rank</label>
                                                <input type="text" class="form-control" name="rank"
                                                    value="{{ $user->rank }}">
                                            </div>
                                            <div class="form-group">
                                                <label> Phone</label>
                                                <input type="text" class="form-control" name="phone"
                                                    value="{{ $user->phone }}">
                                            </div>
                                            <div class="form-group">
                                                <label> Address</label>
                                                <input type="text" class="form-control" name="address"
                                                    value="{{ $user->address }}">
                                            </div>
                                            <div class="form-group">
                                                <label> Email</label>
                                                <input type="text" class="form-control" name="demand_email"
                                                    value="{{ $user->demand_email }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>User ID</label>
                                                <input type="text" readonly class="form-control" name="email"
                                                    value="{{ $user->email }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Role & Permission <span class="requiredStar">*</span></label>
                                                <select class="mb-2 form-control" required name="roles[]">
                                                    <option value=""> Select</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}"
                                                            @if ($role->id == $role_id->id) selected @endif>
                                                            {{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            @if (isset(Auth::user()->sub_org_id))
                                                <div class="form-group">
                                                    <label>Demand & Notesheet Approval Role</label>
                                                    <select class="mb-2 form-control" name="user_approval_role_id">
                                                        <option value="">Select</option>
                                                        @foreach ($user_approval_roles as $user_approval_role)
                                                            @if ($user_approval_role->id == 1)
                                                                <option value="{{ $user_approval_role->id }}"
                                                                    @if ($user->user_approval_role_id == $user_approval_role->id) selected @endif>
                                                                    {{ $user_approval_role->role_name }}</option>
                                                            @elseif($user_approval_role->id == 14)
                                                                <option value="{{ $user_approval_role->id }}"
                                                                    @if ($user->user_approval_role_id == $user_approval_role->id) selected @endif>
                                                                    {{ $user_approval_role->role_name }}</option>
                                                            @elseif($user_approval_role->id == 15)
                                                                <option value="{{ $user_approval_role->id }}"
                                                                    @if ($user->user_approval_role_id == $user_approval_role->id) selected @endif>
                                                                    {{ $user_approval_role->role_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <label>Demand & Notesheet Approval Role</label>
                                                    <select class="mb-2 form-control" name="user_approval_role_id">
                                                        <option value="">Select</option>
                                                        @foreach ($user_approval_roles as $user_approval_role)
                                                            <option value="{{ $user_approval_role->id }}"
                                                                @if ($user->user_approval_role_id == $user_approval_role->id) selected @endif>
                                                                {{ $user_approval_role->role_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <label> For Role</label>
                                                <input type="text" class="form-control" name="for_role"
                                                    value="{{ $user->for_role }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="/admin/scripts/vendor-create.js"></script>
@endpush
