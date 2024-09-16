@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Order List</h4>
                            <p class="mb-0">The order list effectively dictates order presentation and provides
                                space<br> to list your orders and offering in the most appealing way.</p>
                        </div>
                        {{-- <a href="/dashboard/repaid/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Category</a> --}}
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Bill No</th>
                                    <th class="text-start">Payment Method</th>
                                    <th class="text-start">Delivery</th>
                                    <th class="text-start">Tracking Code</th>
                                    <th class="text-start">Date</th>
                                    <th class="text-start">Status</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($repairs && $repairs->count() > 0)
                                    @foreach ($repairs as $item)
                                        <tr id="category{{ $item->id }}">
                                            <td class="text-start">{{ $item->bill_no }}</td>
                                            <td class="text-start">{{ $item->payment_method }}</td>
                                            <td class="text-start">{{ $item->delivery }}</td>
                                            <td class="text-start">{{ $item->tracking_code }}</td>
                                            <td class="text-start">{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                            <td
                                                class="text-start @if ($item->status == 'Delivered') bg-success @else bg-danger @endif">
                                                {{ $item->status }}</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center list-action justify-content-start">
                                                    <a class="badge bg-primary mr-2" data-toggle="tooltip"
                                                        data-placement="top" title="Edit product" data-original-title="Edit"
                                                        href="/dashboard/order/edit/{{ $item->id }}"><i
                                                            class="ri-pencil-line mr-0"></i></a>
                                                    @if (!empty($item->invoice))
                                                        <a class="badge bg-secondary mr-2" data-toggle="tooltip"
                                                            data-placement="top" title="View Invoice"
                                                            data-original-title="View Invoice" href="javascript:void(0)"
                                                            onclick="ViewInvoice('{{ $item->invoice }}')"><i
                                                                class="fa-regular fa-eye"></i></a>
                                                    @endif
                                                    <a class="badge bg-danger mr-2" data-toggle="tooltip"
                                                        data-placement="top" title="Delete product"
                                                        data-original-title="Delete" href="javascript:void(0)"
                                                        onclick="deleteProduct('{{ $item->id }}')"><i
                                                            class="ri-delete-bin-line mr-0"></i></a>

                                                    @if ($item->status == "Delivered")
                                                        <a class="badge bg-warning mr-2" data-toggle="tooltip"
                                                            data-placement="top" title="Return Order"
                                                            data-original-title="Return Order" href="javascript:void(0)"
                                                            onclick="returnProduct('{{ $item->id }}')"><i class="fa-solid fa-repeat"></i></a>
                                                    @endif
                                                </div>
                                            </td>
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
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    type: "delete",
                    url: "/dashboard/order/delete",
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            $("#category" + id).remove();
                        } else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
            return 0;
        }

        function returnProduct(id) {
            if (confirm('Are you sure you want to return?')) {
                $.ajax({
                    type: "post",
                    url: "/dashboard/order/return",
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
            return 0;
        }

        function ViewInvoice(invoice) {
            if (invoice !== "") {
                window.open("/invoice/" + invoice, "_blank");
            } else {
                toastr.error("Invoice not found", "Error");
            }
            return 0;
        }
    </script>
@endsection
