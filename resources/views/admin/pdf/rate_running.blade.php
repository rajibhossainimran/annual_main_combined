<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta charset="utf-8" />
    <title>Note Sheet</title>
    <meta name="author" content="" />
    <meta name="subject"
        content="Cats. Their Varieties, Habits and Management; and for show, the Standard of Excellence and Beauty" />
    <meta name="keywords" content="cats,feline" />
    <meta name="date" content="2014-12-05" />
</head>
<style>
    @page {
        margin: 10px 10px 50px 60px !important;
        /* padding: 0 !important; */
    }

    @media print {

        html,
        body {
            height: 100%;
        }
    }

    td.empty:after {
        content: 'Empty cell';
        visibility: hidden;
        speak: none;
    }

    table {
        empty-cells: show;
    }

    td:empty::before {
        content: '\00a0';
        visibility: hidden
    }
</style>

<body>
    <table width="100%">
        <tr>
            <td>
                <table style="text-align: center" width="100%">
                    <tr style="text-align: center">
                        <td style="text-align: center;font-size: 18px">NOTE SHEET</td>
                    </tr>
                    <tr style="text-align: center">
                        <td style="text-align: center; font-size: 25px">মন্তব্য পত্র</td>
                    </tr>
                </table>
                <table width="100%"
                    style="border-collapse: collapse;border: 1px solid;min-height: 198mm !important;height: 50vh;position: relative; table-layout: fixed;">
                    <tr height="100%">
                        <td width="10%" style="vertical-align:top;border: 1px solid;" height="100%">
                            &nbsp;&nbsp;
                        </td>
                        <td width="80%" style="text-align: justify;border: 1px solid;" height="100%">
                            <table width="100%">
                                <tr>
                                    <td style="text-align: center;font-size: 20px"> সীমিত</td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td style="font-size: 18px">{{ $notesheet->notesheet_id }}</td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td style="font-size: 22px; text-decoration: underline">সশস্ত্র বাহিনীর রোগীদের জন্য
                                        জরুরী ঔষধ সমূহ ক্রয় করণ প্রসংগে</td>
                                </tr>
                            </table>
                            <table width="100%">
                                <tr style="text-align: center">
                                    <td style="font-size: 20px; text-align: center"> ১</td>
                                </tr>
                            </table>
                            <table width="100%">
                                <tr>
                                    <td style="font-size: 18px;float: left;text-align:left">
                                        @php
                                            $notesheetDemandUUIDs = [];
                                        @endphp

                                        @foreach ($notesheetPVMS as $k => $notesheetDemand)
                                            @php
                                                if (in_array($notesheetDemand->uuid, $notesheetDemandUUIDs)) {
                                                    continue;
                                                } else {
                                                    $notesheetDemandUUIDs[] = $notesheetDemand->uuid;
                                                }
                                            @endphp

                                            @if ($k == 0)
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
                                            @elseif ($k == 10)
                                                K.
                                            @elseif ($k == 11)
                                                L.
                                            @elseif ($k == 12)
                                                M.
                                            @elseif ($k == 13)
                                                N.
                                            @elseif ($k == 14)
                                                O.
                                            @elseif ($k == 15)
                                                P.
                                            @elseif ($k == 16)
                                                Q.
                                            @elseif ($k == 17)
                                                R.
                                            @elseif ($k == 18)
                                                S.
                                            @elseif ($k == 19)
                                                T.
                                            @elseif ($k == 20)
                                                U.
                                            @elseif ($k == 21)
                                                V.
                                            @elseif ($k == 22)
                                                W.
                                            @elseif ($k == 23)
                                                X.
                                            @elseif ($k == 24)
                                                Y.
                                            @elseif ($k == 25)
                                                Z.
                                            @endif
                                            &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;{{ $notesheetDemand->sub_name }} -
                                            {{ $notesheetDemand->uuid }} Date -
                                            {{ date('d M Y', strtotime($notesheetDemand->demand_date)) }}<br>
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                            <table width="100%">
                                <tr style="text-align: center;font-size:20px">
                                    <td style="font-size: 17px; text-align: center;font-size:20px"> ২</td>
                                </tr>
                            </table>
                            <table width="100%" style="overflow: wrap; letter-spacing: 0;">
                                <tr>
                                    <td
                                        style="font-size: 18px; text-align: justify-all;letter-spacing: 0;width:100%;padding-bottom: 0px">
                                        {!! $notesheet->notesheet_details !!}
                                    </td>
                                </tr>
                            </table>
                            @if ($notesheet->is_rate_running == 1)
                                {{-- <table width="100%"
                                    style="border-collapse: collapse;border: 1px solid;margin-left:40px; font-size: 14px;padding-top:0;text-align:center;font-size:14px">
                                    <tr>
                                        <td style="border: 1px solid;width:10%; font-size:18px">Tender Ser No</td>
                                        <td style="border: 1px solid;width:15%;font-size:18px">PVMS</td>
                                        <td style="border: 1px solid;width:30%;font-size:18px">Nomenclature</td>

                                        <td style="border: 1px solid;width:5%;font-size:18px">A/U</td>
                                        <td style="border: 1px solid;width:10%;font-size:18px">Rate </td>
                                        <td style="border: 1px solid;width:15%;font-size:18px">Qty</td>
                                        <td style="border: 1px solid;width:15%;font-size:18px">Total Cost</td>
                                    </tr>
                                    <?php $total = 0; ?>
                                    @foreach ($notesheetPVMS as $k => $notesheetPvms)
                                        <tr>
                                            <td style="border: 1px solid;font-size:18px">
                                                {{ $notesheetPvms->tender_ser_no }} S</td>
                                            <td style="border: 1px solid;font-size:18px">{{ $notesheetPvms->pvms_id }}
                                            </td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                {{ $notesheetPvms->nomenclature }}</td>
                                            <td style="border: 1px solid;font-size:18px">{{ $notesheetPvms->acc_name }}
                                            </td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                {{ $notesheetPvms->price }}
                                            </td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                {{ $notesheetPvms->sum }}</td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                <?php
                                                $taka = number_format((float) $notesheetPvms->sum * $notesheetPvms->price, 2, '.', '');
                                                $total += $taka;
                                                ?>
                                                {{ $taka }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tfoot>
                                        <tr>
                                            <td style="border: 1px solid;text-align:right;font-size:18px"
                                                colspan="6">Total Taka= </td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                {{ number_format((float) $total, 2, '.', '') }}</td>
                                        </tr>
                                    </tfoot>

                                </table> --}}
                                <table width="100%"
                                    style="border-collapse: collapse;border: 1px solid;margin-left:40px; font-size: 14px;padding-top:0;text-align:center;font-size:14px">
                                    <tr>
                                        <td style="border: 1px solid;width:10%; font-size:18px">Tender Ser No</td>
                                        <td style="border: 1px solid;width:15%;font-size:18px">PVMS</td>
                                        <td style="border: 1px solid;width:30%;font-size:18px">Nomenclature</td>
                                        <td style="border: 1px solid;width:5%;font-size:18px">A/U</td>
                                        <td style="border: 1px solid;width:10%;font-size:18px">Rate</td>
                                        <td style="border: 1px solid;width:15%;font-size:18px">Qty</td>
                                        <td style="border: 1px solid;width:15%;font-size:18px">Total Cost</td>
                                    </tr>
                                    <?php $total = 0; ?>
                                    @foreach ($notesheetPVMS as $k => $notesheetPvms)
                                        <tr>
                                            <td style="border: 1px solid;font-size:18px">
                                                {{ $notesheetPvms->tender_ser_no ?? '' }} S
                                            </td>
                                            <td style="border: 1px solid;font-size:18px">
                                                {{ $notesheetPvms->pvms_id ?? '' }}
                                            </td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                {{ $notesheetPvms->nomenclature ?? '' }}
                                            </td>
                                            <td style="border: 1px solid;font-size:18px">
                                                {{ $notesheetPvms->acc_name ?? '' }}
                                            </td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                {{ $notesheetPvms->price ?? '' }}
                                            </td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                {{ $notesheetPvms->sum ?? '' }}
                                            </td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                <?php
                                                    $price = $notesheetPvms->price ?? 0;
                                                    $qty = $notesheetPvms->sum ?? 0;
                                                    $taka = number_format((float) ($qty * $price), 2, '.', '');
                                                    $total += $taka;
                                                ?>
                                                {{ $taka }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tfoot>
                                        <tr>
                                            <td style="border: 1px solid;text-align:right;font-size:18px" colspan="6">
                                                Total Taka =
                                            </td>
                                            <td style="border: 1px solid;text-align:left;font-size:18px">
                                                {{ number_format((float) $total, 2, '.', '') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>

                            @else
                               <table width="100%"
                                    style="border-collapse: collapse;border: 1px solid;margin-left:40px; font-size: 14px;padding-top:0;text-align:center;font-size:14px">
                                    <tr>
                                        <td style="border: 1px solid;width:5%">Ser</td>
                                        <td style="border: 1px solid;width:15%">PVMS</td>
                                        <td style="border: 1px solid;width:40%">Nomenclature</td>
                                        <td style="border: 1px solid;width:20%">User/Dmd Unit</td>
                                        <td style="border: 1px solid;width:10%">A/U</td>
                                        <td style="border: 1px solid;width:10%;">Qty</td>
                                    </tr>
                                    @foreach ($notesheetPVMS as $k => $notesheetPvms)
                                        <tr>
                                            <td style="border: 1px solid;">{{ $k + 1 }}</td>
                                            <td style="border: 1px solid;">{{ $notesheetPvms->pvms_id }}</td>
                                            <td style="border: 1px solid;text-align:left">{{ $notesheetPvms->nomenclature ?? '' }}</td>
                                            <td style="border: 1px solid;">
                                                @if (!empty($notesheetPvms->dmd))
                                                    <?php $arr = []; ?>
                                                    @foreach ($notesheetPvms->dmd as $index => $dmd)
                                                        @if (!in_array($dmd, $arr))
                                                            {{ $dmd }}@if ($index < count($notesheetPvms->dmd) - 1), @endif
                                                            <?php $arr[] = $dmd; ?>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{ $notesheetPvms->sub_name ?? '' }}
                                                @endif
                                            </td>
                                            <td style="border: 1px solid;">{{ $notesheetPvms->acc_name ?? '' }}</td>
                                            <td style="border: 1px solid;text-align:right">{{ $notesheetPvms->sum ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </table>

                            @endif

                            <br><br><br>
                            <table width="100%">
                                <tr width="100%">
                                    <td style="" width="40%">&nbsp;</td>
                                    <td width="33%">&nbsp;</td>

                                    {{-- @if (!empty($gso_1->sign))
                                        <td width="27%" style="text-align: left; font-size: 22px">
                                            &nbsp;&nbsp;
                                            <img src="{{ asset('sign/' . $gso_1->sign) }}" style="width: 100px; height: 45px;" />
                                            <br>
                                            মোঃ মশিউর রহমান <br> লে. কর্নেল <br> এডিজিএমএস (স্টোরস) <br>
                                            <span style="font-size: 14px">{{ date('d M Y') }}</span>
                                        </td>
                                    @endif --}}
                                </tr>
                            </table>

                            <table width="100%">
                                <tr>
                                    <td style="font-size: 22px">উপ মহাপরিচালক</td>
                                    <td width="50%" style="font-size: 20px"> ৩</td>
                                </tr>
                                @if (isset($notesheetApp) && !empty($notesheetApp))
                                    @foreach ($notesheetApp as $noteApp)
                                        @if ($noteApp->role_name == 'ddgms')
                                            <tr>
                                                <td colspan="2" style="float: left;text-align:left">
                                                    {{ $noteApp->note }}

                                                    <br>
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
                                                    {{-- @if (isset($noteApp->sign) && !empty($noteApp->sign))
                                                        <img src="{{ asset('sign/' . $noteApp->sign) }}"
                                                            style="width: 100px;height:45px" />
                                                    @endif --}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </table>
                            <br> <br> <br>
                            <table width="100%">
                                <tr>
                                    <td style="font-size: 22px">কনসালটেন্ট ফিজিশিয়ান জেনারেল</td>
                                    <td width="50%" style="font-size: 20px"> ৪</td>
                                </tr>
                                @if (isset($notesheetApp) && !empty($notesheetApp))
                                    @foreach ($notesheetApp as $noteApp)
                                        @if ($noteApp->role_name == 'cpg')
                                            <tr>
                                                <td colspan="2" style="float: left;text-align:left">
                                                    {{ $noteApp->note }}
                                                    <br>
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
                                                    {{-- @if (isset($noteApp->sign) && !empty($noteApp->sign))
                                                        <img src="{{ asset('sign/' . $noteApp->sign) }}"
                                                            style="width: 100px;height:45px" />
                                                    @endif --}}
                                                </td>

                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </table>
                            <br> <br> <br>
                            <table width="100%">
                                <tr>
                                    <td style="font-size: 22px">মহাপরিচালক</td>
                                    <td width="50%" style="font-size: 20px"> ৫</td>
                                </tr>
                                @if (isset($notesheetApp) && !empty($notesheetApp))
                                    @foreach ($notesheetApp as $noteApp)
                                        @if ($noteApp->role_name == 'dgms')
                                            <tr>
                                                <td colspan="2" style="float: left;text-align:left">
                                                    {{ $noteApp->note }}
                                                    <br>
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                                    &nbsp;&nbsp; &nbsp;
                                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;
                                                    {{-- @if (isset($noteApp->sign) && !empty($noteApp->sign))
                                                        <img src="{{ asset('sign/' . $noteApp->sign) }}"
                                                            style="width: 100px;height:45px" />
                                                    @endif --}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </table>
                            <br> <br>
                            <table width="100%">
                                <tr>
                                    <td style="font-size: 20px; text-align: center"> সীমিত</td>
                                </tr>
                            </table>
                            <br> <br>
                        </td>
                        <td width="10%" style="vertical-align:top;border: 1px solid;font-size: 20px">
                            সংলগ্নী নং
                            <table>
                                <tr>
                                    <td style="padding-top: 100px;font-size: 18px">

                                        @php
                                            $notesheetDemandUUIDs = [];
                                        @endphp

                                        @foreach ($notesheetPVMS as $k => $notesheetDemand)
                                            @php
                                                if (in_array($notesheetDemand->uuid, $notesheetDemandUUIDs)) {
                                                    continue;
                                                } else {
                                                    $notesheetDemandUUIDs[] = $notesheetDemand->uuid;
                                                }
                                            @endphp

                                            @if ($k == 0)
                                                ১ A
                                            @elseif ($k == 1)
                                                <br>১ B
                                            @elseif ($k == 2)
                                                <br>১ C
                                            @elseif ($k == 3)
                                                <br>১ D
                                            @elseif ($k == 4)
                                                <br>১ E
                                            @elseif ($k == 5)
                                                <br>১ F
                                            @elseif ($k == 6)
                                                <br>১ G
                                            @elseif ($k == 7)
                                                <br>১ H
                                            @elseif ($k == 8)
                                                <br>১ I
                                            @elseif ($k == 9)
                                                <br>১ J
                                            @elseif ($k == 10)
                                                <br>১ K
                                            @elseif ($k == 11)
                                                <br>১ L
                                            @elseif ($k == 12)
                                                <br>১ M
                                            @elseif ($k == 13)
                                                <br>১ N
                                            @elseif ($k == 14)
                                                <br>১ O
                                            @elseif ($k == 15)
                                                <br>১ P
                                            @elseif ($k == 16)
                                                <br>১ Q
                                            @elseif ($k == 17)
                                                <br>১ R
                                            @elseif ($k == 18)
                                                <br>১ S
                                            @elseif ($k == 19)
                                                <br>১ T
                                            @elseif ($k == 20)
                                                <br>১ U
                                            @elseif ($k == 21)
                                                <br>১ V
                                            @elseif ($k == 22)
                                                <br>১ W
                                            @elseif ($k == 23)
                                                <br>১ X
                                            @elseif ($k == 24)
                                                <br>১ Y
                                            @elseif ($k == 25)
                                                <br>১ Z
                                            @endif
                                            &nbsp;
                                        @endforeach
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>
