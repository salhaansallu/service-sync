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

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Advance</label>
                                            <input type="text" name="advance" class="form-control" value="{{ $repairs->advance }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Total</label>
                                            <input type="text" name="total" class="form-control" value="{{ $repairs->total }}">
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
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select name="status" class="form-control" required>
                                                <option @if($repairs->status == "Delivered") selected @endif value="Delivered">Delivered</option>
                                                <option @if($repairs->status == "Repaired") selected @endif value="Repaired">Repaired</option>
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
