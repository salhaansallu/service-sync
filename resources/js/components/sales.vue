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
                                <div class="label">By customer</div>
                                <select name="" id="" ref="customer">
                                    <option value="0">-- Select customer --</option>
                                    <option v-for="customer in customers" :value="customer['id']">{{ customer['name'] }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="input">
                                <div class="label">From cashier</div>
                                <select name="" id="" ref="cashier">
                                    <option value="0">-- Select cashier --</option>
                                    <option v-for="cashier in cahiers" :value="cashier['user_id']">{{ cashier['fname'] }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="input">
                                <button class="primary-btn border-only submit-btn mt-3" @click="invoiceFilter()">Update
                                    Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="head mt-5">
                <h4>Repair Bills</h4>
            </div>
            <div class="order_table mt-4">
                <div class="table-responsive rounded mb-3">
                    <table class="data-table table mb-0 tbl-server-info">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th class="text-start">Bill number</th>
                                <th class="text-start">Total</th>
                                <th class="text-start">Cost</th>
                                <th class="text-start">Profit</th>
                                <th class="text-start">Customer</th>
                                <th class="text-start">Sales date</th>
                                <th class="text-start">Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <tr v-for="sale in sales">
                                <td class="text-start">{{ sale['bill_no'] }}</td>
                                <td class="text-start">{{ sale['total'] }}</td>
                                <td class="text-start">{{ sale['cost'] }}</td>
                                <td class="text-start">{{ currency(sale['total'] - sale['cost'], '') }}</td>
                                <td class="text-start">{{ getCustomer(sale['customer']) != undefined ? getCustomer(sale['customer'])['name'] : '' }}</td>
                                <td class="text-start">{{ new Date(sale['created_at']).toLocaleString('nl-NL') }}</td>
                                <td class="text-start">
                                    <div class="d-flex align-items-center list-action justify-content-start">
                                        <a class="badge bg-info mr-2" href="javascript:void(0)" title="view"
                                            @click="getProducts(sale['bill_no'])"><i class="fa-regular fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="head mt-5">
                <h4>Spare Parts Used</h4>
            </div>
            <div class="order_table mt-4">
                <div class="table-responsive rounded mb-3">
                    <table class="product-table table mb-0 tbl-server-info text-start">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th class="text-start">Spare name</th>
                                <th class="text-start">Code</th>
                                <th class="text-start">Stock</th>
                                <th class="text-start">Cost</th>
                                <th class="text-start">Supplier ID</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body text-start">

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
    props: ['bladesales', 'customers', 'cahiers', 'isadmin'],
    data() {
        return {
            name: 'dashboardSales',
            orderProduct: [],
            sales: this.bladesales,
        }
    },
    methods: {
        currency,
        getCustomer(id) {
            return this.customers.filter(item => item['id'] == id)[0];
        },
        async getProducts(order_number) {
            this.orderProduct = [];
            var values = [];
            const { data } = await axios.post("/dashboard/sales/get-products", { params: { bill_no: order_number } });
            $(".product-table").DataTable().clear().draw();
            this.orderProduct = data;

            for (let i = 0; i < this.orderProduct.length; i++) {
                values[i] = [
                    this.orderProduct[i]['pro_name'],
                    this.orderProduct[i]['sku'],
                    this.orderProduct[i]['qty'],
                    this.orderProduct[i]['cost'],
                    this.orderProduct[i]['supplier'],
                ]
            }

            $(".product-table").DataTable().rows.add(
                values
            ).draw();
            window.scrollTo(0, document.body.scrollHeight);
        },
        async invoiceFilter() {

            this.sales = [];
            $(".data-table").DataTable().destroy();
            var fromdate = this.$refs.fromdate.value;
            var todate = this.$refs.todate.value;
            var customer = this.$refs.customer.value;
            var cashier = this.$refs.cashier.value;

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

            const { data } = await axios.post("/dashboard/sales/get-invoice", {
                params: {
                    fromdate: fromdate,
                    todate: todate,
                    customer: customer,
                    cashier: cashier,
                }
            });

            this.sales = data;
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
    }
}
</script>
