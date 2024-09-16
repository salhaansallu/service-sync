@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Delivery Details</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="repairsCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf
                                <input type="hidden" name="modelid" value="{{ $orders->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bill No</label>
                                            <input type="text" class="form-control" disabled
                                                value="{{ $orders->bill_no }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Payment Method</label>
                                            <select {{ $orders->status == 'Delivered' ? 'disabled' : '' }}
                                                name="payment_method" class="form-control">
                                                <option {{ $orders->payment_method == 'COD' ? 'selected' : '' }}
                                                    value="COD">COD</option>
                                                <option {{ $orders->payment_method == 'Card Payment' ? 'selected' : '' }}
                                                    value="Card Payment">Card Payment</option>
                                                <option {{ $orders->payment_method == 'Bank Transfer' ? 'selected' : '' }}
                                                    value="Bank Transfer">Bank Transfer</option>
                                                <option {{ $orders->payment_method == 'Cheque' ? 'selected' : '' }}
                                                    value="Cheque">Cheque</option>
                                                <option {{ $orders->payment_method == 'Other' ? 'selected' : '' }}
                                                    value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Delivery Service </label>
                                            <input {{ $orders->status == 'Delivered' ? 'disabled' : '' }} type="text"
                                                class="form-control" name="delivery" value="{{ $orders->delivery }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Delivery Charge </label>
                                            <input {{ $orders->status == 'Delivered' ? 'disabled' : '' }} type="text"
                                                class="form-control" name="charge" value="{{ $orders->charge }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tracking Code <span class="form-text d-inline">(Scan barcode from scanner
                                                    to enter automatically)</span></label>
                                            <input {{ $orders->status == 'Delivered' ? 'disabled' : '' }} type="text"
                                                class="form-control" name="code" value="{{ $orders->tracking_code }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select {{ $orders->status == 'Delivered' ? 'disabled' : '' }} name="status"
                                                id="status" class="form-control">
                                                <option {{ $orders->status == 'Processing' ? 'selected' : '' }}
                                                    value="Processing">Processing</option>
                                                <option {{ $orders->status == 'Delivered' ? 'selected' : '' }}
                                                    value="Delivered">Delivered</option>
                                                <option {{ $orders->status == 'Returned' ? 'selected' : '' }}
                                                    value="Returned">Return</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea {{ $orders->status == 'Delivered' ? 'disabled' : '' }} name="note" class="form-control" id=""
                                                rows="5">{{ str_replace(['<br>', ' <br> ', ' <br>', '<br> '], PHP_EOL, $orders->note) }}</textarea>
                                        </div>
                                    </div>

                                    

                                </div>
                                <button type="submit" {{ $orders->status == 'Delivered' ? 'disabled' : '' }}
                                    id="save_btn" class="btn btn-primary mr-2">Update</button>
                            </form>
                        </div>
                    </div>
                </div>

                @if (isset($products))
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <div class="header-title">
                                    <h4 class="card-title">Edit Sale</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="repairsEdit" action="" data-toggle="validator" onsubmit="return false;">
                                    @csrf
                                    <input type="hidden" name="modelid" value="{{ $products->id }}">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Bill No</label>
                                                <input type="text" class="form-control" disabled
                                                    value="{{ $products->bill_no }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Customer <span class="text-danger">*</span></label>
                                                <select name="customer" class="form-control"
                                                    {{ $orders->status == 'Delivered' ? 'disabled' : '' }} required>
                                                    @foreach ($customers as $customer)
                                                        <option @if ($customer->id == $products->customer) selected @endif
                                                            value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Note</label>
                                                <textarea name="note" {{ $orders->status == 'Delivered' ? 'disabled' : '' }} class="form-control" id=""
                                                    rows="5">{{ str_replace(['<br>', ' <br> ', ' <br>', '<br> '], PHP_EOL, $products->note) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" {{ $orders->status == 'Delivered' ? 'disabled' : '' }}
                                        id="edit_btn" class="btn btn-primary mr-2">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="table-responsive rounded mb-3">
                            <table class="data-table table mb-0 tbl-server-info">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th class="text-start">SKU (Code)</th>
                                        <th class="text-start">Name</th>
                                        <th class="text-start">QTY</th>
                                        <th class="text-start">Total</th>
                                        <th class="text-start">Cost</th>
                                        <th class="text-start">Profit</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    @foreach ((object)json_decode(htmlspecialchars_decode($products->products)) as $item)
                                        <tr>
                                            <td class="text-start">{{ $item->sku }}</td>
                                            <td class="text-start">{{ $item->name }}</td>
                                            <td class="text-start">{{ currency($item->qty) }}</td>
                                            <td class="text-start">{{ currency($item->unit * $item->qty) }}</td>
                                            <td class="text-start">{{ currency($item->cost * $item->qty) }}</td>
                                            <td class="text-start">{{ currency(($item->unit * $item->qty) - ($item->cost * $item->qty)) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <div class="modal fade" id="Confirmation" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="Confirmation" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h4>Please confirm to update</h4>
                    <p class="fs-6 mt-3">Please don't proceed with status <strong>Delivered</strong> untill the product is
                        dispatched. This updates the stock</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="SubmitForm()" class="btn btn-primary">Understood</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#save_btn").click(function(e) {
            e.preventDefault();

            if ($("#status").val() == "Delivered") {
                $("#Confirmation").modal('toggle');
                return;
            }

            SubmitForm();
        });

        $("#repairsCreate").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $('#save_btn').prop('disabled', true);
            $.ajax({
                type: "post",
                url: '/dashboard/order/edit',
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,

                success: function(response) {
                    if (response.error == 0) {
                        toastr.success(response.msg, 'Success');
                        if (response.hasOwnProperty('url') && response.url != "") {
                            window.open(response.url, '_blank');
                        }
                    } else {
                        toastr.error(response.msg, 'Error');
                    }
                }
            });
            $('#save_btn').prop('disabled', false);
        });

        $("#repairsEdit").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $('#edit_btn').prop('disabled', true);
            $.ajax({
                type: "post",
                url: '/dashboard/bill/edit',
                data: formData,
                dataType: "json",
                contentType: false,
                processData: false,

                success: function(response) {
                    if (response.error == 0) {
                        toastr.success(response.msg, 'Success');
                        if (response.hasOwnProperty('url') && response.url != "") {
                            location.reload();
                            window.open(response.url, '_blank');
                        }
                    } else {
                        toastr.error(response.msg, 'Error');
                    }
                }
            });
            $('#edit_btn').prop('disabled', false);
        });

        function SubmitForm() {
            $("#repairsCreate").submit();
            $("#Confirmation").modal('hide');
        }
    </script>
@endsection
