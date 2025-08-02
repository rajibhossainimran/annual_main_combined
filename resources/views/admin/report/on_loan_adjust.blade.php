@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">On Loan Adjustment</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="p-2">
                            <div class="row justify-content-end">
                                <div class="col-3">
                                    <input class="form-control my-2" type="text" name="datefilter" value="" id="reportdaterange" placeholder="Enter date range"/>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped fs--1 mb-0 w-100" id="stockOnLoanAdjustPvmsListTable">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>#</th>
                                        <th>Workorder No.</th>
                                        <th>PVMS</th>
                                        <th>Nomenclature</th>
                                        <th>Account Unit</th>
                                        <th>Adjust Qty</th>
                                        <th>Adjusted On</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
