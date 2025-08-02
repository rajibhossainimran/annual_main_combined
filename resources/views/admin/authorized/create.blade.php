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
                            <h5 class="f-14">Create TO & E</h5>
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
                            <form action="{{route('store.Authorized')}}" method="post">
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
                                        <label>Select PVMS <span class="requiredStar">*</span></label><br>
                                        <div class="dropdown2">
                                            <input type="text" id="inputField" class="form-control showDropdown" placeholder="Search PVMS">
                                            <input type="hidden" name="pvms_id" class="showDropdownHide">
                                            <div id="dropdownMenu" class="dropdown-content2"></div>
                                        </div>

                                        {{-- <div id="selectedItem"></div> --}}
                                    </div>
                                    {{-- <div class="form-group">
                                        <label>Select PVMS <span class="requiredStar">*</span></label>
                                        <select class="mb-2 form-control " required name="pvms_id" >
                                            <option value="" > Select</option>
                                            @foreach($pvms as $ps)
                                                <option value="{{$ps->id}}">{{$ps->pvms_id}} {{$ps->nomenclature}}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <div class="form-group">
                                        <label>Brand, Model & Origin <span class="requiredStar">*</span></label>
                                        <input type="text" min="1" placeholder=" Brand, Model & Origin" required class="form-control" name="bmo">
                                    </div>
                                    <div class="form-group">
                                        <label>Supplier <span class="requiredStar">*</span></label>
                                        <input type="text" min="1" placeholder=" Supplier" required class="form-control" name="supplier">
                                    </div>
                                    <div class="form-group">
                                        <label>Authorized Machine <span class="requiredStar">*</span></label>
                                        <input type="number" min="1" placeholder=" Machine Number" required class="form-control" name="authorized_number">
                                    </div>
                                    <div class="form-group">
                                        <label>Machine Available<span class="requiredStar">*</span></label>
                                        <input type="number" min="1" placeholder=" Machine Available Number" required class="form-control" name="available_number">
                                    </div>
                                    <div class="form-group">
                                        <label>SVC<span class="requiredStar">*</span></label>
                                        <input type="number" min="0" placeholder=" svc" required class="form-control" name="svc">
                                    </div>
                                    <div class="form-group">
                                        <label>Un SVC<span class="requiredStar">*</span></label>
                                        <input type="number" min="0" placeholder=" Un SVC" required class="form-control" name="unsvc">
                                    </div>
                                    <div class="form-group">
                                        <label>Date of Installation<span class="requiredStar">*</span></label>
                                        <input type="date" min="0" placeholder=" Install Date" required class="form-control" name="doi">
                                    </div>
                                    <div class="form-group">
                                        <label>Warranty Period <span class="requiredStar">*</span></label>
                                        <input type="text" min="0" placeholder=" Warranty Period" required class="form-control" name="wp">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Add TO & E </button>
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
