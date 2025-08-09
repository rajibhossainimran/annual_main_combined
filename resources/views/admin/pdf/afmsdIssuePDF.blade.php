<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Demand and Issue Voucher</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'nikosh', sans-serif;
            padding: 30px;
            position: relative;
        }
      
        .bordered {
            border: 1px solid #000;
            background-color: #fff;
            position: relative;
            z-index: 1;
        }

        .section-title {
            font-weight: bold;

            color: #363636 text-transform: uppercase;
        }

        .voucher-title {
            text-align: center;
            font-style: italic;
        }

      

        #voucher-table {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #voucher-table td,
        #voucher-table th {
            border: 1px solid #a4a4a4;
            padding: 8px;
        }

        #voucher-table tr {
            border: 1px solid #696969;
        }

        #voucher-table tr:nth-child(even) {
            background-color: #f4f4f4;
        }

        #voucher-table tr:hover {
            background-color: #b8afaf;
        }

        #voucher-table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #ebebeb;
            color: rgb(41, 41, 41);
        }

        .logo-title {
            text-align: center;
            /* center the whole block */
            margin-top: 8px;
            white-space: nowrap;
            /* prevent wrapping */
        }

        .logo-title img {
            height: 40px !important;
            width: 40px !important;
            vertical-align: middle;
            margin-right: 10px;
        }

        .logo-title h5 {
            display: inline-block;
            vertical-align: middle;
            margin: 0;
        }
    </style>
</head>

<body>

  

    <!-- Main Voucher -->
    <div class="container bordered p-4" style="z-index: 1; position: relative;">


        <div class="logo-title">
            <img src="{{ public_path('admin/img/afmsd.png') }}" alt="AFMSD Logo" style="height:40px; width: 40px;">
            <h5 class="section-title">Armed Forces Medical Store Depot</h5>
        </div>
        <p class="voucher-title">(Issue Voucher)</p>
        <!-- Voucher Details -->
        <div class="row mb-3">

            <div class="col-md-6"><strong>Voucher No:</strong> <u>{{ $data[0]->purchase_number }}</u></div>
            <div class="col-md-6 text-end"> <strong>Date:</strong> {{ date('d-m-Y') }}</div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 mb-2"><strong>From: </strong> AFMSD</div>
            <div class="col-md-6"><strong>To: </strong>{{$data[0]->subOrganization->name}}</div>
        </div>

        <div class="row mb-2">
            <div class="col-md-12">
                The following items have been issued to <b> {{$data[0]->subOrganization->name}} </b> Voucher No: <b>
                    {{ $data[0]->purchase_number }} </b> Dated:
                <b>{{$data[0]->updated_at->format('d-m-Y')}}</b>
            </div>
        </div>


        {{--
        <pre>
    {{ json_encode($data[0], JSON_PRETTY_PRINT) }}
</pre> --}}
        <!-- Table -->

        <table id="voucher-table" class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>S/No</th>
                    <th>PVMS</th>
                    <th>Nomenclature</th>
                    <th>AU</th>
                    <th>Qty Approve</th>
                    <th>Qty Issued</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data[0]->purchaseTypes ?? [] as $index => $purchaseType)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $purchaseType->pvms->pvms_id }}</td>
                        <td>{{ $purchaseType->pvms->nomenclature}}</td>
                        <td>{{ $purchaseType->pvms->accountUnit->name ?? 'N/A' }}</td>
                        <td>{{ $purchaseType->request_qty }}</td>
                        <td>{{ $purchaseType->purchaseDelivery->sum('delivered_qty') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


        <!-- Footer Signatures -->
      

        <div style="text-align: right; font-family: 'Nikosh', sans-serif; font-size: 14px; line-height: 1.6; margin-top: 20px;">
    <p style="margin: 4px 0;">ভাউচারে বর্ণিত দেওয়া মেডিসিন যন্ত্রপাতি সঠিকভাবে বুঝে পেলাম।</p>
    <p style="margin: 8px 0;">নং ..............................................</p>
    <p style="margin: 8px 0;">পদবী ............................................</p>
    <p style="margin: 8px 0;">নাম..............................................</p>
    <p style="margin: 8px 0;">ইউনিট............................................</p>
    <p style="margin: 8px 0;">স্বাক্ষর.............................................</p>
    <p style="margin: 8px 0;">তারিখ.............................................</p>
</div>
    </div>

</body>

</html>