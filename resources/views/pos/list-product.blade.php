@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Spare Part List</h4>
                            <p class="mb-0">The Spare Part list effectively dictates Spare Part presentation and provides
                                space<br> to list your products and offering in the most appealing way.</p>
                        </div>
                        <a href="/dashboard/products/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Product</a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Product</th>
                                    <th class="text-start">Code (SKU)</th>
                                    <th class="text-start">Cost</th>
                                    <th class="text-start">Stock</th>
                                    <th class="text-start">Supplier</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($products && $products->count() > 0)
                                @foreach ($products as $item)
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
                                    <td class="text-start">{{ $item->cost }}</td>
                                    <td class="text-start">{{ $item->qty }}</td>
                                    <td class="text-start">{{ getSupplier($item->supplier)->name }}</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit product"
                                                data-original-title="Edit" href="/dashboard/products/edit/{{ $item->sku }}"><i class="ri-pencil-line mr-0"></i></a>
                                            <a class="badge bg-danger mr-2" data-toggle="tooltip" data-placement="top" title="Delete product"
                                                data-original-title="Delete" href="javascript:void(0)" onclick="deleteProduct('{{ $item->sku }}')"><i class="ri-delete-bin-line mr-0"></i></a>
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
    </script>
@endsection
