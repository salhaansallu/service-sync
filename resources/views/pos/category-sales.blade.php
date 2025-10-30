@extends('pos.app')

@section('dashboard')
    <div class="content-page mb-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Category Wise Sales</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="filters mb-4">
                        <form action="/{{ request()->path() }}" method="get">
                            <div class="row m-0 align-items-end">
                                <div class="col-lg-2">
                                    <label for="">From:</label>
                                    <input type="date" class="form-control" name="fromdate" value="{{ isset($_GET['fromdate'])? sanitize($_GET['fromdate']) : date('Y-m-d') }}">
                                </div>
                                <div class="col-lg-2">
                                    <label for="">To:</label>
                                    <input type="date" class="form-control" name="todate" value="{{ isset($_GET['todate'])? sanitize($_GET['todate']) : date('Y-m-d') }}">
                                </div>
                                <div class="col-lg-1">
                                    <button type="submit" class="primary-btn border-only submit-btn">Filter</button>
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" id="summeryDownload" class="primary-btn border-only submit-btn">Download Summery</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive rounded mb-3 border-bottom" id="accountsSummary">
                        <table class="table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start w-50">Category</th>
                                    <th class="text-end w-25">Sales</th>
                                    <th class="text-end w-25">Cost</th>
                                    <th class="text-end w-25">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @foreach ($productSales as $key => $item)
                                <tr>
                                    <td class="text-start">{{ $item['name'] }}</td>
                                    <td class="text-end">{{ currency($item['sales'], '') }}</td>
                                    <td class="text-end">{{ currency($item['cost'], '') }}</td>
                                    <td class="text-end" style="text-align: right !important;">{{ currency($item['sales'] - $item['cost'], '') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            $('#summeryDownload').click(function (e) {
                e.preventDefault();
                const printContents = document.getElementById('accountsSummary').innerHTML;
                // Save the full page HTML
                const originalContents = document.body.innerHTML;

                // Replace the entire body with only the selected div
                document.body.innerHTML = printContents;

                // Trigger print
                window.print();

                // Restore original page content
                document.body.innerHTML = originalContents;

                // Optional: Reload scripts or event bindings if needed
                location.reload();
            });

        });
    </script>
@endsection
