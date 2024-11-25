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
                                <div class="label">Status</div>
                                <select name="" id="" ref="status">
                                    <option value="">-- Select status --</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Purchased">Purchased</option>
                                    <option value="Canceled">Canceled</option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="input">
                                <div class="label">Bill No</div>
                                <input type="text" name="" id="" ref="bill_no" placeholder="Bill No">
                            </div>
                        </div>

                        <div class="col">
                            <div class="input">
                                <button class="primary-btn border-only submit-btn mt-3" @click="invoiceFilter()">Update Filter</button>
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
                                <th class="text-start"><input type="checkbox" class="selectAllCheckbox" @change="selectAll()"></th>
                                <th class="text-start">Order no</th>
                                <th class="text-start">Bill no</th>
                                <th class="text-start">Panel no</th>
                                <th class="text-start">PCB no</th>
                                <th class="text-start">Price</th>
                                <th class="text-start">QTY</th>
                                <th class="text-start">Order date</th>
                                <th class="text-start">Delivery date</th>
                                <th class="text-start">Status</th>
                                <th class="text-start">Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            <tr v-for="(order, key) in orders">
                                <td class="text-start"><input type="checkbox" :class="'order-'+key+' checkbox'" @change="updateBulkEdit" :value="order['id']" id=""></td>
                                <td class="text-start">{{ order['purchase_no'] }}</td>
                                <td class="text-start">{{ order['bill_no'] }}</td>
                                <td class="text-start">{{ order['panel_no'] }}</td>
                                <td class="text-start">{{ order['pcb_no'] }}</td>
                                <td class="text-start">{{ currency(order['price'], '') }}</td>
                                <td class="text-start">{{ order['qty'] }}</td>
                                <td class="text-start">{{ order['created_at'] != NULL ? new
                                    Date(order['created_at']).toLocaleString('nl-NL') : "N/A" }}</td>
                                <td class="text-start">{{ order['delivery_date'] != NULL ? new
                                    Date(order['delivery_date']).toLocaleString('nl-NL') : "N/A" }}</td>
                                <td
                                    :class="'text-start ' + (order['status'] == 'Pending' ? 'text-bg-warning' : '') + (order['status'] == 'Purchased' ? 'text-bg-success' : '') + (order['status'] == 'Canceled' ? 'text-bg-danger' : '')">
                                    {{ order['status'] }}</td>

                                <td class="text-start">
                                    <div class="d-flex align-items-center list-action justify-content-start">
                                        <a class="badge bg-danger mr-2" href="javascript:void(0)" @click="deleteOrder('.order-'+key)" :data-target="'order-'+key" title="Delete"
                                            ><i class="fa-regular fa-trash-can"></i></a>

                                        <a class="badge bg-info mr-2" :href="'/dashboard/china-order-update/'+order['purchase_no']" title="Edit"
                                            @click=""><i class="fa-solid fa-pencil"></i></a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div :class="(showBulkEdit? '' : 'd-none') +' bulk-edit d-flex gap-4 align-items-center mt-3 mb-5'">
                <i class="fa-solid fa-arrow-turn-up fa-flip-horizontal text-secondary"></i>
                <div class="input">
                    <select name="" id="" ref="bulkAction">
                        <option value="">-- Select --</option>
                        <option value="Pending">Change status to pending</option>
                        <option value="Purchased">Change status to purchased</option>
                        <option value="Canceled">Change status to canceled</option>
                        <option value="Delete">Delete orders</option>
                    </select>
                </div>
                <button class="primary-btn submit-btn border-only" @click="bulkEdit">Bulk Edit</button>
            </div>
        </div>
    </div>
</template>

<script>
import { currency } from '../custom';
import printJS from 'print-js';

export default {
    props: ['customers'],
    data() {
        return {
            name: 'chinaOrders',
            orders: [],
            showBulkEdit: false,
        }
    },
    methods: {
        currency,
        getCustomer(id) {
            return this.customers.filter(item => item['id'] == id)[0];
        },
        async invoiceFilter() {

            this.sales = [];
            $(".data-table").DataTable().destroy();
            var fromdate = this.$refs.fromdate.value;
            var todate = this.$refs.todate.value;
            var status = this.$refs.status.value;
            var bill_no = this.$refs.bill_no.value;

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

            const { data } = await axios.post("/dashboard/china-order-get", {
                params: {
                    fromdate: fromdate,
                    todate: todate,
                    status: status,
                    bill_no: bill_no,
                }
            });

            this.orders = data;
            setTimeout(function () {
                $(".data-table").DataTable();
            }, 500);

            return 0;
        },
        selectAll() {
            document.querySelectorAll('.checkbox').forEach(element => {
                element.checked = document.querySelector('.selectAllCheckbox').checked;
            });
            this.updateBulkEdit()
        },
        updateBulkEdit() {
            this.showBulkEdit = false;
            document.querySelectorAll('.checkbox').forEach(element => {
                element.checked? this.showBulkEdit = true : '';
            });
        },
        getBulkID() {
            var arr = [];
            document.querySelectorAll('.checkbox').forEach(element => {
                element.checked? arr.push(element.value) : '';
            });
            return arr;
        },
        deleteOrder (elem) {
            document.querySelectorAll('.checkbox').forEach(element => {
                element.checked = false;
            });
            document.querySelector(elem).checked = true;
            this.$refs.bulkAction.value = 'Delete';
            this.bulkEdit();
        },
        async bulkEdit() {
            var action = this.$refs.bulkAction.value;
            var ID = this.getBulkID();

            if (action.trim() == "") {
                toastr.error("Please select an action", "Error");
                return 0;
            }

            if (ID.length <= 0) {
                toastr.error("Please atleast 1 record", "Error");
                return 0;
            }

            const { data } = await axios.post("/dashboard/china-order-bulkedit", {
                id: ID,
                action: action
            });

            toastr.success(data.msg, "SuccesS");

            this.invoiceFilter();
        }
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