@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title"> Departments</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit Department</h5>
                            @can('Departments')
                                <a class="nav-link" href="{{route('all.CMHDepartment')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        List of Department
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('update.CMHDepartment')}}" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{$id}}">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Select  <span class="requiredStar">*</span></label>
                                        <select class="mb-2 form-control" required name="sub_org_id">
                                            @if(Auth::user()->sub_org_id)
                                                <option value="{{$subOrg->id}}" selected> {{$subOrg->name}}</option>
                                            @else
                                                <option value="" > Select</option>
                                                @foreach($subOrg as $sub)
                                                    <option value="{{$sub->id}}" @if($depart->sub_org_id == $sub->id) selected @endif>{{$sub->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Department Name <span class="requiredStar">*</span></label>
                                        <input type="text" value="{{$depart->name}}" placeholder="Department Name" required class="form-control" name="name">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Edit Department</button>
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
