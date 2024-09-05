@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #f3f3f3 !important;
        }
    </style>
    <div class="contact-bg" style="background-image: url('{{ asset('assets/images/contact/hero-bg.png') }}')">
        <div class="hero-content">
            <h1>We would like to here from you</h1>
            <p>
                Drop us a message and get in touch
            </p>
        </div>
    </div>

    <div class="container">
        <div class="heading mt-5 pt-5">
            <h2>Contact <span>Us</span></h2>
            <p class="text-center">Drop up a message for contacting or quotations, and we will contact you within 24 working
                hours.</p>
        </div>

        <div class="contact_form">
            <div class="contact_details"
                style="background-image: url('{{ asset('assets/images/contact/contact-details-bg.png') }}');">
                <h4>Contact our company <br>marketing team</h4>

                <div class="dtl">
                    <div class="title">Contact Number</div>
                    <div class="number"><i class="fa-solid fa-phone"></i> +94 74 195 9701</div>
                </div>

                <div class="dtl">
                    <div class="title">Contact Email</div>
                    <div class="number"><i class="fa-solid fa-envelope"></i> info@nmsware.com</div>
                </div>

                <div class="dtl">
                    <div class="title">Social Media</div>
                    <div class="social-icon">
                        <a href=""><i class="fa-brands fa-instagram"></i></a>
                        <a href=""><i class="fa-brands fa-facebook-f"></i></a>
                        <a href=""><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            @isset($userData)
                <form id="contact_form" onsubmit="return false;" action="{{ route('contact') }}" method="POST">
                    @csrf
                    <div class="row row-cols-1 row-cols-xxl-2 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-sm-2">
                        <div class="col">
                            <div class="input">
                                <div class="label">First Name</div>
                                <input type="text" placeholder="First Name" name="fname" value="{{ $userData['fname'] }}"
                                    required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Last Name</div>
                                <input type="text" placeholder="Last Name" name="lname" value="{{ $userData['lname'] }}"
                                    required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Email</div>
                                <input type="text" placeholder="Email" name="email" value="{{ $userData['email'] }}"
                                    required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Phone</div>
                                <input type="text" placeholder="Phone" name="phone" value="{{ $userData['phone'] }}"
                                    required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Company Name</div>
                                <input type="text" placeholder="Company Name" name="company_name"
                                    value="{{ $userData['company_name'] }}" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Industry</div>
                                <input type="text" placeholder="Industry" name="industry" value="{{ $userData['industry'] }}"
                                    required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Country</div>
                                <input type="text" placeholder="Country" name="country" value="{{ $userData['country'] }}"
                                    required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">City</div>
                                <input type="text" placeholder="City" name="city" value="{{ $userData['city'] }}"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-1">
                        <div class="col">
                            <div class="input">
                                <div class="label">Message</div>
                                <textarea id="" cols="30" rows="4" placeholder="Message" name="message" required></textarea>
                            </div>
                        </div>
                    </div>

                    @isset($error)
                        @if ($error['error'] == 1)
                            <span class="invalid-feedback mb-3 d-block" role="alert">
                                <strong>{{ $error['msg'] }}</strong>
                            </span>
                        @endif
                    @endisset

                    <div class="row row-cols-1">
                        <div class="col">
                            <input type="checkbox" name="calls" id=""> <span>I would like to receive phone
                                calls</span>
                        </div>
                    </div>

                    {!! getCaptcha() !!}

                    <div class="row row-cols-1 mt-3">
                        <div class="col">
                            <button type="submit" class="primary-btn">Submit</button>
                        </div>
                    </div>
                </form>
            @else
                <form id="contact_form" onsubmit="return false;" action="{{ route('contact') }}" method="POST">
                    @csrf
                    <div class="row row-cols-1 row-cols-xxl-2 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-sm-2">
                        <div class="col">
                            <div class="input">
                                <div class="label">First Name</div>
                                <input type="text" placeholder="First Name" name="fname" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Last Name</div>
                                <input type="text" placeholder="Last Name" name="lname" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Email</div>
                                <input type="text" placeholder="Email" name="email" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Phone</div>
                                <input type="text" placeholder="Phone" name="phone" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Company Name</div>
                                <input type="text" placeholder="Company Name" name="company_name" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Industry</div>
                                <input type="text" placeholder="Industry" name="industry" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Country</div>
                                <input type="text" placeholder="Country" name="country" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">City</div>
                                <input type="text" placeholder="City" name="city" required>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-1">
                        <div class="col">
                            <div class="input">
                                <div class="label">Message</div>
                                <textarea id="" cols="30" rows="4" placeholder="Message" name="message" required></textarea>
                            </div>
                        </div>
                    </div>

                    @isset($error)
                        @if ($error['error'] == 1)
                            <span class="invalid-feedback mb-3 d-block" role="alert">
                                <strong>{{ $error['msg'] }}</strong>
                            </span>
                        @endif
                    @endisset

                    <div class="row row-cols-1">
                        <div class="col">
                            <input type="checkbox" name="calls" id=""> <span>I would like to receive phone
                                calls</span>
                        </div>
                    </div>

                    {!! getCaptcha() !!}

                    <div class="row row-cols-1 mt-3">
                        <div class="col">
                            <button type="submit" class="primary-btn">Submit</button>
                        </div>
                    </div>
                </form>
            @endisset
        </div>
    </div>

    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalTitle"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body d-flex" style="justify-content: center;">
                    <div class="spinner-border text-light" style="width: 4rem; height: 4rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @isset($userData)
        <script>
            $(document).ready(function() {
                toastr.success("Some information are filled automatically");
            });
        </script>
    @endisset

    <script>
        $(document).ready(function() {

            $("#contact_form").submit(function(e) {
                e.preventDefault();
                $("#loadingModal").modal('show');
                $.ajax({
                    type: "post",
                    url: "/contact",
                    data: $("#contact_form").serialize(),
                    dataType: "json",
                    success: function(response) {
                        setTimeout(() => {
                            $("#loadingModal").modal('hide');
                        }, 2000);

                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            setTimeout(() => {
                                location.href = "/";
                            }, 4000);
                        } else {
                            toastr.error(response.msg, "Error")
                        }
                    }
                });
            });
        });
    </script>
@endsection
