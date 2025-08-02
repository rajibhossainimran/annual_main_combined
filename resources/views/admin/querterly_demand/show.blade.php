@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title annual_demand_bg">Querterly Demand</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center annual_demand_bg py-1">
                            <h5 class="f-14 pl-2">
                                Querterly Demand Details
                            </h5>
                        </div>
                        <div class="">
                            <div id="react-querterly-demand-create"></div>
                            @viteReactRefresh
                            @vite('resources/js/app.jsx')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
