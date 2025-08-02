@extends('admin.master')

@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Issue Page</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div id="react-issue-page" data-id="{{ $id }}"></div>
                        @viteReactRefresh
                        @vite('resources/js/app.jsx') <!-- Or whatever your entry is -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush



