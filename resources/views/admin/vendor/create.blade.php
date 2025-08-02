@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Vendors</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create Vendor</h5>
                            @can('Show Vendors')
                            <a class="nav-link" href="{{route('all.vendor')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Vendors
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('store.vendor')}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Proprietor/Managing Director Name <span class="requiredStar">*</span></label>
                                        <input type="text" required class="form-control" name="name" value="{{ old('name') }}">
                                    </div>
                                    <div class="form-group">
                                        <label> Company Name <span class="requiredStar">*</span></label>
                                        <input type="text" class="form-control" required name="company_name" value="{{ old('company_name') }}">
                                    </div>
                                    <div class="form-group">
                                        <label> Phone <span class="requiredStar">*</span></label>
                                        <input type="text" required class="form-control" name="phone" value="{{ old('phone') }}">
                                    </div>
                                    <div class="form-group">
                                        <label> Address</label>
                                        <input type="text" required class="form-control" name="address" value="{{ old('address') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Email <span class="requiredStar">*</span></label>
                                        <input type="text" required class="form-control" name="company_email" value="{{ old('company_email') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>User ID <span class="requiredStar">*</span></label>
                                        <input type="text" required class="form-control" name="email" value="{{ old('email') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Password <span class="requiredStar">*</span></label>
                                        <input type="password" required class="form-control" name="password" value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password <span class="requiredStar">*</span></label>
                                        <input type="password" required class="form-control" name="password_confirmation" value="">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Submit</button>
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
