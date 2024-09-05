@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Repair List</h4>
                            <p class="mb-0">The repair list effectively dictates repair presentation and provides
                                space<br> to list your repairs and offering in the most appealing way.</p>
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
                                    <th class="text-start">Model No</th>
                                    <th class="text-start">Serial No</th>
                                    <th class="text-start">Fault</th>
                                    <th class="text-start">Advance</th>
                                    <th class="text-start">Balance</th>
                                    <th class="text-start">Total</th>
                                    <th class="text-start">Customer</th>
                                    <th class="text-start">Spare(s)</th>
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
                                    <td class="text-start">{{ $item->model_no }}</td>
                                    <td class="text-start">{{ $item->serial_no }}</td>
                                    <td class="text-start">{{ $item->fault }}</td>
                                    <td class="text-start">{{ currency($item->advance) }}</td>
                                    <td class="text-start">{{ currency(($item->total - $item->advance)) }}</td>
                                    <td class="text-start">{{ currency($item->total) }}</td>
                                    <td class="text-start">{{ $item->customer }}</td>
                                    <td class="text-start">{{ count((array)json_decode($item->spares)) }}</td>
                                    <td class="text-start">{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                    <td class="text-start @if($item->status == 'Repaired') bg-success @elseif($item->status == 'Return') bg-warning @elseif($item->status == 'Delivered') bg-primary @else bg-danger @endif">{{ $item->status }}</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit product"
                                                data-original-title="Edit" href="/dashboard/repairs/edit/{{ $item->id }}"><i class="ri-pencil-line mr-0"></i></a>
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete product"
                                                data-original-title="Delete" href="javascript:void(0)" onclick="deleteProduct('{{ $item->id }}')"><i class="ri-delete-bin-line mr-0"></i></a>
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
                    url: "/dashboard/repairs/delete",
                    data: {id: id, _token: '{{ csrf_token() }}'},
                    dataType: "json",
                    success: function (response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            $("#category"+id).remove();
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
