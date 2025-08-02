@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner ">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Submit Documents</div>
                <div class="tabs-animation app-content-inner">
                    <div class="row app-content-inner">
                        <div class="col-lg-12">
                            <div class="row">

                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <div class="row">
                                        <div class="col-lg-6 offset-3 mt-3">
                                            <h5 class="text-center font-size-17">
                                                <span class="font-weight-bold tender-number">Tender Number: <span class="tender-number-span"">@if(isset($tender->tender_no)){{$tender->tender_no}}@endif</span></span>
                                            </h5>
                                            <br>
                                            <h5 class="">Required Documents </h5>
                                            <form action="{{route('submit.file')}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="tender_id" value="{{$id}}">
                                                @foreach($files as $file)
                                                    <div class="form-group row">
                                                        <label for="inputPassword" class="col-sm-6 col-form-label">{{$file->name}}  (<span class="color-span">{{$file->file_type}}</span>) <span class="requiredStar">*</span></label>
                                                        <div class="col-sm-6">
                                                            <input type="file" name="file[]" required class="" id="file">
                                                        </div>
                                                    </div>
                                                @endforeach
                                                    <div class="form-group row">
                                                        <label for="inputPassword" class="col-sm-6 col-form-label">Financial offer (<span class="color-span">xlsx</span>) <span class="requiredStar">*</span></label>
                                                        <div class="col-sm-6">
                                                            <input type="file" name="technical_submission_file" required class="" id="file">
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
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
