@extends('admin.master')

@push('css')
@endpush

@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">On Loan</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">
                                On Loan
                            </h5>
                            <div class="text-right">
                                @if (auth()->user()->userApprovalRole() &&
                                        isset(auth()->user()->userApprovalRole->role_key) &&
                                        auth()->user()->userApprovalRole->role_key == 'head_clark')
                                    <a class="nav-link" href="{{ route('on_loan.create') }}">
                                        <button class="btn-icon btnc btn-custom">
                                            <i class="fa fa-plus btn-icon-wrapper"></i>
                                            Create On Loan
                                        </button>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3">
                            <table id="datatable" class="table">
                                <thead>
                                    <tr>
                                        <th>Reference No</th>
                                        <th>Date</th>
                                        <th>Vendor</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($on_loans as $on_loan)
                                        <tr>
                                            <td>{{ $on_loan->reference_no }}</td>
                                            <td>{{ $on_loan->reference_date }}</td>
                                            <td>{{ $on_loan->vendor->name }}</td>
                                            <td>
                                                @if (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'AFMSD')
                                                    <div class="text-center mr-3 mx-2">
                                                        <a href="{{ route('on_loan.receive', ['id' => $on_loan->id]) }}">
                                                            <i class="fa fa-cart-arrow-down"></i><br />
                                                            Receive Item
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                dom: '<"top d-flex justify-content-between"<"length-menu"l><"buttons"B>f>rt<"bottom"ip><"clear">',
                buttons: [{
                        extend: 'copy',
                        text: 'Copy',
                        className: 'btn btn-primary'
                    },
                    {
                        extend: 'csv',
                        text: 'Export CSV',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'excel',
                        text: 'Export Excel',
                        className: 'btn btn-info'
                    },
                    {
                        extend: 'pdf',
                        text: 'Export PDF',
                        className: 'btn btn-danger'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-warning'
                    }
                ],
                responsive: true,
                autoWidth: false,
                order: [
                    [1, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ]
            });
        });
    </script>
@endpush
