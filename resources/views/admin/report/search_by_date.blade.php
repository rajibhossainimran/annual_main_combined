@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Search By Date</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Search By Date</h5>

                        </div>

                        <div class="pb-2">
                            <form action="{{ route('report.search.search') }}" method="get">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Organization</label>
                                        <select class="mb-2 form-control" name="sub_org" required>
                                            @if(isset($org_name) && !empty($org_name))
                                                <option value="{{$org_name->id}}" selected> {{$org_name->name}}</option>
                                            @else
                                                <option value=""> Select</option>
                                                @foreach ($org as $sub)
                                                <option value="{{$sub->id}}"> {{$sub->name}}</option>
                                                @endforeach
                                            @endif

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select class="mb-2 form-control" name="type" required>
                                            @if(isset($org_name) && !empty($org_name))
                                            <option value="Demand"> Demand</option>
                                            @else
                                            <option value=""> Select</option>
                                            <option value="Demand"> Demand</option>
                                            <option value="Notesheet"> Notesheet</option>
                                            @endif

                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Date <span class="requiredStar">*</span></label>
                                        <input type="date" required class="form-control"
                                            name="date">
                                    </div>
                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if(isset($data) && !empty($data))
                        <br>
                                <h5 class="text-center">Search Type: {{$type}}</h5>
                        <div class="col-lg-12">
                            <div class="row">

                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $approved = 0; $pending =0; ?>
                                        @foreach($data as $key=>$value)
                                            @if($type == 'Demand')
                                            <tr>
                                                <th scope="row">{{++$key}}</th>
                                                <td>{{$value->uuid}}</td>
                                                <td>{{date('d M Y', strtotime($value->created_at))}}</td>
                                                <td>
                                                    @if($value->status == 'Approved')
                                                        <?php $approved +=1; ?>
                                                    @else
                                                    <?php $pending +=1; ?>
                                                    @endif
                                                    {{ucfirst($value->status)}}
                                                </td>
                                            </tr>
                                            @elseif($type == 'Notesheet')
                                            <tr>
                                                <th scope="row">{{++$key}}</th>
                                                <td>{{$value->notesheet_id}}</td>
                                                <td>{{date('d M Y', strtotime($value->created_at))}}</td>
                                                <td>
                                                    @if(ucfirst($value->status) == 'Approved')
                                                        <?php $approved +=1; ?>
                                                    @else
                                                    <?php $pending +=1; ?>
                                                    @endif
                                                    {{ucfirst($value->status)}}
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                        <tfoot>
                                            <td colspan="3" class="total-search">Total</td>
                                            <td class="fbold" >
                                                Pending : <?php echo $pending; ?><br>
                                                Approved : <?php echo $approved; ?>
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
