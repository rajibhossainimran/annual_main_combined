@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Tender Documents</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit Tender Documents</h5>
                            @can('Show Tender Documents')
                                <a class="nav-link" href="{{route('all.tender.documents')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        Tender Documents
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('update.tender.documents')}}" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{$document->id}}">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>File Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="File Name" required class="form-control" name="name" value="{{ $document->name }}">
                                    </div>
                                    <div class="form-group">
                                        <label>File Type <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="File Type" required class="form-control" name="file_type" value="{{ $document->file_type }}">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Document</button>
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
