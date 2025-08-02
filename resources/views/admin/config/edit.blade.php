@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title"> Config Setting</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit Config</h5>
                            @can('Show Config')
                                <a class="nav-link" href="{{route('all.config')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        Config Setting
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('update.config')}}" method="post">
                                @csrf
                                <div class="col-lg-6">

                                    <div class="form-group">
                                        <label>Select Config <span class="requiredStar">*</span></label>
                                        <select class="mb-2 form-control" required name="key">
                                            <option value="" > Select</option>
                                            <option value="Warning Days" @if($setting->key == 'Warning Days') selected @else disabled @endif> Warning Days</option>
                                            <option value="Reset Days" @if($setting->key == 'Reset Days') selected @else disabled @endif> Reset Days</option>

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Days <span class="requiredStar">*</span></label>
                                        <input type="number" min="1" placeholder="days" required class="form-control" name="value" value="{{$setting->value}}">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Config</button>
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
