@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Personal Credit List</h4>
                            <p class="mb-0">The personal credit list effectively dictates personal credits presentation and provides
                                space<br> to list your personal credit and offering in the most appealing way.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="/dashboard/personal-credits/?status=all" class="btn btn-success add-list"></i>All Credits</a>
                            <a href="/dashboard/personal-credits/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Personal Credit</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Bill number</th>
                                    <th class="text-start">Amount</th>
                                    <th class="text-start">Status</th>
                                    <th class="text-start">Date</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($purchses && $purchses->count() > 0)
                                @foreach ($purchses as $item)
                                <tr id="category{{ $item->id }}">
                                    <td class="text-start">{{ $item->bill_no }}</td>
                                    <td class="text-start">{{ $item->amount }}</td>
                                    <td class="text-start">{{ $item->status }}</td>
                                    <td class="text-start">{{ $item->created_at }}</td>
                                    <td class="text-start d-flex gap-2">
                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit product"
                                                data-original-title="Edit" href="/dashboard/personal-credits/edit/{{ $item->id }}"><i class="ri-pencil-line mr-0"></i></a>
                                        </div>

                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-danger mr-2" onclick="deleteProduct('{{ $item->id }}')" data-toggle="tooltip" data-placement="top" title="Delete credit"
                                                data-original-title="Edit" href="javascript:void(0)"><i class="ri-delete-bin-line mr-0"></i></a>
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
                    url: "/dashboard/personal-credits/delete",
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
