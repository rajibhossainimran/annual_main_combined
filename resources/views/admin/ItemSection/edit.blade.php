@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Item Sections</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit Item Section</h5>
                            @can('Show Item sections')
                            <a class="nav-link" href="{{route('all.item.section')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Item Sections
                                </button>
                            </a>
                            @endcan
                        </div>
                        <div class="pb-2">
                            <form action="{{route('update.item.section')}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                    <input type="hidden" name="id" value="{{$item_section->id}}"/>
                                        <label>Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Name" required class="form-control" name="name" value="{{$item_section->name}}">
                                    </div>
                                    <div class="form-group">
                                        <label>Code <span class="requiredStar">*</span></label>
                                        <input type="number" min="0" placeholder="Code" required class="form-control" name="code" value="{{$item_section->code}}">
                                    </div>
                                    <div class="form-group">
                                        <label>Status <span class="requiredStar">*</span></label>
                                        <select class="mb-2 form-control" required name="status" value="{{ $item_section->status }}">
                                            <option value="" > Select</option>
                                            <option value="0" {{ $item_section->status == 0 ? 'selected' : '' }}> Inactive</option>
                                            <option value="1" {{ $item_section->status == 1 ? 'selected' : '' }}> Active</option>

                                        </select>
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Item Section</button>
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
