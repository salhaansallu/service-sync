@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid add-form-list">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h4 class="card-title">Repair Details</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="repairsCreate" action="" data-toggle="validator" onsubmit="return false;">
                                @csrf
                                <input type="hidden" name="modelid" value="{{ $repair[0]->id }}">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Bill No</label>
                                            <input type="text" class="form-control" disabled
                                                value="{{ $repair[0]->bill_no }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Model No</label>
                                            <input type="text" class="form-control" disabled
                                                value="{{ $repair[0]->model_no }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Serial No</label>
                                            <input type="text" class="form-control" disabled
                                                value="{{ $repair[0]->serial_no }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Total Repair Cost</label>
                                            <input type="text" class="form-control" disabled
                                                value="{{ getTotalRepairSum($repair, 'cost') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Total Bill</label>
                                            <input type="text" class="form-control" disabled
                                                value="{{ getTotalRepairSum($repair, 'total') }}">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->

            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Repair History</h4>
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
                                    <th class="text-start">Fault</th>
                                    <th class="text-start">Cost</th>
                                    <th class="text-start">Total</th>
                                    <th class="text-start">Spare(s)</th>
                                    <th class="text-start">Date</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if (isset($repair[0]['child']) && count($repair[0]['child']) > 0)
                                    @foreach ($repair[0]['child'] as $item)
                                        <tr id="category{{ $item->id }}">
                                            <td class="text-start">{{ $item->bill_no }}</td>
                                            <td class="text-start">{{ $item->fault }}</td>
                                            <td class="text-start">{{ currency($item->cost) }}</td>
                                            <td class="text-start">{{ currency($item->total) }}</td>
                                            <td class="text-start">
                                                <a class="badge bg-secondary mr-2" data-toggle="tooltip"
                                                    data-placement="top" title="View Invoice"
                                                    data-original-title="View Invoice"
                                                    href="?view-spare={{ $item->bill_no }} "><i
                                                        class="fa-regular fa-eye"></i>
                                                </a>
                                            </td>
                                            <td class="text-start">{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Used Spares</h4>
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
                                    <th class="text-start">Spare Code</th>
                                    <th class="text-start">Spare Name</th>
                                </tr>
                            </thead>
                            <tbody class="spareTableBody ligth-body">
                                @php
                                    if (isset($_GET['view-spare'])) {
                                        $items = getRepair(sanitize($_GET['view-spare']));
                                        if (!empty($items->bill_no) && !empty($items->spares)) {
                                            foreach (json_decode($items->spares) as $item):
                                                $spare = getSpare($item);
                                                echo '
                                                    <tr>
                                                        <td class="text-start">' .
                                                    $items->bill_no .
                                                    '</td>
                                                        <td class="text-start">' .
                                                    $spare->code .
                                                    '</td>
                                                        <td class="text-start">' .
                                                    $spare->name .
                                                    '</td>
                                                    </tr>
                                                ';
                                            endforeach;
                                        }
                                    }
                                @endphp
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @isset($_GET['view-spare'])
        <script>
            setTimeout(() => {
                window.scrollTo({
                    top: document.documentElement.scrollHeight,
                    behavior: "smooth"
                });
            }, 100); // Adjust timeout as necessary
        </script>
    @endisset
@endsection
