@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Transit PVMS</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="p-2">
                            <div class="d-flex justify-content-between my-2 mx-2">
                                @if (!auth()->user()->subOrganization || (auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'DGMS' || auth()->user()->subOrganization->type == 'AFMSD')))
                                <div class="d-flex align-content-center gap-2">
                                    <label class="align-self-center pr-2">Unit</label>
                                    <select class="form-control" id="sub_org_id">
                                        <option selected value="">All</option>
                                        @foreach ($units as $unit)
                                            <option value="{{$unit->id}}">{{$unit->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            <table class="table table-bordered table-striped fs--1 mb-0" id="stockTransitPvmsListTable">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>#</th>
                                        <th>PVMS</th>
                                        <th>Nomenclature</th>
                                        <th>Account Unit</th>
                                        <th>Unit</th>
                                        <th>Batch</th>
                                        <th>Qty</th>
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
