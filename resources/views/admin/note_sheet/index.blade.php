@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">

            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Notesheet</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        {{-- <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Notesheet Preparation</h5>
                            @can('Show Notesheet')
                            <a class="nav-link" href="{{route('demand.index')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Notesheet
                                </button>
                            </a>
                            @endcan
                        </div> --}}

                        <div class="">
                            <div id="react-notesheet-details"></div>
                            {{-- <form action="{{route('store.pvms')}}" method="post">
                                @csrf
                            </form> --}}

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
