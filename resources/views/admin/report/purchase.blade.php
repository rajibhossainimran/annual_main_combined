@extends('admin.master')

@push('css')
@endpush

@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Search By Demand Decision</div>
                <div class="main-card app-content-inner card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between align-items-center table-header-bg py-1">
                            <h5 class="f-14">Search By Demand Decision</h5>
                        </div>

                        <div class="pb-2">
                            <form action="{{ route('report.purchase.search') }}" method="get">
                                @csrf
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Organization</label>

                                        <select class="mb-2 form-control" name="sub_org" required>
                                            @can('Get Sub Organization')
                                                <option value="">Select</option>
                                                @foreach ($org as $sub)
                                                    <option value="{{ $sub->id }}"
                                                        @if ($sub_org == $sub->id) selected @endif>
                                                        {{ $sub->name }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="{{ $org_name->id ?? '' }}" selected>
                                                    {{ $org_name->name ?? 'Select' }}
                                                </option>
                                                @foreach ($org as $sub)
                                                    <option value="{{ $sub->id }}"
                                                        @if ($sub_org == $sub->id) selected @endif>
                                                        {{ $sub->name }}
                                                    </option>
                                                @endforeach
                                            @endcan
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Type</label>
                                        @if (auth()->user()->hasRole('Clerk DGMS'))
                                            <select class="mb-2 form-control" name="type" required>
                                                <option value=""> Select</option>
                                                <option value="issued" @if ($type == 'issued') selected @endif>
                                                    Issue </option>
                                            </select>
                                        @else
                                            <select class="mb-2 form-control" name="type" required>
                                                <option value=""> Select</option>
                                                <option value="lp" @if ($type == 'lp') selected @endif> LP
                                                </option>
                                                <option value="issued" @if ($type == 'issued') selected @endif>
                                                    Issue </option>
                                                <option value="on-loan" @if ($type == 'on-loan') selected @endif>
                                                    On Loan </option>
                                                <option value="notesheet" @if ($type == 'notesheet') selected @endif>
                                                    Notesheet </option>
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">

                                    <div class="mt-1">
                                        <button type="submit" class="btn btn-primary mt-1">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @if (isset($data) && !empty($data))
                            <br>
                            <div class="col-lg-12">
                                <div class="">

                                    <form action="{{ route('report.purchase.store') }}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Financial Year<span class="requiredStar">*</span></label>
                                                    <select class="mb-2 form-control" name="fyear" required>
                                                        <option value=""> Select</option>
                                                        @foreach ($fYear as $years)
                                                            <option value="{{ $years->id }}"> {{ $years->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Voucher Number <span class="requiredStar">*</span></label>
                                                    <input type="text" required class="form-control"
                                                        value="{{ $prefix }}" name="voucher">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div id="react-demand-decision"></div>
                                                @viteReactRefresh
                                                @vite('resources/js/app.jsx')
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Send to <span class="requiredStar text-danger">*</span></label>
                                                    <select class="mb-2 form-control select2" name="send_to" required>
                                                        <option value=""> Select</option>
                                                        @foreach ($send_to as $receiver)
                                                            <option value="{{ $receiver->id }}"> {{ $receiver->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="purchase_type" value="{{ $type }}" />
                                        <div class="col-lg-12 pl-1 pr-1">
                                            <table id=""
                                                class="table-width-100 table table-hover table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Demand No</th>
                                                        <th>PVMS</th>
                                                        <th>Nomenclature</th>
                                                        <th>Item Type</th>
                                                        <th>Type</th>
                                                        <th>Qty</th>
                                                        <th width="10%">Check All <input type="checkbox"
                                                                class="check-all"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $key => $value)
                                                        <tr>
                                                            <th scope="row">{{ ++$key }}</th>
                                                            <td>{{ $value->uuid }}</td>
                                                            <td>{{ $value->pvms_id }}</td>
                                                            <td>
                                                                {{ $value->nomenclature }}
                                                            </td>
                                                            <td>
                                                                {{ $value->itemType->name ?? 'N/A' }}
                                                            </td>
                                                            <td>
                                                                @if ($value->purchase_type == 'lp')
                                                                    LP
                                                                @elseif ($value->purchase_type == 'issued')
                                                                    Issue
                                                                @elseif ($value->purchase_type == 'on-loan')
                                                                    On-Loan
                                                                @else
                                                                    Notesheet
                                                                @endif
                                                            </td>
                                                            <td class="text-right">
                                                                @if (isset($value->reviewd_qty))
                                                                    {{ $value->reviewd_qty }}
                                                                    <input type="hidden" name="qty[]"
                                                                        value="{{ $value->reviewd_qty }}">
                                                                @else
                                                                    {{ $value->qty }}
                                                                    <input type="hidden" name="qty[]"
                                                                        value="{{ $value->qty }}">
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" name="pvms_selected[]"
                                                                    value="{{ $value->id }}">
                                                                <input type="hidden" name="pvms_id[]"
                                                                    value="{{ $value->p_id }}">
                                                                <input type="hidden" name="type[]"
                                                                    value="{{ $type }}">
                                                                <input type="hidden" name="sub_org"
                                                                    value="{{ $sub_id }}">
                                                                <input type="hidden" name="demand_id[]"
                                                                    value="{{ $value->d_id }}">
                                                                <input type="hidden" name="demand_pvms_id[]"
                                                                    value="{{ $value->id }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                <tfoot>
                                                    <td colspan="7" class="total-search text-center">
                                                        <button type="submit"
                                                            class="btn btn-primary mt-1">Submit</button>
                                                    </td>
                                                </tfoot>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
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
