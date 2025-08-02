@extends('admin.master')
@push('css')
{{-- for filter demand by searching style --}}
<style>
    .autocomplete-items {
        position: absolute;
        background: white;
        border: 1px solid #ccc;
        border-top: none;
        z-index: 99;
        max-height: 200px;
        overflow-y: auto;
        width: 100%;
    }

    .autocomplete-item {
        padding: 10px;
        cursor: pointer;
    }

    .autocomplete-item:hover {
        background-color: #f0f0f0;
    }
</style>
@endpush

@push('js')

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Demand</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">
                                Demand
                            </h5>
                            {{ $demands->links('vendor.pagination.custom') }}

                            <div class="text-right">
                                @if ($user_approval_role && $user_approval_role->role_key == 'cmh_clark')
                                    <a class="nav-link" href="{{ route('demand.create') }}">
                                        <button class="btn-icon btnc btn-custom">
                                            <i class="fa fa-plus btn-icon-wrapper"></i>
                                            Create Demand
                                        </button>
                                    </a>
                                @endif
                            </div>

                        </div>
                        <form action="" method="get" id="demand-table">
                            <div class="d-flex justify-content-between my-2 mx-2">

                                {{-- Per Page --}}
                                <div class="d-flex">
                                    Per Page
                                    <select class="form-control perpage" name="perpage">
                                        <option value="10">10</option>
                                        <option value="25" @if (request()->get('perpage') == 25) selected @endif>25</option>
                                        <option value="50" @if (request()->get('perpage') == 50) selected @endif>50</option>
                                        <option value="100" @if (request()->get('perpage') == 100) selected @endif>100</option>
                                    </select>
                                </div>

                                {{-- Unit Name --}}
                                {{-- <div class="d-flex">
                                    Unit Name
                                    <select class="form-control" name="sub_org_id" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        @foreach ($subOrg as $unit)
                                            <option value="{{ $unit->id }}"
                                                @if (request()->get('sub_org_id') == $unit->id) selected @endif>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> --}}

                               @php
                                    $unitList = collect($subOrg)->map(function ($u) {
                                        return ['id' => $u->id, 'name' => $u->name];
                                    });
                                @endphp
                                <div class="d-flex ">
                                    <label for="unit_search">Unit <br> Name</label>
                                    <div class="mb-3 position-relative ml-2" style="max-width: 400px;">
                                    
                                    <input type="text" id="unit_search" name="unit_name" class="form-control" autocomplete="off"  placeholder="Type to search unit...">
                                    <input type="hidden" id="sub_org_id" name="sub_org_id">
                                    <div id="autocomplete-list" class="autocomplete-items"></div>
                                </div>
                                </div>
                                

                              



                                {{-- Item Type --}}
                                <div class="d-flex">
                                    Item Type
                                    <select class="form-control type" name="type" >
                                        <option value="">All</option>
                                        @foreach ($item_types as $type)
                                            <option value="{{ $type->id }}"
                                                @if (request()->get('type') == $type->id) selected @endif>{{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </form>

                       {{-- Notification for demand correction --}}
                    @if ($user_approval_role && $user_approval_role->role_key == 'cmh_clark')
                        <button id="returnDemandBtn" class="btn btn-success position-relative my-1">
                            Return Demand
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $demandStatus->count() ?? 0 }}
                            </span>
                        </button>
                    @endif
                        {{-- Notification part end   --}}



                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Demand No</th>
                                    <th>Demand Type</th>
                                    <th>Item Type</th>
                                    @if ($user_approval_role && ($user_approval_role->role_key == 'co' || $user_approval_role->role_key == 'mo'))
                                        <th>CO Approval</th>
                                    @endif
                                    <th>Last Approval</th>
                                    <th>Created By</th>
                                    <th>Tasks Status</th>
                                    <th>Received Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if ($is_filter_applied && request()->get('page') < 2)
                                    @foreach ($pending_demands_for_user as $demand)
                                        <tr style="background: aliceblue">
                                            <td>{{ $demand->uuid }}
                                                @if (
                                                    $demand->status != 'Approve' &&
                                                        $user_approval_role &&
                                                        $user_approval_role->role_key ==
                                                            App\Utill\Approval\DemandApprovalSetps::nextStepDynamic($demand->id)['designation']
                                                )
                                                    <span class="badge bg-success text-white">New</span>
                                                @endif
                                                @if (
                                                    $demand->status != 'Approve' &&
                                                        $user_approval_role &&
                                                        $user_approval_role->role_key == 'mo' &&
                                                        $demand->is_published == 0)
                                                    <span class="badge bg-success text-white">New</span>
                                                @endif
                                            </td>
                                            <td>{{ $demand->demandType->name }}</td>
                                            <td>{{ $demand->demandItemType->name }} @if ($demand->is_dental_type)
                                                    (Dental)
                                                @endif
                                            </td>
                                            @if ($user_approval_role && ($user_approval_role->role_key == 'co' || $user_approval_role->role_key == 'mo'))
                                                <td>{{ count($demand->approval) > 0 ? 'Approved' : 'Pending' }}</td>
                                            @endif
                                            <td class="text-capitalize">
                                                @if (
                                                    $demand->last_approval &&
                                                        $demand->last_approval->user_approval_key &&
                                                        $demand->last_approval->user_approval_key->role_name)
                                                    {{ $demand->last_approval->user_approval_key->role_name }}
                                                @else
                                                    {{ $demand->last_approved_role }}
                                                @endif
                                            </td>
                                            <td>{{ $demand->createdBy->name }}</td>
                                            <td>
                                                @if ($demand->is_published == 1)
                                                    {{ $demand->status }}
                                                @else
                                                    Save as Draft
                                                @endif
                                            </td>

                                            <td>
                                                {{ $demand->approval_date && $demand->approval_date->created_at ? $demand->approval_date->created_at->format('Y-m-d') : '' }}
                                            </td>
                                            <td class="d-flex justify-content-end">

                                                <div class="text-center mr-3 mx-2">

                                                    @if ($demand->is_published == 0)
                                                        <a href="{{ route('demand.edit', $demand->id) }}">
                                                            <i class="fa fa-edit"></i><br />
                                                            Edit
                                                        </a>
                                                    @endif

                                                </div>
                                                @if (
                                                    $demand->status != 'Approve' &&
                                                        $user_approval_role &&
                                                        $user_approval_role->role_key ==
                                                            App\Utill\Approval\DemandApprovalSetps::nextStepDynamic($demand->id)['designation']
                                                )
                                                    <div class="text-center mx-2">
                                                        <a href="#" data-toggle="modal"
                                                            data-target=".bd-example-modal-lg" class="approval"
                                                            data-demand-id="{{ $demand->id }}" data-action="approve">
                                                            <svg xmlns="http://www.w3.org/2000/svg" height="1em"
                                                                viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                                <style>
                                                                    svg {
                                                                        fill: #089c14
                                                                    }
                                                                </style>
                                                                <path
                                                                    d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                                            </svg>

                                                            <br />{{ App\Utill\Approval\DemandApprovalSetps::nextStepDynamic($demand->id)['designation'] == 'head_clark' ? 'Forward' : 'Approve' }}
                                                        </a>
                                                    </div>
                                                @endif
                                                <div class="text-center mx-2">
                                                    <a href="{{ url('demand/download/pdf/' . $demand->id) }}"
                                                        title="Download Notice">
                                                        <i class="fa fa-file-pdf"></i>
                                                        <br /> Download
                                                    </a>
                                                </div>
                                                @if ($demand->demandItemType->name == 'On-Patient')
                                                    <div class="text-center mx-2">
                                                        <a href="#" data-id="{{ $demand->id }}"
                                                            title="Download Patient File" id="patientfile">
                                                            <i class="fas fa-download"></i>
                                                            <br /> File
                                                        </a>
                                                    </div>
                                                @endif
                                                <div class="text-center mx-2">
                                                    <a href="#" data-toggle="modal" data-target=".bd-example-modal-lg"
                                                        class="approval" data-demand-id="{{ $demand->id }}"
                                                        data-action="view">
                                                        <i class="fa fa-eye"></i>
                                                        <br /> View
                                                    </a>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                @foreach ($demands as $demand)
                                    @if (in_array($demand->id, $pending_demand_ids))
                                        @continue
                                    @endif
                                    @php
                                        $next_steps = App\Utill\Approval\DemandApprovalSetps::nextStepDynamic(
                                            $demand->id,
                                        );
                                    @endphp
                                    <tr @if ($demand->status != 'Approve' && $user_approval_role && $user_approval_role->role_key == $next_steps['designation']) style="background: aliceblue" @endif>
                                        <td>{{ $demand->uuid }}
                                            @if (
                                                    $demand->status != 'Approve' &&
                                                    $demand->status != 'Return' &&
                                                    $demand->status != 'Return&Seen' &&
                                                    $user_approval_role &&
                                                    $user_approval_role->role_key == $next_steps['designation']
                                                )
                                                <span class="badge bg-success text-white">New</span>
                                            @elseif(
                                                $demand->status != 'Approve' &&
                                                    $user_approval_role &&
                                                    $user_approval_role->role_key == 'mo' &&
                                                    $demand->is_published == 0)
                                                <span class="badge bg-success text-white">New</span>
                                            @endif
                                        </td>
                                        {{-- <td>
                                            {{ $demand->uuid }}
                                            
                                            @if ($demand->status == 'Reapproval')
                                                <span class="badge bg-warning text-dark">Reapproval</span>

                                            @elseif ($demand->status != 'Approve' && $user_approval_role && $user_approval_role->role_key == $next_steps['designation'])
                                                <span class="badge bg-success text-white">New</span>

                                            @elseif (
                                                $demand->status != 'Approve' &&
                                                $user_approval_role &&
                                                $user_approval_role->role_key == 'mo' &&
                                                $demand->is_published == 0
                                            )
                                                <span class="badge bg-success text-white">New</span>
                                            @endif
                                        </td> --}}

                                        <td>{{ $demand->demandType->name }}</td>
                                        <td>{{ $demand->demandItemType->name ?? '' }} @if ($demand->is_dental_type)
                                                (Dental)
                                            @endif
                                        </td>
                                        @if ($user_approval_role && ($user_approval_role->role_key == 'co' || $user_approval_role->role_key == 'mo'))
                                            <td>{{ count($demand->approval) > 0 ? 'Approved' : 'Pending' }}</td>
                                        @endif
                                        <td class="text-capitalize">
                                            @if (
                                                $demand->last_approval &&
                                                    $demand->last_approval->user_approval_key &&
                                                    $demand->last_approval->user_approval_key->role_name &&
                                                    $demand->last_approval->user_approval_key->role_key != 'mo')
                                                {{ $demand->last_approval->user_approval_key->role_name }}
                                            @else
                                                {{ $demand->last_approved_role }}
                                            @endif
                                        </td>
                                        <td>{{ $demand->createdBy->name }}</td>
                                        <td>
                                            @if ($demand->is_published == 1)
                                                <span class="{{ $demand->status == 'Reapproval' ? 'bg-warning text-dark px-2 py-1 rounded' : '' }}">
                                                    {{ $demand->status }}
                                                </span>
                                            @else
                                                Save as Draft
                                            @endif
                                        </td>
                                        <td>
                                            {{ $demand->approval_date && $demand->approval_date->created_at ? $demand->approval_date->created_at->format('Y-m-d') : '' }}
                                        </td>
                                        <td class="d-flex justify-content-end">

                                            <div class="text-center mr-3 mx-2">

                                                @if ($demand->is_published == 0)
                                                    <a href="{{ route('demand.edit', $demand->id) }}">
                                                        <i class="fa fa-edit"></i><br />
                                                        Edit
                                                    </a>
                                                @endif

                                            </div>
                                            @if (
                                                $demand->status != 'Approve' &&
                                                    $demand->status != 'Return' &&
                                                    $demand->status != 'Return&Seen' &&
                                                    $next_steps['designation'] != 'mo' &&
                                                    $user_approval_role &&
                                                    $user_approval_role->role_key == $next_steps['designation']
                                            )
                                                <div class="text-center mx-2">
                                                    <a href="#" data-toggle="modal" data-target=".bd-example-modal-lg"
                                                        class="approval" data-demand-id="{{ $demand->id }}"
                                                        data-action="approve">
                                                        <svg xmlns="http://www.w3.org/2000/svg" height="1em"
                                                            viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                                            <style>
                                                                svg {
                                                                    fill: #089c14
                                                                }
                                                            </style>
                                                            <path
                                                                d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                                        </svg>

                                                        <br />{{ $next_steps['designation'] == 'head_clark' ? 'Forward' : 'Approve' }}
                                                    </a>
                                                </div>
                                            @endif
                                            <div class="text-center mx-2">
                                                <a href="{{ url('demand/download/pdf/' . $demand->id) }}"
                                                    title="Download Notice">
                                                    <i class="fa fa-file-pdf"></i>
                                                    <br /> Download
                                                </a>
                                            </div>
                                            <div class="text-center mx-2">
                                                <a href="#" data-toggle="modal" data-target=".bd-example-modal-lg"
                                                    class="approval" data-demand-id="{{ $demand->id }}"
                                                    data-action="view">
                                                    <i class="fa fa-eye"></i>
                                                    <br /> View
                                                </a>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $demands->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- demand filter by searching  --}}
<script>
    @if(Auth::check())
        const loggedInUser = @json(Auth::user());
        // console.log('Logged-in user:', loggedInUser);
    @else
        // console.log('No user is logged in.');
    @endif
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const unitSearchInput = document.getElementById("unit_search");
        const unitIdInput = document.getElementById("sub_org_id");
        const suggestionBox = document.getElementById("autocomplete-list");

        const units = @json($unitList);

        unitSearchInput.addEventListener("input", function () {
            const keyword = this.value.toLowerCase();
            suggestionBox.innerHTML = "";

            if (!keyword) return;

            const matchedUnits = units.filter(unit => unit.name.toLowerCase().includes(keyword));

            matchedUnits.forEach(unit => {
                const div = document.createElement("div");
                div.classList.add("autocomplete-item");
                div.textContent = unit.name;
                div.addEventListener("click", function () {
                    unitSearchInput.value = unit.name;
                    unitIdInput.value = unit.id;
                    suggestionBox.innerHTML = "";

                    // ✅ Submit form
                    unitSearchInput.form.submit();
                });
                suggestionBox.appendChild(div);
            });
        });

        document.addEventListener("click", function (e) {
            if (!e.target.closest("#unit_search")) {
                suggestionBox.innerHTML = "";
            }
        });
    });
</script>


        {{-- return demand notification  --}}
<script>
    const returnDemands = @json($returnDemand);
    console.log('Returned Demands:', returnDemands); 
</script>
<script>
document.getElementById('returnDemandBtn').addEventListener('click', function () {
    // Build table rows dynamically using JS from the passed data
    let tableRows = '';
    returnDemands.forEach((demand, index) => {
        tableRows += `
            <tr>
                <td>${index + 1}</td>
                <td>
                    ${demand.uuid}
                    ${demand.status === 'Return' ? '<span class="badge bg-warning text-dark ms-1">New Return</span>' : ''}
                </td>
                <td>${demand.demand_type?.name || ''}</td>
                <td>${demand.demand_item_type?.name || ''}</td>
                <td>${new Date(demand.updated_at).toISOString().split('T')[0]}</td>
                <td>
                    <button type="button" class="btn btn-info btn-sm view-btn" data-id="${demand.id}">View</button>
                </td>
            </tr>
        `;
    });

    const tableHtml = `
        <div style="max-height: 400px; overflow-y: auto; text-align: left;">
            <table class="table table-bordered table-striped" style="width: 100%; font-size: 14px;">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Demand No</th>
                        <th>Demand Type</th>
                        <th>Item Type</th>
                        <th>Date</th>
                        <th>Show Details</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows || `<tr><td colspan="5" class="text-center text-muted">No returned demands found.</td></tr>`}
                </tbody>
            </table>
        </div>
    `;

    Swal.fire({
        title: 'Returned Demands',
        html: tableHtml,
        width: '80%',
        confirmButtonText: 'Close',
        didOpen: () => {
            // Attach click events to view buttons inside the modal
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const id = parseInt(this.getAttribute('data-id'));
                    const selected = returnDemands.find(d => d.id === id);
                    const approvalHistory = selected.approval || [];
                    const lastApproval = approvalHistory.length > 0 ? approvalHistory[approvalHistory.length - 1] : null;


                  Swal.fire({
                        html: `
                            <div style="text-align: left;">
                                <p class="bg-success py-2 text-white pl-2"><strong>Demand No:</strong> ${selected.uuid}</p>
                                <p><strong>Demand Type:</strong> ${selected.demand_type?.name || ''}</p>
                                <p><strong>Item Type:</strong> ${selected.demand_item_type?.name || ''}</p>

                                <hr>
                                <h6><strong>PVMS Items:</strong></h6>
                                <div style="margin-top: 1rem; padding: 1rem; background-color: #fef9e7; border-left: 4px solid #f1c40f; border-radius: 4px;">
                                    <p style="margin: 0 0 0.5rem;"><strong style="color: #b9770e;">Return Note:</strong></p>
                                    <p style="margin: 0 0 1rem; font-style: italic;">${lastApproval?.note || 'No note provided.'}</p>
                                    <p style="margin: 0;font-size:14px;"><strong>Date:</strong> ${lastApproval?.updated_at || ''}</p>
                                </div>

                                <table class="table table-bordered table-striped" style="width: 100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>PVMS No</th>
                                            <th>Nomenclature</th>
                                            <th>Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${selected.demand_p_v_m_s && selected.demand_p_v_m_s.length > 0 ? 
                                            selected.demand_p_v_m_s.map((item, index) => `
                                                <tr>
                                                    <td class="py-1 px-2">${index + 1}</td>
                                                    <td class="py-1 px-2"><strong>${item?.p_v_m_s?.pvms_name || ''}</strong></td>
                                                    <td class="py-1 px-2">${item?.p_v_m_s?.nomenclature || ''}</td>
                                                    <td class="py-1 px-2">${item.qty || 0}</td>
                                                </tr>
                                            `).join('')
                                            : `
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No PVMS items found.</td>
                                            </tr>`
                                        }
                                    </tbody>
                                </table>
                            </div>
                        `,
                        width: '70%',
                        confirmButtonText: 'Seen & Close',
                        showCancelButton: false,
                        preConfirm: () => {
                            return fetch('{{ route("demand.markAsSeen") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ id: selected.id })
                            })
                            .then(response => {
                                if (!response.ok) throw new Error('Failed to mark as seen');
                                return response.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    text: data.message,
                                    timer: 1200,
                                    showConfirmButton: false
                                }).then(() => {
                
                                    location.reload();
                                });
                            })
                            .catch(error => {
                                Swal.showValidationMessage(error.message);
                            });
                        }

                    });


                });
            });
        }
    });
});
</script>

@endsection
@push('js')
    <div class="modal bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Demand Approval - <span
                            id="demand-type-show"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div id="react-demand-approval"></div>
                </div>
            </div>
        </div>
    </div>

    @viteReactRefresh
    @vite('resources/js/app.jsx')
@endpush

