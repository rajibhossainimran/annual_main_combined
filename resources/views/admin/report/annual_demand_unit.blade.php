@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Report</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Consilidated Annual Demand </h5>

                        </div>

                        <div class="pb-2">
                            <form>
                                <div class="row margin-right-0" id="consilidated-annual-demand">
                                    <div class="col-md-6">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Financial Year <span class="requiredStar">*</span></label>
                                                <select class="mb-2 form-control" name="year_id" id="year_id" required>
                                                        <option value=""> Select</option>
                                                        @foreach ($years as $year)
                                                        <option value="{{$year->id}}"> {{$year->name}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>PVMS No <span class="requiredStar">*</span></label>
                                                <input type="text" name="pvms_no" class="form-control" id="pvms_no">
                                            </div>
                                        </div>
                                        <div class="mt-1">
                                            <button type="submit" class="btn btn-primary mt-1 float-right">Search</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>ANX</label>
                                                <select class="mb-2 form-control" name="crv_no" >
                                                    <option value=""> Select</option>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Unit <span class="requiredStar">*</span></label>
                                                <select class="mb-2 form-control" name="unit_id" id="unit_id" required>
                                                    <option value=""> Select</option>
                                                    @foreach ($sub_orgs as $sub_org)
                                                    <option value="{{$sub_org->id}}">{{$sub_org->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                       
                        <br>
                        <h5 class="text-center"></h5>
                        <div class="col-lg-12">
                            <div class="row">

                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="consilidatedAnnualDemand" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr  class="text-center">
                                            <th>SL</th>
                                            <th>PVMS No</th>
                                            <th>Nomenclature</th>
                                            <th>Unit Name</th>
                                            <th>Reqr Quantity</th>
                                            <th>Issue Quantity</th>
                                            <th>Due Quantity</th>
                                        </tr>
                                        </thead>
                                        <tbody  class="text-center">
                                            
                                            <tfoot>
                                                <td colspan="4" class="total-search">Total</td>
                                                <td class="wed-color fbold text-right" >
                                                 
                                                </td>
                                                <td class="total-search"></td>
                                                <td class="wed-color fbold text-right" ></td>
                                            </tfoot>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
