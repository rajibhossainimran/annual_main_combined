@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Organizations</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                       {{-- <h5 class="card-title">Sub Organization</h5> --}}
                       <div class="d-flex justify-content-between align-items-center table-header-bg py-1 mb-1">
                            <h5 class="f-14">Organizations</h5>
{{--                            {{ $sub_organizations->links('vendor.pagination.custom') }}--}}
                            @can('Create Sub Organization')
                            <a class="nav-link" href="{{route('add.sub.organization')}}">
                                <button class="btn-icon btnc btn-custom">
                                    <i class="fa fa-plus btn-icon-wrapper"></i>
                                    Create Organization
                                </button>
                            </a>
                            @endcan
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="example" class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Governing Body</th>
                                            <th>Code</th>
                                            <th>Serial</th>
                                            <th>Division</th>
                                            <th>Service</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        @foreach($sub_organizations as $k=>$sub_organization)
                                            <tr>
                                                <th scope="row">{{$k+1}}</th>
                                                <td>{{$sub_organization->name}}</td>
                                                <td>
                                                    @if($sub_organization->organizationFrom)
                                                        {{ $sub_organization->organizationFrom->name }}
                                                    @endif
                                                </td>
                                                <td>{{$sub_organization->code}}</td>
                                                <td>{{ $sub_organization->serial }}</td>
                                                <td>
                                                    @if ($sub_organization->divisiomFrom)
                                                        {{ $sub_organization->divisiomFrom->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sub_organization->serviceFrom)
                                                        {{ $sub_organization->serviceFrom->name }}
                                                    @endif
                                                </td>
                                                <td>{{$sub_organization->type}}</td>
                                                <td>
                                                    @if($sub_organization->status)
                                                        <div class="badge badge-pill badge-success">Active</div>
                                                    @else
                                                        <div class="badge badge-danger ml-2">Inactive</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($sub_organization->createdBy)
                                                        {{ $sub_organization->createdBy->name }}
                                                    @endif
                                                </td>

                                                <td class="d-flex">
                                                    @can('Edit Sub Organization')
                                                        <a href="{{url('/edit/sub-organization/'.$sub_organization->id)}}">
                                                            <button class="btn btn-outline-info border-0">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Sub Organization')
                                                        <form action="{{url('/delete/sub-organization/'.$sub_organization->id)}}" method="post">
                                                            @csrf
                                                            <button type="submit" id="{{$sub_organization->id}}" class="border-0 btn-transition btn btn-outline-danger delete-account-unit"><i class="fa fa-trash-alt"></i></button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <br/>
{{--                        {{ $sub_organizations->links('vendor.pagination.custom') }}--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@endpush
