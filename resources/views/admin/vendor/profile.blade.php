@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">My Account</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Profile Info</h5>
                        </div>
                        <div class="pb-2">
                            <form action="" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div>
                                        <h5>Personal Info</h5>
                                    </div>
                                    <div class="form-group">
                                        <label>Account Name</label>
                                        <input type="text" placeholder="Account Name" required class="form-control" name="company_name" value="{{ $users->company_name }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Email </label>
                                        <input type="text" placeholder="Email" required class="form-control" readonly name="email" value="{{ $users->email }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Password </label>
                                        <input type="text" placeholder="password" required class="form-control" value="**************" readonly>
                                        <p class="float-right"><a href="{{route('change.password.user')}}">Change Password</a> </p>
                                    </div>
                                    <div>
                                        <h5>Contact Info</h5>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="number" min="11" placeholder="Phone" required class="form-control" name="phone" value="{{ $users->phone }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input type="text" placeholder="Address" required class="form-control" name="address" value="{{ $users->address }}">
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
