@extends('admin.master')
@push('css')

@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner ">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Payment</div>
                <div class="tabs-animation app-content-inner">
                    <div class="row">
                        <div class="col-md-5 order-md-2 mb-4">
                            <h4 class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted"><small class="text-muted">Tender - {{$tender->tender_no}}</small></span>
                                <span class="badge badge-secondary badge-pill">1</span>
                            </h4>
                            <ul class="list-group mb-3">
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between lh-condensed mb-4">
                                        <span>Tender Fee (BDT)</span>
                                        <span>{{$tender->purchase_price}} TK</span>
                                    </div>
                                    <div class="d-flex justify-content-between lh-condensed mb-4">
                                        <span>Platform Fee (BDT)</span>
                                        <span class="payment-free">Free</span>
                                    </div>
                                    <div class="d-flex justify-content-between lh-condensed mb-2">
                                        <div>
                                            <div>Tender Processing Fee (BDT)</div>
                                        </div>
                                        <div>{{number_format((float)$tender->purchase_price*3/100,2,'.','')}} TK</div>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Total (BDT)</span>
                                    <strong>{{number_format((float)(($tender->purchase_price*3/100)+$tender->purchase_price),2,'.','')}} TK</strong>

                                </li>
                            </ul>
                        </div>
                        <div class="col-md-7 order-md-1">
                            <h4 class="mb-3">Billing Info</h4>
                            <form method="POST" class="needs-validation" action="{{url('pay')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="firstName">Full name</label>
                                        <input type="text" name="customer_name" class="form-control" id="customer_name" placeholder=""
                                               value="{{Auth::user()->name}}" required>
                                        <input type="hidden" name="id" value="{{$tender->id}}">
                                        <input type="hidden" name="price" value="{{$tender->purchase_price}}">
                                        <div class="invalid-feedback">
                                            Valid customer name is required.
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="mobile">Mobile</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">+880</span>
                                        </div>
                                        <input type="text" name="customer_mobile" class="form-control" id="mobile" placeholder="Mobile"
                                               value="{{Auth::user()->phone}}" required>
                                        <div class="invalid-feedback table-width-100">
                                            Your Mobile number is required.
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email">Email <span class="text-muted">(Optional)</span></label>
                                    <input type="email" name="customer_email" class="form-control" id="email"
                                           placeholder="you@example.com" value="{{Auth::user()->email}}" required>
                                    <div class="invalid-feedback">
                                        Please enter a valid email address for shipping updates.
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address">Address</label>
                                    <input name="address1" type="text" class="form-control" id="address" placeholder="1234 Main St"
                                           value="{{Auth::user()->address}}" required>
                                    <div class="invalid-feedback">
                                        Please enter your shipping address.
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="country">Country</label>
                                        <select class="custom-select d-block w-100" id="country" required>
                                            <option value="Bangladesh" selected>Bangladesh</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a valid country.
                                        </div>
                                    </div>

                                </div>

{{--                                <hr class="mb-4">--}}
{{--                                <div class="custom-control custom-checkbox">--}}
{{--                                    <input type="checkbox" class="custom-control-input" id="same-address">--}}
{{--                                    <input type="hidden" value="1200" name="amount" id="total_amount" required/>--}}
{{--                                    <label class="custom-control-label" for="same-address">Shipping address is the same as my billing--}}
{{--                                        address</label>--}}
{{--                                </div>--}}
{{--                                <div class="custom-control custom-checkbox">--}}
{{--                                    <input type="checkbox" class="custom-control-input" id="save-info">--}}
{{--                                    <label class="custom-control-label" for="save-info">Save this information for next time</label>--}}
{{--                                </div>--}}
{{--                                <hr class="mb-4">--}}
{{--                                <button class="btn btn-primary btn-lg btn-block" id="sslczPayBtn"--}}
{{--                                        token="if you have any token validation"--}}
{{--                                        postdata="your javascript arrays or objects which requires in backend"--}}
{{--                                        order="If you already have the transaction generated for current order"--}}
{{--                                        endpoint="{{ url('/pay-via-ajax') }}"> Pay Now--}}
{{--                                </button>--}}
                                <button type="submit" class="btn btn-primary mt-1 col-md-12">Pay Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>


    <!-- If you want to use the popup integration, -->
    <script>
        var obj = {};
        obj.cus_name = $('#customer_name').val();
        obj.cus_phone = $('#mobile').val();
        obj.cus_email = $('#email').val();
        obj.cus_addr1 = $('#address').val();
        obj.amount = $('#total_amount').val();
        obj.id = $('#tender').val();

        $('#sslczPayBtn').prop('postdata', obj);
        // for live

        (function (window, document) {
                var loader = function () {
                    var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
                    script.src = "https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7);
                    tag.parentNode.insertBefore(script, tag);
                };

                window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
            })(window, document);

            // for sandbox
        // (function (window, document) {
        //     var loader = function () {
        //         var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
        //         // script.src = "https://seamless-epay.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR LIVE
        //         script.src = "https://sandbox.sslcommerz.com/embed.min.js?" + Math.random().toString(36).substring(7); // USE THIS FOR SANDBOX
        //         tag.parentNode.insertBefore(script, tag);
        //     };

        //     window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
        // })(window, document);
    </script>
@endpush
