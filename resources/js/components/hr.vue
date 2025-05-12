<template>
    <div class="content-page">
        <div class="container-fluid">
            <div class="sales-filter">
                <div class="filter_from table-responsive pb-3">
                    <div class="row row-cols-md-6" style="flex-wrap: nowrap;">
                        <div class="col">
                            <div class="input">
                                <div class="label">From date</div>
                                <input type="date" ref="fromdate">
                            </div>
                        </div>

                        <div class="col">
                            <div class="input">
                                <div class="label">To date</div>
                                <input type="date" ref="todate">
                            </div>
                        </div>

                        <div class="col">
                            <div class="input">
                                <div class="label">Type</div>
                                <select name="" id="" ref="filterType">
                                    <option value="">All</option>
                                    <option value="Salary">Salary</option>
                                    <option value="Food">Food</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Bonus">Bonus</option>
                                    <option value="Commission">Commission</option>
                                    <option value="Medical">Medical</option>
                                    <option value="Accommodation">Accommodation</option>
                                    <option value="OT">OT</option>
                                    <option value="Loan">Loan</option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="input">
                                <div class="label">From employee</div>
                                <select name="" id="" ref="cashier" class="select2">
                                    <option value="">All</option>
                                    <option v-for="cashier in cashiers" :value="cashier['id']">{{ cashier['fname'] + ' '
                                        +
                                        cashier['lname'] }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="input">
                                <button class="primary-btn border-only submit-btn mt-3" @click="getReport()">Get
                                    Report</button>
                                <button class="primary-btn border-only submit-btn mt-3 mx-2"
                                    @click="printReport()">Print</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="head mt-5 d-flex justify-content-between align-items-center">
                <h4>Expenses</h4>
                <button class="btn btn-success mt-3" @click="openModal('addExpense', 'show')">New Expense</button>
            </div>
            <div class="order_table mt-4">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th class="text-start">Employee</th>
                                <th class="text-start">Amount</th>
                                <th class="text-start">Type</th>
                                <th class="text-start">Date</th>
                                <th class="text-start">Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <tr v-for="expense in expenses">
                                <td class="text-start">{{ getUser(expense['user']) != undefined ?
                                    getUser(expense['user'])['fname'] : '' }}</td>
                                <td class="text-start">{{ expense['amount'] }}</td>
                                <td class="text-start">{{ expense['type'] }}</td>
                                <td class="text-start">{{ new Date(expense['created_at']).toLocaleString('nl-NL') }}
                                </td>
                                <td class="text-start">
                                    <div class="d-flex align-items-center list-action justify-content-start">
                                        <a v-if="expense['type'] == 'Loan' && expense['status'] != 'paid'"
                                            class="badge bg-success mr-2" href="javascript:void(0)" title="Pay Expense"
                                            @click="payExpense(expense['id'])">Pay</a>

                                        <a class="badge bg-danger mr-2" href="javascript:void(0)" title="Remove"
                                            @click="removeExpense(expense['id'])"><i
                                                class="fa-solid fa-trash-can"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addExpense" tabindex="-1" role="dialog" aria-labelledby="addExpense" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body d-flex" style="justify-content: center;">
                    <div class="product-wrp mt-1">
                        <div class="row justify-content-between">
                            <div class="col-md-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Employee</label>
                                    <select @change="autoFill()" name="" id="" ref="user">
                                        <option value="">-- Select employee --</option>
                                        <option v-for="cashier in cashiers" :value="cashier['id']">{{ cashier['fname']
                                            + ' ' + cashier['lname'] }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1 d-flex align-items-center gap-3">Amount <div
                                            class="form-text text-danger mt-0" ref="loan">Loan: 0.00</div> <button
                                            @click="payAllExpense()" style="display: none;" ref="loanMassPayBtn" class="btn btn-success btn-sm">Pay
                                            all</button></label>
                                    <input type="number" ref="amount" placeholder="0">
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Type</label>
                                    <select @change="autoFill()" name="" id="" ref="type">
                                        <option value="">-- Select type --</option>
                                        <option value="Salary">Salary</option>
                                        <option value="Food">Food</option>
                                        <option value="Transport">Transport</option>
                                        <option value="Bonus">Bonus</option>
                                        <option value="Commission">Commission</option>
                                        <option value="Medical">Medical</option>
                                        <option value="Accommodation">Accommodation</option>
                                        <option value="OT">OT</option>
                                        <option value="Loan">Loan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Date</label>
                                    <input type="datetime-local" ref="date" value="">
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Note</label>
                                    <textarea name="" id="" ref="note" rows="5"></textarea>
                                </div>
                            </div>

                            <div class="col-12 mt-5">
                                <button @click="addExpense()" class="primary-btn submit-btn">Save</button>
                                <button @click="openModal('addExpense', 'hide')"
                                    style="background: transparent; color: red !important; border: red 1px solid;"
                                    class="primary-btn submit-btn mx-4">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { currency } from '../custom';
import printJS from 'print-js';

export default {
    data() {
        return {
            name: 'hr',
            cashiers: [],
            expenses: [],
        }
    },
    methods: {
        currency,
        openModal(modal, action) {
            $('#' + modal).modal(action);

            const myModalEl = document.getElementById(modal)
            myModalEl.addEventListener('shown.bs.modal', event => {
                $('.custom-select2').select2({
                    dropdownParent: $("#" + modal),
                });
            })

            const now = new Date();
            const offset = now.getTimezoneOffset();
            const localDate = new Date(now.getTime() - offset * 60000); // Adjust for timezone offset
            const formatted = localDate.toISOString().slice(0, 16); // "YYYY-MM-DDTHH:MM"
            this.$refs.date.value = formatted;
        },
        getUser(id) {
            return this.cashiers.filter(item => item['id'] == id)[0];
        },
        async getPosData() {
            this.getUsers();
        },
        async getUsers() {
            const { data } = await axios.post("/dashboard/hr/get-users");
            this.cashiers = data;
        },
        async getReport() {
            var fromdate = this.$refs.fromdate.value;
            var todate = this.$refs.todate.value;
            var cashier = this.$refs.cashier.value;
            var filterType = this.$refs.filterType.value;

            $(".data-table").DataTable().destroy();

            if (fromdate == '') {
                fromdate = new Date()
            }

            if (todate == '') {
                todate = new Date()
            }

            const { data } = await axios.post("/dashboard/hr/get-expenses", {
                fromdate: fromdate,
                todate: todate,
                cashier: cashier,
                type: filterType
            })
            this.expenses = data;

            setTimeout(function () {
                $(".data-table").DataTable().order([3, 'desc']).draw();
            }, 500);
        },
        async printReport() {
            var fromdate = this.$refs.fromdate.value;
            var todate = this.$refs.todate.value;
            var cashier = this.$refs.cashier.value;
            var filterType = this.$refs.filterType.value;

            if (fromdate == '') {
                fromdate = new Date()
            }

            if (todate == '') {
                todate = new Date()
            }

            const { data } = await axios.post("/dashboard/hr/print-expenses", {
                fromdate: fromdate,
                todate: todate,
                cashier: cashier,
                type: filterType
            })

            if (data.error == 0) {
                toastr.success(data.message, 'Success');
                printJS(data.url);
            }
            else {
                toastr.error(data.message, 'Error');
            }
        },
        async addExpense() {
            const user = this.$refs.user.value;
            const amount = this.$refs.amount.value;
            const type = this.$refs.type.value;
            const date = this.$refs.date.value;
            const note = this.$refs.note.value;

            if (user == '' || amount == '' || type == '' || date == '') {
                alert('Please fill all fields');
                return;
            }

            const { data } = await axios.post("/dashboard/hr/add-expense", {
                user: user,
                amount: amount,
                type: type,
                date: date,
                note: note
            });

            if (data.error == 0) {
                this.getReport();
                this.$refs.user.value = '';
                this.$refs.amount.value = '';
                this.$refs.type.value = '';
                this.$refs.note.value = '';

                const now = new Date();
                const offset = now.getTimezoneOffset();
                const localDate = new Date(now.getTime() - offset * 60000); // Adjust for timezone offset
                const formatted = localDate.toISOString().slice(0, 16); // "YYYY-MM-DDTHH:MM"
                this.$refs.date.value = formatted;

                toastr.success(data.message, 'Success');
                this.openModal('addExpense', 'hide');
            }
            else {
                toastr.error(data.message, 'Error');
            }
        },
        async removeExpense(id) {

            if (!confirm('Are you sure you want to delete this expense?')) {
                return;
            }

            const { data } = await axios.post("/dashboard/hr/remove-expense", {
                id: id,
            });

            if (data.error == 0) {
                this.getReport();
                toastr.success(data.message, 'Success');
            }
            else {
                toastr.error(data.message, 'Error');
            }
        },
        async payExpense(id, all = false) {

            if (!confirm('Are you sure you want to pay this expense?')) {
                return;
            }

            const { data } = await axios.post("/dashboard/hr/pay-expense", {
                id: id,
                all: all
            });

            if (data.error == 0) {
                this.getReport();
                toastr.success(data.message, 'Success');
            }
            else {
                toastr.error(data.message, 'Error');
            }
        },
        async payAllExpense() {

            const user = this.$refs.user.value;
            if (user != '') {
                this.payExpense(user, true);
                this.autoFill();
            }
        },
        async getLoanBalance(id) {
            const { data } = await axios.post("/dashboard/hr/get-loan-balance", {
                id: id,
            });

            if (data.error == 0) {
                return data.balance;
            }
            else {
                return 0;
                toastr.error(data.message, 'Error');
            }
        },
        async autoFill() {
            const user = this.$refs.user.value;
            const type = this.$refs.type.value;

            if (user != '' && type != '') {

                const cashier = this.cashiers.filter(item => item['id'] == user)[0];
                if (cashier != undefined) {
                    if (type == 'Salary') {
                        var loan = await this.getLoanBalance(user);
                        this.$refs.loan.innerText = "Loan: " + currency(loan, '');
                        this.$refs.amount.value = cashier['salary'] - loan;

                        if (loan > 0) {
                            $(this.$refs.loanMassPayBtn).show();
                        }
                        else {
                            $(this.$refs.loanMassPayBtn).hide();
                        }
                    }
                    else if (type == 'Food') {
                        this.$refs.amount.value = cashier['food'];
                        $(this.$refs.loanMassPayBtn).hide();
                    }
                    else if (type == 'Transport') {
                        this.$refs.amount.value = cashier['transport'];
                        $(this.$refs.loanMassPayBtn).hide();
                    }
                    else if (type == 'Accommodation') {
                        this.$refs.amount.value = cashier['accommodation'];
                        $(this.$refs.loanMassPayBtn).hide();
                    }
                    else {
                        this.$refs.amount.value = 0;
                        $(this.$refs.loanMassPayBtn).hide();
                    }
                }
            }
        },

    },
    mounted() {
        this.getPosData();
        this.$nextTick(() => {
            $('.select2').select2();
        });

        const now = new Date();
        const offset = now.getTimezoneOffset();
        const localDate = new Date(now.getTime() - offset * 60000); // Adjust for timezone offset
        const formatted = localDate.toISOString().slice(0, 16); // "YYYY-MM-DDTHH:MM"
        this.$refs.date.value = formatted;
    }
}
</script>
