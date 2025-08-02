@extends('admin.master')
@push('css')
@endpush

@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Urgent Notices</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Urgent Notices</h5>

                            @can('Create Urgent Notice')
                                <a class="nav-link" href="{{ route('urgent.notices.create') }}">
                                    <button class="btn-icon btnc btn-custom">
                                        <i class="fa fa-plus btn-icon-wrapper"></i>
                                        Create Notice
                                    </button>
                                </a>
                            @endcan
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12 pl-1 pr-1">
                                    <table id="example"
                                        class="table-width-100 table table-hover table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>PDF File</th>
                                                {{-- <th>Action</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($notices as $k => $notice)
                                                <tr>
                                                    <th scope="row">{{ $k + 1 }}</th>
                                                    <td>{{ $notice->title }}</td>
                                                    <td>{{ $notice->description }}</td>
                                                    <td>
                                                        @if ($notice->file)
                                                            <a href="{{ asset('storage/' . $notice->file) }}"
                                                                class="btn btn-outline-primary" target="_blank"
                                                                title="View PDF">
                                                                <i class="fa fa-file-pdf"></i> View
                                                            </a>
                                                        @else
                                                            No File
                                                        @endif
                                                    </td>
                                                    {{-- <td class="d-flex">
                                                        @can('Edit Notice')
                                                            <a href="{{ url('/edit/notice/' . $notice->id) }}">
                                                                <button class="btn btn-outline-info border-0">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </a>
                                                        @endcan
                                                        @can('Delete Notice')
                                                            <form action="{{ url('/delete/notice/' . $notice->id) }}"
                                                                method="post">
                                                                @csrf
                                                                <button type="submit" id="{{ $notice->id }}"
                                                                    class="border-0 btn-transition btn btn-outline-danger delete-account-unit">
                                                                    <i class="fa fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
