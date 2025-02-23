@extends('pos.app')

@section('dashboard')
    <div class="content-page" id="printArea">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-lg-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Product Purchase Report</h4>
                            </div>
                            <div class="d-lg-flex gap-2 align-items-center">
                                <div class="p-1"><button class="btn btn-primary" onclick="printReport()">Download Report</button> <a href="/dashboard/product-purchase/edit/{{ $purchase->purshace_no }}" class="btn btn-primary" onclick="printReport()">Edit</a></div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group gap-3 align-items-center">
                                            <label class="fs-2">Balance to supplier: <span class="text-danger">{{ currency($purchase->pending, '') }}</span></label>
                                            <div class="form-group d-flex gap-3 align-items-center">
                                                <input type="number" placeholder="Enter amount" id="paymentAmount" class="form-control" style="width: 150px;">
                                                <button class="btn btn-success" onclick="pay('supplier')">Add Payment</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group gap-3 align-items-center">
                                            <label class="fs-2">Balance to shipper: <span class="text-danger">{{ currency($purchase->cbm_price-$shipper_balance, '') }}</span></label>
                                            <div class="form-group d-flex gap-3 align-items-center">
                                                <input type="number" placeholder="Enter amount" id="shipperPaymentAmount" class="form-control" style="width: 150px;">
                                                <button class="btn btn-success" onclick="pay('shipper')">Add Payment</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <div class="form-group d-lg-flex gap-3 align-items-center">
                                            <label>Total in LKR: <span class="">{{ currency($purchase->total, '') }}</span></label>

                                        </div>
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <div class="form-group">
                                            <label>Total in {{ $purchase->currency }}: <span class="">{{ currency($purchase->total_in_currency, '') }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>CBM Price: <span class="">{{ currency($purchase->cbm_price, '') }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cost per {{ $purchase->currency }}: <span class="">{{ currency(divide(($purchase->total+$purchase->cbm_price+$purchase->shipping_charge), $purchase->total_in_currency), '') }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Currency: <span class="">{{ $purchase->currency }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Shipping charge: {{ currency($purchase->shipping_charge, '') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Supplier: <span class="">{{ getSupplier($purchase->supplier_id)->name }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Shipper: <span class="">{{ getShippers($purchase->shipper_id)->company_name }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status: <span class="badge text-bg-{{ statusToBootstrap($purchase->status) }} text-capitalize">{{ $purchase->status }}</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Note:</label>
                                            <textarea name="note" id="" cols="30" rows="5" class="form-control" readonly placeholder="Enter Additional Note">@isset($purchase){{ $purchase->note }}@endisset</textarea>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mt-5">
                                            <table class="table mb-0 tbl-server-info">
                                                <thead class="bg-white text-uppercase">
                                                    <tr class="ligth ligth-data">
                                                        <th class="text-start">Product</th>
                                                        <th class="text-start">Price in {{ $purchase->currency }}</th>
                                                        <th class="text-start">QTY</th>
                                                        <th class="text-start">Cost in LKR</th>
                                                        <th class="text-start">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="ligth-body">
                                                    @foreach (json_decode($purchase->products) as $product)
                                                    <tr>
                                                        <td class="text-start">
                                                            <div class="img">
                                                                <img class="rounded" style="width: 70px;" src="{{ getProductImage($product->sku) }}" alt="">
                                                            </div>
                                                            {{ $product->name }} <div class="form-text">{{ $product->sku }}</div>
                                                        </td>
                                                        <td class="text-start">{{ currency($product->price, '') }}</td>
                                                        <td class="text-start">{{ $product->qty }}</td>
                                                        <td class="text-start">{{ currency(divide(($purchase->total+$purchase->cbm_price+$purchase->shipping_charge), $purchase->total_in_currency) * $product->price, '') }}</td>
                                                        <td class="text-start">
                                                            <a href="/dashboard/products/edit/1002?qty={{ $product->qty }}&cost={{ (divide(($purchase->total+$purchase->cbm_price+$purchase->shipping_charge), $purchase->total_in_currency) * $product->price) }}" class="btn btn-success btn-sm"><i class="fa-solid fa-pencil m-0"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <script>
        function printReport() {
            let printContent = document.getElementById("printArea").innerHTML;
            let originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
        }

        function updateStock(sku, purchase_number) {
            if (confirm('Are you sure you want to update stock?')) {
                $.ajax({
                    type: "post",
                    url: "/dashboard/product-purchase/update-stock",
                    data: {sku: sku, purchase_number: purchase_number, _token: '{{ csrf_token() }}'},
                    dataType: "json",
                    success: function (response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                        }
                        else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
        }

        function pay(to) {
            if (confirm('Are you sure you want to pay?')) {
                $.ajax({
                    type: "post",
                    url: "/dashboard/product-purchase/pay",
                    data: {id: '{{ $purchase->id }}', amount: (to == 'supplier'? $('#paymentAmount').val() : $('#shipperPaymentAmount').val()), to: to, _token: '{{ csrf_token() }}'},
                    dataType: "json",
                    success: function (response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                        else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
        }
    </script>
@endsection
