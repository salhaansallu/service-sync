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
                                    <th class="text-start">Amount</th>
                                    <th class="text-start">Status</th>
                                    <th class="text-start">Note</th>
                                    <th class="text-start">Date</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($results && count($results) > 0)
                                    @foreach ($results as $item)
                                        <tr>
                                            <td class="text-start">{{ $item->amount }}</td>
                                            <td class="text-start text-capitalize">{{ $item->status }}</td>
                                            <td class="text-start">{{ $item->note }}</td>
                                            <td class="text-start">{{ $item->created_at }}</td>
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
