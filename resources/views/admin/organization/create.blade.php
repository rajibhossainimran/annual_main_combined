@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Governing Body</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create Governing Body</h5>
                            @can('Show Organization')
                            <a class="nav-link" href="{{route('all.organization')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    All Governing Body
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('store.organization')}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Name" required class="form-control" name="name" value="{{ old('name') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Code <span class="requiredStar">*</span></label>
                                        <input type="number" min="0" placeholder="Code" required class="form-control" name="code" value="{{ old('code') }}">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Add Organization</button>
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
