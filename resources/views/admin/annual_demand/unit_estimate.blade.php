@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">

            <div class="col-lg-12 app-content">
                <div class="app-content-top-title annual_demand_bg">Annual Demand</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="">
                            <div id="react-annual-demand-estimation"></div>
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
