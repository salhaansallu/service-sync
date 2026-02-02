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
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <script>
        $("#repairsCreate").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $('#save_btn').prop('disabled', true);
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
                        }, 3000);
                    } else {
                        toastr.error(response.msg, 'Error');
                    }
                }
            });
            $('#save_btn').prop('disabled', false);
        });
    </script>
@endsection
