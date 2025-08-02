@extends('admin.master')

@push('css')
@endpush

@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Add PVMS Stock</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Add PVMS Stock</h5>
                            <a class="nav-link" href="{{ route('all.pvms') }}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    All PVMS
                                </button>
                            </a>
                        </div>

                        <!-- Display Success Message -->
                        @if (session('message'))
                            <div class="alert alert-success">
                                {{ session('message') }}
                            </div>
                        @endif

                        <!-- Display Error Message -->
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="pb-2">
                            <form action="{{ route('update-pvms-stock') }}" method="post">
                                @csrf
                                <input type="hidden" value="{{ $pvms->id }}" name="id">
                                <div class="col-lg-12">
                                    <div class="row bg-heavy-rain py-2">
                                        <div class="col-lg-6">
                                            <div class="row">
                                                <div class="form-group col-lg-6">
                                                    <label>PVMS ID</label>
                                                    <input type="text" placeholder="PVMS ID" class="form-control"
                                                        name="" value="{{ $pvms->id }}" readonly>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <label>Nomenclature</label>
                                                    <input type="text" placeholder="Nomenclature" class="form-control"
                                                        name="" value="{{ $pvms->nomenclature }}" readonly>
                                                </div>
                                                <div class="form-group col-lg-12">
                                                    <label>Batch</label>
                                                    <input type="text" placeholder="Batch" class="form-control"
                                                        name="batch" value="{{ old('batch', $pvms->batch) }}">
                                                    @error('batch')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Current Stock</label>
                                                <input type="number" placeholder="Current Stock" class="form-control"
                                                    name="current_stock"
                                                    value="{{ old('current_stock', $pvms->current_stock) }}">
                                                @error('current_stock')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group">
                                                <label>Expiry Date</label>
                                                <input type="date" class="form-control" name="expiry_date"
                                                    value="{{ old('expiry_date', $pvms->expiry_date) }}">
                                                @error('expiry_date')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-1 text-center">
                                    <button type="submit" class="btn btn-primary mt-1">Add Stock</button>
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
