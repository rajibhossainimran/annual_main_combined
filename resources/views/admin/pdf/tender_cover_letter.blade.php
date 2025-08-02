<?php use App\Services\BanglaDate;
//$bn = new BanglaDate(time());

?>
    <!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <meta charset="utf-8" />
    <title>Tender Cover Letter</title>
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
        font-size: 17px;
        margin-bottom: 2px;
        margin-top: 2px;

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
            margin: 40px 50px !important;
            /* padding: 0 !important; */
        }
</style>

<body style="paddding-left: 15px">
    {!! $tender->details !!}
    <br><br>
    <br>
    <table style="padding-left: 15%">
        <tr>
            <td style="font-size: 17px; width:15%;text-align:right">
                সভাপতি :
            </td>
            <td style="width:8%">

            </td>
            <td style="font-size: 15px">
                {{$president->name}}
            </td>
        </tr>
        <tr>
            <td style="font-size: 22px; width:15%;text-align:right">

            </td>
            <td style="width:8%">

            </td>
            <td style="font-size: 15px">
                {{$president->rank}}
            </td>
        </tr>
        <tr>
            <td style="font-size: 22px; width:15%;text-align:right">

            </td>
            <td style="width:8%">

            </td>
            <td style="font-size: 15px">
                <span style="font-size: 17px">সামরিক চিকিৎসা সার্ভিস মহাপরিদপ্তর</span>
            </td>
        </tr>
    </table>
    <br>
    <br><br>
    <table style="padding-left: 15%">
        @foreach($member as $k=>$m)
        <tr>
            <td style="font-size: 17px; width:15%;text-align:right">
                @if($k==0)
                সদস্য :
                @endif
            </td>
            <td style="width:8%;text-align: center;font-size: 14px">
                {{$k+1}}<span style="font-size: 17px">।</span>
            </td>
            <td style="font-size: 15px">
                {{$m->name}}
            </td>
        </tr>
        <tr>
            <td style="font-size: 22px; width:15%;text-align:right">

            </td>
            <td style="width:8%">

            </td>
            <td style="font-size: 15px">
                {{$m->rank}}
            </td>
        </tr>
        <tr style="padding-bottom: 20px">
            <td style="font-size: 22px; width:15%;text-align:right">

            </td>
            <td style="width:8%">

            </td>
            <td style="font-size: 18px;padding-bottom: 25px">
                <span style="font-size: 17px">সামরিক চিকিৎসা সার্ভিস মহাপরিদপ্তর</span>
            </td>
        </tr>
        @endforeach
    </table>

    <table style="padding-left: 11%">
        @foreach($co_member as $k=>$m)
        <tr>
            <td style="font-size: 17px; width:16%;text-align:right">
                @if($k==0)
                কো-অপটেড সদস্য :
                @endif
            </td>
            <td style="width:8%;text-align: center;font-size: 14px">
                {{$k+1}}<span style="font-size: 17px">।</span>
            </td>
            <td style="font-size: 15px;width: 74%">
                {{$m->name}}
            </td>
        </tr>
        <tr>
            <td style="font-size: 22px; width:15%;text-align:right">

            </td>
            <td style="width:8%">

            </td>
            <td style="font-size: 15px;padding-bottom: 15px">
                {{$m->rank}}
            </td>
        </tr>

        @endforeach
    </table>
    <span style="font-size: 17px">পর্ষদ সভাপতির আদেশক্রমে একত্রিত হইয়া কাৰ্য্য শুরু করিতেছে । </span>
</body>
</html>


