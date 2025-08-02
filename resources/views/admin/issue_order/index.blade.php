@extends('admin.master')

@push('css')
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Issue Orders</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Issue Orders</h5>

                            @can('Issue Order Create')
                                <a class="nav-link" href="{{ route('report.purchase') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create Issue Order
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="example"
                                        class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Ref. No</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Total Item</th>
                                                <th>Unit Name</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($records as $k => $record)
                                                <tr>
                                                    <th scope="row">{{ $k + 1 }}</th>
                                                    <td>{{ \Carbon\Carbon::parse($record->created_at)->format('d-m-Y') }}
                                                    </td>
                                                    <td>{{ $record->purchase_number }}</td>
                                                    <td>{{ $record->purchase_item_type }}</td>
                                                    <td>{{ $record->status }}</td>
                                                    <td>{{ $record->total_item }}</td>
                                                    <td>{{ $record->subOrganization ? $record->subOrganization->name : 'N/A' }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            <div class="text-center mx-2">
                                                                <a href="#" 
                                                                    class="approvalview d-block"
                                                                    data-demand-id="{{ $record->id }}" 
                                                                    data-action="view">
                                                                    <i class="fa fa-eye"></i><br />View
                                                                </a>
                                                            </div>
                                                            <div class="text-center mx-2">
                                                                <a target="_blank" href="{{ url('/issue-order/download/pdf/' . $record->id) }}"
                                                                    class="d-block" title="Download">
                                                                    <i class="fa fa-file-pdf"></i><br />Download
                                                                </a>
                                                            </div>
                                                            
                                                            
                                                            @if ($show == true)
                                                                <div class="text-center mx-2">
                                                                    @if ($record->status == 'pending')
                                                                        <a href="#" class="approval d-block"
                                                                            data-workorder-id="{{ $record->id }}"
                                                                            data-action="approve">
                                                                            <i
                                                                                class="fa fa-check-square text-info"></i><br />Approve
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted">
                                                                            <i class="fa fa-check-square"></i><br />Approved
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            {{-- @if ($showForwardButton == true)
                                                                <div class="text-center mx-2">
                                                                    @if ($record->status == 'approved')
                                                                        <a href="#" class="forward d-block"
                                                                            data-workorder-id="{{ $record->id }}"
                                                                            data-action="forward">
                                                                            <i
                                                                                class="fa fa-forward text-info"></i><br />Forward
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted">
                                                                            <i class="fa fa-forward"></i><br />Forward
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endif --}}
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
@endsection

@push('js')
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const records = @json($records);
    console.log(records);
</script>

    <script>
        // Handle Approve button click
        $(document).on('click', '.approval[data-action="approve"]', function(e) {
            e.preventDefault();

            var workorderId = $(this).data('workorder-id');

            $.ajax({
                url: '/issue-order/details/' + workorderId,
                type: 'GET',
                success: function(response) {
                    console.log(response.data);

                    let tableRows = '';
                    $.each(response.data.purchase_types, function(index, item) {
                        tableRows += `
                            <tr>
                                <td style="padding:8px;">${index + 1}</td>
                                <td style="padding:8px;text-align:left;">${item.pvms?.pvms_id ?? 'N/A'}</td>
                                <td style="padding:8px;text-align:left;">${item.pvms?.nomenclature ?? 'N/A'}</td>
                                <td style="text-align:left;padding:8px;">
                                    <input type="number" class="form-control qty-input" name="quantities[]" data-id="${item.id}" value="${item.request_qty}"  />
                                </td>

                            </tr>
                        `;
                    });

                    Swal.fire({
                        
                        html: `
                            <div style="overflow-x:auto">
                                <table class="table-width-100 table table-hover table-striped table-bordered" style="width:100%; font-size:14px">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th style="text-align:left;padding-left:5px;">PVMS</th>
                                            <th style="text-align:left;padding-left:5px;">Nomenclature</th>
                                            <th style="text-align:left;padding-left:5px;" >Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${tableRows}
                                    </tbody>
                                </table>
                            </div>
                            
                        `,
                        width: '80%',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Approve!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const updatedQuantities = [];

                            $('.qty-input').each(function () {
                                updatedQuantities.push({
                                    id: $(this).data('id'),
                                    qty: $(this).val()
                                });
                            });

                            approveWorkorder(workorderId, updatedQuantities);
                        }

                    });
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to load order details.', 'error');
                }
            });
        });


        // Function to send the AJAX request for approval
        function approveWorkorder(workorderId, quantities) {
        $.ajax({
            url: '/issue-order/approve/' + workorderId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: workorderId,
                quantities: quantities
            },
            success: function(response) {
                Swal.fire(
                    'Approved!',
                    'The order has been approved with updated quantities.',
                    'success'
                ).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire(
                    'Error!',
                    xhr.responseJSON?.error || 'There was an error approving the order.',
                    'error'
                );
            }
        });
    }


         // issur order view data dispaly 
          $(document).on('click', '.approvalview[data-action="view"]', function(e) {
            e.preventDefault();

            var workorderId = $(this).data('demand-id');

            $.ajax({
                url: '/issue-order/details/' + workorderId,
                type: 'GET',
                success: function(response) {
                    console.log(response.data);
                    const subOrgName = response.data.sub_organization?.name ?? '';
                    const purchaseNo = response.data.purchase_number ?? '';
                    const status = response.data.status ?? '';


                    let tableRows = '';
                    $.each(response.data.purchase_types, function(index, item) {
                        tableRows += `
                            <tr>
                                <td style="padding:8px;">${index + 1}</td>
                                <td style="padding:8px;text-align:left;padding-left:5px;">${item.pvms?.pvms_id ?? 'N/A'}</td>
                                <td style="padding:8px;text-align:left;padding-left:5px;">${item.pvms?.nomenclature ?? 'N/A'}</td>
                                <td style="text-align:left; padding:8px;padding-left:5px;">${item.request_qty}</td>
                                
                            </tr>
                        `;
                    });

                    Swal.fire({
                        
                        html: `
                            <div style="overflow-x:auto">
                                <div style="background-color: #006b50; padding: 5px; display: flex; align-items: center;">
                                    <span style="color: white; font-weight: 600; margin-right: 10px;">Purchase No</span>
                                    <span style="background-color: white; padding: 2px 15px; border-radius: 20px; font-weight: 500; color: black;">
                                        ${purchaseNo}
                                    </span>
                                </div>

                                <h4 style="text-align:left; font-weight:600; color:green;padding:5px 0;">${subOrgName}</h4>
                                 <p style="text-align:left; margin: 10px 0px; border-radius: 20px; font-weight: 500; color: black;font-size:14px;">
                                     Status : ${status}   
                                </p>

                                <table class="table-width-100 table table-hover table-striped table-bordered" style="width:100%; font-size:14px">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th style="text-align:left;padding-left:5px;">PVMS</th>
                                            <th style="text-align:left;padding-left:5px;">Nomenclature</th>
                                            <th style="text-align:left;padding-left:5px;">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${tableRows}
                                    </tbody>
                                </table>
                            </div>
                            
                        `,
                        width: '80%',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'Close'
                    })
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to load order details.', 'error');
                }
            });
        });
    </script>
@endpush
