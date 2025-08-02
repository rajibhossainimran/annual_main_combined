<!doctype html>

<html lang="en">

@include('admin.layouts.header')

@stack('css')

<?php

use App\Utill\Const\SubOrganizationTypes as SubOrganizationTypes;

$org_id = Auth::user()->org_id;
$sub_org_id = Auth::user()->sub_org_id;
$branch_id = Auth::user()->branch_id;

$type = '';

if (Auth::user()->suborganization) {
    $type = Auth::user()->suborganization->type;
}
?>

@if (Auth::user()->is_vendor == 1)
    @include('admin.layouts.vendor.header_menu')
    @include('admin.layouts.vendor.sidebar')
@else
    @include('admin.layouts.header_menu')
    @include('admin.layouts.sidebar')
@endif

@yield('content')

@include('admin.layouts.footer')

@stack('js')
</body>

</html>
