@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Product Purchase</h4>
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
                                            <label>Total in LKR <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Total in LKR"
                                                name="total"
                                                value="@isset($purchase){{ $purchase->total }}@else{{ '0' }}@endisset"
                                                required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>CBM Price <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" placeholder="Enter CBM Price"
                                                        name="cbm_price"
                                                        value="@isset($purchase){{ $purchase->cbm_price }}@else{{ '0' }}@endisset"
                                                        required>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Shipper <span class="text-danger">*</span></label>
                                                    <select id="" class="form-control select2"
                                                        name="shipper" required>
                                                        <option value="other">Other</option>
                                                        @foreach (getShippers('all') as $item)
                                                            <option
                                                                @isset($purchase) {{ $item->id == $purchase->shipper_id ? 'selected' : '' }} @endisset
                                                                value="{{ $item->id }}">{{ $item->company_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Shipping charge </label>
                                            <input type="text" class="form-control" placeholder="Enter Shipping Charge"
                                                name="shipping_charge"
                                                value="@isset($purchase){{ $purchase->shipping_charge }}@else{{ '0' }}@endisset">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Supplier <span class="text-danger">*</span></label>
                                            <select id="" class="form-control select2" name="supplier"
                                                required>
                                                <option value="other">Other</option>
                                                @foreach (getSupplier('all') as $item)
                                                    <option
                                                        @isset($purchase) {{ $item->id == $purchase->supplier_id ? 'selected' : '' }} @endisset
                                                        value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select id="" class="form-control" name="status" required>
                                                <option value="pending"
                                                    @isset($purchase) {{ $purchase->status == 'pending' ? 'selected' : '' }} @endisset>
                                                    Pending</option>
                                                <option value="approved"
                                                    @isset($purchase) {{ $purchase->status == 'approved' ? 'selected' : '' }} @endisset>
                                                    Approved</option>
                                                <option value="cancelled"
                                                    @isset($purchase) {{ $purchase->status == 'cancelled' ? 'selected' : '' }} @endisset>
                                                    Cancelled</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Currency <span class="text-danger">*</span></label>
                                            <select id="currencySelector" class="form-control" name="currency" required>
                                                <option value="USD"
                                                    @isset($purchase) {{ $purchase->currency == 'USD' ? 'selected' : '' }}@endisset>
                                                    USD</option>
                                                <option value="LKR"
                                                    @isset($purchase) {{ $purchase->currency == 'LKR' ? 'selected' : '' }}@endisset>
                                                    LKR</option>
                                                <option value="RMB"
                                                    @isset($purchase) {{ $purchase->currency == 'RMB' ? 'selected' : '' }}@endisset>
                                                    RMB</option>
                                                <option value="INR"
                                                    @isset($purchase) {{ $purchase->currency == 'INR' ? 'selected' : '' }}@endisset>
                                                    INR</option>
                                                <option value="GBP"
                                                    @isset($purchase) {{ $purchase->currency == 'GBP' ? 'selected' : '' }}@endisset>
                                                    GBP</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea name="note" id="" cols="30" rows="5" class="form-control"
                                                placeholder="Enter Additional Note">
@isset($purchase)
{{ $purchase->note }}
@endisset
</textarea>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="row" id="purchaseForm">
                                        <purchase-form :products="{{ json_encode(getAllProducts()) }}"
                                            :orderitem="{{ isset($purchase) ? json_encode($purchase->products) : json_encode([]) }}" />
                                    </div>
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2 mt-5">
                                    @isset($purchase)
                                        Update purchase
                                    @else
                                        Add purchase
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
                    url: '/dashboard/product-purchase/edit',
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
                    url: '/dashboard/product-purchase/create',
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

    <script>
        // Run once on load
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
