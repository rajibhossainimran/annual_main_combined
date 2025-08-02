@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">On Loan PVMS</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="p-2">
                            <table class="table table-bordered table-striped fs--1 mb-0 w-100" id="stockOnLoanPvmsListTable">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>#</th>
                                        <th>Ref. No</th>
                                        <th>Vendor</th>
                                        <th>PVMS</th>
                                        <th>Nomenclature</th>
                                        <th>Account Unit</th>
                                        <th>Qty</th>
                                        <th>Qty Rec.</th>
                                        <th>Qty Adj.</th>
                                        <th>Due</th>
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
