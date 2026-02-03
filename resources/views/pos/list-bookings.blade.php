@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Booking List</h4>
                            <p class="mb-0">The booking list effectively dictates booking presentation and provides
                                space<br> to list your bookings and offering in the most appealing way.</p>
                        </div>
                        {{-- <a href="/dashboard/repaid/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Category</a> --}}
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
                                    <th class="text-start">Action</th>
                                    <th class="text-start">Booking ID</th>
                                    <th class="text-start">Customer Name</th>
                                    <th class="text-start">Customer Phone</th>
                                    <th class="text-start">TV Brand</th>
                                    <th class="text-start">TV Model</th>
                                    <th class="text-start">Issue Type</th>
                                    <th class="text-start">Address</th>
                                    <th class="text-start">Pickup Option</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if (isset($bookings))
                                    @foreach ($bookings as $item)
                                        <tr id="category{{ $item->id }}">
                                            <td class="text-start">
                                                <div class="d-flex align-items-center list-action justify-content-start">
                                                    <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit product" data-original-title="Edit" href="/pos/?action=new-order&model_no={{ $item->tv_brand.' '. $item->tv_model }}&customer_name={{ $item->customer_name }}&customer_phone={{ $item->customer_phone }}&issue_type={{ $item->issue_type }}&address={{ $item->address }}" onclick="changeStatus('{{ $item->id }}', event)">
                                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                                    </a>

                                                    <a class="badge bg-danger mr-2" data-toggle="tooltip"
                                                        data-placement="top" title="Delete product"
                                                        data-original-title="Delete" href="javascript:void(0)"
                                                        onclick="deleteProduct('{{ $item->id }}')"><i
                                                            class="ri-delete-bin-line mr-0"></i></a>
                                                </div>
                                            </td>
                                            <td class="text-start">{{ $item->booking_id }}</td>
                                            <td class="text-start">{{ $item->customer_name }}</td>
                                            <td class="text-start">{{ $item->customer_phone }}</td>
                                            <td class="text-start">{{ $item->tv_brand }}</td>
                                            <td class="text-start">{{ $item->tv_model }}</td>
                                            <td class="text-start">{{ $item->issue_type }}</td>
                                            <td class="text-start">{{ $item->address }}</td>
                                            <td class="text-start text-capitalize ">{{ str_replace('-', ' ', $item->pickup_option) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-end py-5">
                        {{ $bookings->appends(['status' => request()->status, 's' => request()->s])->links() }}
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
                    url: "/dashboard/booking/delete",
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

        function changeStatus(id, event) {
            if (confirm('Are you sure you want to convert this booking to a repair?')) {
                $.ajax({
                    type: "post",
                    url: "/dashboard/booking/change-status",
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            <?php if(isset($_GET['status']) && sanitize($_GET['status'] == 'pending')) {
                                echo '$("#category" + id).remove();';
                            } ?>
                        } else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
            else {
                event.preventDefault();
                return false;
            }
        }
    </script>
@endsection
