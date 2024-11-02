<template>
    <div class="content-page">
        <div class="container-fluid">
            <div class="sales-filter">
                <div class="filter_from table-responsive pb-3">
                    <div class="row row-cols-md-6 m-0" style="flex-wrap: nowrap;">
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
                                <button class="primary-btn border-only submit-btn mt-3" @click="invoiceFilter()">Get
                                    Report</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="total_report mt-5">
                <div class=" rounded mb-3">
                    <table class="table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th class="text-center">Dates</th>
                                <th class="text-center">Total Units Sold</th>
                                <th class="text-center">Total Revenew</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <tr>
                                <td class="text-center" style="text-align: center !important;">{{ dates }}</td>
                                <td class="text-center">{{ report_sold }}</td>
                                <td class="text-center" style="text-align: center !important;">{{ report_total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="head mt-5">
                <h4>Spare Report</h4>
            </div>
            <div class="order_table mt-4">
                <div class="rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th class="text-start">Spare Code</th>
                                <th class="text-start">Spare Name</th>
                                <th class="text-start">Sold QTY</th>
                                <th class="text-start">Revenew</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <tr v-for="report in reports">
                                <td class="text-start">{{ report['spare_code'] }}</td>
                                <td class="text-start">{{ report['spare_name'] }}</td>
                                <td class="text-start">{{ report['total_sold'] }}</td>
                                <td class="text-start">{{ report['total_revenew'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { currency } from '../custom';
import printJS from 'print-js';

export default {
    props: ['reports'],
    data() {
        return {
            name: 'spareReport',
            reports: this.reports,
            dates: "Today",
            report_sold: 0.00,
            report_total: 0.00,
        }
    },
    methods: {
        currency,
        async invoiceFilter() {

            this.reports = [];

            $(".data-table").DataTable().destroy();
            var fromdate = this.$refs.fromdate.value;
            var todate = this.$refs.todate.value;

            if (fromdate == "") {
                toastr.error('Please select From Date');
                return false;
            }

            if (todate == "") {
                toastr.error('Please select To Date');
                return false;
            }

            if (fromdate > todate) {
                toastr.error('Please select from date lower than to date');
                return false;
            }

            const { data } = await axios.post("/dashboard/spares/get-report", {
                fromdate: fromdate,
                todate: todate,
            });

            this.reports = data;

            this.report_sold = 0;
            this.report_total = 0;

            this.reports.forEach(element => {
                this.report_sold += parseFloat(element["total_sold"]);
                this.report_total += parseFloat(element["total_revenew"]);
            });

            this.dates = fromdate + " | " + todate;

            setTimeout(function () {
                $(".data-table").DataTable();
            }, 500);

            return 0;
        },
    },
    beforeMount() {
    },
    mounted() {
        this.$nextTick(() => {
            $('.select2-multiple').select2();
        });

        this.report_sold = 0;
        this.report_total = 0;

        this.reports.forEach(element => {
            this.report_sold += parseFloat(element["total_sold"]);
            this.report_total += parseFloat(element["total_revenew"]);
        });
    }
}
</script>