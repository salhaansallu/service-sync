@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            @php
            if (isset($args)) {
                if (isset($args["error"]) && isset($args["message"])) {
                    # code...
                }
            }

                if (isset($_GET['error']) && isset($_GET['message'])) {
                    echo '
                            <div class="alert '.(sanitize($_GET['error']) == 0? 'alert-success' : 'alert-danger').' alert-dismissible fade show" role="alert">
                              ' .
                        str_replace('-', ' ', sanitize($_GET['message'])) .
                        '
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                    ';
                }
            @endphp
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Petty Cash</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="/dashboard/petty-cash/reload" method="POST" data-toggle="validator">
                                @csrf
                                <input type="hidden" name="model_id" value="{{ isset($id) ? $id : '' }}">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Petty Cash Amount <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Amount"
                                                name="amount" value="" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea name="note" id="" cols="30" rows="5" class="form-control"
                                                placeholder="Enter Additional Note">{{ isset($_GET['note']) ? sanitize($_GET['note']) : '' }}</textarea>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Add</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex">
                            <div class="header-title row w-100">
                                <div class="col-md-7"><h4 class="card-title text-left">Petty Cash Balance</h4></div>
                                <div class="col-md-5"><a class="d-block text-end" href="/dashboard/petty-cash/{{ isset($id) ? $id : '' }}/list" >See all transactions</a></div>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h1
                                class="fw-bold text-center my-3 {{ isset($balance) && is_numeric($balance) ? ($balance > 0 ? 'text-success' : 'text-danger') : 'text-danger' }}">
                                {{ isset($balance) ? currency($balance) : 0.0 }}</h1>

                                <form class="pt-4" action="/dashboard/petty-cash/transfer" method="POST" data-toggle="validator">
                                    @csrf
                                    <input type="hidden" name="model_id" value="{{ isset($id) ? $id : '' }}">
                                    <div class="row justify-content-center">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Transfer Amount <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" placeholder="Enter Amount"
                                                    name="amount" value="" required>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Transfer Department <span class="text-danger">*</span></label>
                                                    <select id="" class="form-control" name="department" required>
                                                        <option value="other">-- Select --</option>
                                                        @foreach (getDepartments() as $item)
                                                            <option value="{{ $item['slug'] }}">{{ $item['name'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mr-2">Transfer</button>
                                </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Add Purchases</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="addPurchase" action="/dashboard/purchase/create" data-toggle="validator" method="POST" onsubmit="return false;">
                                        @csrf
                                        <input type="hidden" name="model_id" value="{{ isset($id) ? $id : '' }}">
                                        <input type="hidden" name="ajax" value="false">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Purchase number <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Purchase Number" id="BarCodeValue"
                                                        name="purchase_number" value="" required>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Price <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" placeholder="Enter Price"
                                                        name="price" value="" required>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Stock <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" placeholder="Enter Sock"
                                                        name="stock" value="" required>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Discount </label>
                                                    <input type="text" class="form-control" placeholder="Enter Discount"
                                                        name="discount" value="">
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Shipping charge </label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Shipping Charge" name="shipping_charge"
                                                        value="">
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Supplier <span class="text-danger">*</span></label>
                                                    <select id="" class="form-control" name="supplier" required>
                                                        <option value="other">Other</option>
                                                        @foreach (getSupplier('all') as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <textarea name="note" id="" cols="30" rows="5" class="form-control"
                                                        placeholder="Enter Additional Note"></textarea>
                                                    <div class="help-block with-errors"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" id="save_btn" class="btn btn-primary mr-2">Add purchase</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div class="header-title">
                                        <h4 class="card-title">Purchase List</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive rounded mb-3">
                                        <table class="data-table table mb-0 tbl-server-info">
                                            <thead class="bg-white text-uppercase">
                                                <tr class="ligth ligth-data">
                                                    <th class="text-start">Purchase number</th>
                                                    <th class="text-start">Price</th>
                                                    <th class="text-start">Stock</th>
                                                    <th class="text-start">Discount</th>
                                                    <th class="text-start">Shipping charge</th>
                                                    <th class="text-start">Supplier</th>
                                                    <th class="text-start">Note</th>
                                                    <th class="text-start">Purchase date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="ligth-body">
                                                @if (isset($purchases) && $purchases->count() > 0)
                                                    @foreach ($purchases as $item)
                                                        <tr>
                                                            <td class="text-start">{{ $item->purshace_no }}</td>
                                                            <td class="text-start">{{ $item->price }}</td>
                                                            <td class="text-start">{{ $item->qty }}</td>
                                                            <td class="text-start">{{ $item->discount }}</td>
                                                            <td class="text-start">{{ $item->shipping_charge }}</td>
                                                            <td class="text-start">{{ $item->supplier_id }}</td>
                                                            <td class="text-start">{{ $item->note }}</td>
                                                            <td class="text-start">{{ $item->created_at }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <script>
        $("#addPurchase").submit(function(e) {
            e.preventDefault();

            $('#save_btn').prop('disabled', true);
            var formData = new FormData(this);
            formData.append('department', '{{ isset($id) ? $id : '' }}');
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
                        location.href="/dashboard/petty-cash/{{ isset($id) ? $id : '' }}?error=0&message="+response.msg.replaceAll(' ', '-');
                    } else {
                        toastr.error(response.msg, 'Error');
                    }
                }
            });
            $('#save_btn').prop('disabled', false);
        });
    </script>
@endsection
