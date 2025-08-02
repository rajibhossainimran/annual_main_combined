@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">

            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Item Type</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit Item Type</h5>
                            @can('Show Item Types')
                            <a class="nav-link" href="{{route('all.item.types')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Item Types
                                </button>
                            </a>
                            @endcan
                        </div>
                        <div class="pb-2">
                            <form action="{{url('/settings/edit/item-types/'.$item_type->id)}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                <input type="hidden" name="id" value="{{$item_type->id}}"/>
                                    <div class="form-group">
                                        <label>Item Type Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Item Type Name" required class="form-control" name="name" value="{{ $item_type->name }}">
                                    </div>
                                    <div class="form-group">
                                        <label>ANX <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="ANX" required class="form-control" name="anx" value="{{ $item_type->anx }}">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Item Type</button>
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
