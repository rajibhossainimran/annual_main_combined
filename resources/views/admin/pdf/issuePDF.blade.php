<?php use App\Services\BanglaDate;
//$bn = new BanglaDate(time());
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta charset="utf-8" />
    <title>Demand Issue</title>
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
        font-size: 17px
    }

    .tcenter {
        text-align: center;
    }
</style>

<body>
    <div>
        <p class="textJust" style="padding-bottom: 10px;padding-top: 20px">
            {{-- {!! $tender->details !!} --}}
        </p>
        <table class="tableBorder">
            <tr>
                <td width="37%" rowspan="2" style="text-align: left">চাহিদা পত্র অথবা প্রদান আদেশ নং
                    <br>
                    V.No: {{ $issue->purchase_number }}
                    <br>
                    Date:
                    <br>
                    পৃষ্ঠাসমূহের সংখ্যাঃ
                    <br>
                    পৃষ্ঠা নং

                </td>
                <td width="15%" colspan="2">চাহিদা পত্রের শ্রেণী বিন্যাস </td>
                <td width="12%" rowspan="2">ত্বরার প্রকারভেদ (সাধারণ/জরুরী)</td>
                <td width="7%" rowspan="2">সরবরাহ ভান্ডার (প্রেরক) AFMSD Dhaka Cantt</td>
                <td width="7%" colspan="1">তারিখ </td>
                <td width="7%" colspan="1">প্রদান ভাউচার নং </td>
                <td width="10%" colspan="1">প্রদানের শ্রেণী</td>

            </tr>
            <tr>
                <td>জরুরী সাধারণ প্রসারণ</td>
                <td>নিয়ন্ত্রিত প্রারম্ভিক পরিশোধনীয়</td>
                <td></td>
                <td></td>
                <td>প্রারম্ভিক রক্ষণাবেক্ষণ অন্যান্য ব্যবস্থাপনাদি</td>
            </tr>
            <tr>
                <td rowspan="3" style="text-align: left;">

                    চাহিদাকারী: {{ $issue->name }}
                    <br>
                    নিকটবর্তী রেলওয়ে ষ্টেশন:
                    <br>
                    <br>
                    দস্তখত:
                    <br><br>
                </td>
                <td width="" rowspan="3">ভান্ডার নির্বাচনকারী মোড়ককারী <br></td>
                <td width="" rowspan="3">অনুস্বাক্ষর</td>
                <td width="" rowspan="3">তারিখ </td>
                <td width="" rowspan="3">ভান্ডার খতিয়ান হিসাব প্রেরণকারী পরীক্ষাকারী</td>
                <td width="" rowspan="3">অনুস্বাক্ষর</td>
                <td width="" rowspan="3">তারিখ </td>
                <td width="" rowspan="3"> </td>

            </tr>
        </table>
        <p>প্রাধিকারঃ &nbsp;&nbsp;&nbsp; বিশেষ নির্দেশাবলীঃ </p>
        <table class="tableBorder">
            <tr>
                <td>১। </td>
                <td>২।</td>
                <td>৩।</td>
                <td>৪।</td>
                <td>৫।</td>
                <td>৬।</td>
                <td>৭।</td>
                <td>৮।</td>
                <td>৯।</td>
                <td>১০।</td>
                <td>১১।</td>
                <td>১২।</td>
                <td>১৩।</td>
            </tr>
            <tr>
                <td>ক্রমিক নং </td>
                <td>পিভিএমএস নং</td>
                <td width="30%">ভান্ডাবের বিস্তারিত বিবরণ</td>
                <td>হিসাবের একক </td>
                <td>মজুদের পরিমাণ </td>
                <td>বিগত তিন মাসের গড় খরচ</td>
                <td>চাহিদার পরিমাণ</td>
                <td>অনুমোদনের পরিমাণ</td>
                <td>প্রদানের পরিমাণ</td>
                <td>অনুগমনীয়</td>
                <td>বকেয়ার পরিমান</td>
                <td>মূল্য
                </td>
                <td>মন্তব্য </td>
            </tr>
            @foreach ($data as $k => $datum)
                <tr>
                    <td>{{ $k + 1 }}</td>
                    <td>{{ $datum->pvms_name }}</td>
                    <td>{{ $datum->nomenclature }}</td>
                    <td>{{ $datum->name }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ $datum->request_qty }}</td>
                    <td>{{ $datum->received_qty }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td> </td>
                </tr>
            @endforeach

        </table>
        <br>
        <table>
            <tr>
                <td width="50%">
                    <table class="">
                        <tr>
                            <td width="10%">প্রেরণের ভাউচার</td>
                            <td rowspan="7" width="15%">
                                <span style="font-size: 150px">{</span>
                            </td>
                            <td rowspan="7">

                                প্রেরণের তারিখ
                                <br>
                                সড়ক/রেলগাড়ী <br>
                                সামরিক জমা নং <br><br>
                                আর আর নং <br><br>
                                মোড়কের সংখ্যা <br><br>
                                (ক) দাহ্য
                                <br>
                                (খ) অদাহ্য
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="tableBorder">
                        <tr>
                            <td colspan="2" width="30%">ওজন ও পরিমান</td>
                            <td colspan="2">
                                মূল্য
                            </td>
                            <td width="20%">ভান্ডার প্রাপ্তি</td>
                            <td rowspan="3" width="20%">প্রাপ্তি রশিদ ভাউচার নং </td>
                            <td rowspan="3">তারিখ</td>
                        </tr>
                        <tr>
                            <td>কিলোগ্রাম</td>
                            <td>গ্রাম</td>
                            <td>টাকা</td>
                            <td>পয়সা</td>
                            <td rowspan="2" style="text-align: left">
                                স্বাক্ষর <br>
                                পদবী
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: left">
                                ঘনমিটার
                                <br>
                                জমা নং

                            </td>
                            <td colspan="2" style="text-align: left">কপি নং</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


    </div>
    </div>
</body>

</html>
