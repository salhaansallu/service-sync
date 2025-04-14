@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Add Expense</h4>
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
                                        <div class="form-group" style="position: relative;">
                                            <label>Item <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter Item Name" id="item"
                                                name="item"
                                                value="@isset($purchase){{ $purchase->item }}@endisset"
                                                required>

                                            <div class="autocomplete_panel">
                                                <ul class="autocomplete_list">
                                                    {{--  --}}
                                                </ul>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select Supplier <span class="text-danger">*</span></label>
                                            <select name="supplier" id="" class="form-control select2" required>
                                                <option value="0">None</option>
                                                @foreach (getSupplier('all') as $item)
                                                    <option value="{{ $item->id }}"
                                                        @isset($purchase){{ $purchase->supplier_id == $item->id ? 'selected' : '' }}@endisset>{{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select User <span class="text-danger">*</span></label>
                                            <select name="user" id="" class="form-control select2" required>
                                                <option value="">-- Select User --</option>
                                                @foreach (getUsers() as $item)
                                                    <option value="{{ $item->id }}"
                                                        @isset($purchase){{ $purchase->user == $item->id ? 'selected' : '' }}@endisset>{{ $item->fname. ' '.$item->lname }}
                                                    </option>
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
                                                value="@isset($purchase){{ $purchase->amount }}@else{{ '0' }}@endisset"
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
                                            <label>Payment Method <span class="text-danger">*</span></label>
                                            <select name="payment" id="" class="form-control" required>
                                                <option @isset($purchase){{ $purchase->payment == 'Cash' ? 'selected' : '' }}@endisset value="Cash">Cash</option>
                                                <option @isset($purchase){{ $purchase->payment == 'Card' ? 'selected' : '' }}@endisset value="Card">Card</option>
                                                <option @isset($purchase){{ $purchase->payment == 'Cheque' ? 'selected' : '' }}@endisset value="Cheque">Cheque</option>
                                                <option @isset($purchase){{ $purchase->payment == 'Credit' ? 'selected' : '' }}@endisset value="Credit">Credit</option>
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
                                <button type="submit" id="save_btn" class="btn btn-primary mr-2">
                                    @isset($purchase)
                                        Update expense
                                    @else
                                        Add expense
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

    <script>
        $("#item").keyup(function(e) {
            e.preventDefault();

            $('.autocomplete_panel').hide();

            $.ajax({
                type: "post",
                url: '/dashboard/expense/get-items',
                data: {
                    item: $(this).val(),
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",

                success: function(response) {
                    if (response.error == 0) {

                        var html = '';
                        $.each(response.data, function(index, value) {
                            html += '<li class="list-items">' + value['item'] + '</li>';
                        });
                        $('.autocomplete_list').html(html);
                        $('.autocomplete_panel').show();

                        var list = document.querySelectorAll('.list-items');
                        list.forEach(function(item) {
                            item.addEventListener('click', function() {
                                $('#item').val(this.innerText);
                                $('.autocomplete_panel').hide();
                            });
                        });

                    } else {
                        $('.autocomplete_panel').hide();
                    }
                }
            });
        });
    </script>

    @isset($purchase)
        <script>
            $("#PurchaseCreate").submit(function(e) {
                e.preventDefault();

                $('#save_btn').prop('disabled', true);
                var formData = new FormData(this);
                $.ajax({
                    type: "post",
                    url: '/dashboard/expense/edit',
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
                    url: '/dashboard/expense/create',
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
