@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Repair Commission Report</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">First Name</th>
                                    <th class="text-start">Last Name</th>
                                    <th class="text-start">Commission</th>
                                    <th class="text-start" style="width: 150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($results && count($results) > 0)
                                    @foreach ($results as $item)
                                        <tr>
                                            <td class="text-start">{{ $item->fname }}</td>
                                            <td class="text-start">{{ $item->lname }}</td>
                                            <td class="text-start">{{ currency($item->commission) }}</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center list-action justify-content-start">
                                                    @if ($item->commission > 0)
                                                        <a class="badge bg-success mr-2" data-toggle="tooltip"
                                                            data-placement="top" title="Mark as Paid"
                                                            onclick="markPaid({{ $item->id }})"
                                                            data-original-title="Mark as Paid" href="javascript:void(0)"><i
                                                                class="fa-solid fa-check"></i> Mark as paid</a>
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
        function markPaid(id) {
            if (confirm('Are you sure you want to mark as paid?')) {
                $.ajax({
                    type: "post",
                    url: "/dashboard/repair-commissions/update",
                    data: {
                        user_id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.error == 0) {
                            toastr.success(response.msg, "Success");
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            toastr.error(response.msg, "Error");
                        }
                    }
                });
            }
            return 0;
        }
    </script>
@endsection
