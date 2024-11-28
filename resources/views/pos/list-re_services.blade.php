@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Re-service List</h4>
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
                                    <th class="text-start">Model No</th>
                                    <th class="text-start">Serial No</th>
                                    <th class="text-start">Fault</th>
                                    <th class="text-start">Advance</th>
                                    <th class="text-start">Balance</th>
                                    <th class="text-start">Total</th>
                                    <th class="text-start">Customer</th>
                                    <th class="text-start">Partner</th>
                                    <th class="text-start">Spare(s)</th>
                                    <th class="text-start">Date</th>
                                    <th class="text-start">Status</th>
                                    <th class="text-start">Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if (isset($repairs) && count($repairs) > 0)
                                    @foreach ($repairs as $item)
                                        <tr id="category{{ $item->id }}">
                                            <td class="text-start">{{ $item->bill_no }}</td>
                                            <td class="text-start">{{ $item->model_no }}</td>
                                            <td class="text-start">{{ $item->serial_no }}</td>
                                            <td class="text-start">{{ $item->fault }}</td>
                                            <td class="text-start">{{ currency($item->advance) }}</td>
                                            <td class="text-start">{{ currency($item->total - $item->advance) }}</td>
                                            <td class="text-start">{{ currency($item->total) }}</td>
                                            <td class="text-start">{{ getCustomer($item->customer)->phone }} ({{ getCustomer($item->customer)->name }})</td>
                                            <td class="text-start">{{ !empty(getPartner($item->partner)->name) ? getPartner($item->partner)->phone." (".getPartner($item->partner)->company.")" : "Wefix" }}</td>
                                            <td class="text-start">{{ count((array) json_decode($item->spares)) }}</td>
                                            <td class="text-start">{{ date('d-m-Y', strtotime($item->created_at)) }}</td>
                                            <td
                                                class="text-start @if ($item->status == 'Repaired') bg-success @elseif($item->status == 'Return' || $item->status == 'Customer Pending') bg-warning @elseif($item->status == 'Delivered') bg-primary @elseif($item->status == 'Awaiting Parts') bg-secondary @else bg-danger @endif">
                                                {{ $item->status }}</td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center list-action justify-content-start">
                                                    <a class="badge bg-secondary mr-2" data-toggle="tooltip"
                                                        data-placement="top" title="View Order History"
                                                        data-original-title="View Order History" href="/dashboard/repair-history/{{ $item->bill_no }}" ><i class="fa-regular fa-eye"></i></a>
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
