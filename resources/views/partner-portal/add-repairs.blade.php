@extends('partner-portal.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Repair Details</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="" data-toggle="validator" onsubmit="return false;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bill No</label>
                                            <input readonly type="text" class="form-control" disabled value="{{ $repairs->bill_no }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Cashier </label>
                                                    <input readonly type="text" class="form-control" disabled value="{{ getUser($repairs->cashier)->fname }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Model No <span class="text-danger">*</span></label>
                                            <input readonly type="text" name="model_no" class="form-control" value="{{ $repairs->model_no }}" data-errors="Please Enter Model No." required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Serial No</label>
                                            <input readonly type="text" name="serial_no" class="form-control" value="{{ $repairs->serial_no }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fault</label>
                                            <input readonly type="text" name="fault" class="form-control" value="{{ $repairs->fault }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Delivery</label>
                                            <input readonly type="text" name="delivery" class="form-control" value="{{ $repairs->delivery }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Advance</label>
                                            <input readonly type="text" name="advance" class="form-control" value="{{ $repairs->advance }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Total</label>
                                            <input readonly type="text" name="total" class="form-control" value="{{ $repairs->total }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer <span class="text-danger">*</span></label>
                                            <select disabled name="customer" class="form-control" required>
                                                <option value="">{{ getCustomer($repairs->customer)->name }} ({{ getCustomer($repairs->customer)->phone }})</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Spare Parts</label>
                                            <select disabled name="spares[]" multiple="multiple" class="form-control select2-multiple">
                                                @foreach ($spares as $spare)
                                                    <option @if(in_array($spare->id, (array)json_decode($repairs->spares))) selected @endif >{{ $spare->pro_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select disabled name="status" class="form-control">
                                                <option @if($repairs->status == "Delivered") selected @endif value="Delivered">Delivered</option>
                                                <option @if($repairs->status == "Repaired") selected @endif value="Repaired">Repaired</option>
                                                <option @if($repairs->status == "Return") selected @endif value="Return">Return</option>
                                                <option @if($repairs->status == "Pending") selected @endif value="Pending">Pending</option>
                                                <option @if($repairs->status == "Awaiting Parts") selected @endif value="Awaiting Parts">Awaiting Parts</option>
                                                <option @if($repairs->status == "Customer Pending") selected @endif value="Customer Pending">Customer Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea readonly name="note" class="form-control" id="" rows="5">{{ str_replace(['<br>', ' <br> ', ' <br>', '<br> '], PHP_EOL, $repairs->note) }}</textarea>
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
@endsection
