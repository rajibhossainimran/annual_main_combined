<?php use App\Services\BanglaDate;
//$bn = new BanglaDate(time());
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta charset="utf-8" />
    <title>Note Sheet</title>
    <meta name="author" content="Harrison Weir" />
    <meta name="subject"
        content="Cats. Their Varieties, Habits and Management; and for show, the Standard of Excellence and Beauty" />
    <meta name="keywords" content="cats,feline" />
    <meta name="date" content="2014-12-05" />
</head>
<style>
    .heading {
        text-align: center;
        text-transform: uppercase;
    }

    table {
        width: 100%;
    }

    .upper {
        text-transform: uppercase;
    }

    .fl-right {
        float: right;
    }

    .wd-20 {
        width: 30%;
    }

    .textJust {
        text-align: justify;
    }

    p {
        font-size: 17px;
        margin-bottom: 2px;
        margin-top: 2px;

    }

    .tableBorder,
    .tableBorder tr,
    .tableBorder td {
        border: 1px solid black;
        border-collapse: collapse;
        /* text-align: center; */
    }

    .tcenter {
        text-align: center !important;
    }

    @page {
        margin: 40px 30px !important;
        /* padding: 0 !important; */
    }
</style>

<body style="margin: 1px">
    <p class="tcenter" style="font-size: 22px;text-decoration: underline;font-weight: bold">C.S.R</p>
    <p class="tcenter" style="font-size: 18px;text-decoration: underline;">
        Tender No:{{ $csr->tender_no }} Open dt {{ date('d F Y', strtotime($csr->deadline)) }}
    </p>
    <table class="tableBorder">
        <tr>
            <td width="5%" style="text-align: center">S/No</td>
            <td width="15%" style="text-align: center">PVMS</td>
            <td width="50%" style="text-align: center">Nomenclature</td>
            <td width="10%" style="text-align: center">A/U</td>
            <td width="20%" style="text-align: center">Qty</td>
        </tr>
        <tr>
            <td width="5%" style="text-align: center">{{ $position }}</td>
            <td width="15%" style="text-align: center"> {{ $csr->PVMS->pvms_id }} </td>
            <td width="50%" style="text-align: left"> {{ $csr->PVMS->nomenclature }} </td>
            <td width="10%" style="text-align: center">
                @if (isset($acc_unit) && !empty($acc_unit))
                    {{ $acc_unit->name }}
                @endif
            </td>
            <td width="20%" style="text-align: center"> {{ $csr->pvms_quantity }} </td>
        </tr>
    </table>
    <br>
    <table class="tableBorder">
        <tr>
            <td width="5%" style="text-align: center">S/No</td>
            <td width="15%" style="text-align: center">PVMS No</td>
            <td width="20%" style="text-align: center">Particular of Bidder</td>
            <td width="30%" style="text-align: center">Nomenclature</td>
            <td width="10%" style="text-align: center">A/U</td>
            <td width="20%" style="text-align: center">Unit Price</td>
        </tr>
        @foreach ($csr->vandorPerticipate as $k => $vendor)
            <tr>
                <td width="5%" style="text-align: center">{{ $k + 1 }}</td>
                <td width="15%" style="text-align: center"> {{ $csr->PVMS->pvms_name }}</td>
                <td width="20%" style="text-align: center"> {{ $vendor->v_name }}</td>
                <td width="30%" class="textJust" style="text-align: left; ">{{ $csr->PVMS->nomenclature }}
                    <br>
                    <br>
                    {!! $vendor->details !!}
                </td>
                <td width="10%" style="text-align: center">
                    @if (isset($acc_unit) && !empty($acc_unit))
                        {{ $acc_unit->name }}
                    @endif

                </td>
                <td width="20%" style="text-align: right"> {{ $vendor->offered_unit_price }} </td>
            </tr>
        @endforeach
    </table>

    <br><br><br>

    <table width="100%" style="margin-left: 40px; margin-right: 80px">
        @if (isset($CSRApp) && !empty($CSRApp))
            @foreach ($CSRApp as $k => $CSRIndividualApp)
                @if ($CSRIndividualApp->role_name == 'hod')
                    <tr>
                        <td colspan="2" style="font-size: 14px; text-decoration: underline;">
                            {{ $CSRIndividualApp->name }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 17px; width: 70%; vertical-align: top;">
                            {{ $CSRIndividualApp->remarks }}
                        </td>
                        <td style="text-align: right; width: 30%; vertical-align: top;">
                            @if (isset($CSRIndividualApp->sign) && !empty($CSRIndividualApp->sign))
                                <img src="{{ asset('sign/' . $CSRIndividualApp->sign) }}"
                                    style="width: 100px; height: 45px;" /><br>
                                {{-- <span style="font-size: 14px;">{{ $CSRIndividualApp->sign }}</span><br> --}}
                                {{-- <span style="font-size: 14px;">{{ $CSRIndividualApp->name }}</span><br>
                                    <span style="font-size: 14px;">{{ $CSRIndividualApp->rank }}</span><br>
                                    <span style="font-size: 14px;">{{ $CSRIndividualApp->address }}</span><br> --}}
                                <span
                                    style="font-size: 14px;">{{ date('d M Y', strtotime($CSRIndividualApp->created_at)) }}</span>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        @endif
    </table>

    <br><br><br>

    <table width="100%" style="margin-left: 40px; margin-right: 80px">
        @if (isset($CSRApp) && !empty($CSRApp))
            @foreach ($CSRApp as $k => $CSRIndividualApp)
                @if ($CSRIndividualApp->role_name == 'csg')
                    <tr>
                        <td colspan="2" style="font-size: 14px; text-decoration: underline;">
                            {{ $CSRIndividualApp->name }}</td>
                    </tr>
                    <tr>
                        <td style="font-size: 17px; width: 70%; vertical-align: top;">
                            {{ $CSRIndividualApp->remarks }}
                        </td>
                        <td style="text-align: right; width: 30%; vertical-align: top;">
                            @if (isset($CSRIndividualApp->sign) && !empty($CSRIndividualApp->sign))
                                <img src="{{ asset('sign/' . $CSRIndividualApp->sign) }}"
                                    style="width: 100px; height: 45px;" /><br>
                                {{-- <span style="font-size: 14px;">{{ $CSRIndividualApp->sign }}</span><br> --}}
                                {{-- <span style="font-size: 14px;">{{ $CSRIndividualApp->name }}</span><br>
                                    <span style="font-size: 14px;">{{ $CSRIndividualApp->rank }}</span><br>
                                    <span style="font-size: 14px;">{{ $CSRIndividualApp->address }}</span><br> --}}
                                <span
                                    style="font-size: 14px;">{{ date('d M Y', strtotime($CSRIndividualApp->created_at)) }}</span>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        @endif
    </table>
</body>

</html>
