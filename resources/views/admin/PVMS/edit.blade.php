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
                            <h5 class="f-14">Edit PVMS</h5>
                            @can('Show PVMS')
                                <a class="nav-link" href="{{ route('all.pvms') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        All PVMS
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{ route('update.pvms') }}" method="post">
                                @csrf
                                <input type="hidden" value="{{ $pvms->id }}" name="id">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>PVMS <span class="requiredStar">*</span></label>
                                                <input type="text" placeholder="PVMS Name" required class="form-control"
                                                    name="pvms_name" value="{{ $pvms->pvms_name }}">
                                            </div>
                                            {{--                                            <div class="form-group"> --}}
                                            {{--                                                <label>PVMS Id<span class="required-sign">*</span></label> --}}
                                            {{--                                                <input type="text" placeholder="PVMS Id" required class="form-control" --}}
                                            {{--                                                       name="pvms_id" value="{{$pvms->pvms_id}}"> --}}
                                            {{--                                            </div> --}}
                                            <div class="form-group">
                                                <label>Nomenclature <span class="requiredStar">*</span></label>
                                                <input type="text" placeholder="Nomenclature" required
                                                    class="form-control" name="nomenclature"
                                                    value="{{ $pvms->nomenclature }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Specification </label>
                                                <select class="form-control" name="specifications_id">
                                                    <option value="">Select</option>
                                                    @foreach ($specification as $specifi)
                                                        <option value="{{ $specifi->id }}"
                                                            @if ($specifi->id == $pvms->specifications_id) selected @endif>
                                                            {{ $specifi->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Item Section </label>
                                                <select class="form-control" name="item_sections_id">
                                                    <option value="">Select</option>
                                                    @foreach ($itemSections as $section)
                                                        <option @if ($section->id == $pvms->item_sections_id) selected @endif
                                                            value="{{ $section->id }}">{{ $section->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Control Type </label>
                                                <select class="form-control" name="control_type_id">
                                                    <option value="">Select</option>
                                                    @foreach ($controlType as $control)
                                                        <option @if ($control->id == $pvms->control_types_id) selected @endif
                                                            value="{{ $control->id }}">{{ $control->control_type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Page No </label>
                                                <input type="text" placeholder="Page No" class="form-control"
                                                    name="page_no" value="{{ $pvms->page_no }}">
                                            </div>
                                             <div class="form-group">
                                                <label>Item Source </label>
                                                <input type="text" placeholder="item_sourc" class="form-control"
                                                    name="item_source" value="{{ $pvms->item_source }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Old PVMS</label>
                                                <input type="text" placeholder="Old PVMS Name" class="form-control"
                                                    name="pvms_old_name" value="{{ $pvms->pvms_old_name }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Account Units <span class="requiredStar">*</span></label>
                                                <select class="form-control" required name="account_units_id">
                                                    <option value="">Select</option>
                                                    @foreach ($units as $unit)
                                                        <option @if ($unit->id == $pvms->account_units_id) selected @endif
                                                            value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Item Group</label>
                                                <select class="form-control" name="item_groups_id">
                                                    <option value="">Select</option>
                                                    @foreach ($itemGroup as $group)
                                                        <option @if ($group->id == $pvms->item_groups_id) selected @endif
                                                            value="{{ $group->id }}">{{ $group->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Item Type <span class="requiredStar">*</span></label>
                                                <select class="form-control" required name="item_types_id">
                                                    <option value="">Select</option>
                                                    @foreach ($itemType as $itype)
                                                        <option @if ($itype->id == $pvms->item_types_id) selected @endif
                                                            value="{{ $itype->id }}">{{ $itype->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Item Department</label>
                                                <select class="form-control" name="item_departments_id">
                                                    <option value="">Select</option>
                                                    @foreach ($itemDepartment as $dept)
                                                        <option @if ($dept->id == $pvms->item_departments_id) selected @endif
                                                            value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Remarks </label>
                                                <input type="text" placeholder="Remark" class="form-control"
                                                    name="remarks" value="{{ $pvms->remarks }}">
                                            </div>

                                           

                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1 text-center">
                                    <button type="submit" class="btn btn-primary mt-1">Update</button>
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
