@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Expense List</h4>
                            <p class="mb-0">The Expense list effectively dictates Expense presentation and provides
                                space<br> to list your Expenses and offering in the most appealing way.</p>
                        </div>
                        <div class="d-block">
                            <a href="/dashboard/expense/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Expense</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Item</th>
                                    <th class="text-start">Amount</th>
                                    <th class="text-start">QTY</th>
                                    <th class="text-start">Payment Method</th>
                                    <th class="text-start">Supplier</th>
                                    <th class="text-start">By</th>
                                    <th class="text-start">Purchase date</th>
                                    @if (isAdmin())
                                    <th class="text-start">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($purchses && $purchses->count() > 0)
                                @foreach ($purchses as $item)
                                <tr id="pro{{ $item->id }}">
                                    <td class="text-start">{{ $item->item }}</td>
                                    <td class="text-start">{{ currency($item->amount, '') }}</td>
                                    <td class="text-start">{{ $item->qty }}</td>
                                    <td class="text-start">{{ $item->payment }}</td>
                                    <td class="text-start">{{ getSupplier($item->supplier_id)->name }}</td>
                                    <td class="text-start">{{ getUser($item->user)->fname }}</td>
                                    <td class="text-start">{{ $item->created_at }}</td>
                                    @if (isAdmin())
                                    <td class="text-start">
                                        <div class="d-flex align-items-center list-action justify-content-start">

                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit expense"
                                                data-original-title="Edit" href="/dashboard/expense/edit/{{ $item->id }}"><i class="ri-pencil-line mr-0"></i></a>

                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete expense"
                                                data-original-title="Delete" href="javascript:void(0)" onclick="deleteProduct('{{ $item->id }}')"><i class="fa-solid fa-trash-can"></i></a>

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
        function deleteProduct(code) {
            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    type: "delete",
                    url: "/dashboard/expense/delete/",
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
