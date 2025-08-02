@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title"> Wings</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Create Wing</h5>
                            
                        </div>

                        <div class="pb-2">
                            <form action="{{route('wing.store')}}" method="post">
                                @csrf
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Select CMH Unit<span class="requiredStar">*</span></label>
                                                <select id="subOrg" class="mb-2 form-control" required name="sub_org_id">
                                                    @if(Auth::user()->sub_org_id)
                                                        <option value="{{$subOrg->id}}" selected> {{$subOrg->name}}</option>
                                                    @else
                                                        <option value="">Select</option>
                                                        @foreach($subOrg as $sub)
                                                            <option value="{{$sub->id}}">{{$sub->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            
                                            
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Wing Name <span class="requiredStar">*</span></label>
                                                <input type="text" required class="form-control" name="wing_name" value="{{ old('wing_name') }}">
                                            </div>
                                            
                                            <br>
                                            <div class="mt-1">
                                                <button type="submit" class="btn btn-primary mt-1">Add Wing</button>
                                            </div>
                                        </div>
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
