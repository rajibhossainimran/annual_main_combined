@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">On Loan Receive</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div id="react-receive-on-loan"></div>
                        @viteReactRefresh
                        @vite('resources/js/app.jsx')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script src="/suggested_notesheet_no_prefix.js?on_loan_id={{$on_loan->id}}"></script>
@endpush
