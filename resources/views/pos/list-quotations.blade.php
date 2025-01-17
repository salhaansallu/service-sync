@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Quotations List</h4>
                        </div>
                        {{-- <a href="/dashboard/repaid/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Category</a> --}}
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info" data-order="[]">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Quotation No</th>
                                    <th class="text-start">Bill No</th>
                                    <th class="text-start">Cargo Type</th>
                                    <th class="text-start"><small>Repair bill</small><br>Advance</th>
                                    <th class="text-start">Balance</th>
                                    <th class="text-start"><small>Quotation</small><br>Total</th>
                                    <th class="text-start">Customer</th>
                                    <th class="text-start">Expiry</th>
                                    <th class="text-start">Date</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($quotations && count($quotations) > 0)
                                    @foreach ($quotations as $item)
                                        <tr id="category{{ $item->q_id }}">
                                            <td class="text-start">{{ $item->q_no }}</td>
                                            <td class="text-start">{{ $item->quote_bill }}</td>
                                            <td class="text-start">{{ $item->cargo_type }}</td>
                                            <td class="text-start">{{ currency($item->advance) }}</td>
                                            <td class="text-start">{{ currency($item->quote_total - $item->advance) }}</td>
                                            <td class="text-start">{{ currency($item->quote_total) }}</td>
                                            <td class="text-start">{{ $item->quote_bill != "custom"? getCustomer($item->customer)->phone : "N/A" }} ({{ $item->quote_bill != "custom"? getCustomer($item->customer)->name : "N/A" }})</td>
                                            <td class="text-start">{{ date('d-m-Y', strtotime($item->expiry_date)) }}</td>
                                            <td class="text-start">{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center list-action justify-content-start">
                                                    <a class="badge bg-info mr-2" data-toggle="tooltip"
                                                        data-placement="top" title="Edit Quotation"
                                                        data-original-title="Edit Quotation" href="/dashboard/quotations/edit/{{ $item->q_no }}">
                                                        <i class="fa-solid fa-pencil"></i>
                                                    </a>
                                                    <a class="badge bg-secondary mr-2" data-toggle="tooltip"
                                                        data-placement="top" title="View Quotation"
                                                        data-original-title="View Quotation" href="javascript:void(0)"
                                                        onclick="ViewInvoice('{{ getQuotationURL($item->q_no, $item->pos_code) }}')">
                                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                                    </a>
                                                    <a class="badge bg-danger mr-2" data-toggle="tooltip"
                                                        data-placement="top" title="Delete product"
                                                        data-original-title="Delete" href="javascript:void(0)"
                                                        onclick="deleteProduct('{{ $item->q_id }}')">
                                                        <i class="ri-delete-bin-line mr-0"></i></a>
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
                    url: "/dashboard/quotations/delete",
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

        function ViewInvoice(invoice) {
            window.open(invoice);
        }
    </script>
@endsection
