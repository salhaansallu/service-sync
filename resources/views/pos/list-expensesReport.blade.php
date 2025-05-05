@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Expense Report</h4>
                            <p class="mb-0">The Expense Report effectively dictates Expense presentation and provides
                                space<br> to list your Expenses and offering in the most appealing way.</p>
                        </div>
                        <div class="d-block">
                            <a href="/dashboard/expense/create" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Expense</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Item</th>
                                    <th class="text-start">Amount</th>
                                    <th class="text-start">QTY</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($purchses && $purchses->count() > 0)
                                @foreach ($purchses as $item)
                                <tr>
                                    <td class="text-start">{{ $item->item }}</td>
                                    <td class="text-start">{{ $item->total_qty }}</td>
                                    <td class="text-start">{{ $item->total_amount }}</td>
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
