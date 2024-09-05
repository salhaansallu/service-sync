@extends('../account')

@section('account_content')
    <style>
        .addvert,
        footer {
            display: none;
        }

        .subscribelist form .row .col {
            margin-top: 20px
        }
    </style>

    <div class="overview">
        <div class="subscribelist">
            <div class="head">Your details <i class="fa-solid fa-bars d-lg-none" id="account_menu"></i></div>

            <form action="" onsubmit="return false;" id="detailsForm">
                @csrf
                <div class="row row-cols-1 row-cols-xxl-2 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-sm-2 mt-3">
                    <div class="col">
                        <div class="input">
                            <div class="label">First Name</div>
                            <input type="text" placeholder="First Name" value="{{ Auth::user()->fname }}" name="fname"
                                id="fname">
                        </div>
                    </div>

                    <div class="col">
                        <div class="input">
                            <div class="label">Last Name</div>
                            <input type="text" placeholder="Last Name" value="{{ Auth::user()->lname }}" name="lname"
                                id="lname">
                        </div>
                    </div>

                    <div class="col">
                        <div class="input">
                            <div class="label">Email</div>
                            <input type="email" placeholder="Email" value="{{ Auth::user()->email }}" name="email"
                                id="email">
                        </div>
                    </div>

                    @if ($company->count() > 0)
                        <div class="col">
                            <div class="input">
                                <div class="label">Company Name</div>
                                <input type="text" placeholder="Company Name" value="{{ $company[0]->company_name }}"
                                    name="company_name" id="cname">
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">Industry</div>
                                <input type="text" placeholder="Industry" value="{{ $company[0]->industry }}"
                                    name="industry" id="industry">
                            </div>
                        </div>

                        <div class="col">
                            <div class="input">
                                <div class="label">Country</div>
                                <select id="countryList" name="country" id="country">
                                    <script>
                                        $(document).ready(function() {
                                            validateCountry('get').forEach(country => {
                                                if (country == '{{ $company[0]->country }}') {
                                                    $("<option value='" + country + "' selected>" + country + "</option>").appendTo(
                                                        "#countryList");
                                                } else {
                                                    $("<option value=" + country + ">" + country + "</option>").appendTo("#countryList");
                                                }
                                            });
                                        });
                                    </script>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input">
                                <div class="label">City</div>
                                <input type="text" placeholder="City" value="{{ $company[0]->city }}" name="city"
                                    id="city">
                            </div>
                        </div>
                    @endif

                    @if ($user_info->count() > 0)
                        <div class="col">
                            <div class="input">
                                <div class="label">Phone number</div>
                                <input type="number" placeholder="Phone number" value="{{ $user_info[0]->phone }}"
                                    name="phone" id="phone">
                            </div>
                        </div>
                    @endif

                    <div class="col-12 w-100 mt-4">
                        <button type="submit" class="primary-btn submit-btn">Save</button>
                    </div>
                </div>
            </form>

        </div>

        <div class="subscribelist mt-5">
            <div class="head">Update password</div>

            <form action="" id="passwordForm" onsubmit="return false;">
                @csrf
                <div class="row row-cols-1 row-cols-xxl-2 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-sm-2 mt-3">
                    <div class="col">
                        <div class="input">
                            <div class="label">Old password</div>
                            <input type="password" placeholder="Old password" name="oldpass" id="oldpass">
                        </div>
                    </div>

                    <div class="col">
                        <div class="input">
                            <div class="label">New password</div>
                            <input type="password" placeholder="New password" name="newpass" id="newpass">
                        </div>
                    </div>

                    <div class="col">
                        <div class="input">
                            <div class="label">Re-enter password</div>
                            <input type="password" placeholder="Re-enter password" name="confirmpass" id="confirmpass">
                        </div>
                    </div>

                    <div class="col-12 w-100 mt-4">
                        <button type="submit" class="primary-btn submit-btn">Update</button>
                    </div>
                </div>
            </form>

        </div>
    </div>


    @if ($company->count() > 0)
        <script>
            $("#detailsForm").submit(function(e) {
                e.preventDefault();

                if (validateCountry($("#countryList").val())) {
                    $.ajax({
                        type: "post",
                        url: "/update-details",
                        data: $("#detailsForm").serialize(),
                        dataType: "json",
                        success: function(response) {
                            if (response.error == 0) {
                                toastr.success(response.msg, "Success");
                            } else {
                                toastr.error(response.msg, "Error");
                            }
                        }
                    });
                }
                else {
                    toastr.error("Invalid country", "Error");
                }
            });
        </script>
    @else
        <script>
            $("#detailsForm").submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: "post",
                    url: "/update-details",
                    data: $("#detailsForm").serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                        } else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            });
        </script>
    @endif
@endsection
