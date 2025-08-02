@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title"> TO & E</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit TO & E</h5>
                            @can('TO & E')
                                <a class="nav-link" href="{{route('all.Authorized')}}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        List of TO & E
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('update.Authorized')}}" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{$authorized->id}}">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Select  CMH Unit<span class="requiredStar">*</span></label>
                                        <select id="subOrg" class="mb-2 form-control" required name="sub_org_id">
                                            @if(Auth::user()->sub_org_id)
                                                <option value="{{$subOrg->id}}" selected> {{$subOrg->name}}</option>
                                            @else
                                                <option value="">Select</option>
                                                @foreach($subOrg as $sub)
                                                    <option value="{{$sub->id}}" @if($authorized->sub_org_id == $sub->id) selected @endif>{{$sub->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Select  CMH Department<span class="requiredStar">*</span></label>
                                        <select id="dept" class="mb-2 form-control" required name="dept_id">
                                            <option value="" > Select</option>
                                            @foreach($dept as $dp)
                                                <option value="{{$dp->id}}" @if($dp->id == $authorized->dept_id) selected @endif> {{$dp->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Select PVMS <span class="requiredStar">*</span></label>
                                        <select class="mb-2 form-control select2 select2-hidden-accessible" required name="pvms_id" style="width: 100%;" tabindex="-1" aria-hidden="true">
                                            <option value="" > Select</option>
                                            @foreach($pvms as $ps)
                                                <option value="{{$ps->id}}" @if($authorized->pvms_id == $ps->id) selected @endif>{{$ps->pvms_id}} {{$ps->nomenclature}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Authorized Machine <span class="requiredStar">*</span></label>
                                        <input type="number" value="{{$authorized->authorized_number}}" min="1" placeholder="Authorized Machine Number" required class="form-control" name="authorized_number">
                                    </div>
                                    <div class="form-group">
                                        <label>Authorized Machine Available<span class="requiredStar">*</span></label>
                                        <input type="number" value="{{$authorized->available_number}}" min="1" placeholder="Authorized Machine Available Number" required class="form-control" name="available_number">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update TO & E</button>
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
