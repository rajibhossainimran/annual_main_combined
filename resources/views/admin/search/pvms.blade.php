@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">{{$type}}</div>
                <div class="main-card card app-content-inner">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">{{$type}}</h5>

                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <h5>PVMS List ({{count($pvms)}})</h5>
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="example" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>PVMS</th>
                                            <th>Old PVMS</th>
                                            <th>Nomenclature</th>
                                            <th>Item Type</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($pvms as $k=>$demandPVMS)
                                            <tr>
                                                <th scope="row">{{++$k}}</th>
                                                <td>{{$demandPVMS->pvms_name}}</td>
                                                <td>{{$demandPVMS->pvms_old_name}}</td>
                                                <td>{{$demandPVMS->nomenclature}}</td>
                                                <td>{{$demandPVMS->name}}</td>

                                            </tr>
                                        @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>

                        <br/>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
