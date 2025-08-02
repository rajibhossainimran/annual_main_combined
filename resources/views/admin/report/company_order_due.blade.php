@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Company Order & Due</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                            <div id="react-company-order-due"></div>
                            @viteReactRefresh
                            @vite('resources/js/app.jsx')
                            <div class="row">
                                <div class='col-12'>
                                    <table class="table table-bordered w-100" id="companyOrderDueList">
                                        <thead>
                                            <tr class='text-left'>
                                                <th>Sl.</th>
                                                <th>PVMS No</th>
                                                <th>Nomenclature</th>
                                                <th>A/U</th>
                                                <th>Order Qty</th>
                                                <th>Receieved Qty</th>
                                                <th>DUE</th>
                                                <th>Rate</th>
                                                <th>Contract Date</th>
                                                <th>Contract No</th>
                                                <th>Supplier</th>
                                                <th>Last Sub Date</th>
                                                <th>Last Received Date</th>
                                                <th>Compare Day</th>
                                                <th>Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
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
