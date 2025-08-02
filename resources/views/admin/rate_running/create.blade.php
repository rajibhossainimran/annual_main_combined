@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Rate Running PVMS</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create Rate Running PVMS</h5>
                            @can('Show Rate Running PVMS')
                            <a class="nav-link" href="{{route('all.rate.running')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Rate Running PVMS
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form id="pvmsForm" >
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Supplier Name <span class="requiredStar">*</span></label>
                                        <select id="supplier" class="mb-2 form-control" name="supplier" value="">
                                            <option value="" > Select</option>
                                            @foreach($supplier as $supp)
                                                <option value="{{$supp->id}}" >{{$supp->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Start Date <span class="requiredStar">*</span></label>
                                        <input type="date" required class="form-control strd" name="start_date">
                                    </div>
                                    <div class="form-group">
                                        <label>End Date <span class="requiredStar">*</span></label>
                                        <input type="date" required class="form-control endd" name="end_date">
                                    </div>
                                    <div class="form-group">
                                        <label>Tender Ser No <span class="requiredStar">*</span></label>
                                        <input type="text" required class="form-control tender" name="tender_ser_no">
                                    </div>
                                    <div id="selectedItem"></div>
                                    <table class="table-width-100 table table-hover table-striped table-bordered">
                                        <tbody id="pvms-tbody">
                                            <input type="hidden" name="pvms_id[]">
                                        </tbody>
                                    </table>
                                    <div class="form-group">
                                        <label>Select PVMS <span class="requiredStar">*</span></label><br>
                                        <div class="dropdown2">
                                            <input type="text" id="inputField" class="form-control showDropdownPVMS" placeholder="Search PVMS">
                                            <input type="hidden" name="pvms_id" class="showDropdownHide">
                                            <div id="dropdownMenu" class="dropdown-content2"></div>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <button id="submit-btn" class="btn btn-primary mt-1">Add PVMS</button>
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
