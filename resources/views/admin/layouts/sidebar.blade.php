<?php
$menus = App\Http\Controllers\MenuController::parentMenu();
//var_dump();
//dd($menus);
?>
<div class="app-main">
    <div class="app-sidebar sidebar-shadow bg-success sidebar-text-light">
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
        <div class="scrollbar-sidebar c4">
            <div class="app-sidebar__inner">
                <div class="sidebarmenu-background"></div>
                <ul class="vertical-nav-menu" id="all-menus">
                    {{-- <li class="app-sidebar__heading">Menu</li> --}}
                    @foreach ($menus as $k => $menu)
                        <li class="main-side-menu">
                            <div class="sidebar-icon">
                                <i class="metismenu-icon {{ $menu->icon }}"></i>
                            </div>

                            <a
                                href="@if ($menu->common_url != '#') {{ route($menu->common_url) }}@else{{ '#' }} @endif">
                                <div class="d-flex align-items-center">
                                    <div>{{ $menu->name }}</div>
                                    @if ($menu->common_url == 'demand.index')
                                        <div id="pending_demand_count"></div>
                                    @elseif ($menu->common_url == 'notesheet.index')
                                        <div id="pending_notesheet_count"></div>
                                    @elseif ($menu->common_url == 'workorder.index')
                                        <div id="new_workorder_count"></div>
                                    @elseif ($menu->common_url == 'csr.index')
                                        <div id="pending_csr_count"></div>
                                    @endif
                                </div>
                                @if (isset($menu->sub_menu) && !empty($menu->sub_menu))
                                    <i class="metismenu-state-icon pe-7s-angle-down caret-left c5"></i>
                                @endif
                            </a>
                            @if (isset($menu->sub_menu) && !empty($menu->sub_menu))
                                <ul>
                                    @foreach ($menu->sub_menu as $sub)
                                        <li class="sub-menus"><a href="{{ route($sub->url) }}"><i
                                                    class="metismenu-icon"></i>
                                                {{ $sub->show_name }} </a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                    {{-- <li><a href="{{ route('add.menu') }}"><i class="metismenu-icon pe-7s-graph2"></i>Add menu</a></li> --}}

                </ul>
            </div>
        </div>
    </div>
