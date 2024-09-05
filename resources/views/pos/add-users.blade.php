@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Users</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="CategoryCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                @isset($user)  
                                <input type="hidden" name="modelid" value="{{ $user->user_id }}">
                                @endisset

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter First Name"
                                                name="fname"
                                                value="@isset($user){{ $user->fname }}@endisset"
                                                data-errors="Please Enter First Name." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Last Name </label>
                                            <input type="text" class="form-control" placeholder="Enter Last Name"
                                                name="lname"
                                                value="@isset($user){{ $user->lname }}@endisset">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    @isset($user)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Account Email (cannot be updated) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" readonly value="{{ $user->email }}">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" placeholder="Enter Email"
                                                name="email"
                                                value="@isset($user){{ $user->email }}@endisset"
                                                data-errors="Please Enter Email." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" placeholder="Enter Password"
                                                name="password"
                                                data-errors="Please Enter Password." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    @endisset
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">@isset($user) Update user @else Add user @endisset</button>
                            </form>
                        </div>
                    </div>
                </div>

                @isset($user)
                    {{-- No Need --}}
                @else
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Invite Users</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="inviteUsers" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" placeholder="Enter Email"
                                                name="email" data-errors="Please Enter Email." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="save_btn2" class="btn btn-primary mr-2">Invite user</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endisset
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
                    url: '/dashboard/user/edit',
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
                    url: '/dashboard/user/create',
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

            $("#inviteUsers").submit(function(e) {
                e.preventDefault();

                $('#save_btn2').prop('disabled', true);
                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/user/invite',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(res) {
                        if (res.error == 0) {
                            toastr.success(res.msg, 'Success');
                            setInterval(() => {
                                location.reload();
                            }, 3000);
                        } else {
                            toastr.error(res.msg, 'Error');
                        }
                    }
                });
                $('#save_btn2').prop('disabled', false);
            });
        </script>
    @endisset
@endsection
