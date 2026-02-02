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
                    <h5 class="mb-3 mt-5">Paid Bills</h5>
                    <div class="table-responsive rounded mb-3 border-bottom" style="overflow-y: auto;max-height: 300px;">
                        <table class="table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Order No</th>
                                    <th class="text-start">Model</th>
                                    <th class="text-start">Technician</th>
                                    {{-- <th class="text-start">Total</th> --}}
                                    <th class="text-start">Spare Cost</th>
                                    <th class="text-start">Commission</th>
                                    <th class="text-start">Cost</th>
                                    <th class="text-start">Total</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if (isset($repairs) && $repairs->count() > 0)
                                @foreach ($repairs as $item)
                                @if ($item->creditAmount == 0)
                                    <tr>
                                        <td class="text-start"><a href="/dashboard/repairs/edit/{{ $item->id }}" target="_blank">{{ $item->bill_no }}</a></td>
                                        <td class="text-start">{{ $item->model_no }}</td>
                                        <td class="text-start">{{ getUser($item->techie)->fname }}</td>
                                        {{-- <td class="text-start">{{ currency($item->total, '') }}</td> --}}
                                        <td class="text-start">{{ currency($item->cost, '') }}</td>
                                        <td class="text-start">{{ currency($item->commission, '') }}</td>
                                        <td class="text-start">{{ currency($item->cost + $item->commission, '') }}</td>
                                        <td class="text-start">{{ currency($item->finaltotal, '') }}</td>
                                    </tr>
                                @endif
                                @endforeach
                                {{-- <tr>
                                    <td class="text-start fw-bold" colspan="2">Total:</td>
                                    <td class="text-start fw-bold">{{ currency($repairSales, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($spareCost, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($commission, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($spareCost + $commission, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($repairs->sum('finaltotal'), '') }}</td>
                                </tr> --}}
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mb-3 mt-5">Bills with Credit</h5>
                    <div class="table-responsive rounded mb-3 border-bottom" style="overflow-y: auto;max-height: 300px;">
                        <table class="table mb-0 tbl-server-info">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th class="text-start">Order No</th>
                                    <th class="text-start">Model</th>
                                    <th class="text-start">Technician</th>
                                    {{-- <th class="text-start">Total</th> --}}
                                    <th class="text-start">Spare Cost</th>
                                    <th class="text-start">Commission</th>
                                    <th class="text-start">Cost</th>
                                    <th class="text-start">Total</th>
                                    <th class="text-start">Credit</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @if (isset($repairs) && $repairs->count() > 0)
                                @foreach ($repairs as $item)
                                @if ($item->creditAmount == 0)
                                    <tr>
                                        <td class="text-start"><a href="/dashboard/repairs/edit/{{ $item->id }}" target="_blank">{{ $item->bill_no }}</a></td>
                                        <td class="text-start">{{ $item->model_no }}</td>
                                        <td class="text-start">{{ getUser($item->techie)->fname }}</td>
                                        {{-- <td class="text-start">{{ currency($item->total, '') }}</td> --}}
                                        <td class="text-start">{{ currency($item->cost, '') }}</td>
                                        <td class="text-start">{{ currency($item->commission, '') }}</td>
                                        <td class="text-start">{{ currency($item->cost + $item->commission, '') }}</td>
                                        <td class="text-start">{{ currency($item->finaltotal, '') }}</td>
                                        <td class="text-start">{{ currency($item->creditAmount, '') }}</td>
                                    </tr>
                                @endif
                                @endforeach
                                {{-- <tr>
                                    <td class="text-start fw-bold" colspan="2">Total:</td>
                                    <td class="text-start fw-bold">{{ currency($repairSales, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($spareCost, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($commission, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($spareCost + $commission, '') }}</td>
                                    <td class="text-start fw-bold">{{ currency($repairs->sum('finaltotal'), '') }}</td>
                                </tr> --}}
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="row" id="accountsSummary">
                        <div class="col-lg-6">
                            <div class="mt-5">
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-success">Repair Sales:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-success text-end">{{ currency($repairs->where('creditAmount', 0)->sum('finaltotal'), '') }}</div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Service Parts Cost:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($spareCost, '') }}</div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Commission:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($commission, '') }}</div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Total Staff Commission:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($staffCommission, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Commission" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Food Expenses:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($staffFood, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Food Expenses" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Staff Salary:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($staffSalary, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Salary" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Staff Transport:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($staffTransport, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Transport" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Staff Bonus:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($staffBonus, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Bonus" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Staff Medical:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($staffMedical, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Medical" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Staff Accommodation:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($staffAccommodation, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Accommodation" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Staff OT:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($staffOT, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff OT" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Staff Loan:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($staffLoan, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Staff Loan" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Rent & Sadaqah:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($rent, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="Rent & Sadaqah" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">FB Cash:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger">{{ currency($fbCash, '') }}</div>
                                    <div class="col-4 col-lg-3 py-2 text-start"><a href="javascript:void(0)" data-expense="FB Cash" class="expense-btn primary-btn submit-btn border-only py-1" style="font-size: 13px;">Add New</a></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 bg-success">Final Profit:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end bg-success">{{ currency((($repairs->sum('finaltotal')) - $spareCost - $staffCommission - $staffFood - $staffSalary - $staffTransport - $staffBonus - $staffMedical - $staffAccommodation - $staffOT - $staffLoan - $rent - $fbCash), 'LKR') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mt-5">
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-success">TV Repair Sales:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-success text-end"><input type="number" name="" id="tvSales" class="w-100 form-control form-control-sm text-end" value="{{ $tvRepairs->sum('finaltotal') }}"></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-success">Other Repair Sales:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-success text-end"><input type="number" name="" id="otherSales" class="w-100 form-control form-control-sm text-end" value="{{ $otherRepairs->sum('finaltotal') }}"></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-danger">Refund Money:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end text-danger"><input type="number" name="" id="refundMoney" class="w-100 form-control form-control-sm text-end" value="0"></div>
                                </div>
                                <div class="row m-0 fw-semibold">
                                    <div class="col-4 col-lg-4 border-bottom py-2 bg-success">Result:</div>
                                    <div class="col-4 col-lg-4 border-bottom py-2 text-end bg-success" id="calculatedResult">{{ $tvRepairs->sum('finaltotal') + $otherRepairs->sum('finaltotal') }}</div>
                                </div>
                            </div>
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

            $('#refundMoney, #tvSales, #otherSales').change(function (e) {
                if ($('#tvSales').val().toString() == "") {
                    $('#tvSales').val('0');
                }

                if ($('#otherSales').val().toString() == "") {
                    $('#otherSales').val('0');
                }

                if ($('#refundMoney').val().toString() == "") {
                    $('#refundMoney').val('0');
                }

                let tvAmount = parseFloat($('#tvSales').val());
                let otherAmount = parseFloat($('#otherSales').val());
                let refund = parseFloat($('#refundMoney').val());

                $('#calculatedResult').text((tvAmount+otherAmount) - refund);
            });

            $('#refundMoney, #tvSales, #otherSales').keyup(function (e) {
                let tvAmount = parseFloat($('#tvSales').val());
                let otherAmount = parseFloat($('#otherSales').val());
                let refund = parseFloat($('#refundMoney').val());

                $('#calculatedResult').text((tvAmount+otherAmount) - refund);
            });

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
