@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="header-title">
                                <h4 class="card-title mb-1">3rd Party Product Returns</h4>
                                <p class="mb-0 text-muted">Return Fix AI products that were used in repairs.</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="thirdPartyReturnForm" onsubmit="return false;">
                                @csrf

                                <div class="d-flex gap-3 mb-4">
                                    <button type="button" id="addRowBtn" class="btn btn-primary">
                                        Add Product
                                    </button>
                                    <button type="button" id="reloadProductsBtn" class="btn btn-outline-secondary">
                                        Reload Products
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table mb-0 tbl-server-info">
                                        <thead class="bg-white text-uppercase">
                                            <tr class="ligth ligth-data">
                                                <th>Product</th>
                                                <th style="width: 180px;">Available Qty</th>
                                                <th style="width: 180px;">Return Qty</th>
                                                <th style="width: 120px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="thirdPartyReturnRows"></tbody>
                                    </table>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" id="submitReturnBtn" class="btn btn-primary">
                                        Submit Return
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const thirdPartyReturnState = {
            products: [],
            rowCount: 0,
        };

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function normalizeThirdPartyProduct(product) {
            const qty = product.qty ?? product.quantity ?? product.stock ?? '';
            const name = product.pro_name ?? product.name ?? product.title ?? `Product #${product.id}`;
            const sku = product.sku ? ` (${product.sku})` : '';

            return {
                id: product.id,
                name: `${name}${sku}`,
                qty: qty,
            };
        }

        function getProductOptions(selectedId = '') {
            const defaultOption = '<option value="">-- Select Product --</option>';
            const options = thirdPartyReturnState.products.map(product => {
                const selected = String(product.id) === String(selectedId) ? 'selected' : '';
                return `<option value="${escapeHtml(product.id)}" ${selected}>${escapeHtml(product.name)}</option>`;
            });

            return defaultOption + options.join('');
        }

        function refreshSelect2() {
            $('.third-party-product-select').select2({
                width: '100%',
            });
        }

        function updateRowMeta(row) {
            const selectedId = $(row).find('.third-party-product-select').val();
            const product = thirdPartyReturnState.products.find(item => String(item.id) === String(selectedId));
            $(row).find('.available-qty').text(product ? (product.qty === '' ? 'N/A' : product.qty) : '-');
        }

        function addReturnRow(selectedId = '', qty = '') {
            thirdPartyReturnState.rowCount += 1;
            const rowHtml = `
                <tr data-row-id="${thirdPartyReturnState.rowCount}">
                    <td>
                        <select class="form-control third-party-product-select">
                            ${getProductOptions(selectedId)}
                        </select>
                    </td>
                    <td class="available-qty align-middle">-</td>
                    <td>
                        <input type="number" class="form-control return-qty" value="${qty}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-danger remove-row-btn">Remove</button>
                    </td>
                </tr>
            `;

            $('#thirdPartyReturnRows').append(rowHtml);
            refreshSelect2();
            updateRowMeta($('#thirdPartyReturnRows tr').last());
        }

        function collectReturnProducts() {
            const products = [];
            let hasError = false;

            $('#thirdPartyReturnRows tr').each(function() {
                const productId = $(this).find('.third-party-product-select').val();
                const qty = $(this).find('.return-qty').val();

                if (!productId || qty === '') {
                    hasError = true;
                    return false;
                }

                products.push({
                    id: productId,
                    qty: qty,
                });
            });

            if (hasError) {
                return null;
            }

            return products;
        }

        function setSubmitState(disabled) {
            $('#submitReturnBtn').prop('disabled', disabled);
            $('#addRowBtn').prop('disabled', disabled);
            $('#reloadProductsBtn').prop('disabled', disabled);
        }

        function loadThirdPartyProducts(showSuccessToast = false) {
            setSubmitState(true);

            $.ajax({
                type: 'POST',
                url: '/dashboard/repairs/third-party-returns/products',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                dataType: 'json',
                success: function(response) {
                    if (response.error == 0 && Array.isArray(response.data)) {
                        thirdPartyReturnState.products = response.data.map(normalizeThirdPartyProduct);

                        const existingRows = $('#thirdPartyReturnRows tr').length;
                        $('#thirdPartyReturnRows').empty();

                        if (existingRows > 0) {
                            for (let i = 0; i < existingRows; i++) {
                                addReturnRow();
                            }
                        } else {
                            addReturnRow();
                        }

                        if (showSuccessToast) {
                            toastr.success(response.msg || 'Products loaded successfully', 'Success');
                        }
                    } else {
                        toastr.error(response.msg || 'Failed to load products', 'Error');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.msg || 'Failed to load products';
                    toastr.error(message, 'Error');
                },
                complete: function() {
                    setSubmitState(false);
                }
            });
        }

        $(document).ready(function() {
            loadThirdPartyProducts();

            $('#addRowBtn').on('click', function() {
                if (thirdPartyReturnState.products.length === 0) {
                    toastr.error('Load products before adding rows', 'Error');
                    return;
                }

                addReturnRow();
            });

            $('#reloadProductsBtn').on('click', function() {
                loadThirdPartyProducts(true);
            });

            $(document).on('change', '.third-party-product-select', function() {
                updateRowMeta($(this).closest('tr'));
            });

            $(document).on('click', '.remove-row-btn', function() {
                if ($('#thirdPartyReturnRows tr').length === 1) {
                    $(this).closest('tr').find('.third-party-product-select').val('').trigger('change');
                    $(this).closest('tr').find('.return-qty').val('');
                    updateRowMeta($(this).closest('tr'));
                    return;
                }

                $(this).closest('tr').remove();
            });

            $('#thirdPartyReturnForm').on('submit', function(e) {
                e.preventDefault();

                const products = collectReturnProducts();
                if (!products || products.length === 0) {
                    toastr.error('Please select a product and enter a valid quantity for each row', 'Error');
                    return;
                }

                setSubmitState(true);

                $.ajax({
                    type: 'POST',
                    url: '/dashboard/repairs/third-party-returns/submit',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        products: products,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.error == 0 || response.error === '0') {
                            toastr.success(response.msg || 'Return submitted successfully', 'Success');
                            $('#thirdPartyReturnRows').empty();
                            addReturnRow();
                            loadThirdPartyProducts();
                        } else {
                            toastr.error(response.msg || 'Failed to submit return', 'Error');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.msg || 'Failed to submit return';
                        toastr.error(message, 'Error');
                    },
                    complete: function() {
                        setSubmitState(false);
                    }
                });
            });
        });
    </script>
@endsection
