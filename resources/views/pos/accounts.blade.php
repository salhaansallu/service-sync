@extends('pos.app')

@section('dashboard')
    <div class="content-page mb-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="mb-3">Accounts Summery</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="filters mb-4">
                        <form action="/dashboard/accounts" method="get">
                            <div class="row m-0 align-items-end">
                                <div class="col-lg-2">
                                    <label for="">From:</label>
                                    <input type="date" class="form-control" name="fromdate" value="{{ isset($_GET['fromdate'])? sanitize($_GET['fromdate']) : date('Y-m-d') }}">
                                </div>
                                <div class="col-lg-2">
                                    <label for="">To:</label>
                                    <input type="date" class="form-control" name="todate" value="{{ isset($_GET['todate'])? sanitize($_GET['todate']) : date('Y-m-d') }}">
                                </div>
                                <div class="col-lg-2">
                                    <button type="submit" class="primary-btn border-only submit-btn">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive rounded mb-3 border-bottom" style="overflow-y: auto;max-height: 300px;">
                        <table class="table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Order No</th>
                                    <th class="text-start">Model</th>
                                    <th class="text-start">Technician</th>
                                    <th class="text-start">Total</th>
                                    <th class="text-start">Spare Cost</th>
                                    <th class="text-start">Commission</th>
                                    <th class="text-start">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if (isset($tvRepairs) && $tvRepairs->count() > 0)
                                @foreach ($tvRepairs as $item)
                                <tr>
                                    <td class="text-start">{{ $item->bill_no }}</td>
                                    <td class="text-start">{{ $item->model_no }}</td>
                                    <td class="text-start">{{ getUser($item->techie)->fname }}</td>
                                    <td class="text-start">{{ currency($item->total, '') }}</td>
                                    <td class="text-start">{{ currency($item->cost, '') }}</td>
                                    <td class="text-start">{{ currency($item->commission, '') }}</td>
                                    <td class="text-start">{{ currency($item->cost + $item->commission, '') }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="text-start fw-bold" colspan="3">Total:</td>
                                    <td class="text-start fw-bold">{{ currency($tvRepairSales + $otherRepairSales, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($tvSpareCost + $otherSpareCost, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($staffCommission, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($tvSpareCost + $otherSpareCost + $staffCommission, '') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2">TV Repair Sales:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end">{{ currency($tvRepairSales, '') }}</div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2">Other Repair Sales:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end">{{ currency($otherRepairSales, '') }}</div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-success">Total Sales:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-success">{{ currency($tvRepairSales + $otherRepairSales, '') }}</div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Service Parts Cost (TV):</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($tvSpareCost, '') }}</div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Service Parts Cost (Other):</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($otherSpareCost, '') }}</div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Total Service Parts Cost:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($tvSpareCost + $otherSpareCost, '') }}</div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Commission (TV):</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($tvCommission, '') }}</div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Commission (Other):</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($otherCommission, '') }}</div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Total Staff Commission:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($staffCommission, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Commission" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Food Expenses:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($staffFood, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Food Expenses" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Staff Salary:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($staffSalary, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Salary" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Staff Transport:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($staffTransport, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Transport" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Staff Bonus:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($staffBonus, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Bonus" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Staff Medical:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($staffMedical, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Medical" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Staff Accommodation:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($staffAccommodation, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Accommodation" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Staff OT:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($staffOT, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff OT" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Staff Loan:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($staffLoan, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Loan" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">Rent & Sadaqah:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($rent, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="Rent & Sadaqah" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 text-danger">FB Cash:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end text-danger">{{ currency($fbCash, '') }}</div>
                            <div class="col-4 col-lg-2 py-2 text-start"><a href="javascript:void(0)" data-expense="FB Cash" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                        </div>
                        <div class="row m-0 fw-semibold">
                            <div class="col-4 col-lg-2 border-bottom py-2 bg-success">Final Profit:</div>
                            <div class="col-4 col-lg-2 border-bottom py-2 text-end bg-success">{{ currency((($tvRepairSales+$otherRepairSales) - $tvSpareCost - $otherSpareCost - $staffCommission - $staffFood - $staffSalary - $staffTransport - $staffBonus - $staffMedical - $staffAccommodation - $staffOT - $staffLoan - $rent - $fbCash), 'LKR') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page end  -->
        </div>
    </div>

    <div class="modal fade" id="employeeExpensesModal" tabindex="-1" aria-labelledby="employeeExpensesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <iframe src="/dashboard/hr?loadby=accounts" class="w-100" style="height: 800px;" frameborder="0"></iframe>
                </div>
                <div class="modal-footer justify-content-start px-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generalExpensesModal" tabindex="-1" aria-labelledby="generalExpensesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content p-5">
                <div class="row m-0 mb-4">
                    <div class="col-lg-5 p-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back to accounts summery</button>
                    </div>
                </div>
                <div class="expense-form" style="overflow-y: auto; overflow-x: hidden;">
                    @include('pos.partials.add-expense')
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            $('.expense-btn').click(function (e) {
                e.preventDefault();
                var type = $(this).data('expense');
                if (type == 'Staff Commission' || type == 'Food Expenses' || type == 'Staff Salary') {
                    $('#employeeExpensesModal').modal('show');
                }
                else if(type == 'Rent & Sadaqah' || type == 'FB Cash') {
                    $("#item").val(type);
                    $('#generalExpensesModal').modal('show');
                }
            });

        });

        window.addEventListener('message', function(event) {
            if (event.data.action === 'closeEmployeeExpensesModal') {
                const modalEl = document.getElementById('employeeExpensesModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                    location.reload();
                }
            }
        });
    </script>
@endsection
