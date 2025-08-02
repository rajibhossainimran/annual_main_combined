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
                            <h5 class="f-14">Create User</h5>
                            @can('Show Users')
                                <a class="nav-link" href="{{ route('all.user') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        Users
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{ route('store.user') }}" method="post">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="row pl-1 pr-1">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label> Name <span class="requiredStar">*</span></label>
                                                <input type="text" required class="form-control" name="name"
                                                    value="{{ old('name') }}">
                                            </div>
                                            <div class="form-group">
                                                <label>User ID <span class="requiredStar">*</span></label>
                                                <input type="text" onkeyup="nospaces(this)" required class="form-control"
                                                    name="email" value="{{ old('email') }}">
                                            </div>
                                            <div class="form-group">
                                                <label> Rank</label>
                                                <input type="text" class="form-control" name="rank"
                                                    value="{{ old('rank') }}">
                                            </div>
                                            <div class="form-group">
                                                <label> Phone</label>
                                                <input type="text" class="form-control" name="phone"
                                                    value="{{ old('phone') }}">
                                            </div>
                                            <div class="form-group">
                                                <label> Address</label>
                                                <input type="text" class="form-control" name="address"
                                                    value="{{ old('address') }}">
                                            </div>
                                            <div class="form-group">
                                                <label> Email</label>
                                                <input type="text" class="form-control" name="demand_email"
                                                    value="{{ old('demand_email') }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="group_id">Group ID</label>
                                                <select name="group_id" id="group_id" class="form-control select2">
                                                    @foreach ($item_groups as $group)
                                                        <option value="{{ $group->id }}">{{ $group->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Password <span class="requiredStar">*</span></label>
                                                <input type="password" required class="form-control" name="password"
                                                    value="">
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm Password <span class="requiredStar">*</span></label>
                                                <input type="password" required class="form-control"
                                                    name="password_confirmation" value="">
                                            </div>
                                            <div class="form-group">
                                                @foreach ($organizations as $organization)
                                                    <input name="org_id" id="org_id" type="hidden"
                                                        value="{{ $organization->id }}">
                                                @endforeach
                                            </div>
                                            @if (isset(Auth::user()->org_id))
                                                <div id="sub_org_section" class="form-group">
                                                    <label>Organization</label>
                                                    <input type="hidden" name="sub_org_id"
                                                        value="{{ Auth::user()->sub_org_id }}" />
                                                    <select id="sub_org_id" class="mb-2 form-control" name="sub_org_id"
                                                        value="{{ Auth::user()->sub_org_id }}"
                                                        {{ isset(Auth::user()->sub_org_id) ? 'disabled' : '' }}>
                                                        <option value=""> Select</option>
                                                        @foreach ($sub_organizations as $suborganization)
                                                            <option value="{{ $suborganization->id }}"
                                                                {{ isset(Auth::user()->sub_org_id) && Auth::user()->sub_org_id == $suborganization->id ? 'selected' : '' }}>
                                                                {{ $suborganization->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div id="sub_org_section" class="form-group">
                                                    <label>Organization</label>
                                                    <select id="sub_org_id" class="mb-2 form-control" name="sub_org_id"
                                                        value="">

                                                    </select>
                                                </div>
                                            @endif
                                            @if (isset(Auth::user()->sub_org_id))
                                                <div id="branch_section" class="form-group">
                                                    <label>Store</label>
                                                    <select id="branch_id" class="mb-2 form-control" name="branch_id"
                                                        value="">
                                                        <option value=""> Select</option>
                                                        @foreach ($branches as $branch)
                                                            <option value="{{ $branch->id }}">{{ $branch->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div id="branch_section" class="form-group">
                                                    <label>Store</label>
                                                    <select id="branch_id" class="mb-2 form-control" name="branch_id"
                                                        value=""></select>
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <label>Role & Permission <span class="requiredStar">*</span></label>
                                                <select class="mb-2 form-control" required name="roles[]">
                                                    <option value=""> Select</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if (isset(Auth::user()->sub_org_id))
                                                <div class="form-group">
                                                    <label>Demand & Notesheet Approval Role</label>
                                                    <select class="mb-2 form-control" name="user_approval_role_id"
                                                        id="user_approval_role_id">
                                                        <option value="">Select</option>
                                                        @foreach ($user_approval_roles as $user_approval_role)
                                                            @if ($user_approval_role->id == 1)
                                                                <option value="{{ $user_approval_role->id }}">
                                                                    {{ $user_approval_role->role_name }}</option>
                                                            @elseif($user_approval_role->id == 14)
                                                                <option value="{{ $user_approval_role->id }}">
                                                                    {{ $user_approval_role->role_name }}</option>
                                                            @elseif($user_approval_role->id == 15)
                                                                <option value="{{ $user_approval_role->id }}">
                                                                    {{ $user_approval_role->role_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <label>Demand & Notesheet Approval Role</label>
                                                    <select class="mb-2 form-control" name="user_approval_role_id"
                                                        id="user_approval_role_id">
                                                        <option value="">Select</option>
                                                        @foreach ($user_approval_roles as $user_approval_role)
                                                            <option value="{{ $user_approval_role->id }}">
                                                                {{ $user_approval_role->role_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="form-group dis-none" id="wing_form">
                                                <label>Wing Head</label>
                                                <select class="mb-2 form-control" name="wing_id" id="wing_id">
                                                    <option value="">Select Wing</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label> For Role</label>
                                                <input type="text" class="form-control" name="for_role"
                                                    value="{{ old('for_role') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1 text-center">
                                    <button type="submit" class="btn btn-primary mt-1">Submit</button>
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
    <script src="/admin/scripts/user-create.js"></script>
@endpush
