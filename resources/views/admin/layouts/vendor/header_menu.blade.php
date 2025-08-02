<?php
//$menus = App\Http\Controllers\MenuController::parentMenu();
//var_dump();
//dd($menus);
?>
</head>

<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar vendor-container">
        <input type="hidden" name="_token" value="{{ @csrf_token() }}">
        <div class="app-header header-shadow header-text-light vendor-panel">
            <div class="app-header__logo">
                <img src="{{ asset('/admin/css/assets/images/logo.png') }}" width="40" />
                <div class="panel-top-name vendor-top-panel">
                    <div class="org-name font-width-bold">DGMS</div>
                    <div class="admin-panel-text font-15">Vendor Panel</div>
                </div>
                {{-- <div class="header__pane ml-auto">
                    <div><button type="button" class="hamburger close-sidebar-hide close-sidebar-btn hamburger--elastic" data-class="closed-sidebar"><span class="hamburger-box"><span class="hamburger-inner"></span></span></button></div>
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar" style="position: relative">
                            <i class="fa fa-play ham-open-close"></i>
                        </button>
                    </div>
                </div> --}}
            </div>
            <div class="app-header__mobile-menu">
                <div><button type="button" class="hamburger hamburger--elastic mobile-toggle-nav"><span
                            class="hamburger-box"><span class="hamburger-inner"></span></span></button></div>
            </div>
            <div class="app-header__menu"><span><button type="button"
                        class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav"><span
                            class="btn-icon-wrapper"><i class="fa fa-ellipsis-v fa-w-6"></i></span></button></span>
            </div>
            <div class="app-header__content">
                <div class="app-header-left justify-content-center width-80">

                </div>
                <div class="app-header-right">

                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left logout-btn">
                                    <div class="btn-group"><a data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" class="p-0 btn"><img width="42"
                                                class="rounded-circle" src="assets/images/avatars/1.jpg" alt><i
                                                class="fa fa-angle-down ml-2 opacity-8"></i></a>
                                        <div tabindex="-1" role="menu" aria-hidden="true"
                                            class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right">
                                            <div class="dropdown-menu-header">
                                                <div class="dropdown-menu-header-inner">
                                                    <div class="menu-header-image opacity-2"
                                                        style="background-image: url('assets/images/dropdown-header/city3.jpg');">
                                                    </div>
                                                    <div class="menu-header-content text-left">
                                                        <div class="widget-content-wrapper">
                                                            <div class="widget-content-left mr-3"><img width="42"
                                                                    class="rounded-circle"
                                                                    src="assets/images/avatars/1.jpg" alt></div>
                                                            <div class="widget-content-left">
                                                                <div class="widget-heading color-gg" style="color: #00664e">
                                                                    {{ Auth::user()->name }}</div>
                                                                <div class="widget-subheading opacity-8 color-g" style="color: #00664e">{{ Auth::user()->email }}
                                                                </div>
                                                                <br>
                                                                <div class="widget-subheading opacity-8 color-g-f">
                                                                    <a href="{{route('change.password.user')}}">
                                                                        Change Password
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <div class="widget-content-right mr-2">
                                                                <form action="{{ route('logout') }}" method="post">
                                                                    @csrf
                                                                    <button
                                                                        class="btn-pill btn-shadow btn-shine btn btn-focus">Logout</button>
                                                                </form>
                                                            </div>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="widget-content-left  ml-3 header-user-info">
                                    <div class="widget-heading"> {{ Auth::user()->name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
