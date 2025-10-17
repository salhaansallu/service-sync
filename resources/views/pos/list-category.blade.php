@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Category List</h4>
                            <p class="mb-0">The Category list effectively dictates Category presentation and provides
                                space<br> to list your Categories and offering in the most appealing way.</p>
                        </div>
                        <a href="/dashboard/category/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Category</a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Name</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($categories && $categories->count() > 0)
                                @foreach ($categories as $item)
                                <tr id="pro{{ $item->id }}">
                                    <td class="text-start">{{ $item->category_name }}</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit product"
                                                data-original-title="Edit" href="/dashboard/category/edit/{{ $item->id }}"><i class="ri-pencil-line mr-0"></i></a>
                                            @if (isAdmin())
                                                <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete product"
                                                data-original-title="Delete" href="javascript:void(0)" onclick="deleteProduct('{{ $item->id }}')"><i class="ri-delete-bin-line mr-0"></i></a>
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
        function deleteProduct(sku) {
            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    type: "delete",
                    url: "/dashboard/category/delete",
                    data: {id: sku, _token: '{{ csrf_token() }}'},
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
    </script>
@endsection
