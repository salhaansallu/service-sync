@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Personal Credit</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="PurchaseCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf

                                @isset($credit)
                                <input type="hidden" name="modelid" value="{{ $credit->id }}">
                                @endisset

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Order number <span class="text-danger">*</span></label>
                                            <select id="" class="form-control select2-multiple" name="bill_no" required>
                                                <option value="">Select Bill</option>
                                                @isset($credit)
                                                    <option value="{{ $credit->bill_no }}" selected>{{ $credit->bill_no }}</option>
                                                @endisset
                                                @foreach ($bills as $item)
                                                    <option @isset($credit) {{ $item->bill_no == $credit->bill_no ? 'selected' : '' }} @endisset value="{{ $item->bill_no }}">{{ $item->bill_no }}</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Amount <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Amount"
                                                name="amount"
                                                value="@isset($credit){{ $credit->amount }}@else{{ '0' }}@endisset" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status <span class="text-danger">*</span></label>
                                            <select id="" class="form-control" name="status" required>
                                                <option @isset($credit) {{ $credit->status == 'Pending' ? 'selected' : '' }} @endisset value="Pending">Pending</option>
                                                <option @isset($credit) {{ $credit->status == 'Delivered' ? 'selected' : '' }} @endisset value="Delivered">Delivered</option>
                                                <option @isset($credit) {{ $credit->status == 'Paid' ? 'selected' : '' }} @endisset value="Paid">Paid</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea name="note" id="" cols="30" rows="5" class="form-control" placeholder="Enter Additional Note">@isset($credit){{ $credit->note }}@endisset</textarea>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">@isset($credit) Update credit @else Add credit @endisset</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    @isset($credit)
        <script>
            $("#PurchaseCreate").submit(function(e) {
                e.preventDefault();

                $('#save_btn').prop('disabled', true);
                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/personal-credits/edit',
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
                    url: '/dashboard/personal-credits/create',
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
