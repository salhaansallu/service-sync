@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Partner</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="CategoryCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                @isset($user)
                                    <input type="hidden" name="modelid" value="{{ $user->id }}">
                                @endisset

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Partner Brand Logo </label>
                                            <input type="file" class="form-control" name="logo">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Partner Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Partner Name"
                                                name="name"
                                                value="@isset($user){{ $user->name }}@endisset"
                                                data-errors="Please Enter Partner Name." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Company Name</label>
                                            <input type="text" class="form-control" placeholder="Enter Company Name"
                                                name="company"
                                                value="@isset($user){{ $user->company }}@endisset"
                                                data-errors="Please Enter Company Name.">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone Number </label>
                                            <input type="number" class="form-control" placeholder="Enter Phone Number"
                                                name="phone"
                                                value="@isset($user){{ $user->phone }}@endisset"
                                                data-errors="Please Enter Phone Number.">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address </label>
                                            <input type="text" class="form-control" placeholder="Enter Address"
                                                name="address"
                                                value="@isset($user){{ $user->address }}@endisset"
                                                data-errors="Please Enter Address." >
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email </label>
                                            <input type="email" class="form-control" placeholder="Enter Email"
                                                name="email"
                                                value="@isset($user){{ $user->email }}@endisset"
                                                data-errors="Please Enter Email." >
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Username </label>
                                            <input type="text" class="form-control" placeholder="Enter Username"
                                                name="username"
                                                value="@isset($user){{ $user->username }}@endisset"
                                                data-errors="Please Enter Username." >
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password </label>
                                            <input type="password" class="form-control" placeholder="Enter Password"
                                                name="password" data-errors="Please Enter Password.">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">
                                    @isset($user)
                                        Update partner
                                    @else
                                        Add partner
                                    @endisset
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    @isset($user)
        <script>
            $("#CategoryCreate").submit(function(e) {
                e.preventDefault();

                $('#save_btn').prop('disabled', true);
                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/partner/edit',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                        } else {
                            toastr.error(response.msg, 'Error');
                        }
                    }
                });
                $('#save_btn').prop('disabled', false);
            });
        </script>
    @else
        <script>
            $("#CategoryCreate").submit(function(e) {
                e.preventDefault();

                $('#save_btn').prop('disabled', true);

                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/partner/create',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                            setInterval(() => {
                                location.reload();
                            }, 3000);
                        } else {
                            toastr.error(response.msg, 'Error');
                        }
                    }
                });
                $('#save_btn').prop('disabled', false);
            });
        </script>
    @endisset
@endsection
