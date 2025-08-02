@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">PVMS</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">PVMS</h5>
                            {{ $pvms->links('vendor.pagination.custom') }}
                            @can('Create PVMS')
                                <a class="nav-link" href="{{ route('add.pvms') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create PVMS
                                    </button>
                                </a>
                            @endcan
                        </div>
                        <div class="d-flex custom-table-filter" id="pvms-table">
                            <form action="" method="get">
                                <select class="form-control limit" name="limit">
                                    <option>20</option>
                                    <option @if (request()->get('limit') == 50) selected @endif>50</option>
                                    <option @if (request()->get('limit') == 100) selected @endif>100</option>
                                </select>
                            </form>

                            <input type="text" class="form-control search" placeholder="search pvms" />
                        </div>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id=""
                                        class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>PVMS</th>
                                                <th>Nomenclature</th>
                                                <th>Account Unit</th>
                                                <th>Specification </th>
                                                <th>Item Group</th>
                                                <th>Item Section</th>
                                                <th>Item Type</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="pvms-tbody">

                                            @foreach ($pvms as $k => $data)
                                                <tr>
                                                    <th scope="row">{{ $k + 1 }}</th>
                                                    <td>{{ $data->pvms_name }}</td>
                                                    <td>{{ $data->nomenclature }}</td>
                                                    <td>
                                                        @if ($data->unitName)
                                                            {{ $data->unitName->name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($data->specificationName)
                                                            {{ $data->specificationName->name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($data->itemGroupName)
                                                            {{ $data->itemGroupName->name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($data->itemSectionName)
                                                            {{ $data->itemSectionName->name }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($data->itemTypename)
                                                            {{ $data->itemTypename->name }}
                                                        @endif
                                                    </td>

                                                    <td class="d-flex">
                                                        @can('Edit PVMS')
                                                            <a href="{{ url('/settings/edit/pvms/' . $data->id) }}">
                                                                <button class="btn btn-outline-info border-0">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </a>
                                                        @endcan
                                                        @if ($canAddPvmsStock == true)
                                                            <a
                                                                href="{{ url('/settings/add-pvms-stock/pvms/' . $data->id) }}">
                                                                <button class="btn btn-outline-info border-0">
                                                                    <i class="fas fa-plus-circle"></i>
                                                                </button>
                                                            </a>
                                                        @endif
                                                        @can('Delete PVMS')
                                                            <form action="{{ url('/settings/delete/pvms/' . $data->id) }}"
                                                                method="post">
                                                                @csrf
                                                                <button type="submit" id="{{ $data->id }}"
                                                                    class="border-0 btn-transition btn btn-outline-danger delete-account-unit"><i
                                                                        class="fa fa-trash-alt"></i></button>
                                                            </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br />
                        {{ $pvms->appends($_GET)->links('vendor.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        window.canAddPvmsStock = @json($canAddPvmsStock);
    </script>
@endpush
