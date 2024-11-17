@extends('customer-portal.app')

@section('customer')
    <div class="container py-5">
        <h1 class="text-center mb-5">We Fix LK Customer Portal</h1>
        <div class="row justify-content-center">
            <div class="verification_form col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Customer Verification</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" data-toggle="validator" method="POST" onsubmit="return false;">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Mobile Number <span class="text-danger">*</span></label>
                                        <input type="text" class="mobile_number form-control" placeholder="Enter Mobile Number" name="phone" required>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="otp-field col-12" style="display: none">
                                    <div class="form-group">
                                        <label>OTP <span class="text-danger">*</span></label>
                                        <input type="text" class="otp_code form-control" placeholder="Enter OTP" name="otp">
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="send_btn" class="btn btn-primary mr-2">Send OTP</button>
                            <button type="button" id="verify" class="btn btn-primary mr-2" style="display: none;">Verify OTP</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="repairs_table col-12" style="display: none;">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Repair List</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="rounded mb-3">
                            <table class="data-table table mb-0 tbl-server-info" style="overflow: unset;">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th class="text-start" style="min-width: 100px;">Bill number</th>
                                        <th class="text-start" style="min-width: 100px;">Model number</th>
                                        <th class="text-start" style="min-width: 100px;">Serial number</th>
                                        <th class="text-start" style="min-width: 200px;">Fault</th>
                                        <th class="text-start" style="min-width: 70px;">Advance</th>
                                        <th class="text-start" style="min-width: 70px;">Total</th>
                                        <th class="text-start" style="min-width: 100px;">Status</th>
                                        <th class="text-start" style="min-width: 170px;">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    {{--  --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .DTFC_LeftBodyLiner {
            overflow: unset !important;
        }

        .DTFC_LeftBodyWrapper {
            border-right: 1px solid #c4c4c4;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#send_btn').click(function(e) {
                e.preventDefault();
                let phone = $('.mobile_number').val();
                const phoneRegex = /^(0|94|\+94)?7\d{8}$/;

                if (phone.trim() != "" && phoneRegex.test(phone)) {
                    $('#send_btn').prop('disabled', true);
                    $.ajax({
                        type: "post",
                        url: "/customer-portal/send-code",
                        data: {
                            phone: phone,
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        success: function(response) {
                            $('#send_btn').prop('disabled', false);
                            if (response.error == 0) {
                                $('.otp-field').show();
                                $('#send_btn').hide();
                                $('#verify').show();

                                toastr.success(response.msg, "Success");
                                return
                            }
                            toastr.error(response.msg, "Error");
                        },
                        error: function(error) {
                            $('#send_btn').prop('disabled', false);
                        }
                    });
                } else {
                    toastr.error("Invalid Phone Number", "Error");
                }
            });

            $('#verify').click(function(e) {
                e.preventDefault();
                let otp = $('.otp_code').val();
                const regex = /^\d{4}$/;

                if (otp.trim() != "" && regex.test(otp)) {
                    $('#verify').prop('disabled', true);

                    if ($.fn.dataTable.isDataTable('.data-table')) {
                        $('.data-table').DataTable().clear().destroy();
                    }

                    $.ajax({
                        type: "post",
                        url: "/customer-portal/verify-code",
                        data: {
                            code: otp,
                            _token: $('input[name="_token"]').val()
                        },
                        dataType: "json",
                        success: function(response) {
                            $('#verify').prop('disabled', false);
                            if (response.error == 0) {
                                var tableBody = $('.data-table tbody');
                                tableBody.empty(); // Clear any existing data

                                // Loop through the data and append rows to the table
                                response.repairs.forEach(function(item) {
                                    var row = '<tr>';
                                    row += '<td>' + item["bill_no"] + '</td>';
                                    row += '<td>' + item["model_no"] + '</td>';
                                    row += '<td>' + item["serial_no"] + '</td>';
                                    row += '<td>' + item["fault"] + '</td>';
                                    row += '<td>' + item["advance"] + '</td>';
                                    row += '<td>' + item["total"] + '</td>';
                                    if (item["status"] == "Pending") {
                                        row +=
                                            '<td class="text-bg-warning text-light text-center fw-bold">' +
                                            item["status"] + '</td>';
                                    } else if (item["status"] == "Repaired") {
                                        row +=
                                            '<td class="text-bg-success text-center fw-bold">' +
                                            item["status"] + '</td>';
                                    } else if (item["status"] == "Delivered") {
                                        row +=
                                            '<td class="text-bg-primary text-center fw-bold">' +
                                            item["status"] + '</td>';
                                    } else if (item["status"] == "Awaiting Parts") {
                                        row +=
                                            '<td class="text-bg-secondary text-center fw-bold">' +
                                            item["status"] + '</td>';
                                    } else if (item["status"] == "Customer Pending") {
                                        row +=
                                            '<td class="text-bg-info text-light text-center fw-bold">' +
                                            item["status"] + '</td>';
                                    } else {
                                        row +=
                                            '<td class="text-bg-danger text-center fw-bold">' +
                                            item["status"] + '</td>';
                                    }

                                    row += '<td>' + new Date(item["created_at"])
                                        .toLocaleString() + '</td>';
                                    row += '</tr>';
                                    tableBody.append(row);
                                });

                                $('.verification_form').hide();
                                $('.repairs_table').show();

                                $('.data-table').DataTable({
                                    scrollX: true,
                                    fixedColumns: {
                                        leftColumns: 1
                                    }
                                });

                                toastr.success(response.msg, "Success");
                                return
                            }
                            toastr.error(response.msg, "Error");
                        },
                        error: function(error) {
                            $('#verify').prop('disabled', false);
                        }
                    });
                } else {
                    toastr.error("Invalid OTP", "Error");
                }
            });
        });
    </script>
@endsection
