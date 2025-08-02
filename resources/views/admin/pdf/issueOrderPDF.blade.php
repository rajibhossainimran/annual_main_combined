<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta charset="utf-8" />
    <title>Issue Order</title>
    <style>
        .heading {
            text-align: center;
            text-transform: uppercase;
            font-weight: normal;
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
        .tableBorder td,
        .tableBorder th {
            border: 1px solid black;
            border-collapse: collapse;
            text-align: center;
        }

        th {
            font-weight: normal;
        }

        table.print-friendly {
            page-break-inside: avoid;
            font-size: 15px;
        }

        .text-normal {
            font-size: 15px;
            font-weight: normal;
        }

        .sign-image {
            width: 100px;
            height: 45px;
        }

        .restricted-text {
            text-align: center;
            text-transform: uppercase;
            font-size: 15px;
            font-weight: normal;
        }
    </style>
</head>

<body style="text-align:justify;font-size:20px">
    <p class="restricted-text">RESTRICTED</p><br>

    <table class="table1">
        <tr><td></td><td class="wd-30 text-normal">Directorate General of Military Medical Services</td></tr>
        <tr><td></td><td class="wd-37 text-normal">Ministry of Defence</td></tr>
        <tr><td></td><td class="wd-37 text-normal">Dhaka Cantt</td></tr>
        <tr><td></td><td class="wd-37 text-normal">Tel: 8711111 Ext: 4194</td></tr>
        <tr><td></td><td class="wd-37 text-normal">Fax: 9834496</td></tr>
        <tr><td></td><td class="wd-37 text-normal">Email: dgmsstorebr@gmail.com</td></tr>
    </table>

    <table class="table2">
        <tr>
            <td style="width:60%;" class="text-normal">{{ $purchaseDetails['purchase_number'] ?? '' }}</td>
            <td class="text-normal" style="width:40%">
                {{ isset($purchaseDetails['created_at']) ? \Carbon\Carbon::parse($purchaseDetails['created_at'])->format('d F Y') : '' }}
            </td>
        </tr>
        <tr>
            <td class="upper" style="text-decoration: underline;padding-top: 15px;padding-bottom: 10px;font-size:13px">
                Issue Order of Medical Supplies - {{ $subOrganizationName ?? '' }}
            </td>
            <td class="wd-20"></td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="text-normal" style="padding-top: 10px; padding-bottom: 10px;">
                {{ strip_tags($purchaseDetails['top_details'] ?? '') }}
            </td>
        </tr>
    </table>

    <table class="tableBorder" style="margin-left:20px; font-size:15px">
        <tr>
            <th width="5%">Ser</th>
            <th width="12%">PVMS No</th>
            <th width="30%">Nomenclature</th>
            <th width="5%">A/U</th>
            <th width="9%">Total Qty</th>
            <th width="10%">Remarks</th>
        </tr>
        @foreach ($purchase->purchaseTypes ?? [] as $data)
            <tr>
                <td class="text-normal">{{ $loop->iteration }}</td>
                <td class="text-normal">{{ $data['pvms']['pvms_id'] ?? '' }}</td>
                <td class="text-normal">{{ $data['pvms']['nomenclature'] ?? '' }}</td>
                <td class="text-normal">{{ $data['pvms']['accountUnit']['name'] ?? '' }}</td>
                <td class="text-normal">{{ $data['request_qty'] ?? '' }}</td>
                @if ($loop->first)
                    <td rowspan="{{ $count ?? 1 }}"></td>
                @endif
            </tr>
        @endforeach
    </table>

    <table>
        <tr>
            <td class="text-normal" style="padding-top: 15px; padding-bottom: 10px;">
                {{ strip_tags($purchaseDetails['bottom_details'] ?? '') }}
            </td>
        </tr>
    </table>

    <table width="100%" class="print-friendly">
        <tr><td colspan="2" style="height: 40px;"></td></tr>

        @if (isset($OIC))
            <tr>
                <td style="width: 67%"></td>
                <td style="width:33%">
                    @if (!empty($purchaseDetails['sign']))
                        <img src="{{ public_path('sign/' . $purchaseDetails['sign']) }}" class="sign-image" />
                    @endif
                </td>
            </tr>
            @if (!empty($purchaseDetails['name']))
                <tr>
                    <td></td>
                    <td style="font-size:15px;font-weight:bold">{{ strip_tags($purchaseDetails['name'] ?? '') }}</td>
                </tr>
            @endif
            @if (!empty($purchaseDetails['rank']))
                <tr>
                    <td></td>
                    <td style="font-size:15px">{{ strip_tags($purchaseDetails['rank'] ?? '') }}</td>
                </tr>
            @endif
        @endif

        {{-- <tr><td></td><td>For Comdt</td></tr> --}}
        <tr><td class="small-text">Extl :</td><td class="small-text"></td></tr>
        <tr><td colspan="2" style="height: 10px;"></td></tr>
        <tr><td class="small-text">Distr :</td><td class="small-text"></td></tr>
        <tr><td>Extl :</td><td></td></tr>
        <tr><td style="padding-bottom: 15px"></td><td></td></tr>
        <tr><td>AFMSD</td><td></td></tr>
        <tr><td>Dhaka Cantt</td><td></td></tr>
        <tr><td colspan="2" style="height: 10px;"></td></tr>
        <tr><td class="small-text">Act :</td><td class="small-text"></td></tr>

        <tr>
            <td class="small-text">{{ $subOrganizationName ?? '' }}</td>
            <td>
                @foreach ($approved ?? [] as $appr)
                    @if (!empty($appr->sign) && $appr->role_name === 'mo')
                        <img src="{{ public_path('sign/' . $appr->sign) }}" class="sign-image" />
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="small-text">Dhaka Cantt</td>
            <td>
                @if (!empty($clerk->sign))
                    <img src="{{ public_path('sign/' . $clerk->sign) }}" class="sign-image" />
                @endif
            </td>
        </tr>

        <tr>
            <td colspan="2" style="text-align: center;text-transform: uppercase;">
                <br><br>
                <p class="restricted-text">RESTRICTED</p>
            </td>
        </tr>
    </table>
</body>

</html>
