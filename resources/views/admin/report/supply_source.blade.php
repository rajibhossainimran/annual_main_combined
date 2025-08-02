@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Supply Source</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="p-2">
                            <div class="d-flex justify-content-between my-2 mx-2">
                                <div class="d-flex align-content-center gap-2">
                                    <label class="align-self-center pr-2">Financial Year</label>
                                    <select class="form-control" id="fy_id">
                                        <option selected value="">Select Financial Year</option>
                                        @foreach ($financial_years as $financial_year)
                                            <option value="{{$financial_year->id}}">{{$financial_year->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped fs--1 mb-0 w-100" id="supplySourceList">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>#</th>
                                        <th>PVMS</th>
                                        <th>Nomenclature</th>
                                        <th>Account Unit</th>
                                        <th>Specification</th>
                                        <th>Company</th>
                                        <th>Qty Ordered</th>
                                        <th>Qty Supplied</th>
                                        <th>Qty Due</th>
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
