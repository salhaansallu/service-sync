@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Edit Repair Orders</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="repairsCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf
                                <input type="hidden" name="modelid" value="{{ $repairs->id }}">
                                <input type="hidden" name="move_type" id="move_type" value="">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bill No</label>
                                            <input type="text" class="form-control" disabled value="{{ $repairs->bill_no }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Model No <span class="text-danger">*</span></label>
                                            <input type="text" name="model_no" class="form-control" value="{{ $repairs->model_no }}" data-errors="Please Enter Model No." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Serial No</label>
                                            <input type="text" name="serial_no" class="form-control" value="{{ $repairs->serial_no }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fault</label>
                                            <input type="text" name="fault" class="form-control" value="{{ $repairs->fault }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Delivery</label>
                                            <input type="text" name="delivery" class="form-control" value="{{ $repairs->delivery }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Advance</label>
                                            <input type="text" name="advance" class="form-control" value="{{ $repairs->advance }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row m-0">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Cost</label>
                                                    <input type="text" name="cost" class="form-control" value="{{ $repairs->cost }}">
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Total</label>
                                                    <input type="text" name="total" class="form-control" value="{{ $repairs->total }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Technician</label>
                                            <select name="techie" class="form-control">
                                                <option value="">-- Select Technician --</option>
                                                @foreach ($users as $user)
                                                    <option @if($user->user_id == $repairs->techie) selected @endif value="{{ $user->user_id }}">{{ $user->fname }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Commission</label>
                                            <input type="text" name="commission" class="form-control" value="{{ $repairs->commission }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Cashier </label>
                                                    <select name="cashier" class="form-control" required>
                                                        <option value="">-- Select Cashier --</option>
                                                        @foreach ($users as $user)
                                                            <option @if($user->user_id == $repairs->cashier) selected @endif value="{{ $user->user_id }}">{{ $user->fname }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Cashier Code </label>
                                                    <input type="text" name="" readonly class="form-control" value="{{ getCashierCode($repairs->cashier)->cashier_code }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer <span class="text-danger">*</span></label>
                                            <select name="customer" class="form-control" required>
                                                @foreach ($customers as $customer)
                                                    <option @if($customer->id == $repairs->customer) selected @endif value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Partner </label>
                                            <select name="partner" class="form-control">
                                                <option value="0">None</option>
                                                @foreach ($partners as $partner)
                                                    <option @if($partner->id == $repairs->partner) selected @endif value="{{ $partner->id }}">{{ $partner->company }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Spare Parts</label>
                                            <select name="spares[]" multiple="multiple" class="form-control select2-multiple">
                                                @foreach ($spares as $spare)
                                                    <option @if(in_array($spare->id, (array)json_decode($repairs->spares))) selected @endif value="{{ $spare->id }}">{{ $spare->pro_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea name="note" class="form-control" id="" rows="5">{{ str_replace(['<br>', ' <br> ', ' <br>', '<br> '], PHP_EOL, $repairs->note) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select name="status" class="form-control" required>
                                                <option @if($repairs->status == "Delivered") selected @endif value="Delivered">Delivered</option>
                                                <option @if($repairs->status == "Repaired") selected @endif value="Repaired">Repaired</option>
                                                <option @if($repairs->status == "Return") selected @endif value="Return">Return</option>
                                                <option @if($repairs->status == "Pending") selected @endif value="Pending">Pending</option>
                                                <option @if($repairs->status == "Awaiting Parts") selected @endif value="Awaiting Parts">Awaiting Parts</option>
                                                <option @if($repairs->status == "Customer Pending") selected @endif value="Customer Pending">Customer Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Warranty <span class="text-danger">*</span></label>
                                            <select name="warranty" class="form-control" required>
                                                <option @if($repairs->warranty == "0") selected @endif value="0">No warranty</option>
                                                <option @if($repairs->warranty == "1") selected @endif value="1">1 Month</option>
                                                <option @if($repairs->warranty == "3") selected @endif value="3">3 Months</option>
                                                <option @if($repairs->warranty == "6") selected @endif value="6">6 Months</option>
                                                <option @if($repairs->warranty == "12") selected @endif value="12">1 Year</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="update_btn" class="btn btn-primary mr-2">Update</button>
                                @if ($repairs->paid_at != null)
                                    <button type="button" id="paid_btn" data-status="unpaid" class="btn btn-danger mr-2">Mark as Unpaid</button>
                                @else
                                    <button type="button" id="paid_btn" data-status="paid" class="btn btn-success mr-2">Mark as Paid</button>
                                @endif
                                <button type="button" id="move_btn" class="btn btn-info mr-2" data-move-to-type="{{ $repairs->type == 'repair' ? 'other' : 'repair' }}">Move To {{ $repairs->type == 'repair' ? 'Other Repair' : 'TV Repair' }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <script>
        $("#move_btn").click(function(e) {
            e.preventDefault();
            var moveType = $(this).data('move-to-type');
            $('#move_type').val(moveType);
            $('#repairsCreate').submit();
        });

        $("#repairsCreate").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $('button[type=submit]', this).prop('disabled', true);
            $.ajax({
                type: "post",
                url: '/dashboard/repairs/edit',
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,

                success: function(response) {
                    if (response.error == 0) {
                        toastr.success(response.msg, 'Success');
                        setInterval(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(response.msg, 'Error');
                    }
                }
            });
            $('#save_btn').prop('disabled', false);
        });

        $("#paid_btn").click(function(e) {
            e.preventDefault();

            var formData = new FormData($("#repairsCreate")[0]);
            formData.append('status', $(this).data('status'));
            $('#paid_btn').prop('disabled', true);
            $.ajax({
                type: "post",
                url: '/dashboard/repairs/mark-paid',
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,

                success: function(response) {
                    if (response.error == 0) {
                        toastr.success(response.msg, 'Success');
                        setInterval(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(response.msg, 'Error');
                    }
                }
            });

            $('#paid_btn').prop('disabled', false);
        });
    </script>
@endsection
