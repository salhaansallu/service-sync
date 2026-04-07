@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">
                                    @isset($quotation) Edit @else Add @endisset Sales Quotation
                                </h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="salesQuotationForm" action="" onsubmit="return false;">
                                @csrf
                                <input type="hidden" name="modelid" value="@isset($quotation){{ $quotation->id }}@endisset">

                                {{-- Customer Details --}}
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Name <span class="text-danger">*</span></label>
                                            <input type="text" name="customer_name" class="form-control"
                                                value="@isset($quotation){{ $quotation->customer_name }}@endisset"
                                                placeholder="Customer Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Phone <span class="text-danger">*</span></label>
                                            <input type="text" name="customer_phone" class="form-control"
                                                value="@isset($quotation){{ $quotation->customer_phone }}@endisset"
                                                placeholder="Phone Number" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- Items Table --}}
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label>Items <span class="text-danger">*</span></label>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="items-table">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Item Name</th>
                                                        <th style="width:110px;">Qty</th>
                                                        <th style="width:140px;">Unit Price</th>
                                                        <th style="width:140px;">Total</th>
                                                        <th style="width:50px;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="items-body">
                                                    @isset($quotation)
                                                        @foreach ($quotation->items as $item)
                                                            <tr class="item-row">
                                                                <td>
                                                                    <input type="text" name="item_name[]"
                                                                        class="form-control item-name-input"
                                                                        list="item-suggestions"
                                                                        value="{{ $item['name'] }}"
                                                                        placeholder="Item name" required>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="item_qty[]"
                                                                        class="form-control item-qty"
                                                                        value="{{ $item['qty'] }}"
                                                                        min="0" step="any" required>
                                                                </td>
                                                                <td>
                                                                    <input type="number" name="item_unit_price[]"
                                                                        class="form-control item-unit-price"
                                                                        value="{{ $item['unit_price'] }}"
                                                                        min="0" step="any" required>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="item_total[]"
                                                                        class="form-control item-total bg-light"
                                                                        value="{{ $item['total'] }}" readonly>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="ri-delete-bin-line"></i></button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr class="item-row">
                                                            <td>
                                                                <input type="text" name="item_name[]"
                                                                    class="form-control item-name-input"
                                                                    list="item-suggestions"
                                                                    placeholder="Item name" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="item_qty[]"
                                                                    class="form-control item-qty"
                                                                    value="1" min="0" step="any" required>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="item_unit_price[]"
                                                                    class="form-control item-unit-price"
                                                                    value="0" min="0" step="any" required>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="item_total[]"
                                                                    class="form-control item-total bg-light"
                                                                    value="0.00" readonly>
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-danger remove-row"><i class="ri-delete-bin-line"></i></button>
                                                            </td>
                                                        </tr>
                                                    @endisset
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="5">
                                                            <button type="button" id="add-row-btn" class="btn btn-sm btn-outline-primary">
                                                                <i class="fa-solid fa-plus me-1"></i> Add Row
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr class="fw-bold">
                                                        <td colspan="3" class="text-end">Grand Total:</td>
                                                        <td><input type="text" id="grand-total" class="form-control bg-light fw-bold" readonly value="{{ isset($quotation) ? $quotation->total : '0.00' }}"></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- Note --}}
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea name="note" class="form-control" rows="3"
                                                placeholder="Optional note for this quotation">@isset($quotation){{ $quotation->note }}@endisset</textarea>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" id="sq_save_btn" class="btn btn-primary mr-2">
                                    @isset($quotation) Update Sales Quotation @else Add Sales Quotation @endisset
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Datalist for item name suggestions --}}
    <datalist id="item-suggestions">
        @foreach ($suggestions as $suggestion)
            <option value="{{ $suggestion }}">
        @endforeach
    </datalist>

    <script>
        // Row template for adding new rows
        function newRow() {
            return `<tr class="item-row">
                <td><input type="text" name="item_name[]" class="form-control item-name-input" list="item-suggestions" placeholder="Item name" required></td>
                <td><input type="number" name="item_qty[]" class="form-control item-qty" value="1" min="0" step="any" required></td>
                <td><input type="number" name="item_unit_price[]" class="form-control item-unit-price" value="0" min="0" step="any" required></td>
                <td><input type="text" name="item_total[]" class="form-control item-total bg-light" value="0.00" readonly></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="ri-delete-bin-line"></i></button></td>
            </tr>`;
        }

        $(document).on('click', '#add-row-btn', function () {
            $('#items-body').append(newRow());
        });

        $(document).on('click', '.remove-row', function () {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                updateGrandTotal();
            } else {
                toastr.warning('At least one item is required', 'Warning');
            }
        });

        $(document).on('input', '.item-qty, .item-unit-price', function () {
            var row = $(this).closest('tr');
            var qty = parseFloat(row.find('.item-qty').val()) || 0;
            var unit = parseFloat(row.find('.item-unit-price').val()) || 0;
            row.find('.item-total').val((qty * unit).toFixed(2));
            updateGrandTotal();
        });

        function updateGrandTotal() {
            var total = 0;
            $('.item-total').each(function () {
                total += parseFloat($(this).val()) || 0;
            });
            $('#grand-total').val(total.toFixed(2));
        }

        // Submit
        $('#salesQuotationForm').submit(function (e) {
            e.preventDefault();
            $('#sq_save_btn').prop('disabled', true);

            var formData = new FormData(this);
            @isset($quotation)
            var url = '/dashboard/sales-quotations/edit';
            @else
            var url = '/dashboard/sales-quotations/create';
            @endisset

            $.ajax({
                type: 'post',
                url: url,
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (response) {
                    $('#sq_save_btn').prop('disabled', false);
                    if (response.error == 0) {
                        toastr.success(response.msg, 'Success');
                        @unless(isset($quotation))
                        setTimeout(function () { location.href = '/dashboard/sales-quotations'; }, 2000);
                        @endunless
                    } else {
                        toastr.error(response.msg, 'Error');
                    }
                },
                error: function () {
                    $('#sq_save_btn').prop('disabled', false);
                    toastr.error('Something went wrong, please try again', 'Error');
                }
            });
        });

        // Init grand total on page load
        updateGrandTotal();
    </script>
@endsection
