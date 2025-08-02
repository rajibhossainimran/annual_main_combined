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
                            <h5 class="f-14">Change Password</h5>
                            @can('Show Users')
                                <a class="nav-link" href="{{route('all.user')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        Users
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('update.password.user.all')}}" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{$id}}">
                                <div class="col-lg-12">
                                    <div class="row pl-1 pr-1">
                                        <div class="col-lg-6">
                                            @if(Auth::user()->id == $id)
                                                <div class="form-group">
                                                    <label>old Password</label>
                                                    <input type="password" required class="form-control" name="old_password" value="">
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <label>New Password</label>
                                                <input type="password" required class="form-control" name="new_password" value="">
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm Password</label>
                                                <input type="password" required class="form-control" name="new_password_confirmation" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1 text-center">
                                    <button type="submit" class="btn btn-primary mt-1">Change Password</button>
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
