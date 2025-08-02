<?php use App\Services\BanglaDate;
//$bn = new BanglaDate(time());

?>
    <!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <meta charset="utf-8" />
    <title>Tender Notice</title>
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
    .table1 tr td{
        float: right;
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
    }
    .tableBorder, .tableBorder tr, .tableBorder td {
        border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
    }
    .tcenter{
        text-align: center;
    }
</style>

<body>
<table>
    <tr>
        <td width="20%">
            <img src="{{asset('admin/css/assets/images/logo.png')}}" width="80px">
        </td>
        <td width="60%" class="tcenter">
            <p class="bangla" style="text-decoration: underline">সামরিক চিকিৎসা সার্ভিস মহাপরিদপ্তর</p>
            <p class="bangla" style="text-decoration: underline">প্রতিরক্ষা মন্ত্রণালয়, ঢাকা সেনানিবাস </p>
            <p class="bangla" style="text-decoration: underline">জরুরী অনলাইন দরপত্র বিজ্ঞপ্তি </p>
        </td>
        <td width="20%"></td>
    </tr>
</table>
<div>
    <p class="textJust" style="padding-bottom: 10px;padding-top: 20px">
        {!! $tender->details !!}
    </p>
    <table class="tableBorder">
        <tr>
            <td width="5%" rowspan="2">ক্রমিক নং</td>
            <td width="40%" rowspan="2">দ্রব্যের বিবরণ</td>
            <td width="10%" colspan="2">সিডিউল বিক্রয়ের তারিখ </td>
            <td width="15%" rowspan="2">দরপত্র খোলার তারিখ</td>
            <td width="15%" rowspan="2">দরপত্র নম্বর</td>
        </tr>
        <tr>
            <td>হইতে</td>
            <td>পর্যন্ত</td>
        </tr>
        <?php $pvms_type_id = 0;
            $is_dental = 0;
            ?>
        @foreach($tender->tenderCsr as $k=>$tend)
            <!-- commmented on May 14, 25 Abdullah -->
            <!-- @if($k == 0) -->
            <?php // $pvms_type_id = $tend->PVMS->item_types_id;
                  //  $is_dental = $tend->csrDemands[0]->notesheet->is_dental;
            ?>
            <!-- @endif -->  

            <tr>
                <td>{{$k+1}}</td>
                <td style="text-align: left">{{$tend->PVMS->nomenclature}}</td>
                @if($k == 0)
                <td rowspan="{{count($tender->tenderCsr)}}">{{date('d-m-Y',strtotime($tender->start_date))}}</td>
                <td rowspan="{{count($tender->tenderCsr)}}">{{date('d-m-Y',strtotime($tender->deadline))}}</td>
                <td rowspan="{{count($tender->tenderCsr)}}">{{date('d-m-Y',strtotime($tender->deadline))}}</td>
                <td rowspan="{{count($tender->tenderCsr)}}">{{$tender->tender_no}}</td>
                @endif
            </tr>
        @endforeach

    </table>
    <p style="padding-top: 20px" class="textJust">
        ৩।  &nbsp;&nbsp;&nbsp; অনলাইনে তারকা চিহ্নিত স্থানে দরপত্রের সাথে নিম্নলিখিত তথ্যাদি/ নথিপত্র সংযুক্ত করতে হবে :
    </p>
    <p style="padding-left: 40px">

            @foreach($tender->requiredFiles as $k=>$file)
                <span style="font-size: 14px">
                {{$k+1}}. &nbsp;&nbsp;
                </span>
                {{$file->requiredDocument->name}}
                <br>
            @endforeach
        {{-- <span>
            <span style="font-size: 14px">
                 {{count($tender->requiredFiles)+1}}. &nbsp;&nbsp;
            </span>
            আইটেমের নমুনা / অরিজিনাল ক্যাটালগ ।
            দরপত্রের হার্ড কপির সাথে MS Word এ প্রস্তুতকৃত সফট কপির সিডি।
        </span><br> --}}
        {{-- <span>
            <span style="font-size: 14px">
                 {{count($tender->requiredFiles)+2}}. &nbsp;&nbsp;
            </span>
            সিডিউল ক্রয়ের রশিদ।
        </span> --}}
</p>
<p style="padding-top: 5px" class="textJust">
৪।  &nbsp;&nbsp;&nbsp; কর্তৃপক্ষ কোন কারণ দর্শানো ছাড়াই অনলাইন দরপত্র বাতিল করার ক্ষমতা রাখেন ।
</p>
<p style="padding-top: 0px" class="textJust">
৫।  &nbsp;&nbsp;&nbsp; বর্ণিত দ্রব্যের বিস্তারিত বিবরণ অত্র মহাপরিদপ্তরের অভ্যর্থনায় রক্ষিত আছে ।
</p>
</div>
<br>
<br>
<br>
<div style="width: 100%">
<div style="width: 60%;float: left">
<p>তারিখঃ  <span style="font-size: 14px">{{date('d M Y', strtotime($tender->start_date))}}</span></p>
</div>
<div style="width:40%; float: right">
    {{-- @if($pvms_type_id == 1)
        <p style="font-size: 14px">CGO-1 </p>
    @elseif($pvms_type_id == 3)
        @if($is_dental == 1)
            <p style="font-size: 14px">CGO-1 </p>
        @else
            <p style="font-size: 14px">Corresponden & Control </p>
        @endif
    @elseif($pvms_type_id == 4)
        <p style="font-size: 14px">Planning & Purchase </p>
    @elseif($pvms_type_id == 5)
        <p style="font-size: 14px">Corresponden & Control </p>
    @endif --}}
    @if(isset($user->sign) && !empty($user->sign))
        <img src="{{asset('sign/'.$user->sign)}}" style="width: 100px;height:45px"/>
    @endif
    <p>রাশা রহমান </p>
    <p>মেজর </p>
    <p>পক্ষে মহাপরিচালক </p>
</div>
</div>
</body>
</html>


