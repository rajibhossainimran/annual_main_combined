@extends('admin.master')
@push('css')
@endpush
@push('js')
<script src="{{ asset('admin/scripts/main.js') }}"></script>
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">

            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">PVMS STOCK ADD A</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create PVMS STOCK ADD</h5>
                            @can('Show Demand')
                                <a class="nav-link" href="{{ route('issue-order-direct.index') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        PVMS STOCK ADD
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="p-2">
                            <div id="react-receivePvmsBatchStock"></div>
                            <form action="" method="post">
                                @csrf
                            </form>
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
    <script src="/suggested-demand-no-prefix.js"></script>
@endpush
