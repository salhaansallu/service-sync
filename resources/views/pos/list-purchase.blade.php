@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Purchase List</h4>
                            <p class="mb-0">The purchase list effectively dictates purchase presentation and provides
                                space<br> to list your purchases and offering in the most appealing way.</p>
                        </div>
                        <a href="/dashboard/purchase/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Purchase</a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Purchase number</th>
                                    <th class="text-start">Price</th>
                                    <th class="text-start">Stock</th>
                                    <th class="text-start">Discount</th>
                                    <th class="text-start">Shipping charge</th>
                                    <th class="text-start">Supplier</th>
                                    <th class="text-start">Note</th>
                                    <th class="text-start">Purchase date</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($purchses && $purchses->count() > 0)
                                @foreach ($purchses as $item)
                                <tr>
                                    <td class="text-start">{{ $item->purshace_no }}</td>
                                    <td class="text-start">{{ $item->price }}</td>
                                    <td class="text-start">{{ $item->qty }}</td>
                                    <td class="text-start">{{ $item->discount }}</td>
                                    <td class="text-start">{{ $item->shipping_charge }}</td>
                                    <td class="text-start">{{ $item->supplier_id }}</td>
                                    <td class="text-start">{{ $item->note }}</td>
                                    <td class="text-start">{{ $item->created_at }}</td>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center list-action justify-content-start">
                                            <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit product"
                                                data-original-title="Edit" href="/dashboard/purchase/edit/{{ $item->purshace_no }}"><i class="ri-pencil-line mr-0"></i></a>
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
@endsection
