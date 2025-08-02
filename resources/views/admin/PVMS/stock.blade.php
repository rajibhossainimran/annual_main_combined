@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Current Stock</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="p-2">
                            <div class="d-flex justify-content-between my-2 mx-2">
                                @if (!auth()->user()->subOrganization || (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'DGMS'))
                                <div class="d-flex align-content-center gap-2">
                                    {{-- <label class="align-self-center pr-2">Unit</label> --}}
                                    <select class="form-control" id="sub_org_id">
                                        <option selected value="">AFMSD</option>
                                        @foreach ($units as $unit)
                                            <option value="{{$unit->id}}">{{$unit->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div>
                                    <div class="d-flex align-content-center">
                                        {{-- <label class="align-self-center pr-2">Item</label> --}}
                                        <select class="form-control" id="pvms_item_type">
                                            <option selected value="">Select Item Type</option>
                                            @foreach ($item_types as $item_type)
                                                <option value="{{$item_type->id}}">{{$item_type->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input class="form-control my-2" type="text" name="datefilter" value="" id="reportdaterange" placeholder="Enter date range"/>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped fs--1 mb-0" id="stockPvmsListTable">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>#</th>
                                        <th>PVMS</th>
                                        <th>Nomenclature</th>
                                        <th>Account Unit</th>
                                        <th>Specification </th>
                                        <th>Item Group</th>
                                        <th>Item Section</th>
                                        <th>Item Type</th>
                                        <th>Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
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
@endpush
