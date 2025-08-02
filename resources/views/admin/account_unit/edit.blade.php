@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            {{-- <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-drawer icon-gradient bg-happy-itmeo"></i>
                        </div>
                        <div>Account Unit<div class="page-title-subheading">Update Account Unit</div>
                        </div>
                    </div>
                    <div class="page-title-actions">
                        <div class="d-inline-block dropdown">
                            <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn-shadow dropdown-toggle btn btn-info">
                      <span class="btn-icon-wrapper pr-2 opacity-7">
                        <i class="fa fa-business-time fa-w-20"></i>
                      </span> Action </button>
                            <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
                                <ul class="nav flex-column">
                                    @can('Show Account Units')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{route('all.account.units')}}">
                                            <i class="nav-link-icon lnr-inbox"></i>
                                            <span>Account Units</span>
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Account Units</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center  table-header-bg py-1">
                            <h5 class="f-14">Edit Account Units</h5>
                            @can('Show Account Units')
                            <a class="nav-link" href="{{route('all.account.units')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Account Units
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-4">
                            <form action="{{url('/settings/edit/account-units/'.$account_unit->id)}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <input type="hidden" name="id" value="{{$account_unit->id}}"/>
                                    <div class="form-group">
                                        <label>Account Unit Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Account Unit Name" required class="form-control" name="name" value="{{ $account_unit->name }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Status <span class="requiredStar">*</span></label>
                                        <select class="mb-2 form-control" required name="status" value="{{ $account_unit->status }}">
                                            <option value="" > Select</option>
                                            <option value="0" {{ $account_unit->status == 0 ? 'selected' : '' }}> Inactive</option>
                                            <option value="1" {{ $account_unit->status == 1 ? 'selected' : '' }}> Active</option>

                                        </select>
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Unit</button>
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
