{{-- <x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="app-container app-theme-white body-tabs-shadow">
        <div class="app-container">
            <div class="h-100">
                <div class="h-100 row gap-4 justify-content-center align-items-center" style="margin: 0px 0px;">
                    <div class="d-flex justify-content-center align-items-center col-12 col-sm-10 col-md-8 col-lg-7 col-xl-6" style="padding: 0px 0px;border: 1px solid #00664e;border-radius:3px;background: #00664e;">
                        <div class="mx-auto app-login-box col-12 col-lg-7 justify-content-center align-items-center p-4 " style="background:#f1f4f6">
                            <div>
                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf
                                    <div class="form-row justify-content-center">
                                        <div class="col-10">
                                            <div class="position-relative form-group">
                                                <label for="exampleEmail" class="">Email</label>
                                                <input name="email" id="exampleEmail" placeholder="Email here..."
                                                    type="email" class="form-control" required>
                                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row justify-content-center">
                                        <div class="mt-2 d-flex flex-column align-items-center col-10">
                                            <div class="">
                                                <button class="btn btn-lg"  style="background:#00664e;color:white;width:100%">Recover Password</button>
                                            </div>
                                            <h6 class="pt-2">
                                                <a href="{{ route('login') }}" class=""  style="color:#626166">Sign in existing
                                                    account</a>
                                            </h6>
                                        </div>
                                    </div>

                                </form>
                                </form>
                            </div>
                        </div>
                        <div class="d-none d-lg-block col-lg-5 ">
                            <div class="d-flex flex-column gap-4 justify-content-center align-items-center">
                                <div> <img src="{{asset('/admin/css/assets/images/logo.png')}}" width="40"/></div>
                                <div class="text-center" style="color:white;font-size:16px;font-weight:bold;padding:5px 5px;">
                                    Lorem Ipsum Text
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout> --}}
