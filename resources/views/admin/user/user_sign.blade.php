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
                            <h5 class="f-14">Change Signature</h5>

                        </div>

                        <div class="pb-2">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{url('update/user/digital/sign')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="row pl-1 pr-1">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Upload Signature (width: 200px, Height:100px)</label>
                                                <input type="file" class="form-control" name="image" value="">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1 text-center">
                                    <button type="submit" class="btn btn-primary mt-1">Update Sign</button>
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
