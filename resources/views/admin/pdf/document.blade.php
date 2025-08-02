<?php use App\Services\BanglaDate;
//$bn = new BanglaDate(time());

?>
    <!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <meta charset="utf-8" />
    <title>Note Sheet</title>
    <meta name="author" content="Harrison Weir"/>
    <meta name="subject" content="Cats. Their Varieties, Habits and Management; and for show, the Standard of Excellence and Beauty"/>
    <meta name="keywords" content="cats,feline"/>
    <meta name="date" content="2014-12-05"/>
</head>
<style>
    .heading{
        text-align: center;
        text-transform: uppercase;
    }
    table{
        width: 100%;
    }

    .upper{
        text-transform: uppercase;
    }
    .fl-right{
        float: right;
    }
    .wd-20{
        width: 30%;
    }
    .textJust{
        text-align: justify;
    }
    p{
        font-size: 18px;
        margin-bottom: 2px;
        margin-top: 2px;
        overflow: wrap;

    }
    .tableBorder, .tableBorder tr, .tableBorder td {
        border: 1px solid black;
        border-collapse: collapse;
        /* text-align: center; */
    }
    .tcenter{
        text-align: center !important;
    }
    @page {
            margin: 10px 10px 50px 60px !important;
            /* padding: 0 !important; */
        }
        @font-face {

        font-style: normal;
        font-weight: normal;
        }
</style>

<body style="margin: 1px;">
    <p class="tcenter" style="font-size: 15px">NOTE SHEET</p>
    <p class="tcenter" style="font-size: 22px">মন্তব্য পত্র</p>
    <table class="tableBorder" style="autosize:0;overflow: wrap;">

        <tr>
            <td width="8%"></td>
            <td width="84%" style="padding-left: 5px;">
                <div align="center">
                    <p class="tcenter" align="center" style="text-align: center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        সীমিত</p>
                </div>
                <p style="float: left; font-size: 16px">{{$notesheet->notesheet_id}}</p>
                <p style="text-decoration: underline; font-size:18px">সশস্ত্র বাহিনীর রোগীদের জন্য জরুরী ঔষধ সমূহ ক্রয় করণ প্রসংগে</p>
                <p class="tcenter" style="text-align: center;font-size:18px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    ১</p>
                    <?php $total = count($notesheetPVMS);  ?>
                @foreach ($notesheetPVMS as $k=>$notesheetDemand)
                <p style="font-size: 15px">
                    @if($k == 0)
                        A.
                    @elseif ($k == 1)
                        B.
                    @elseif ($k == 2)
                        C.
                    @elseif ($k == 3)
                        D.
                    @elseif ($k == 4)
                        E.
                    @elseif ($k == 5)
                        F.
                    @elseif ($k == 6)
                        G.
                    @elseif ($k == 7)
                        H.
                    @elseif ($k == 8)
                        I.
                    @elseif ($k == 9)
                        J.
                    @endif
                     &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                    {{$notesheetDemand->sub_name}} - {{$notesheetDemand->uuid}} Date - {{date('d M Y', strtotime($notesheetDemand->demand_date))}}
                </p>
                @endforeach

                <p class="tcenter" style="text-align: center;font-size:18px">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    ২</p>

                <div class="textJust" style=" font-size: 17px; text-alignment:justify;align-items:center;">
                    {!! $notesheet->notesheet_details !!}
                </div>
                <div>
                    <table style="border: none">
                        <tr>
                            <td width="7%" style="border: none">

                            </td>
                            <td style="border: none">
                                <table class="tableBorder" style="padding-left: 50px">
                                    <tr>
                                        <td width="5%">Ser</td>
                                        <td width="15%">PVMS </td>
                                        <td width="35%">Nomenclature</td>
                                        <td width="25%">User/Dmd Unit</td>
                                        <td width="10%">A/U</td>
                                        <td width="10%">Qty</td>
                                    </tr>
                                    @foreach ($notesheetPVMS as $k=>$notesheetPvms)
                                    <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{$notesheetPvms->pvms_id}}</td>
                                        <td>{{$notesheetPvms->nomenclature}}</td>
                                        <td>{{$notesheetPvms->sub_name}}</td>
                                        <td>{{$notesheetPvms->acc_name}}</td>
                                        <td>{{$notesheetPvms->total_quantity}}</td>
                                    </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>

                <br>
                <br>
                <div width="100%">
                    <table width="100%">
                        <tr>
                            <td width="75%" style="border: none">

                            </td>
                            <td width="25%" style="border: none; text-align:left">
                                <p> মোঃ মশিউর রহমান </p>
                                <p>লে. কর্নেল</p>
                                <p> এডিজিএমএস (স্টোরস)</p>
                                <p style="font-size:14px">{{date('d M Y')}}</p>

                            </td>
                        </tr>
                    </table>
                </div>

                <p>উপ মহাপরিচালক
                    <span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        ৩
                </span></p>
                @if(count($notesheetPVMS) > 5)
                <br></br><br></br><br>
                @else
                <br></br><br></br><br></br><br></br><br></br>
                @endif

                <p> কনসালটেন্ট ফিজিশিয়ান জেনারেল
                    <span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                         ৪
                    </span>
                </p>
                @if(count($notesheetPVMS) > 5)
                <br></br><br></br></br><br>
                @else
                <br></br><br></br><br></br><br>
                @endif

                <p>মাহাপরিচালক
                    <span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        ৫
                    </span>
                </p>
                @if(count($notesheetPVMS) > 5)
                <br/><br/><br></br><br>
                @else
                <br/><br/><br></br><br></br><br></br><br>
                @endif

                <p class="tcenter" style="text-align: center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    সীমিত</p>
                    <br/><br/><br>
            </td>
            <td width="8%" style="text-align: center"> সংলগ্নী নং
                <br><br><br><br>
                {{-- @if($total == 1)
                {{-- <br><br> --}}
                {{-- @endif --}}
                @foreach ($notesheetPVMS as $k=>$notesheetDemand)
                ১
                @if($k == 0)
                        A
                    @elseif ($k == 1)
                        ,B
                    @elseif ($k == 2)
                        ,C
                    @elseif ($k == 3)
                        ,D
                    @elseif ($k == 4)
                        ,E
                    @elseif ($k == 5)
                        ,F
                    @elseif ($k == 6)
                        ,G
                    @elseif ($k == 7)
                        ,H
                    @elseif ($k == 8)
                        ,I
                    @elseif ($k == 9)
                        ,J
                    @endif
                &nbsp;
                @endforeach
                <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p>  <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p>  <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p>
                <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p><p>&nbsp;</p> <p>&nbsp;</p>
                <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p><p>&nbsp;</p> <p>&nbsp;</p>
                <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p><p>&nbsp;</p> <p>&nbsp;</p>
                <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p><p>&nbsp;</p> <p>&nbsp;</p>
                <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p> <p>&nbsp;</p><p>&nbsp;</p> <p>&nbsp;</p>

            </td>
        </tr>
    </table>
</body>
</html>


