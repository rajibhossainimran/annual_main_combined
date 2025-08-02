<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="app-container app-theme-white body-tabs-shadow">
        <div class="app-container login-bg">
            <div class="h-100">
                <div class="h-100 row gap-4 justify-content-center align-items-center m-0">
                    <div
                        class="d-flex justify-content-center align-items-center col-12 col-sm-10 col-md-8 col-lg-7 col-xl-6 s1">
                        <div class="position-absolute">

                        </div>
                        <div
                            class="mx-auto app-login-box col-12 col-lg-6 justify-content-center align-items-center p-4 backgroud-c1">
                            <div class="alert alert-danger">
                                @if (Session::has('error'))
                                    <div id="session-error" class="">{{ session('error') }}</div>
                                @endif
                            </div>
                            @if (count($errors) > 0)
                                <div class = "alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div>
                                <form method="POST" action="{{ url('update-password/user/expire') }}">
                                    @csrf
                                    <div class="form-row justify-content-center">
                                        <h5 class="password-title">Change Password </h5>
                                        <div class="col-10">
                                            <div class="">
                                                <label>Old Password</label>
                                                <input type="password" required class="form-control" name="old_password"
                                                    value="">
                                            </div>
                                            <div class="">
                                                <label>New Password</label>
                                                <input type="password" required class="form-control" name="new_password"
                                                    value="">
                                            </div>
                                            <div class="">
                                                <label>Confirm Password</label>
                                                <input type="password" required class="form-control"
                                                    name="new_password_confirmation" value="">
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-row justify-content-center">
                                        <button class="btn col-10 s2">Change Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="d-none d-lg-block col-lg-6 ">
                            <h4 class="login-header-title text-center">Tender Management System</h4>
                            <div class="d-flex flex-column gap-4 justify-content-center align-items-center">
                                <div class=""> <img src="{{ asset('/admin/css/assets/images/logo.png') }}"
                                        width="150" /></div>
                                <div class="text-center s3">
                                    Directorate General of Medical Services
                                </div>
                            </div>
                            <p class="footer-text s4">Powered by <a href="https://tilbd.net/" class="color1">Trust
                                    Innovation Limited</a>
                            </p>
                        </div>
                        {{-- <div class="position-absolute" style="bottom:-70px;right:0px;">
                            <p class="footer-text">Powered by <a href="https://tilbd.net/">Trust Innovation Limited</a> © 2023. All rights reserved.
                            </p>
                        </div> --}}
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-guest-layout>
