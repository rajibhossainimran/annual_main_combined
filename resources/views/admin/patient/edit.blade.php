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
                            <h5 class="f-14">edit Patients</h5>
                            @can('Show Account Units')
                            <a class="nav-link" href="{{route('all.patient')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Patients
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-2">
                            <form action="{{url('/settings/update/patient/'.$patients->id)}}" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{$patients->id}}">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Identification No. <span class="text-danger">*</span> </label>
                                        <div class="row px-3">
                                            <select class="form-control col-3" required="" name="type">
                                                <option value="">Type</option>
                                                <option value="BA" @if($type[0] == 'BA') selected @endif>BA</option>
                                                <option value="No" @if($type[0] == 'No') selected @endif>No</option>
                                                <option value="TSO" @if($type[0] == 'TSO') selected @endif>TSO</option>
                                                <option value="CS" @if($type[0] == 'CS') selected @endif>CS</option>
                                                <option value="MS" @if($type[0] == 'MS') selected @endif>MS</option>
                                                <option value="MES" @if($type[0] == 'MES') selected @endif>MES</option>
                                            </select>
                                            <input type="text" required="" value="{{$type[1]}}" class="form-control col-9" name="number" placeholder="Identification Number">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Patient Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Patient Name" required class="form-control" name="name" value="{{ $patients->name }}">
                                    </div>
                                    <div class="form-group">
                                        <div class="row px-1">
                                            <div class="col-6">
                                                <label>Relation <span class="text-danger">*</span></label>
                                                <select class="form-control" required="" name="relation">
                                                    <option value="">Relation</option>
                                                    <option value="Self" @if($patients->relation == 'Self') selected @endif>Self</option>
                                                    <option value="W/O" @if($patients->relation == 'W/O') selected @endif>W/O</option>
                                                    <option value="D/O" @if($patients->relation == 'D/O') selected @endif>D/O</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label>Unit <span class="text-danger">*</span></label>
                                                <select class="form-control" required name="unit_id">
                                                    <option value="">Unit</option>
                                                    @foreach ($units as $unit)
                                                    <option value="{{$unit->id}}" @if($patients->unit_id == $unit->id) selected @endif>{{$unit->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Patient</button>
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
