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
                        <div class="d-flex justify-content-end">
                            <div class="p-2">
                                <a href="{{url('settings/add/pvms/niv')}}" target="_blank" class="btn-icon btn btn-secondary">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                    Add NIV
                                </a>
                            </div>

                        </div>
                        <div class="">
                            <div id="react-annual-demand"></div>
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
