@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add China Order</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="PurchaseCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                @isset($purchase)
                                    <input type="hidden" name="modelid" value="{{ $purchase->id }}">
                                @endisset

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Order no <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Order no"
                                                id="BarCodeValue" name="purchase_no"
                                                value="@isset($purchase){{ $purchase->purchase_no }}@endisset"
                                                required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bill no <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Bill no"
                                                name="bill_no"
                                                value="@isset($purchase){{ $purchase->bill_no }}@endisset"
                                                required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Panel no</label>
                                            <input type="text" class="form-control" placeholder="Enter Panel no"
                                                name="panel_no" value="@isset($purchase){{ $purchase->panel_no }}@endisset">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>PCB no </label>
                                            <input type="text" class="form-control" placeholder="Enter PCB no"
                                                name="pcb_no" value="@isset($purchase){{ $purchase->pcb_no }}@endisset">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Price <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Price"
                                                name="price"
                                                value="@isset($purchase){{ $purchase->price }}@else{{ '0' }}@endisset"
                                                required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>QTY <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter QTY"
                                                name="qty"
                                                value="@isset($purchase){{ $purchase->qty }}@else{{ '0' }}@endisset"
                                                required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Order date</label>
                                            <input type="date" class="form-control" name="order_date"
                                                value="@isset($purchase){{ date('Y-m-d', strtotime($purchase->created_at)) }}@else{{ date('Y-m-d') }}@endisset">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Delivery date </label>
                                            <input type="date" class="form-control" name="delivery_date"
                                                value="@isset($purchase){{ !empty($purchase->delivery_date)? date('Y-m-d', strtotime($purchase->delivery_date)) : '' }}@else{{ date('Y-m-d') }}@endisset">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select id="" class="form-control" name="status" required>
                                                <option @isset($purchase){{ $purchase->status == "Pending"? 'selected' : '' }}@endisset value="Pending">Pending</option>
                                                <option @isset($purchase){{ $purchase->status == "Purchased"? 'selected' : '' }}@endisset value="Purchased">Purchased</option>
                                                <option @isset($purchase){{ $purchase->status == "Canceled"? 'selected' : '' }}@endisset value="Canceled">Canceled</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">
                                    @isset($purchase)
                                        Update order
                                    @else
                                        Add order
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

    @isset($purchase)
        <script>
            $("#PurchaseCreate").submit(function(e) {
                e.preventDefault();

                $('#save_btn').prop('disabled', true);
                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/china-order-update',
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
            $("#PurchaseCreate").submit(function(e) {
                e.preventDefault();

                $('#save_btn').prop('disabled', true);
                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/china-order-add',
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
