@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mt-4">
                        <div>
                            <h4>Stock Report</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="total_report mt-5">
                <div class=" rounded mb-3">
                    <table class="table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th class="text-center">Total Spares</th>
                                <th class="text-center">Total Stock</th>
                                <th class="text-center">Total Cost</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <tr>
                                <td class="text-center" style="text-align: center !important;">{{ isset($items)? $items : '0' }}</td>
                                <td class="text-center">{{ isset($stock)? $stock : '0' }}</td>
                                <td class="text-center" style="text-align: center !important;">{{ isset($costs)? currency($costs) : '0' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
