@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Expire Date Wise Medicine List</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="p-2">
                            <div class="row my-2">
                                <div class="col-6">
                                    <select class="form-control" id="month_durations">
                                        <option selected value="">Select Month</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @if (!auth()->user()->subOrganization || (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'DGMS'))
                                <div class="col-6 mb-2">
                                    {{-- <label class="align-self-center pr-2">Unit</label> --}}
                                    <select class="form-control" id="sub_org_id">
                                        <option selected value="">AFMSD</option>
                                        @foreach ($units as $unit)
                                            <option value="{{$unit->id}}">{{$unit->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="col-6">
                                    <select class="form-control" id="pvms_item_type">
                                        <option selected value="">Select Item Type</option>
                                        @foreach ($item_types as $item_type)
                                            <option value="{{$item_type->id}}">{{$item_type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped fs--1 mb-0 w-100" id="stockExpireDateWisesPvmsListTable">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>#</th>
                                        <th>FY</th>
                                        <th>PVMS</th>
                                        <th>Nomenclature</th>
                                        <th>Account Unit</th>
                                        <th>Item Group</th>
                                        <th>Supplier</th>
                                        <th>Contract No</th>
                                        <th>CRV No</th>
                                        <th>Qty Supplied</th>
                                        <th>Received Date</th>
                                        <th>Date of Expire</th>
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
