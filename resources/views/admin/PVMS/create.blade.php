@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">PVMS</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create PVMS</h5>
                            @can('PVMS')
                            <a class="nav-link" href="{{route('all.pvms')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    All PVMS
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{ route('store.pvms') }}" method="post">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>PVMS  <span class="requiredStar">*</span>
{{--                                                    <span class="required-sign">*</span>--}}
                                                </label>
                                                <input type="text" placeholder="PVMS Name" required class="form-control"
                                                       name="pvms_name" value="{{ old('pvms_name') }}">
                                            </div>
{{--                                            <div class="form-group">--}}
{{--                                                <label>PVMS Id<span class="required-sign">*</span></label>--}}
{{--                                                <input type="text" placeholder="PVMS Id" required class="form-control"--}}
{{--                                                       name="pvms_id" value="{{ old('pvms_id') }}">--}}
{{--                                            </div>--}}
                                            <div class="form-group">
                                                <label>Nomenclature  <span class="requiredStar">*</span></label>
                                                <input type="text" placeholder="Nomenclature" required class="form-control"
                                                       name="nomenclature" value="{{ old('nomenclature') }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Specification </label>
                                                <select class="form-control"  name="specifications_id">
                                                    <option value="">Select</option>
                                                    @foreach ($specification as $specifi)
                                                        <option value="{{ $specifi->id }}">{{ $specifi->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Item Section </label>
                                                <select class="form-control" name="item_sections_id">
                                                    <option value="">Select</option>
                                                    @foreach ($itemSections as $section)
                                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Control Type </label>
                                                <select class="form-control" name="control_type_id">
                                                    <option value="">Select</option>
                                                    @foreach ($controlType as $control)
                                                        <option value="{{ $control->id }}">{{ $control->control_type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Page No </label>
                                                <input type="text" placeholder="Page No" class="form-control" name="page_no"
                                                       value="{{ old('page_no') }}">
                                            </div>
                                             <div class="form-group">
                                                <label>Item Source </label>
                                                <input type="text" placeholder="Item source" class="form-control"
                                                    name="item_source" value="{{ old('item_source') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Old PVMS</label>
                                                <input type="text" placeholder="Old PVMS Name" class="form-control"
                                                       name="pvms_old_name" value="{{ old('pvms_old_name') }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Account Units  <span class="requiredStar">*</span></label>
                                                <select class="form-control" required name="account_units_id">
                                                    <option value="">Select</option>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Item Group</label>
                                                <select class="form-control"  name="item_groups_id">
                                                    <option value="">Select</option>
                                                    @foreach ($itemGroup as $group)
                                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Item Type <span class="requiredStar">*</span></label>
                                                <select class="form-control" required name="item_types_id">
                                                    <option value="">Select</option>
                                                    @foreach ($itemType as $itype)
                                                        <option value="{{ $itype->id }}">{{ $itype->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Item Department</label>
                                                <select class="form-control" name="item_departments_id">
                                                    <option value="">Select</option>
                                                    @foreach ($itemDepartment as $dept)
                                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Remarks </label>
                                                <input type="text" placeholder="Remark" class="form-control"
                                                       name="remarks" value="{{ old('remarks') }}">
                                            </div>
                                        </div>
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
@endsection
@push('js')
@endpush
