@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Update Invoice Layout Settings</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="InvoiceUpdate" action="" onsubmit="return false;">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="qr_code" type="checkbox" role="switch" id="qr_codecheck" {{ ($settings->qr_code=='active' || $settings->qr_code=='')? 'checked' : '' }}>
                                                <label for="qr_codecheck">Display QR code in invoice</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="datetime" type="checkbox" role="switch" id="datetimecheck" {{ ($settings->datetime=='active' || $settings->datetime=='')? 'checked' : '' }}>
                                                <label for="datetimecheck">Display date time in invoice</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="industry" type="checkbox" role="switch" id="industrycheck" {{ ($settings->industry=='active' || $settings->industry=='')? 'checked' : '' }}>
                                                <label for="industrycheck">Display industry in invoice</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" name="title" type="checkbox" role="switch" id="titlecheck" {{ ($settings->title=='active' || $settings->title=='')? 'checked' : '' }}>
                                                <label for="titlecheck">Display title (Cash Receipt) in invoice</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <script>
        $("#InvoiceUpdate").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $.ajax({
                type: "post",
                url: '/dashboard/invoice-settings',
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
        });
    </script>
@endsection
