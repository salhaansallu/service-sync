@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Sales Quotations</h4>
                        </div>
                        <a href="/dashboard/sales-quotations/create" class="btn btn-primary">
                            <i class="fa-solid fa-plus mr-2"></i> Add Sales Quotation
                        </a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info" data-order="[]">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">SQ No</th>
                                    <th class="text-start">Customer Name</th>
                                    <th class="text-start">Customer Phone</th>
                                    <th class="text-start">Items</th>
                                    <th class="text-start">Total</th>
                                    <th class="text-start">Date</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($salesQuotations && $salesQuotations->count() > 0)
                                    @foreach ($salesQuotations as $item)
                                        <tr id="sq{{ $item->id }}">
                                            <td class="text-start">{{ $item->sq_no }}</td>
                                            <td class="text-start">{{ $item->customer_name }}</td>
                                            <td class="text-start">{{ $item->customer_phone }}</td>
                                            <td class="text-start">
                                                @foreach ($item->items as $product)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $product['name'] }} &times; {{ $product['qty'] }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td class="text-start">{{ currency($item->total, company()->currency) }}</td>
                                            <td class="text-start">{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center list-action justify-content-start">
                                                    <a class="badge bg-success mr-2" title="Download PDF"
                                                        href="/dashboard/sales-quotations/pdf/{{ $item->sq_no }}" target="_blank">
                                                        <i class="fa-solid fa-file-pdf"></i>
                                                    </a>
                                                    <a class="badge bg-info mr-2" title="Edit"
                                                        href="/dashboard/sales-quotations/edit/{{ $item->sq_no }}">
                                                        <i class="fa-solid fa-pencil"></i>
                                                    </a>
                                                    @if (isAdmin())
                                                        <a class="badge bg-danger mr-2" title="Delete"
                                                            href="javascript:void(0)"
                                                            onclick="deleteSQ('{{ $item->id }}')">
                                                            <i class="ri-delete-bin-line mr-0"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">No sales quotations found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (isAdmin())
        <script>
            function deleteSQ(id) {
                if (confirm('Are you sure you want to delete this sales quotation?')) {
                    $.ajax({
                        type: 'delete',
                        url: '/dashboard/sales-quotations/delete',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.error == 0) {
                                toastr.success(response.msg, 'Success');
                                $('#sq' + id).remove();
                            } else {
                                toastr.error(response.msg, 'Error');
                            }
                        }
                    });
                }
            }
        </script>
    @endif
@endsection
