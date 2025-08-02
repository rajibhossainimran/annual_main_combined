<?php use App\Services\BanglaDate;
//$bn = new BanglaDate(time());
ini_set('memory_limit', '512M');
?>


<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta charset="utf-8" />
    <title>Demand</title>
    <meta name="author" content="" />
    <meta name="subject"
        content="Cats. Their Varieties, Habits and Management; and for show, the Standard of Excellence and Beauty" />
    <meta name="keywords" content="cats,feline" />
    <meta name="date" content="2014-12-05" />
</head>
<style>
    .heading {
        text-align: center;
        text-transform: uppercase;
        font-weight: 400;
        padding-bottom: 0px;
    }

    table {
        width: 100%;
    }

    .table1 tr td {
        float: right;
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

    .wd-33 {
        width: 33%;
    }

    .wd-37 {
        width: 37%;
    }

    .textJust {
        text-align: justify;
    }

    p {
        font-size: 18px;
        margin-bottom: 2px;
        margin-top: 2px;
    }

    .tableBorder,
    .tableBorder tr,
    .tableBorder td {
        border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
    }


    td table,
    td table tr,
    td table td {
        border: 1px solid #000;
        border-collapse: collapse;

        /* border-left: 1px solid #000; */
    }

    td table {
        border-collapse: collapse;
        width: 100%;
        text-align: center;
    }
</style>
<style>
    table.print-friendly {
        page-break-inside: avoid;
    }
</style>

<body style="text-align:justify;font-size:15px">
    <h4 class="heading">Restricted</h4>
    <table class="table1" style="padding-top:0px">
        <tr>
            <td class="" style="font-size:15px">
                @if (isset($approved) && !empty($approved))
                    @foreach ($approved as $appr)
                        @if (isset($appr->sign) && !empty($appr->sign))
                            @if ($appr->role_name == 'deputy_commandend')
                                Dy Comdt: &nbsp;
                                <img src="{{ asset('sign/' . $appr->sign) }}" style="width: 100px;height:45px" />
                                {{-- <p style="font-size: 13px;padding-left:10px">{{date('d M Y', strtotime($appr->sign_date))}}</p> --}}
                            @endif
                        @endif
                    @endforeach
                @endif
            </td>
            <td class="wd-37" style="font-size:15px">
                @if (isset($approved) && !empty($approved))
                    @foreach ($approved as $appr)
                        @if (isset($appr->sign) && !empty($appr->sign))
                            @if ($appr->role_name == 'cmdt')
                                Comdt: &nbsp;
                                <img src="{{ asset('sign/' . $appr->sign) }}" style="width: 100px;height:45px" />
                                {{-- <p style="font-size: 13px;padding-left:10px">{{date('d M Y', strtotime($appr->sign_date))}}</p> --}}
                            @endif
                        @endif
                    @endforeach
                @endif
            </td>
        </tr>


        @if (isset($OIC->address) && !empty($OIC->address))
            <tr>
                <td></td>
                <td class="wd-37" style="font-size:15px">{{ $OIC->address }}</td>
            </tr>
        @endif


     
        <tr>
            <td></td>
            <td class="wd-37" style="font-size:15px"></td>
        </tr>
        <tr>
            <td></td>
            <td class="wd-37" style="font-size:15px"></td>
        </tr>
    </table>
    <table class="table2">
        <tr>
        
            <td class="" style="font-size:15px;width:40%">
                {{ date('d M Y') }}
            </td>
        </tr>
        {{-- <tr>
            <td class="upper" style="text-decoration: underline;padding-top: 10px;padding-bottom: 10px;font-weight:bold;font-size:15px">Urgent DMD medical store</td>
            <td class=" wd-20"></td>
        </tr> --}}
    </table>
    <div>


        <table class="tableBorder" style="margin-left:28px;font-size:15px">
            <tr>
                <td width="5%" style="font-size:15px">Ser</td>
                <td width="12%" style="font-size:15px">PVMS No</td>
                <td width="12%" style="font-size:15px">Old PVMS</td>
                <!-- <td width="30%" style="font-size:15px">Group</td> -->
                <td width="30%" style="font-size:15px">Nomenclature</td>
               
            </tr>
            @foreach ($demands as $k => $demand)
                <tr>
                    <td style="font-size:15px">{{ $k + 1 }}</td>
                    <td style="font-size:15px">{{ $demand->pvms_id }}</td>
                    <td style="font-size:15px">{{ $demand->pvms_old_name }}</td>
                    <!-- <td style="text-align: center; font-size:15px;text-align:left">{{ $demand->item_groups_id }}</td> -->
                    <td style="font-size:15px;text-align:left">{{ $demand->nomenclature }}</td>
                   
                </tr>
            @endforeach
        </table>
        <table>
            <tr>
                <td style="text-align: left;font-size:15px;padding-bottom:10px;padding-top:10px">
                    {!! $demand->description1 !!}
                </td>
            </tr>
        </table>
    </div>


    <table width="100%" class="print-friendly">
        <tr>
            <td style=" font-size:15px;padding-top:0px" colspan="2">
                {{-- 2. &nbsp; You are req to take nec act for issuance of aforesaid item immediately. --}}
            </td>
        </tr>
        <tr>
            <td
                @if (count($demands) > 5) style="padding-bottom: 50px"  @else style="padding-bottom: 80px" @endif>
            </td>
            <td></td>
        </tr>
        <!-- <tr>
            <td style="width: 67%"></td>
            <td style="font-size:15px;font-weight:bold;width:33%">
                @if (isset($approved) && !empty($approved))
                    @foreach ($approved as $appr)
                        @if (isset($appr->sign) && !empty($appr->sign))
                            @if ($appr->role_name == 'oic')
                                &nbsp;&nbsp;
                                <img src="{{ asset('sign/' . $appr->sign) }}" style="width: 100px;height:45px" />
                                {{-- <p style="font-size: 13px;padding-left:10px">{{date('d M Y', strtotime($appr->sign_date))}}</p> --}}
                            @endif
                        @endif
                    @endforeach
                @endif

            </td>
        </tr> -->
        @if (isset($OIC->name) && !empty($OIC->name))
            <tr>
                <td style="width: 63%"></td>
                <td style="font-size:15px;font-weight:bold;width:37%">{{ $OIC->name }}</td>
            </tr>
        @endif




        <tr>
            <td style="padding-bottom: 15px"> </td>
            <td></td>
        </tr>

        <tr>
            {{-- <td></td> --}}
            <td style="text-align: center;text-transform: uppercase;font-weight: 400;" colspan="2">
                <br><br>Restricted
            </td>
        </tr>
    </table>
    {{-- <div style="width: 100%">
        <div style="width: 67%;float: left;font-size:15px">
            <p style="font-size:15px">Distr : </p>
            <p style="font-size:15px">Extl : </p>
            <p style="font-size:15px">Act : </p><br>
            <p style="font-size:15px">Dte Gen Med Svcs </p>
            <p style="font-size:15px">Min of Def</p>
            <p style="font-size:15px">Dhaka Cantt</p><br>
            <p style="font-size:15px">Info :</p><br>
            <p style="font-size:15px">AFMSD</p>
            <p style="font-size:15px">Dhaka Cantt</p>
        </div>
        @if (isset($OIC->name) && !empty($OIC->name))
        <div style="width:33%; float: right">
            <p style="font-size:15px;font-weight:bold;">{{$OIC->name}}</p>
            <p style="font-size:15px">{{$OIC->rank}}</p>
            <p style="font-size:15px">For Comdt</p>
        </div>
        @endif
    </div> --}}
    {{-- <h4 class="heading">Restricted</h4> --}}
</body>

</html>
