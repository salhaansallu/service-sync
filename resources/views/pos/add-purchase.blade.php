@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Purchase</h4>
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
                                            <label>Purchase number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Purchase Number" id="BarCodeValue"
                                                name="purchase_number"
                                                value="@isset($purchase){{ $purchase->purshace_no }}@endisset" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Price <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Price"
                                                name="price"
                                                value="@isset($purchase){{ $purchase->price }}@else{{ '0' }}@endisset" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Stock <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Sock"
                                                name="stock"
                                                value="@isset($purchase){{ $purchase->qty }}@else{{ '0' }}@endisset" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Discount </label>
                                            <input type="text" class="form-control" placeholder="Enter Discount"
                                                name="discount"
                                                value="@isset($purchase){{ $purchase->discount }}@else{{ '0' }}@endisset">
                                            <div class="help-block with-errors"></div>
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
                                            <select id="" class="form-control" name="supplier" required>
                                                <option value="other">Other</option>
                                                @foreach (getSupplier('all') as $item)
                                                    <option @isset($purchase) {{ $item->id == $purchase->supplier_id ? 'selected' : '' }} @endisset value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea name="note" id="" cols="30" rows="5" class="form-control" placeholder="Enter Additional Note">@isset($purchase){{ $purchase->note }}@endisset</textarea>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">@isset($purchase) Update purchase @else Add purchase @endisset</button>
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
                    url: '/dashboard/purchase/edit',
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
                    url: '/dashboard/purchase/create',
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
