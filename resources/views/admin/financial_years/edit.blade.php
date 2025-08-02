@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Division</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Update Financial Year</h5>
                            @can('Show Financial Year')
                                <a class="nav-link" href="{{route('all.financial.year')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        Financial Years
                                    </button>
                                </a>
                            @endcan
                        </div>
                        <div class="pb-2">
                            <form action="{{route('update.financial.year')}}" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{$year->id}}">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Name" required class="form-control" name="name" value="{{ $year->name }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Status <span class="requiredStar">*</span></label>
                                        <select class="mb-2 form-control" required name="status">
                                            <option value="" > Select</option>
                                            <option value="0" {{ $year->status == 0 ? 'selected' : '' }}> Inactive</option>
                                            <option value="1" {{ $year->status == 1 ? 'selected' : '' }}> Active</option>

                                        </select>
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Year</button>
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
