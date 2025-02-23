@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Shipper</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="CategoryCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                @isset($shipper)
                                <input type="hidden" name="modelid" value="{{ $shipper->id }}">
                                @endisset

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Shipper Company Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Shipper Name"
                                                name="name"
                                                value="@isset($shipper){{ $shipper->company_name }}@endisset"
                                                data-errors="Please Enter Name." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="number" class="form-control" placeholder="Enter Phone Number"
                                                name="phone"
                                                value="@isset($shipper){{ $shipper->phone }}@endisset">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button id="save_btn" type="submit" class="btn btn-primary mr-2">@isset($shipper) Update Shipper @else Add Shipper @endisset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    @isset($shipper)
        <script>
            $("#CategoryCreate").submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                $('#save_btn').prop('disabled', true);
                $.ajax({
                    type: "post",
                    url: '/dashboard/shipper/edit',
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
    @else
        <script>
            $("#CategoryCreate").submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                $('#save_btn').prop('disabled', true);
                $.ajax({
                    type: "post",
                    url: '/dashboard/shipper/create',
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
