@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Add Menu</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <h5 class="f-14">Create Menu & Sub Menu</h5>

                            <div class="pb-2">
                                <form action="{{route('store.menu')}}" method="post">
                                    @csrf
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label> Menu Name</label>
                                            <input type="text" required class="form-control" name="name" value="{{ old('name') }}" placeholder="Menu Name">
                                        </div>
                                        <div class="form-group">
                                            <label>Sub Menu Url</label>
                                            <input type="text" required class="form-control" name="common_url" value="{{ old('common_url') }}" placeholder="Menu Url">
                                        </div>
                                        <div class="mt-1">
                                            <button type="submit" class="btn btn-primary mt-1">Submit</button>
                                        </div>
                                    </div>
                                </form>
                                <hr>
                                <form action="{{route('store.submenu')}}" method="post">
                                    @csrf
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Menu </label>
                                            <select class="form-control" required name="menu_id" id="parent_menu">
                                                <option value="">Select</option>
                                                @foreach($menus as $menu)
                                                    <option value="{{$menu->id}}">{{$menu->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="position-relative form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input id="is_show" type="checkbox" name="is_show" value="Yes" class="form-check-input"> Is show sidebar sub menu
                                            </label>
                                        </div>
                                        <div id="sub_menu_div">

                                        </div>
                                        <div class="form-group" id="sub_menu">
                                            <label>Sub Menu Name</label>
                                            <input type="text" class="form-control" name="show_name" value="{{ old('name') }}" placeholder="Sub Menu Name">
                                        </div>
                                        <div class="form-group">
                                            <label>Permission Name</label>
                                            <input type="text" required class="form-control" name="name" value="{{ old('name') }}" placeholder="Permission Name">
                                        </div>
                                        <div class="form-group">
                                            <label>Sub Menu Url</label>
                                            <input type="text" required class="form-control" name="url" value="{{ old('url') }}" placeholder="Sub Menu Url">
                                        </div>
                                        <div class="mt-1">
                                            <button type="submit" class="btn btn-primary mt-1">Submit</button>
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
    <script src="/admin/scripts/menu.js"></script>
@endpush
