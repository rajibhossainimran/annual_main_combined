@if ($errors->any())
    <div class="position-absolute footer-right">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <span>
                    <p>{{ $error }}</p>
                </span>
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
@endif
</div>
<div class="mt-5">
    <div class="text-right pr-5">
        <p>Powered by <a href="https://tilbd.net/">Trust Innovation Limited</a> © @php echo date('Y') @endphp. All rights
            reserved.
        </p>
    </div>
</div>

@if (Session::has('message'))
    <div id="session-success" class="d-none">{{ session('message') }}</div>
@endif

@if (Session::has('error'))
    <div id="session-error" class="d-none">{{ session('error') }}</div>
@endif

@if (Session::has('info'))
    <div id="session-info" class="d-none">{{ session('info') }}</div>
@endif

@if (Session::has('warning'))
    <div id="session-warning" class="d-none">{{ session('warning') }}</div>
@endif
</div>
<script type="text/javascript" src="{{ asset('admin/scripts/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/scripts/toastr.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/scripts/datatable.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/scripts/main.js?v=0.1') }}"></script>
<script type="text/javascript" src="{{ asset('admin/scripts/moment.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/scripts/datepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('admin/scripts/daterange.js') }}"></script>

{{-- <script type="text/javascript" src="{{ asset('admin/scripts/select2.min.js') }}"></script> --}}
