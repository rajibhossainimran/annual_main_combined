@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title"> Department Category</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create Department Category</h5>
                            @can('TO & E')
                                <a class="nav-link" href="{{route('all.CMHDepartmentCategory')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        List of Department Category
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('store.CMHDepartmentCategory')}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Select  CMH Unit<span class="requiredStar">*</span></label>
                                        <select id="subOrg" class="mb-2 form-control" required name="sub_org_id">
                                            @if(Auth::user()->sub_org_id)
                                                <option value="{{$subOrg->id}}" selected> {{$subOrg->name}}</option>
                                            @else
                                                <option value="">Select</option>
                                                @foreach($subOrg as $sub)
                                                    <option value="{{$sub->id}}">{{$sub->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Select  CMH Department<span class="requiredStar">*</span></label>
                                        <select id="dept" class="mb-2 form-control" required name="dept_id">
                                            <option value="" > Select</option>
                                            @if(Auth::user()->sub_org_id)
                                                @foreach($dept as $dep)
                                                    <option value="{{$dep->id}}">{{$dep->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Category Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Name" required class="form-control" name="name">
                                    </div>
                                    <div class="form-group">
                                        <label>Category Code <span class="requiredStar">*</span></label>
                                        <input type="number" placeholder="Code" required class="form-control" name="code">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Add Category</button>
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
<script src="/admin/scripts/authorized-create.js"></script>
@endpush
