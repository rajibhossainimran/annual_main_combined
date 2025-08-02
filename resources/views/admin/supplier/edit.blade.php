@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Supplier</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit Supplier</h5>
                            @can('Show Account Units')
                            <a class="nav-link" href="{{route('all.supplier')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Supplier
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{url('settings/update/supplier/'.$supplier->id)}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Supplier Name <span class="requiredStar">*</span></label>
                                        <input type="text" value="{{$supplier->name}}" placeholder="Supplier Name" required class="form-control" name="name" value="{{ old('name') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Address <span class="requiredStar">*</span></label>
                                        <input type="text" value="{{$supplier->address}}" placeholder="Address" required class="form-control" name="address" value="{{ old('address') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Contact Number <span class="requiredStar">*</span></label>
                                        <input type="number" value="{{$supplier->contact_no}}"  min="1" minlength="1"  placeholder="contact number" required class="form-control" name="contact_no" value="{{ old('contact_no') }}">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Edit Supplier</button>
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
