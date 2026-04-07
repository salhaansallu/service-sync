@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Order Requests</h4>
                            <p class="mb-0">View and manage order requests submitted from API and N8N.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 py-4">
                    <form action="" method="get" class="d-flex gap-2">
                        <input type="search" name="s" placeholder="Search here..." class="form-control" style="width: 200px;" value="{{ isset($_GET['s']) ? sanitize($_GET['s']) : '' }}">
                        <button class="btn btn-primary"><i class="fa-solid fa-search" aria-hidden="true"></i></button>
                    </form>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Request ID</th>
                                    <th class="text-start">Customer Name</th>
                                    <th class="text-start">Customer Phone</th>
                                    <th class="text-start">Products</th>
                                    <th class="text-start">Status</th>
                                    <th class="text-start">Date</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if (isset($orderRequests) && $orderRequests->count() > 0)
                                    @foreach ($orderRequests as $item)
                                        <tr>
                                            <td class="text-start">{{ $item->request_id }}</td>
                                            <td class="text-start">{{ $item->customer_name }}</td>
                                            <td class="text-start">{{ $item->customer_phone }}</td>
                                            <td class="text-start">
                                                @foreach ($item->products as $product)
                                                    <span class="badge bg-light text-dark">ID: {{ $product['id'] }} x{{ $product['qty'] }}</span>
                                                @endforeach
                                            </td>
                                            <td class="text-start">
                                                @if ($item->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif ($item->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif ($item->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $item->status }}</span>
                                                @endif
                                            </td>
                                            <td class="text-start">{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">No order requests found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if (isset($orderRequests))
                        {{ $orderRequests->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
