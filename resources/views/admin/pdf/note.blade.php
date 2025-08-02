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
                        <td style="text-align: center;">NOTE SHEET</td>
                    </tr>
                    <tr style="text-align: center">
                        <td style="text-align: center; font-size: 22px">মন্তব্য পত্র</td>
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
                                    <td style="text-align: center;font-size: 16px"> সীমিত</td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td>{{ $notesheet->notesheet_id }}</td>
                                </tr>
                            </table>
                            <table>
                                <tr>
                                    <td style="font-size: 17px; text-decoration: underline">সশস্ত্র বাহিনীর রোগীদের জন্য
                                        জরুরী ঔষধ সমূহ ক্রয় করণ প্রসংগে</td>
                                </tr>
                            </table>
                            <table width="100%">
                                <tr style="text-align: center">
                                    <td style="font-size: 17px; text-align: center"> ১</td>
                                </tr>
                            </table>
                           <table width="100%">
                                <tr>
                                    <td style="font-size: 14px; text-align: left">
                                        @php
                                            $notesheetDemandUUIDs = [];
                                            $alphabets = range('A', 'Z');
                                            $letterIndex = 0;
                                        @endphp

                                        @foreach ($notesheetPVMS as $notesheetDemand)
                                            @php
                                                $uuid = $notesheetDemand->uuid ?? null;
                                                $subName = $notesheetDemand->sub_name ?? null;
                                                $demandDate = $notesheetDemand->demand_date ?? null;
                                            @endphp

                                            @if (!empty($uuid) && !in_array($uuid, $notesheetDemandUUIDs))
                                                @php $notesheetDemandUUIDs[] = $uuid; @endphp

                                                {{ $alphabets[$letterIndex++] ?? '' }}.
                                                &nbsp;&nbsp;&nbsp;&nbsp;

                                                @if (!empty($subName))
                                                    {{ $subName }} Letter No - {{ $uuid }}
                                                @else
                                                    Letter No - {{ $uuid }}
                                                @endif

                                                @if (!empty($demandDate))
                                                    &nbsp;&nbsp;Dated - {{ date('d M Y', strtotime($demandDate)) }}
                                                @endif

                                                <br>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            </table>

                            <table width="100%">
                                <tr style="text-align: center">
                                    <td style="font-size: 17px; text-align: center"> ২</td>
                                </tr>
                            </table>
                            <table width="100%" style="overflow: wrap; letter-spacing: 0;">
                                <tr>
                                    <td
                                        style="font-size: 17px; text-align: justify-all;letter-spacing: 0;width:100%;padding-bottom: 0px">
                                        {!! $notesheet->notesheet_details !!}
                                    </td>
                                </tr>
                            </table>
                            @if ($notesheet->is_rate_running == 1)
                                <table width="100%"
                                    style="border-collapse: collapse;border: 1px solid;margin-left:40px; font-size: 14px;padding-top:0;text-align:center;font-size:14px">
                                    <tr>
                                        <td style="border: 1px solid;width:10%;">Tender Ser No</td>
                                        <td style="border: 1px solid;width:10%">PVMS</td>
                                        <td style="border: 1px solid;width:30%">Nomenclature</td>
                                        <td style="border: 1px solid;width:10%">A/U</td>
                                        <td style="border: 1px solid;width:10%">Rate</td>
                                        <td style="border: 1px solid;width:15%;">Qty</td>
                                        <td style="border: 1px solid;width:15%">Total Cost</td>
                                    </tr>

                                    @php $total = 0; @endphp

                                    @foreach ($notesheetPVMS as $k => $notesheetPvms)
                                        @php
                                            $tender_ser_no = $notesheetPvms->tender_ser_no ?? '';
                                            $pvms_id = $notesheetPvms->pvms_id ?? '';
                                            $nomenclature = $notesheetPvms->nomenclature ?? '';
                                            $acc_name = $notesheetPvms->acc_name ?? '';
                                            $price = $notesheetPvms->price ?? 0;
                                            $sum = $notesheetPvms->sum ?? 0;
                                            $taka = number_format((float) $sum * $price, 2, '.', '');
                                            $total += $taka;
                                        @endphp

                                        <tr>
                                            <td style="border: 1px solid;">{{ $tender_ser_no }}</td>
                                            <td style="border: 1px solid;">{{ $pvms_id }}</td>
                                            <td style="border: 1px solid; text-align:left">{{ $nomenclature }}</td>
                                            <td style="border: 1px solid;">{{ $acc_name }}</td>
                                            <td style="border: 1px solid; text-align:left">{{ $price != 0 ? $price : '' }}</td>
                                            <td style="border: 1px solid; text-align:left">{{ $sum != 0 ? $sum : '' }}</td>
                                            <td style="border: 1px solid; text-align:left">{{ $taka != 0 ? $taka : '' }}</td>
                                        </tr>
                                    @endforeach

                                    <tfoot>
                                        <tr>
                                            <td style="border: 1px solid;text-align:right" colspan="6">Total Taka=</td>
                                            <td style="border: 1px solid;text-align:left">
                                                {{ number_format((float) $total, 2, '.', '') }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>

                            @else

                                @php $grandTotal = 0; @endphp

                                    @foreach ($notesheetPVMS as $k => $notesheetPvms)
                                        @php
                                            $totalQuantity = $notesheetPvms->sum ?? 0;
                                            $unitPrice = $notesheetPvms->unit_price ?? 0;
                                            $totalPrice = $totalQuantity * (float)$unitPrice;
                                            $grandTotal += $totalPrice;
                                        @endphp
                                    @endforeach   
                                <table width="100%" style="border-collapse: collapse; border: 1px solid; margin-left:20px; font-size: 14px; padding-top:0; text-align:center;">
                                        <tr>
                                            <td style="border: 1px solid; width:5%">Ser</td>
                                            <td style="border: 1px solid; width:10%">PVMS</td>
                                            <td style="border: 1px solid; width:35%">Nomenclature</td>
                                            <td style="border: 1px solid; width:10%">User/Dmd Unit</td>
                                            <td style="border: 1px solid; width:5%">A/U</td>
                                            <td style="border: 1px solid; width:10%">Approx Price</td>
                                            <td style="border: 1px solid; width:10%">Qty</td>
                                            <td style="border: 1px solid; width:15%">Total Cost</td>
                                        </tr>

                                        @foreach ($notesheetPVMS as $k => $notesheetPvms)
                                             @php
                                            $totalQuantity = $notesheetPvms->sum ?? 0;
                                            $unitPrice = $notesheetPvms->unit_price ?? 0;
                                            $totalPrice2 = $totalQuantity * (float)$unitPrice;
                                        @endphp
                                            <tr>
                                                <td style="border: 1px solid;">{{ $k + 1 }}</td>

                                                <td style="border: 1px solid;">
                                                    {{ $notesheetPvms->pvms_id ?? '' }}
                                                </td>

                                                <td style="border: 1px solid; text-align:left;">
                                                    {{ $notesheetPvms->nomenclature ?? '' }}
                                                </td>

                                                <td style="border: 1px solid;">
                                                    @if (!empty($notesheetPvms->dmd) && is_array($notesheetPvms->dmd))
                                                        @php $shown = []; @endphp
                                                        @foreach ($notesheetPvms->dmd as $index => $dmd)
                                                            @if (!in_array($dmd, $shown))
                                                                {{ $dmd }}@if ($index < count($notesheetPvms->dmd) - 1), @endif
                                                                @php $shown[] = $dmd; @endphp
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{ $notesheetPvms->sub_name ?? '' }}
                                                    @endif
                                                </td>

                                                <td style="border: 1px solid;">
                                                    {{ $notesheetPvms->acc_name ?? '' }}
                                                </td>

                                                <td style="border: 1px solid; text-align:right;">
                                                    {{ $notesheetPvms->unit_price ?? '' }}&nbsp;
                                                </td>

                                                <td style="border: 1px solid; text-align:center;">
                                                    {{ $notesheetPvms->sum ?? '' }}&nbsp;
                                                </td>
                                                 <td style="border: 1px solid; text-align:right;">
                                                    {{  number_format($totalPrice2, 2) }}&nbsp;
                                                </td>
                                            </tr>
                                        @endforeach
                                        {{-- Grand Total Row --}}
                                        <tr>
                                            <td colspan="7" style="text-align:right;  border: 1px solid; padding:8px 0;"> Total Taka=</td>
                                            <td style="text-align:right;  border: 1px solid;">
                                                {{ number_format($grandTotal, 2) ?? '' }}
                                            </td>
                                        </tr>
                                        
                                    </table>



                            @endif
                            <table width="100%" style="overflow: wrap; letter-spacing: 0;">
                                <tr>
                                    <td
                                        style="font-size: 17px; text-align: justify-all;letter-spacing: 0;width:100%;padding-bottom: 0px">
                                        {!! $notesheet->notesheet_details1 !!}
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <table width="100%">
                                <tr width="100%">
                                    <td style="" width="40%">
                                        &nbsp;
                                    </td>
                                    <td width="33%">&nbsp;</td>
                                    <td width="27%" style="text-align: left; font-size: 17px">
                                        &nbsp;&nbsp;
                                        @if (isset($notesheetApp) && !empty($notesheetApp))
                                            @foreach ($notesheetApp as $noteApp)
                                                @if ($noteApp->role_name == 'gso-1')
                                                    @if (isset($gso_1->sign) && !empty($gso_1->sign))
                                                        <img src="{{ asset('sign/' . $gso_1->sign) }}"
                                                            style="width: 100px;height:45px" />
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif

                                        <br>
                                        মোঃ মশিউর রহমান <br> লে. কর্নেল <br> এডিজিএমএস (স্টোরস) <br> <span
                                            style="font-size: 14px">
                                            @if ($notesheet->notesheet_date)
                                                {{ date('d M Y', strtotime($notesheet->notesheet_date)) }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <table width="100%">
                                <tr>
                                    <td style="font-size: 17px;text-decoration: underline">উপ মহাপরিচালক</td>
                                    <td width="50%" style="font-size: 17px"> ৩</td>
                                </tr>
                                @if (isset($notesheetApp) && !empty($notesheetApp))
                                    @foreach ($notesheetApp as $k => $noteApp)
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
                                                    @if (isset($noteApp->sign) && !empty($noteApp->sign))
                                                        <img src="{{ asset('sign/' . $noteApp->sign) }}"
                                                            style="width: 100px;height:45px" />

                                                        <span
                                                            style="font-size: 14px;float:right;text-align:right">{{ date('d M Y', strtotime($noteApp->created_at)) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </table>
                            <br> <br><br>
                            <table width="100%">
                                <tr>
                                    @if ($notesheet->notesheet_item_type == 5)
                                        <td style="font-size: 17px;text-decoration: underline">কনসালটেন্ট সার্জন জেনারেল
                                        </td>
                                    @else
                                        <td style="font-size: 17px;text-decoration: underline">কনসালটেন্ট ফিজিশিয়ান
                                            জেনারেল</td>
                                    @endif
                                    <td width="50%" style="font-size: 17px"> ৪</td>
                                </tr>
                                @if ($notesheet->notesheet_item_type == 5)
                                    @if (isset($notesheetApp) && !empty($notesheetApp))
                                        @foreach ($notesheetApp as $noteApp)
                                            @if ($noteApp->role_name == 'csg')
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
                                                        @if (isset($noteApp->sign) && !empty($noteApp->sign))
                                                            <img src="{{ asset('sign/' . $noteApp->sign) }}"
                                                                style="width: 100px;height:45px" />

                                                            <span
                                                                style="font-size: 14px;float:right;text-align:right">{{ date('d M Y', strtotime($noteApp->created_at)) }}</span>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                @else
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
                                                        @if (isset($noteApp->sign) && !empty($noteApp->sign))
                                                            <img src="{{ asset('sign/' . $noteApp->sign) }}"
                                                                style="width: 100px;height:45px" />

                                                            <span
                                                                style="font-size: 14px;float:right;text-align:right">{{ date('d M Y', strtotime($noteApp->created_at)) }}</span>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                @endif

                            </table>
                            <br> <br><br>
                            <table width="100%">
                                <tr>
                                    <td style="font-size: 17px;text-decoration: underline">মহাপরিচালক</td>
                                    <td width="50%" style="font-size: 17px"> ৫</td>
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
                                                    @if (isset($noteApp->sign) && !empty($noteApp->sign))
                                                        <img src="{{ asset('sign/' . $noteApp->sign) }}"
                                                            style="width: 100px;height:45px" />

                                                        <span
                                                            style="font-size: 14px;float:right;text-align:right">{{ date('d M Y', strtotime($noteApp->created_at)) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </table>
                            <br> <br><br><br>
                            <table width="100%">
                                <tr>
                                    <td style="font-size: 17px; text-align: center"> সীমিত</td>
                                </tr>
                            </table>
                            <br> <br>
                        </td>
                        <td width="10%" style="vertical-align:top;border: 1px solid;">
                            সংলগ্নী নং
                            <table>
                                <tr>
                                    <td style="padding-top: 88px">
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
