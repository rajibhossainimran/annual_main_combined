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
                            {{-- <h4 class="login-header-title">Tender Management System</h4> --}}
                        </div>
                        <div
                            class="mx-auto app-login-box col-12 col-lg-6 justify-content-center align-items-center p-4 backgroud-c1">
                            @if (Session::has('message'))
                                <div class="alert alert-danger">
                                    <div id="session-error" class="">{{ session('message') }}</div>
                                </div>
                            @endif
                            <div>
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-row justify-content-center">
                                        <div class="col-10">
                                            <div class="position-relative form-group">
                                                <label for="" class="">User ID</label>
                                                <input name="email" id="" placeholder="User ID" autofocus
                                                    type="text" class="form-control">
                                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row justify-content-center">
                                        <div class="col-10">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword" class="">Password</label>
                                                <div class="input-group">
                                                    <input name="password" id="examplePassword"
                                                        placeholder="Password here..." type="password"
                                                        class="form-control">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="togglePassword"
                                                            style="cursor: pointer;">
                                                            <i class="fa fa-eye"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="position-relative form-check">
                                        <input name="check" id="exampleCheck" type="checkbox"
                                            class="form-check-input">
                                        <label for="exampleCheck" class="form-check-label">Remember</label>
                                    </div> --}}
                                    <div class="form-row justify-content-center">
                                        <div class="form-group col-10 d-sm-flex align-items-center">
                                            <div class="w-50 text-left">
                                                <label class="checkbox-wrap checkbox-primary mb-0 font-14">
                                                    <input type="checkbox">
                                                    <span class="checkmark"></span>
                                                    Remember Me
                                                </label>
                                            </div>
                                            {{-- <div class="w-50 text-sm-right">
                                                @if (Route::has('password.request'))
                                                    <a href="{{ route('password.request') }}"
                                                        class="btn-lg btn btn-link" style="color:#626166">Forget
                                                        Password</a>
                                                @endif
                                            </div> --}}
                                        </div>

                                    </div>
                                    <div class="form-row justify-content-center">
                                        <button class="btn col-10 s2">Login</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="d-none d-lg-block col-lg-6 ">
                            <h4 class="login-header-title text-center">Tender & Store Management</h4>
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

    <script>
        document.getElementById('togglePassword').addEventListener('click', function(e) {
            const passwordInput = document.getElementById('examplePassword');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>

</x-guest-layout>
