@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Contract Bill Process Report</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Search </h5>

                        </div>

                        <div class="pb-2">
                            <form action="{{ route('report.bill.process.result') }}" method="get">
                                @csrf
                                <div class="row margin-right-0">
                                    <div class="col-md-6">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Financial Year <span class="requiredStar">*</span></label>
                                                <select class="mb-2 form-control" name="year_id" required>
                                                        <option value=""> Select</option>
                                                        @foreach ($years as $year)
                                                        <option value="{{$year->id}}"> {{$year->name}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Purchase Contract No <span class="requiredStar">*</span></label>
                                                <select class="mb-2 form-control" name="contract_number" required>
                                                        <option value=""> Select</option>
                                                        @foreach ($workoderNumbers as $workoderNumber)
                                                        <option value="{{$workoderNumber->contract_number}}"> {{$workoderNumber->contract_number}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-1">
                                            <button type="submit" class="btn btn-primary mt-1 float-right">Search</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>CRV No</label>
                                                <select class="mb-2 form-control" name="crv_no" >
                                                        <option value=""> Select</option>
                                                        @foreach ($crv as $number)
                                                        <option value="{{$number->crv_no}}"> {{$number->crv_no}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if(isset($data) && !empty($data))
                        <br>
                        <h5 class="text-center">Bill Process Report </h5>
                        <div class="col-lg-12">
                            <div class="row">

                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr  class="text-center">
                                            <th>SL</th>
                                            <th>Purchase Contract No</th>
                                            <th>CRV No</th>
                                            <th>PVMS No</th>
                                            <th>Nomenclature</th>
                                            <th>A/U</th>
                                            <th>Qty Received </th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody  class="text-center">
                                            <?php $total_tk = 0; $qty_total =0; ?>
                                            @foreach($data as $key=>$value)
                                                    @php
                                                    $total_tk += $value->total_price;
                                                    $qty_total += $value->qty;
                                                    @endphp
                                                    <tr>
                                                        <th scope="row">{{++$key}}</th>
                                                        <td class="text-center">{{$value->order_no}}</td>
                                                        <td class="text-center">{{$value->crv_no}}</td>
                                                        <td class="text-center">{{$value->pvms}}</td>
                                                        <td class="text-center">{{$value->nomenclature}}</td>
                                                        <td class="text-center">{{$value->name}}</td>
                                                        <td class="fbold text-right"> 
                                                            {{$value->qty}}
                                                        </td>
                                                        <td  class="fbold text-right">
                                                            {{$value->unit_price}}
                                                        </td>
                                                        <td  class="fbold text-right">
                                                            {{$value->total_price}}
                                                        </td>
                                                    </tr>
                                            @endforeach
                                            <tfoot>
                                                <td colspan="6" class="total-search">Total</td>
                                                <td class="wed-color fbold text-right" >
                                                 <?php echo number_format($qty_total); ?>
                                                </td>
                                                <td class="total-search">Total</td>
                                                <td class="wed-color fbold text-right" >
                                                 <?php echo number_format($total_tk,2); ?>
                                                </td>
                                            </tfoot>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
