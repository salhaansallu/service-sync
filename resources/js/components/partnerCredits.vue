<template>
    <div class="content-page">
        <div class="container-fluid">
            <div class="sales-filter">
                <div class="filter_from table-responsive pb-3">
                    <div class="row row-cols-md-6 align-items-center justify-content-between" style="flex-wrap: nowrap;">
                        <div class="col">
                            <div class="input">
                                <div class="label">Select Partner</div>
                                <select name="" id="" ref="partner" @change="search()" value="Today's Credits">
                                    <option :value="partner['id']" v-for="partner in partners">{{ partner['name'] }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <button class="btn btn-primary" @click="payCredit()">Pay Credit</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="head mt-5">
                <h4>Credits</h4>
            </div>
            <div class="order_table mt-4">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info" style="width: 98%;">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th class="text-start">partner</th>
                                <th class="text-start">Pending Amount</th>
                                <th class="text-start">Order Number</th>
                                <th class="text-start">Credit Date</th>
                                <th class="text-start">Last Paid</th>
                                <th class="text-start">Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <tr v-for="item in credit">
                                <td class="text-start">{{ getpartner(item['partner']['id']) != undefined ?
                                    getpartner(item['partner']['id'])['name'] : '' }}</td>
                                <td class="text-start">{{ item['ammount'] }}</td>
                                <td class="text-start">{{ item['order_id'] }}</td>
                                <td class="text-start">{{ new Date(item['created_at']).toLocaleString('nl-NL') }}</td>
                                <td class="text-start">{{ new Date(item['updated_at']).toLocaleString('nl-NL') }}</td>
                                <td class="text-start">
                                    <div class="d-flex align-items-center list-action justify-content-start">
                                        <a class="badge bg-success mr-2" href="javascript:void(0)" title="View"
                                            @click="getHistory(item['id'])"><i class="fa-regular fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="head mt-5">
                        <h4>Total</h4>
                    </div>
                    <div class="order_table mt-4">
                        <div class="table-responsive rounded mb-3">
                            <table class="history-table table mb-0 tbl-server-info" style="width: 98%;">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th class="text-start w-50">Total Bills</th>
                                        <th class="text-start w-50">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    <tr>
                                        <td class="text-start">{{ total_bills }}</td>
                                        <td class="text-start">{{ currency(total_amount, '') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="head mt-5">
                        <h4>Credit Payment History</h4>
                    </div>
                    <div class="order_table mt-4">
                        <div class="table-responsive rounded mb-3">
                            <table class="history-table table mb-0 tbl-server-info" style="width: 98%;">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th class="text-start w-50">Date</th>
                                        <th class="text-start w-50">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    <tr v-for="history in histories">
                                        <td class="text-start">{{ new Date(history['created_at']).toLocaleString('nl-NL') }}
                                        </td>
                                        <td class="text-start">{{ history['ammount'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="PaymentModal" tabindex="-1" role="dialog" aria-labelledby="PaymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-5">
                    <div class="row row-cols-lg-2">
                        <div class="col">
                            <div class="head">
                                <h4>Enter amount to pay</h4>
                            </div>
                            <div class="form-group mt-4">
                                <label>Amount</label>
                                <input ref="amount" type="text" class="form-control" placeholder="Enter Amount">
                            </div>
                            <button class="btn btn-info text-light" @click="payNow()">Pay Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalTitle"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-transparent border-0" style="box-shadow: none;">
                <div class="modal-body d-flex" style="justify-content: center; box-shadow: none;">
                    <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import toastr from 'toastr';
import { currency, isNumber } from '../custom';

export default {
    props: ['credits', 'partners'],
    data() {
        return {
            name: 'dashboardCredits',
            credit: this.credits,
            histories: [],
            payID: 0,
            total_bills: 0,
            total_amount: 0,
        }
    },

    methods: {
        currency,
        getpartner(id) {
            return this.partners.filter(item => item['id'] == id)[0];
        },
        async search() {

            this.credit = [];
            $(".data-table").DataTable().destroy();
            var partner = this.$refs.partner.value;

            if (partner != 'all') {
                if (this.partners.filter(item => item['id'] == partner).length == 0) {
                    toastr.error('Invalid partner', "Error");
                    return false;
                }
            }

            const { data } = await axios.post("/dashboard/partner-credits/get-credits", {
                params: {
                    partner_id: partner,
                }
            });

            this.credit = data;

            this.histories = [];

            this.total_amount = 0;
            this.total_bills = 0;

            this.credit.forEach(element => {
                this.total_amount += element['ammount']
            });

            this.total_bills = this.credit.length;

            setTimeout(function () {
                $(".data-table").DataTable().order([3, 'desc']).draw();
            }, 500);

            return 0;
        },
        getHistory(id) {
            $(".history-table").DataTable().destroy();
            this.histories = this.credit.filter(item => item['id'] == id)[0]['history'].sort((b, a) => {
                if (a.created_at < b.created_at) return -1;
                if (a.created_at > b.created_at) return 1;
                return 0;
            });

            setTimeout(function () {
                $(".history-table").DataTable().order([0, 'desc']).draw();
            }, 500);
        },
        payCredit() {
            if (this.$refs.partner.value == '' || this.$refs.partner.value == 0 || this.$refs.partner.value == 'all') {
                toastr.error('Please select a partner', 'Error');
                return;
            }

            $("#PaymentModal").modal('show');
        },
        async payNow() {
            var credit = this.$refs.partner.value;
            var amount = this.$refs.amount.value;
            if (credit == '' || credit == 0 || credit == 'all') {
                toastr.error('Invalid partner', "Error");
                return false;
            }

            if (!isNumber(amount)) {
                toastr.error('Please enter amount in number only', "Error");
                return false;
            }

            $("#PaymentModal").modal('hide');
            $("#loadingModal").modal('show');

            const { data } = await axios.post("/dashboard/partner-credits/pay-credit", {
                params: {
                    partner_id: credit,
                    amount: amount,
                }
            });

            $("#loadingModal").modal('hide');

            if (data.error == 0) {
                $("#loadingModal").modal('hide');

                toastr.success('Credit paid', 'Success');

                this.search();

                this.$refs.amount.value = '0';

                let objFra = document.createElement('iframe');
                objFra.style.visibility = 'hidden';
                objFra.src = '/credit-invoice/' + data.bill;
                document.body.appendChild(objFra);
                objFra.contentWindow.focus();
                objFra.contentWindow.print();
            }
            else {
                toastr.error(data.msg, "Error");
            }

            setTimeout(function () {
                $("#loadingModal").modal('hide');
            }, 1000);

        }
    },
    beforeMount() {
    },
    mounted() {
        // Initialize select2
        $(this.$refs.partner).select2();

        // Listen to the select2 change event
        $(this.$refs.partner).on("change", (event) => {
            this.search();
        });
    }
}
</script>
