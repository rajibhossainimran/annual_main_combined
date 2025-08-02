@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            {{-- <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-drawer icon-gradient bg-happy-itmeo"></i>
                        </div>
                        <div>Store<div class="page-title-subheading">Edit Store</div>
                        </div>
                    </div>
                    <div class="page-title-actions">
                        <div class="d-inline-block dropdown">
                            <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                class="btn-shadow dropdown-toggle btn btn-info">
                                <span class="btn-icon-wrapper pr-2 opacity-7">
                                    <i class="fa fa-business-time fa-w-20"></i>
                                </span> Action </button>
                            <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
                                <ul class="nav flex-column">
                                    @can('Show Branch')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('all.branch') }}">
                                                <i class="nav-link-icon lnr-inbox"></i>
                                                <span>All Store</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Stores</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5>Create Store</h5>
                            @can('Show Branch')
                            <a class="nav-link" href="{{route('all.branch')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Stores
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{ url('/update/branch/' . $branch->id) }}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <input type="hidden" name="id" value="{{ $branch->id }}" />
                                        <label>Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Name" required class="form-control"
                                            name="name" value="{{ $branch->name }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Code <span class="requiredStar">*</span></label>
                                        <input type="number" min="0" placeholder="Code" required
                                            class="form-control" name="code" value="{{ $branch->code }}">
                                    </div>
                                    <div class="form-group">
                                        <label>Organization <span class="requiredStar">*</span></label>
                                        <select class="mb-2 form-control" required name="sub_org_id"
                                            value="{{ $branch->sub_org_id }}">
                                            <option value=""> Select</option>
                                            @foreach ($sub_organizations as $k => $sub_organization)
                                                <option value="{{ $sub_organization->id }}"
                                                    {{ $branch->sub_org_id == $sub_organization->id ? 'selected' : '' }}>
                                                    {{ $sub_organization->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status <span class="requiredStar">*</span></label>
                                        <select class="mb-2 form-control" required name="status"
                                            value="{{ $branch->status }}">
                                            <option value=""> Select</option>
                                            <option value="0" {{ $branch->status == 0 ? 'selected' : '' }}> Inactive
                                            </option>
                                            <option value="1" {{ $branch->status == 1 ? 'selected' : '' }}> Active
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Store</button>
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
@endpush
