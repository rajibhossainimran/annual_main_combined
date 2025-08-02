@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title"> Head of The Department</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create HOD</h5>
                            @can('HOD')
                                <a class="nav-link" href="{{route('all.HOD')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        List of HOD
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('store.HOD')}}" method="post">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Select CMH Unit<span class="requiredStar">*</span></label>
                                                <select id="subOrg" class="mb-2 form-control" required name="sub_org_id">
                                                    @if(Auth::user()->sub_org_id)
                                                        <option value="{{$subOrg->id}}" selected> {{$subOrg->name}}</option>
                                                    @else
                                                        <option value="">Select</option>
                                                        @foreach($subOrg as $sub)
                                                            <option value="{{$sub->id}}">{{$sub->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Select CMH Department<span class="requiredStar">*</span></label>
                                                <select id="dept" class="mb-2 form-control" required name="dept_id">
                                                    <option value="" > Select</option>
                                                    @if(Auth::user()->sub_org_id)
                                                        @foreach($dept as $dep)
                                                            <option value="{{$dep->id}}">{{$dep->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Name <span class="requiredStar">*</span></label>
                                                <input type="text" placeholder="Name" required class="form-control" name="name">
                                            </div>
                                            <div class="form-group">
                                                <label>Rank <span class="requiredStar">*</span></label>
                                                <input type="text" placeholder="Rank" required class="form-control" name="rank">
                                            </div>
                                            <div class="form-group">
                                                <label>Phone <span class="requiredStar">*</span></label>
                                                <input type="number" placeholder="Phone" required class="form-control" name="phone">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>User ID/Email <span class="requiredStar">*</span></label>
                                                <input type="text" onkeyup="nospaces(this)" required class="form-control" name="email" value="{{ old('email') }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Password <span class="requiredStar">*</span></label>
                                                <input type="password" required class="form-control" name="password" value="">
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm Password <span class="requiredStar">*</span></label>
                                                <input type="password" required class="form-control" name="password_confirmation" value="">
                                            </div>
                                            <div class="form-group">
                                                <label>Role & Permission <span class="requiredStar">*</span></label>
                                                <select class="mb-2 form-control" required name="roles[]">
                                                    <option value="" > Select</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <br>
                                            <div class="mt-1">
                                                <button type="submit" class="btn btn-primary mt-1">Add HOD</button>
                                            </div>
                                        </div>
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
<script src="{{asset('/admin')}}/scripts/authorized-create.js"></script>
@endpush
