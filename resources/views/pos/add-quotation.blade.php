@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">
                                    @isset($quotation)
                                        Edit
                                    @else
                                        Add
                                    @endisset Quotation
                                </h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="repairsCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf
                                <input type="hidden" name="modelid" value="@isset($quotation){{ $quotation->id }}@endisset">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Quotation No <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="q_no"
                                                value="@isset($quotation){{ $quotation->q_no }}@endisset"
                                                required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bill No <span class="text-danger">*</span></label>
                                            <select name="bill_no" class="form-control" required>
                                                <option value="">-- Select Repair Bill --</option>
                                                <option value="custom "@if (isset($quotation) && $quotation->bill_no == "custom") selected @endif>Custom Quotation</option>
                                                @foreach ($bills as $bill)
                                                    <option @if (isset($quotation) && $quotation->bill_no == $bill->bill_no) selected @endif
                                                        value="{{ $bill->bill_no }}">{{ $bill->bill_no }} -
                                                        {{ getCustomer($bill->customer)->name }}
                                                        ({{ getCustomer($bill->customer)->phone }})</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cargo Type <span class="text-danger">*</span></label>
                                            <select name="cargo_type" class="form-control" required>
                                                <option value="">-- Select Cargo Type --</option>
                                                <option @if (isset($quotation) && $quotation->cargo_type == 'Sea Cargo') selected @endif value="Sea Cargo">Sea Cargo</option>
                                                <option @if (isset($quotation) && $quotation->cargo_type == 'Air Cargo') selected @endif value="Air Cargo">Air Cargo</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Total <span class="text-danger">*</span></label>
                                            <input type="text" name="total" class="form-control"
                                                value="@isset($quotation){{ $quotation->total }}@else{{ '0' }}@endisset"
                                                required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Expiry Date <span class="text-danger">*</span></label>
                                            <input type="date" name="expiry_date" class="form-control"
                                                value="@isset($quotation){{ date('Y-m-d', strtotime($quotation->expiry_date)) }}@else{{ date('Y-m-d', strtotime('+14 days')) }}@endisset"
                                                required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Delivery Date <span class="text-danger">*</span></label>
                                            <input type="date" name="delivery_date" class="form-control"
                                                value="@isset($quotation){{ date('Y-m-d', strtotime($quotation->delivery_date)) }}@else{{ date('Y-m-d', strtotime('+60 days')) }}@endisset"
                                                required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">
                                    @isset($quotation)
                                        Update quotation
                                    @else
                                        Add quotation
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

    @isset($quotation)
        <script>
            $("#repairsCreate").submit(function(e) {
                e.preventDefault();

                $('#save_btn').prop('disabled', true);
                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/quotations/edit',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        $('#save_btn').prop('disabled', false);
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                        } else {
                            toastr.error(response.msg, 'Error');
                        }
                    },
                    error: function() {
                        $('#save_btn').prop('disabled', false);
                    }
                });
            });
        </script>
    @else
        <script>
            $("#repairsCreate").submit(function(e) {
                e.preventDefault();

                $('#save_btn').prop('disabled', true);

                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/quotations/create',
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        $('#save_btn').prop('disabled', false);
                        if (response.error == 0) {
                            toastr.success(response.msg, 'Success');
                            setInterval(() => {
                                location.reload();
                            }, 3000);
                        } else {
                            toastr.error(response.msg, 'Error');
                        }
                    },
                    error: function() {
                        $('#save_btn').prop('disabled', false);
                    }
                });
            });
        </script>
    @endisset
@endsection
