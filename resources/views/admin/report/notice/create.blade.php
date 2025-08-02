@extends('admin.master')
@push('css')
@endpush

@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Notices</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create Notice</h5>
                            {{-- @can('Show Notices') --}}
                            <a class="nav-link" href="{{ route('urgent.notices') }}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Notices
                                </button>
                            </a>
                            {{-- @endcan --}}
                        </div>

                        <div class="pb-2">
                            <form action="{{ route('urgent.notices.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="row pl-1 pr-1">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Title <span class="requiredStar">*</span></label>
                                                <input type="text" required class="form-control" name="title"
                                                    value="{{ old('title') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Description <span class="requiredStar">*</span></label>
                                                <textarea class="form-control" name="description" required>{{ old('description') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Upload File</label>
                                                <input type="file" class="form-control-file" name="file">
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
    <script src="/admin/scripts/notice-create.js"></script>
@endpush
