@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Item Group</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit Item Group</h5>
                            @can('Show Item Groups')
                            <a class="nav-link" href="{{route('all.group.management')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Item Groups
                                </button>
                            </a>
                            @endcan
                        </div>
                        <div class="pb-2">
                            <form action="{{url('/settings/edit/group-management/'.$item_group->id)}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Item Group Code <span class="requiredStar">*</span></label>
                                        <input type="number" placeholder="Item Group Name" required class="form-control" name="code" value="{{ $item_group->code }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" name="id" value="{{$item_group->id}}"/>
                                        <label>Item Group Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Item Group Name" required class="form-control" name="name" value="{{ $item_group->name }}">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Group</button>
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
