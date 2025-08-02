@extends('admin.master')

@push('css')
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
@endpush

@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Unit Delivery Approval</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Unit Delivery Approval</h5>
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="example"
                                        class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Ref. No</th>
                                                <th>Issue Date</th>
                                                <th>Status</th>
                                                <th>Unit Name</th>
                                                @if ($group_incharge == 1)
                                                    <th>PVMS Name</th>
                                                    <th>Nomenclature</th>
                                                    <th>Issue QTY</th>
                                                    <th>Delivered QTY</th>
                                                    <th>Group ID</th>
                                                @endif
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchases as $k => $record)
                                                <tr>
                                                    <th scope="row">{{ $k + 1 }}</th>
                                                    <td>{{ $group_incharge != 1 ? $record->purchase_number : $record['purchase']->purchase_number }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($group_incharge != 1 ? $record->created_at : $record['purchase']->created_at)->format('d-m-Y') }}
                                                    </td>
                                                    <td>
                                                        @switch($group_incharge != 1 ? $record->stage :
                                                            $record['purchase']->stage)
                                                            @case(0)
                                                                Forwarded by AFMSD Clerk
                                                            @break

                                                            @case(2)
                                                                Approved by Stock Control Officer
                                                            @break

                                                            @case(3)
                                                                Approved by AFMSD CO
                                                            @break

                                                            @default
                                                                Unknown Status
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        {{ $group_incharge != 1 ? ($record->subOrganization ? $record->subOrganization->name : 'N/A') : ($record['purchase']->subOrganization ? $record['purchase']->subOrganization->name : 'N/A') }}
                                                    </td>
                                                    @if ($group_incharge == 1)
                                                        <th>{{ $record['pvms']->pvms_name ?? '' }}</th>
                                                        <th>{{ $record['pvms']->nomenclature ?? '' }}</th>
                                                        <th>{{ $record['purchase_type']->request_qty ?? '' }}</th>
                                                        <th>{{ $record['delivery']->delivered_qty ?? '' }}</th>
                                                        <th>{{ $record['pvms']->item_groups_id ?? '' }}</th>
                                                    @endif
                                                    <td>
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            {{-- <div class="text-center mx-2">
                                                                <a href="#" data-toggle="modal"
                                                                    data-target=".bd-example-modal-lg"
                                                                    class="approval d-block"
                                                                    data-demand-id="{{ $group_incharge != 1 ? $record->id : $record['purchase']->id }}"
                                                                    data-action="view">
                                                                    <i class="fa fa-eye"></i><br />View
                                                                </a>
                                                            </div> --}}
                                                            @if ($group_incharge != 1)
                                                                <div class="text-center mx-2">
                                                                    <a href="#" class="approve d-block"
                                                                        data-purchase-id="{{ $record->id }}"
                                                                        data-action="approve">
                                                                        <i
                                                                            class="fa fa-check-square text-info"></i><br />Approve
                                                                    </a>
                                                                </div>
                                                            @else
                                                                <div class="text-center mx-2">
                                                                    @if ($record['delivery']->purchase_type_delivered == 0)
                                                                        <a href="#" class="approval d-block"
                                                                            data-group-incharge="{{ $record['purchase_type']->id }}"
                                                                            data-action="approval">
                                                                            <i
                                                                                class="fa fa-check-square text-info"></i><br />Approve
                                                                        </a>
                                                                    @else
                                                                        <span class="text-success fa fa-check">
                                                                            Approved</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade mt-5" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel"
        aria-hidden="true" aria-modal="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-grow-early text-white">
                    <h5 class="modal-title" id="approveModalLabel">Approve Delivery</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Data will be loaded dynamically via JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success btn-lg" id="modalApproveBtn"
                        data-group-incharge="">Approve</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Include Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('click', '.approve[data-action="approve"]', function(e) {
            e.preventDefault();

            var purchaseId = $(this).data('purchase-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to approve this unit delivery!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    approveDelivery(purchaseId);
                }
            });
        });

        function approveDelivery(purchaseId) {
            $.ajax({
                url: '/unit-delivery/approve/' + purchaseId,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: purchaseId
                },
                success: function(response) {
                    Swal.fire(
                        'Approved!',
                        'The unit delivery has been approved.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        'There was an error approving.',
                        'error'
                    );
                }
            });
        }
    </script>

    <script>
        $(document).on('click', '.approval[data-action="approval"]', function(e) {
            e.preventDefault();

            var groupIncharge = $(this).data('group-incharge');

            $.ajax({
                url: '/purchase-type/details/' + groupIncharge,
                type: 'GET',
                success: function(response) {
                    // Generate table rows for deliveries dynamically
                    var deliveriesHtml = '';
                    response.deliveries.forEach(function(delivery) {
                        deliveriesHtml += `
                    <tr>
                        <td>${delivery.delivery_number}</td>
                        <td>${delivery.delivered_qty}</td>
                        <td>${delivery.delivery_date}</td>
                    </tr>
                `;
                    });

                    // Set the modal content dynamically
                    $('.modal-body').html(`
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><strong>Purchase Type ID</strong></th>
                            <th><strong>Purchase Number</strong></th>
                            <th><strong>Unit Name</strong></th>
                            <th><strong>Status</strong></th>
                            <th><strong>Issue Quantity</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>${response.purchase_type_id}</td>
                            <td>${response.purchase_number}</td>
                            <td>${response.unit_name}</td>
                            <td>${response.status}</td>
                            <td>${response.issue_qty}</td>
                        </tr>
                    </tbody>
                </table>

                <h5>Deliveries</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><strong>Delivery Number</strong></th>
                            <th><strong>Delivered Quantity</strong></th>
                            <th><strong>Delivery Date</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        ${deliveriesHtml}
                    </tbody>
                </table>
            `);

                    $('#modalApproveBtn').attr('data-group-incharge',
                        groupIncharge);

                    $('#approveModal').modal('show');
                    $(".modal-backdrop").remove();
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to fetch record details.', 'error');
                }
            });
        });

        $('#modalApproveBtn').on('click', function() {
            var groupIncharge = $(this).attr('data-group-incharge');

            if (!groupIncharge) {
                Swal.fire('Error!', 'No group in charge data found.', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to approve this unit delivery!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/purchase-type/approve/' + groupIncharge,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: groupIncharge,
                        },
                        success: function(response) {
                            $('#approveModal').modal('hide');
                            Swal.fire(
                                'Approved!',
                                'The unit delivery has been approved.',
                                'success'
                            ).then(() => location.reload());
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'There was an error approving.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endpush
