@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Role & Permission</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Edit Role & Permission</h5>
                            @can('Show Role')
                            <a class="nav-link" href="{{route('all.permission')}}">
                                <button class="btnc btn-icon btn-custom">
                                    <i class="fa fa-eye btn-icon-wrapper"></i>
                                    Roles
                                </button>
                            </a>
                            @endcan
                        </div>
                        <form action="{{route('update.permission')}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-3"></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="text" class="form-control" readonly name="" value="{{$role->name}}" placeholder="Enter Role Name">
                                        <input type="hidden" class="form-control" name="role_id" value="{{$role->id}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="mb-2 mr-2 btn-icon btn-square btn btn-primary">Update</button>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 px-4">
                                    <input type="checkbox" id="checkAll"> Check All
{{--                                    <div class="row">--}}
{{--                                        @foreach($menus as $m)--}}
{{--                                            <div class="col-md-3" style="border: 1px solid #ddd">--}}
{{--                                                <label style="background: #eee; width: 100%">--}}
{{--                                                    <input class="name" @if(in_array($m->id,$menu_has_permission)) checked @endif name="menu_id[]" type="checkbox" value="{{$m->id}}"> {{$m->name}}--}}
{{--                                                </label>--}}
{{--                                                <div class="ml-3" style="border-left: 2px solid #ddd; padding-left: 2px">--}}
{{--                                                    @foreach($m->permission as $p)--}}
{{--                                                        <label>--}}
{{--                                                            <input class="name" @if(in_array($p->id,$rolePermissions)) checked @endif name="permission[]" type="checkbox" value="{{$p->id}}"> {{$p->name}}--}}
{{--                                                        </label><br>--}}
{{--                                                    @endforeach--}}
{{--                                                </div>--}}

{{--                                            </div>--}}
{{--                                        @endforeach--}}
{{--                                    </div>--}}
                                    <div class="row">
                                        @foreach($menus as $m)
                                            <div class="col-md-3 role-1">
                                                <label class="role-2">
                                                    <input class="name" @if(in_array($m->id,$menu_has_permission)) checked @endif name="menu_id[]" type="checkbox" value="{{$m->id}}"> {{$m->name}}
                                                </label>
                                                <div class="ml-3 role-3">
                                                    @foreach($m->permission as $p)

                                                        <label>
                                                            <input class="name"  @if(in_array($p->id,$rolePermissions)) checked @endif name="permission[]" type="checkbox" value="{{$p->id}}"> @if(isset($p->show_name) && !empty($p->show_name)){{$p->show_name}}@else {{$p->name}}@endif
                                                        </label><br>
                                                        @if(isset($p->permission_sub_menu) && !empty($p->permission_sub_menu))
                                                            @foreach($p->permission_sub_menu as $sub)
                                                                <label class="role-4">
                                                                    <input class="name" @if(in_array($sub->id,$rolePermissions)) checked @endif name="permission[]" type="checkbox" value="{{$sub->id}}"> {{$sub->name}}
                                                                </label><br>

                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </div>

                                            </div>
                                        @endforeach
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
