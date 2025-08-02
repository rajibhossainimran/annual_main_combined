@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Current Stock Batch Wise</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="p-2">
                            @if (auth()->user()->subOrganization && auth()->user()->subOrganization->type == 'DGMS')
                                <label><b>Afmsd Stock</b></label>
                            @endif
                            <div class="row">
                                <div class="col-12">
                                    PVMS: <b>{{$data->pvms_name}}</b>
                                </div>
                                <div class="col-12">
                                    Nomenclature: <b>{{$data->nomenclature}}</b>
                                </div>
                                <div class="col-12">
                                    Stock: <b class="text-success">{{$data->stock_qty ?? 0}}</b>
                                </div>
                                @if ((!isset(auth()->user()->subOrganization) && auth()->user()->org_id == 1) || (auth()->user()->subOrganization && (auth()->user()->subOrganization->type == 'DGMS' || auth()->user()->subOrganization->type == 'AFMSD')))
                                    <div class="col-12">
                                        Ready for delivery Qty: <b class="text-primary">{{isset($onloan_qty) ? $data->stock_qty - $onloan_qty : $data->stock_qty}}</b>
                                    </div>
                                    <div class="col-12">
                                        Onloan Qty: <b class="text-danger">{{$onloan_qty ?? 0}}</b>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="p-2">
                            <table class="table table-bordered table-striped fs--1 mb-0">
                                <thead class="bg-200 text-900">
                                    <tr>
                                        <th>#</th>
                                        <th>Batch</th>
                                        <th>Expiry Date</th>
                                        <th>Available Qty</th>

                                    </tr>
                                </thead>
                                <tbody class="list">

                                    @if(count($data->batchList)>0)
                                        @foreach ($data->batchList as $eachBatch)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $eachBatch->batch_no }}</td>
                                                <td>{{ $eachBatch->expire_date }}</td>
                                                <td>{{ $eachBatch->available_quantity }}</td>

                                            </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td class="text-center" colspan="4">No Stock Found</td>
                                    </tr>
                                    @endif
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
