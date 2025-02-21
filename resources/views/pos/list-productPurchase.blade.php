@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Product Purchase List</h4>
                            <p class="mb-0">The Product purchase list effectively dictates Product purchase presentation and provides
                                space<br> to list your Product purchases and offering in the most appealing way.</p>
                        </div>
                        <div class="d-block">
                            <a href="/dashboard/product-purchases?q=all" class="btn btn-success add-list"></i>List All Purchases</a>
                            <a href="/dashboard/product-purchase/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Purchase</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Action</th>
                                    <th class="text-start">Purchase number</th>
                                    <th class="text-start">Total</th>
                                    <th class="text-start">QTY</th>
                                    <th class="text-start">CBM Price</th>
                                    <th class="text-start">Shipping Charge</th>
                                    <th class="text-start">Total in Currency</th>
                                    <th class="text-start">Supplier</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($purchses && $purchses->count() > 0)
                                @foreach ($purchses as $item)
                                <tr id="pro{{ $item->id }}">
                                    <td class="text-start text-bg-{{ statusToBootstrap($item->status) }}">
                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit purchase"
                                                data-original-title="Edit" href="/dashboard/product-purchase/edit/{{ $item->purshace_no }}"><i class="ri-pencil-line mr-0"></i></a>
                                            <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Edit purchase"
                                                data-original-title="Edit" href="/dashboard/product-purchase/report/{{ $item->purshace_no }}"><i class="fa-regular fa-file-lines"></i></a>
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete purchase"
                                                data-original-title="Delete" href="javascript:void(0)" onclick="deleteProduct('{{ $item->id }}')"><i class="fa-solid fa-trash-can"></i></a>
                                        </div>
                                    </td>
                                    <td class="text-start text-bg-{{ statusToBootstrap($item->status) }}">{{ $item->purshace_no }}</td>
                                    <td class="text-start text-bg-{{ statusToBootstrap($item->status) }}">{{ currency($item->total, 'LKR') }}</td>
                                    <td class="text-start text-bg-{{ statusToBootstrap($item->status) }}">{{ $item->qty }}</td>
                                    <td class="text-start text-bg-{{ statusToBootstrap($item->status) }}">{{ currency($item->cbm_price, '') }}</td>
                                    <td class="text-start text-bg-{{ statusToBootstrap($item->status) }}">{{ currency($item->shipping_charge, '') }}</td>
                                    <td class="text-start text-bg-{{ statusToBootstrap($item->status) }}">{{ currency($item->total_in_currency, $item->currency) }}</td>
                                    <td class="text-start text-bg-{{ statusToBootstrap($item->status) }}">{{ getSupplier($item->supplier_id)->name }}</td>
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
        function deleteProduct(code) {
            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    type: "delete",
                    url: "/dashboard/product-purchase/delete/",
                    data: {code: code, _token: '{{ csrf_token() }}'},
                    dataType: "json",
                    success: function (response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            $("#pro"+code).remove();
                        }
                        else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
            return 0;
        }
    </script>
@endsection
