@extends('pos.app')

@section('dashboard')
    <div class="content-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">{{ getDepartment($id) }} Petty Cash Transactions</h4>
                        </div>
                        <a href="/dashboard/petty-cash/{{ isset($id) ? $id : '' }}" class="btn btn-primary add-list"><i class="fa-solid fa-plus mr-3"></i>Add Petty Cash</a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="data-table table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Amount</th>
                                    <th class="text-start">Note</th>
                                    <th class="text-start">Department</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if ($lists && $lists->count() > 0)
                                @foreach ($lists as $item)
                                <tr>
                                    <td class="text-start">{{ $item->amount }}</td>
                                    <td class="text-start">{{ $item->note }}</td>
                                    <td class="text-start">{{ getDepartment($item->department) }}</td>
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
