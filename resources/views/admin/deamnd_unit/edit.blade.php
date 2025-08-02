@extends('admin.master')
@push('css')

@endpush
@section('content')
<div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Demand Units</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center  table-header-bg py-1">
                            <h5 class="f-14">Edit Demand Units</h5>
                            @can('Show Account Units')
                            <a class="nav-link" href="{{route('all.demand.unit')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Demand Units
                                </button>
                            </a>
                            @endcan
                        </div>

                        <div class="pb-4">
                            <form action="{{url('/settings/edit/demand-units/'.$account_unit->id)}}" method="post">
                                @csrf
                                <div class="col-lg-6">
                                    <input type="hidden" name="id" value="{{$account_unit->id}}"/>
                                    <div class="form-group">
                                        <label>Demand Unit Name <span class="requiredStar">*</span></label>
                                        <input type="text" placeholder="Account Unit Name" required class="form-control" name="name" value="{{ $account_unit->name }}">
                                    </div>

                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Update Unit</button>
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
