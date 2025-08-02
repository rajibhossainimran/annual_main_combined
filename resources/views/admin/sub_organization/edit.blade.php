@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Organization</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit Organization</h5>
                            @can('Show Sub Organization')
                                <a class="nav-link" href="{{ route('all.sub.organization') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-eye btn-icon-wrapper"></i>
                                        All Organization
                                    </button>
                                </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{ url('/update/sub-organization/' . $sub_organization->id) }}" method="post">
                                @csrf
                                <div class="row p-3">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <input type="hidden" name="id" value="{{ $sub_organization->id }}" />
                                            <label>Name <span class="requiredStar">*</span></label>
                                            <input type="text" placeholder="Name" required class="form-control"
                                                name="name" value="{{ $sub_organization->name }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Type <span class="requiredStar">*</span></label>
                                            <select id="sub_org_type" class="mb-2 form-control" required name="type"
                                                value="{{ $sub_organization->type }}">
                                                <option value=""> Select</option>
                                                @foreach ($types as $k => $type)
                                                    <option value="{{ $type }}"
                                                        {{ $sub_organization->type == $type ? 'selected' : '' }}>
                                                        {{ $type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Code</label>
                                            <input type="number" min="0" placeholder="Code" class="form-control"
                                                name="code" value="{{ $sub_organization->code }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Serial</label>
                                            <input type="number" min="0" placeholder="Serial" class="form-control"
                                                name="serial" value="{{ $sub_organization->serial }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Governing Body <span class="requiredStar">*</span></label>
                                            <select class="mb-2 form-control" required name="org_id"
                                                value="{{ $sub_organization->org_id }}">
                                                <option value=""> Select</option>
                                                @foreach ($organizations as $k => $organization)
                                                    <option value="{{ $organization->id }}"
                                                        {{ $sub_organization->org_id == $organization->id ? 'selected' : '' }}>
                                                        {{ $organization->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Division</label>
                                            <select class="mb-2 form-control" name="division"
                                                value="{{ $sub_organization->division_id }}">
                                                <option value=""> Select</option>
                                                @foreach ($divisions as $k => $division)
                                                    <option value="{{ $division->id }}"
                                                        {{ $sub_organization->division_id == $division->id ? 'selected' : '' }}>
                                                        {{ $division->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Service</label>
                                            <select class="mb-2 form-control" name="service"
                                                value="{{ $sub_organization->service_id }}">
                                                <option value=""> Select</option>
                                                @foreach ($services as $k => $service)
                                                    <option value="{{ $service->id }}"
                                                        {{ $sub_organization->service_id == $service->id ? 'selected' : '' }}>
                                                        {{ $service->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Status <span class="requiredStar">*</span></label>
                                            <select class="mb-2 form-control" required name="status"
                                                value="{{ $sub_organization->status }}">
                                                <option value=""> Select</option>
                                                <option value="0" {{ $sub_organization->status == 0 ? 'selected' : '' }}>
                                                    Inactive</option>
                                                <option value="1" {{ $sub_organization->status == 1 ? 'selected' : '' }}>
                                                    Active</option>

                                            </select>
                                        </div>
                                        <div class="mt-1">
                                            <button type="submit" class="btn btn-primary mt-1">Update Organization</button>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h3>Approval Layers</h3>
                                        <h5>All Layer</h5>
                                        <ul class="approval-layer-selection-container p-0" data-type="all">
                                            @foreach ($approval_layer['all'] as $all_layer)
                                            <li data-role-key="{{$all_layer->designation}}">
                                                <div class="selection"></div>
                                                <button type="button" class="approval-layer-add btn btn-primary">+</button>
                                                <button type="button" class="approval-layer-remove btn btn-danger">-</button>
                                            </li>
                                            @endforeach
                                        </ul>
                                        
                                        <h5>Repair Layer</h5>
                                        <ul class="approval-layer-selection-container p-0" data-type="repair">
                                            @foreach ($approval_layer['repair'] as $all_layer)
                                            <li data-role-key="{{$all_layer->designation}}">
                                                <div class="selection"></div>
                                                <button type="button" class="approval-layer-add btn btn-primary">+</button>
                                                <button type="button" class="approval-layer-remove btn btn-danger">-</button>
                                            </li>
                                            @endforeach
                                        </ul>
                                        
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script src="/admin/scripts/approval-layer.js"></script>
@endpush
