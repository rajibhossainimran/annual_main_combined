<?php
$menus = App\Http\Controllers\MenuController::parentMenu();
//var_dump();
//dd($menus);
?>
<div class="horizontal_main ">
    <div class="horizontal_sidebar back-tr">
        <div class="app-header__logo">
            <div class="logo-src"></div>
            <div class="header__pane ml-auto">
                <div><button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                        data-class="closed-sidebar"><span class="hamburger-box"><span
                                class="hamburger-inner"></span></span></button></div>
            </div>
        </div>
        <div class="app-header__mobile-menu">
            <div><button type="button" class="hamburger hamburger--elastic mobile-toggle-nav"><span
                        class="hamburger-box"><span class="hamburger-inner"></span></span></button></div>
        </div>
        <div class="app-header__menu"><span><button type="button"
                    class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav"><span
                        class="btn-icon-wrapper"><i class="fa fa-ellipsis-v fa-w-6"></i></span></button></span></div>
        <div class="scrollbar-sidebar">
            <div class="app-sidebar__inner">
                <ul class="vertical-nav-menu horizontal-nav-menu">
                    <!-- <li class="app-sidebar__heading">Menu</li> -->
                    @foreach ($menus as $k => $menu)
                        <li class="">
                            <a
                                href="@if ($menu->common_url != '#') {{ route($menu->common_url) }}@else{{ '#' }} @endif"><i
                                    class="metismenu-icon pe-7s-rocket"></i>
                                    <div class="d-flex">
                                        <div>{{ $menu->name }}</div>
                                        <div id="{{$menu->common_url}}_count"></div>
                                    </div>

                                @if (isset($menu->sub_menu) && !empty($menu->sub_menu))
                                    <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                @endif
                            </a>
                            @if (isset($menu->sub_menu) && !empty($menu->sub_menu))
                                <ul class="horizontal-sub_menu sidebar-shadow" style="border: 1px solid rgba(26,54,126,.125);
                                border-radius: .3rem;">
                                    @foreach ($menu->sub_menu as $sub)
                                        <li><a href="{{ route($sub->url) }}"><i class="metismenu-icon"></i>
                                                {{ $sub->show_name }} </a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach

                    <li><a href="{{ route('add.menu') }}"><i class="metismenu-icon pe-7s-graph2"></i>Add menu</a></li>

                </ul>
            </div>
        </div>
    </div>
</div>
<div class="mr-tr">
