@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Stock Report</h4>
                        </div>
                        <a href="javascript:void(0)" id="stock_report_download" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Download Report</a>
                    </div>
                </div>
                <div class="col-lg-3 mb-3 {{ Request::is('dashboard/low-stock-products') ? '' : 'd-none' }}">
                    <form action="" method="get">
                        <div class="form-group">
                            <label for="">Minimum stock amount</label>
                            <input type="text" class="form-control" name="qty" placeholder="Enter minimum stock amount">
                        </div>
                    </form>
                </div>
                <div class="col-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Product</th>
                                    <th class="text-start">Code (SKU)</th>
                                    <th class="text-start">Price</th>
                                    <th class="text-start">Cost</th>
                                    <th class="text-start">Stock</th>
                                    <th class="text-start">Stock Value</th>
                                    @if (isAdmin())
                                    <th class="text-start">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($stocks && count($stocks) > 0)
                                @foreach ($stocks as $item)
                                <tr id="pro{{ $item->sku }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ productImage($item->pro_image) }}" class="img-fluid rounded avatar-50 mr-3"
                                                alt="image">
                                            <div class="text-start">
                                                {{ $item->pro_name }}
                                                {{-- <p class="mb-0" > <small></small></p > --}}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-start">{{ $item->sku }}</td>
                                    <td class="text-start">{{ $item->price }}</td>
                                    <td class="text-start">{{ $item->cost }}</td>
                                    <td class="text-start">{{ $item->qty }}</td>
                                    <td class="text-start">{{ currency(($item->cost * $item->qty), '') }}</td>
                                    @if (isAdmin())
                                    <td class="text-start">
                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit product"
                                                data-original-title="Edit" href="/dashboard/products/edit/{{ $item->sku }}"><i class="ri-pencil-line mr-0"></i></a>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <script>
        function deleteProduct(sku) {
            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    type: "delete",
                    url: "/dashboard/products/delete",
                    data: {sku: sku, _token: '{{ csrf_token() }}'},
                    dataType: "json",
                    success: function (response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            $("#pro"+sku).remove();
                        }
                        else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
            return 0;
        }

        $('#stock_report_download').click(function (e) {
            e.preventDefault();
            toastr.warning('Please wait while the report is generated', 'Please wait..');
            $.ajax({
                type: "post",
                url: "{{ Request::is('dashboard/low-stock-products') ? '/dashboard/low-stock-report' : '/dashboard/stock/get-report' }}",
                data: {_token: '{{ csrf_token() }}', qty: "{{ isset($_GET['qty']) ? sanitize($_GET['qty']) : 5 }}"},
                dataType: "json",
                success: function (response) {
                    if (response.error == 0) {
                        toastr.success(response.msg, "Success");
                        window.open('/invoice/'+response.url, '_blank');
                    }
                    else {
                        toastr.error(response.msg, "Error");
                    }
                }
            });
        });
    </script>
@endsection
