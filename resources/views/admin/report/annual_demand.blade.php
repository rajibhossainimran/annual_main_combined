@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Annual Demand Unit Report</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Search </h5>

                        </div>

                        <div class="pb-2">
                            <form action="{{ route('report.annual.demand.result') }}" method="get">
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
                                                <label>PVMS No <span class="requiredStar">*</span></label>
                                                <input type="text" required class="form-control" name="pvms_no">
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>ANX <span class="requiredStar">*</span></label>
                                                <select class="mb-2 form-control" name="type_id" required>
                                                        <option value=""> Select</option>
                                                        @foreach ($items as $item)
                                                        <option value="{{$item->id}}"> {{$item->name}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div> -->
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Unit <span class="requiredStar">*</span></label>
                                                <select class="mb-2 form-control" name="sub_org" required>
                                                        <option value="All"> All</option>
                                                        @foreach ($org as $sub)
                                                        <option value="{{$sub->id}}"> {{$sub->name}}</option>
                                                        @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-1">
                                            <button type="submit" class="btn btn-primary mt-1 float-right">Search</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if(isset($data) && !empty($data))
                        <br>
                        <h5 class="text-center">Annual Requirement of Unit </h5>
                        <div class="col-lg-12">
                            <div class="row">

                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr  class="text-center">
                                            <th>SL</th>
                                            <th>PVMS</th>
                                            <th>Nomenclature</th>
                                            <th>Unit Name</th>
                                            <th>AVG Qtr Reqr</th>
                                            <th>Annual Reqr</th>
                                        </tr>
                                        </thead>
                                        <tbody  class="text-center">
                                            <?php $avg_total = 0; $annual_total =0; ?>
                                            @foreach($data as $key=>$value)
                                                @if(isset($value->dg_qty) && !empty($value->dg_qty))
                                                    @php
                                                    $avg_total += floor($value->dg_qty / 4);
                                                    $annual_total += $value->dg_qty;
                                                    @endphp
                                                    <tr>
                                                        <th scope="row">{{++$key}}</th>
                                                        <td class="text-center">{{$value->pvms_id}}</td>
                                                        <td>{{$value->nomenclature}}</td>
                                                        <td>
                                                            {{$value->name}}
                                                        </td>
                                                        <td class="fbold"> 
                                                            {{floor($value->dg_qty / 4)}}
                                                        </td>
                                                        <td  class="fbold">
                                                            {{$value->dg_qty}}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tfoot>
                                                <td colspan="4" class="total-search">Total</td>
                                                <td class="wed-color fbold text-right" >
                                                 <?php echo number_format($avg_total); ?>
                                                </td>
                                                <td class="wed-color fbold text-right" >
                                                 <?php echo number_format($annual_total); ?>
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
