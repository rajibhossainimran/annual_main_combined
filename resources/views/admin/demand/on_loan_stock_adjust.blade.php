@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">On Loan Stock Adjust</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">
                                On Loan Stock Adjust
                            </h5>
                        </div>
                        
                        <div>
                            <div id="onloan-stock-adjust"></div>
                            @viteReactRefresh
                            @vite('resources/js/app.jsx')
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

