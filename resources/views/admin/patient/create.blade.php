@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Patients</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create Patients</h5>
                            @can('Show Account Units')
                            <a class="nav-link" href="{{route('all.demand.unit')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Patients
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{route('store.patient')}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Identification No. <span class="text-danger">*</span> </label>
                                        <div class="row px-3">
                                            <select class="form-control col-3" required="" name="type">
                                                <option value="">Type</option>
                                                <option value="BA">BA</option>
                                                <option value="No">No</option>
                                                <option value="TSO">TSO</option>
                                                <option value="CS">CS</option>
                                                <option value="MS">MS</option>
                                                <option value="MES">MES</option>
                                            </select>
                                            <input type="text" required="" class="form-control col-9" name="number" placeholder="Identification Number">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Patient Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Patient Name" required class="form-control" name="name" value="{{ old('name') }}">
                                    </div>
                                    <div class="form-group">
                                        <div class="row px-1">
                                            <div class="col-6">
                                                <label>Relation <span class="text-danger">*</span></label>
                                                <select class="form-control" required="" name="relation">
                                                    <option value="">Relation</option>
                                                    <option value="Self">Self</option>
                                                    <option value="W/O">W/O</option>
                                                    <option value="D/O">D/O</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label>Unit <span class="text-danger">*</span></label>
                                                <select class="form-control" required name="unit_id">
                                                    <option value="">Unit</option>
                                                    @foreach ($units as $unit)
                                                    <option value="{{$unit->id}}">{{$unit->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Add Patient</button>
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
