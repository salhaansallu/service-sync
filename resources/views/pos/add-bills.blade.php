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
                                            <label>Cashier </label>
                                            <input type="text" class="form-control" disabled value="{{ getUser($repairs->cashier)->fname }}">
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
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select name="status" class="form-control" required>
                                                <option @if($repairs->status == "Delivered") selected @endif value="Delivered">Delivered</option>
                                                <option @if($repairs->status == "Return") selected @endif value="Return">Return</option>
                                                <option @if($repairs->status == "Pending") selected @endif value="Pending">Pending</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea name="note" class="form-control" id="" rows="5">{{ str_replace(['<br>', ' <br> ', ' <br>', '<br> '], PHP_EOL, $repairs->note) }}</textarea>
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
                url: '/dashboard/bill/edit',
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
