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
                            <h5 class="f-14">Edit Vendor</h5>
                            @can('Show Vendor')
                            <a class="nav-link" href="{{route('all.vendor')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Users
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-4">
                            <form action="{{url('/update/vendor/'.$vendor->id)}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Name <span class="requiredStar">*</span></label>
                                        <input type="text" required class="form-control" name="name" value="{{$vendor->name}}">
                                    </div>
                                    <div class="form-group">
                                        <label> Caompany Name <span class="requiredStar">*</span></label>
                                        <input type="text" required class="form-control" name="company_name" value="{{$vendor->company_name}}">
                                    </div>
                                    <div class="form-group">
                                        <label> Phone <span class="requiredStar">*</span></label>
                                        <input type="text" required class="form-control" name="phone" value="{{$vendor->phone}}">
                                    </div>
                                    <div class="form-group">
                                        <label> Address <span class="requiredStar">*</span></label>
                                        <input type="text" required class="form-control" name="address" value="{{$vendor->address}}">
                                    </div>
                                    <div class="form-group">
                                        <label>Email <span class="requiredStar">*</span></label>
                                        <input type="text" readonly class="form-control" name="email" value="{{$vendor->email}}">
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

@endpush
