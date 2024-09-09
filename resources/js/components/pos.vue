<template>
    <div class="pos-wrap">
        <div class="category">
            <div v-if="posData.plan == 1" class="disabled">
                <div class="ctnt">
                    <b>To use this feature</b><br><br>
                    <a href="/contact" class="primary-btn submit-btn border-only"><i class="fa-solid fa-crown"></i>
                        Upgrade to premium</a>
                </div>
            </div>
            <div class="head">
                <h2>Service Options</h2>
            </div>

            <div class="favourits">
                <button @click="goToDashboard" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-chart-line"></i>Dashboard</button>
            </div>

            <div class="favourits">
                <button @click="newOrder('show')" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-plus"></i>New Order</button>
            </div>

            <div class="favourits">
                <button @click="newCustomer('show')" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-user-plus"></i>Customers</button>
            </div>

            <div class="favourits">
                <button @click="newSale('show')" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-cash-register"></i>Sales</button>
            </div>

            <!-- <div class="favourits">
                <button @click="newOrder('show')" class="primary-btn submit-btn border-only"><i class="fa-solid fa-cash-register"></i>Generate Coupon</button>
            </div> -->

            <div class="categories">
                <ul>
                    <!--  -->
                </ul>
            </div>
        </div>

        <div class="products">
            <div class="searchbar">
                <div class="input">
                    <input type="text" ref="searchbar" placeholder="Search here" value=""
                        @keyup="searchProducts($event)">
                </div>
            </div>

            <div class="row">
                <div class="col-12 mt-4 mb-2">
                    <div class="order-display">
                        <div class="row">
                            <div class="col-1 form-text">Bill No</div>
                            <div class="col-2 form-text">Model No</div>
                            <div class="col-2 form-text">Serial No</div>
                            <div class="col-2 form-text">Fault</div>
                            <div class="col-2 form-text">Customer</div>
                            <div class="col-1 form-text">Status</div>
                            <div class="col-1 form-text">Action</div>
                        </div>
                    </div>
                </div>

                <div v-for="repair in repairs"
                    :class="'col-12 mt-2 py-2 bg-light pos-status-' + getStatus(repair.status)"
                    style="border-radius: 5px;box-shadow: 0px 0px 2px 0px #00000029;">
                    <div class="order-display">
                        <div class="row">
                            <div
                                class="col-1 form-text mt-0 d-flex align-items-center control-text-overflow text-primary">
                                {{ repair.bill_no }}</div>
                            <div class="col-2 form-text mt-0 d-flex align-items-center control-text-overflow">{{
                                repair.model_no }}</div>
                            <div class="col-2 form-text mt-0 d-flex align-items-center control-text-overflow">{{
                                repair.serial_no }}</div>
                            <div class="col-2 form-text mt-0 d-flex align-items-center control-text-overflow">{{
                                repair.fault }}</div>
                            <div class="col-2 form-text mt-0 d-flex align-items-center control-text-overflow">{{
                                repair.customer }}</div>
                            <div class="col-1 form-text mt-0 d-flex align-items-center control-text-overflow"><span
                                    :class="'badge bg-' + getStatus(repair.status)">{{ repair.status }}</span></div>
                            <div class="col-1 form-text mt-0 d-flex align-items-center">
                                <button @click="finishOrder('show', repair.bill_no)" v-if="repair.status == 'Pending'"
                                    class="primary-btn submit-btn"
                                    style="font-size: 14px;padding: 5px 15px;">Finished</button>
                                <button @click="selectProduct(repair.bill_no)" v-if="repair.status == 'Return'"
                                    class="primary-btn submit-btn"
                                    style="font-size: 14px;padding: 5px 15px;">Checkout</button>
                                <button @click="selectProduct(repair.bill_no)" v-if="repair.status == 'Repaired'"
                                    class="primary-btn submit-btn"
                                    style="font-size: 14px;padding: 5px 15px;">Checkout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="order">
            <div class="head">
                <h2>New Order</h2> <button @click="reloadPOS()"
                    class="primary-btn border-only submit-btn">Clear</button>
            </div>

            <div class="total bg-grey">
                <div class="row row-cols-2">
                    <div class="col">
                        Sub total
                    </div>
                    <div class="col" ref="subtotal">
                        LKR 0.00
                    </div>
                </div>

                <div class="row row-cols-2" style="border-bottom: #e9e9e9 3px dotted;">
                    <div class="col">
                        Advance
                    </div>
                    <div class="col" ref="order_advance">
                        LKR 0.00
                    </div>
                </div>

                <div class="row row-cols-2 order-total">
                    <div class="col">
                        Total
                    </div>
                    <div class="col" ref="order_total">
                        LKR 0.00
                    </div>
                </div>

                <div class="row row-cols-2">
                    <div class="col">
                        Cash
                    </div>
                    <div class="col">
                        <input type="number" ref="cashin" value="0"
                            @keyup="$event.key == 'Enter' ? proceed() : updateOrder()" @focus="$event.target.select();">
                    </div>
                </div>

                <div class="row row-cols-2 border-0 pb-0">
                    <div class="col">
                        Balance
                    </div>
                    <div class="col" ref="balance">
                        LKR 0.00
                    </div>
                </div>
            </div>

            <div class="payment_type">
                <div class="row row-cols-3 justify-content-between">
                    <div class="col">
                        <div class="method active" ref="cash" @click="paymentMethod('cash')">
                            <i class="fa-solid fa-money-bill-1-wave"></i>
                        </div>
                        <div class="title">Cash/Card</div>
                    </div>
                    <div class="col">
                        <div class="method" ref="credit" @click="paymentMethod('credit')">
                            <i class="fa-solid fa-money-check-dollar"></i>
                        </div>
                        <div class="title">Credit</div>
                    </div>
                </div>
            </div>

            <div class="proceed_btn">
                <div class="row row-cols-1">
                    <div class="col">
                        <button class="primary-btn submit-btn w-100" @click="proceed()">Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalTitle"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body d-flex" style="justify-content: center;">
                    <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="NewOrder" tabindex="-1" role="dialog" aria-labelledby="NewOrder" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body d-flex" style="justify-content: center;">
                    <div class="product-wrp mt-1">
                        <div class="row justify-content-between">

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Model No</label>
                                    <input ref="model_no" type="text" placeholder="Model No" value="">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Serial No</label>
                                    <input ref="serial_no" type="text" placeholder="Serial No" value="">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Fault</label>
                                    <input ref="fault" type="text" placeholder="Fault" value="">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Advance</label>
                                    <input ref="advance" type="text" placeholder="Advance" value="0">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Total</label>
                                    <input ref="total" type="text" placeholder="Total" value="0">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Customer</label>
                                    <select ref="customer" name="">
                                        <option v-for="customer in users" :value="customer.id">{{ customer.name }} ({{
                                            customer.phone }})</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="inner border p-3">
                                    <div class="input d-flex align-items-center">
                                        <input type="checkbox" :value="'Box'" ref="box" class="d-inline"
                                            style="width: 15px; height: 15px; margin-right: 10px;">
                                        <label for="" class="mb-1 d-inline">Box</label>
                                    </div>
                                    <div class="input d-flex align-items-center">
                                        <input type="checkbox" :value="'Stand'" ref="stand" class="d-inline"
                                            style="width: 15px; height: 15px; margin-right: 10px;">
                                        <label for="" class="mb-1 d-inline">Stand</label>
                                    </div>
                                    <div class="input d-flex align-items-center">
                                        <input type="checkbox" :value="'Remote'" ref="remote" class="d-inline"
                                            style="width: 15px; height: 15px; margin-right: 10px;">
                                        <label for="" class="mb-1 d-inline">Remote</label>
                                    </div>
                                    <div class="input d-flex align-items-center">
                                        <input type="checkbox" :value="'Wall Bracket'" ref="wall" class="d-inline"
                                            style="width: 15px; height: 15px; margin-right: 10px;">
                                        <label for="" class="mb-1 d-inline">Wall Bracket</label>
                                    </div>
                                    <div class="input d-flex align-items-center">
                                        <input type="checkbox" :value="'Panel Scratches'" ref="panel" class="d-inline"
                                            style="width: 15px; height: 15px; margin-right: 10px;">
                                        <label for="" class="mb-1 d-inline">Panel Scratches</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Note</label>
                                    <textarea name="" ref="note" rows="4" placeholder="Note"></textarea>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <button :disabled="isDisabled" @click="PlaceOrder()" class="primary-btn submit-btn">Save</button>
                                <button @click="newOrder('hide')"
                                    style="background: transparent; color: red !important; border: red 1px solid;"
                                    class="primary-btn submit-btn mx-4">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="FinishOrder" tabindex="-1" role="dialog" aria-labelledby="FinishOrder"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body d-flex" style="justify-content: center;">
                    <div class="product-wrp mt-1">
                        <div class="row justify-content-between">
                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Bill No</label>
                                    <input ref="finish_bill_no" disabled type="text" placeholder="Bill No" value="">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Total</label>
                                    <input ref="finish_total" type="text" placeholder="Total" value="">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Spares</label>
                                    <div class="row" v-for="spare in spareCount">
                                        <div class="col-9">
                                            <select :ref="'finish_spare_' + spare" :name="'finish_spare_' + spare">
                                                <option v-for="spare in Spares" :value="spare.id">{{ spare.pro_name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <input type="number" min="1" value="1" :ref="'qty_finish_spare_' + spare"
                                                :name="'qty_finish_spare_' + spare">
                                        </div>
                                    </div>
                                    <span class="d-flex w-100 justify-content-between">
                                        <span @click="spareCountUpdate('-')" class="form-text mt-3"
                                            style="cursor: pointer;">- Remove spare</span>
                                        <span @click="spareCount++" class="form-text mt-3" style="cursor: pointer;">+
                                            Add spare</span>
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 mt-3" v-if="spareCount == 0">
                                <div class="input">
                                    <label for="" class="mb-1">Service Cost (%)</label>
                                    <input ref="finish_service_cost" type="text" placeholder="Service Cost" value="10">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Status</label>
                                    <select ref="finish_status" name="finish_status">
                                        <option value="Repaired">Repaired</option>
                                        <option value="Return">Return</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Note</label>
                                    <textarea ref="finish_note" name="" rows="4" placeholder="Note"></textarea>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <button @click="FinishRepair()" class="primary-btn submit-btn">Save</button>
                                <button @click="finishOrder('hide')"
                                    style="background: transparent; color: red !important; border: red 1px solid;"
                                    class="primary-btn submit-btn mx-4">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="NewCustomer" tabindex="-1" role="dialog" aria-labelledby="NewCustomer"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body d-flex" style="justify-content: center;">
                    <div class="product-wrp mt-1">
                        <div class="existingcustomer mb-4 border" style="max-height: 400px; overflow-y: auto;">
                            <div class="fs-5 mb-2 p-4">Existing Customers</div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Mobile</th>
                                        <th scope="col">Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(cus, index) in users" :key="index">
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ cus.name }}</td>
                                        <td>{{ cus.phone }}</td>
                                        <td>{{ cus.address.substr(0, 30) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row justify-content-between">
                            <div class="col-12">
                                <div class="fs-5 mb-1">Create New Customer</div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Customer Name</label>
                                    <input ref="cus_name" type="text" placeholder="Customer Name" value="">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Customer Mobile</label>
                                    <input ref="cus_mobile" type="text" placeholder="Customer Mobile" value="">
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Customer Address</label>
                                    <input ref="cus_address" type="text" placeholder="Customer Address" value="">
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button @click="createCustomer()" class="primary-btn submit-btn">Save</button>
                                <button @click="newCustomer('hide')"
                                    style="background: transparent; color: red !important; border: red 1px solid;"
                                    class="primary-btn submit-btn mx-4">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="NewSale" tabindex="-1" role="dialog" aria-labelledby="NewSale" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-body bg-grey p-0">
                    <div class="product-wrp mt-1">
                        <div class="row">
                            <div class="col-12 mt-4">
                                <button @click="newSale('hide')"
                                    style="background: transparent; color: red !important; border: red 1px solid;"
                                    class="primary-btn submit-btn mx-4"><i class="fa-solid fa-arrow-left-long mx-2"></i>
                                    Back To POS</button>
                            </div>
                        </div>
                    </div>
                    <sale_pos />
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { ref } from 'vue';
import toastr from 'toastr';
import { validateName, checkEmpty, validateCountry, validatePhone, getUrlParam, currency } from '../custom';
import axios from 'axios';
import printJS from 'print-js';
import sale_pos from './sale_pos.vue';

export default {
    props: ['app_url'],
    components: {
        sale_pos
    },
    data() {
        return {
            name: 'pos',
            repairs: [],
            Spares: [],
            proBackup: [],
            selectedRepair: [],
            users: [],
            posData: [],
            paymentMod: 'cash',
            spareCount: 1,
            finishOrderNo: 0,
            isDisabled: false,
        }
    },
    methods: {
        currency,
        printJS,
        loadModal(action) {
            $("#loadingModal").modal(action);
        },
        goToDashboard() {
            window.location.href = '/dashboard';
        },
        newOrder(action) {
            $("#NewOrder").modal(action);
        },
        newCustomer(action) {
            $("#NewCustomer").modal(action);
        },
        newSale(action) {
            $("#NewSale").modal(action);
        },
        finishOrder(action, bill_no = 0) {
            $("#FinishOrder").modal(action);
            this.$refs['finish_bill_no'].value = bill_no;
            this.$refs['finish_note'].value = this.repairs.filter(item => item['bill_no'] == bill_no)[0].note;
            this.$refs['finish_total'].value = this.repairs.filter(item => item['bill_no'] == bill_no)[0].total;
            this.finishOrderNo = bill_no;
        },
        async getPosData() {
            const { data } = await axios.post("/pos/pos_data");
            this.posData = data;
            if (this.posData.plan > 1) {
                this.getCustomers();
                this.getRepairs();
                this.getSpares();
            }
        },
        async getRepairs() {
            const { data } = await axios.post("/pos/get_repairs");
            this.repairs = data;
            this.proBackup = data;
        },
        async getCustomers() {
            const { data } = await axios.post("/pos/get_customers");
            this.users = data;
        },
        async getSpares() {
            const { data } = await axios.post("/pos/get_spares");
            this.Spares = data;
        },
        Enter(e, target) {
            if (e.key == 'Enter') {
                this.$refs[target].select();
            }
        },
        updateOrder() {
            if (this.selectedRepair.length > 0) {
                console.log(this.selectedRepair);

                this.$refs["subtotal"].innerText = currency(this.selectedRepair[0]["total"], this.posData.currency);
                this.$refs["order_advance"].innerText = currency(this.selectedRepair[0]["advance"], this.posData.currency);
                this.$refs["order_total"].innerText = currency((this.selectedRepair[0]["total"] - this.selectedRepair[0]["advance"]), this.posData.currency);
                this.$refs["balance"].innerText = currency((this.selectedRepair[0]["total"] - this.selectedRepair[0]["advance"]) - this.$refs["cashin"].value, this.posData.currency);
                return;
            }

            this.$refs["subtotal"].innerText = "0.00";
            this.$refs["order_advance"].innerText = "0.00";
            this.$refs["order_total"].innerText = "0.00";
            this.$refs["balance"].innerText = "0.00";
        },
        searchProducts(e) {
            var pro = this.proBackup;
            if (this.$refs['searchbar'].value == "") {
                this.repairs = this.proBackup;
            }
            else {
                pro = this.proBackup;
                this.repairs = pro.filter(item => item['model_no'].toLowerCase().includes(this.$refs['searchbar'].value.toLowerCase()) || item['bill_no'].toLowerCase().includes(this.$refs['searchbar'].value.toLowerCase()));
            }
        },
        removeProduct(pro) {
            this.selectedRepair = [];
            this.updateOrder();
        },
        selectProduct(pro) {
            this.selectedRepair = [];
            this.repairs.forEach(element => {
                if (element['bill_no'] == pro) {
                    this.selectedRepair.push(element);
                    return false;
                }
            });

            this.updateOrder();
        },
        reloadPOS() {
            this.selectedRepair = [];

            this.spareCount = 1;

            this.$refs["cashin"].value = '0';
            this.$refs["total"].innerText = "LKR 0.00";
            this.$refs["subtotal"].innerText = "LKR 0.00";
            this.$refs["balance"].innerText = "LKR 0.00";
            this.paymentMethod("cash");
        },
        get_total() {

            var price = 0.00;

            if (this.selectedRepair.length > 0) {
                price = (this.selectedRepair[0]["total"] - this.selectedRepair[0]["advance"])
            }

            return parseFloat(price);
        },
        paymentMethod(method) {
            if (this.posData['plan'] == 1 && method == 'credit') {
                toastr.error('Credit option only available for premium users', 'Error');
                return 0;
            }
            this.paymentMod = method;
            this.$refs.cash.classList.remove("active");
            this.$refs.credit.classList.remove("active");
            method != "" ? this.$refs[method].classList.add("active") : this.paymentMod = '';
        },
        async proceed() {
            if (Array.isArray(this.selectedRepair) && this.selectedRepair.length > 0) {
                var total = this.get_total();
                var payment = this.paymentMod;
                var cashin = this.$refs.cashin.value == "" ? 0 : this.$refs.cashin.value;
                var repair = [];

                if (parseFloat(cashin) < parseFloat(total) && (payment == "cash")) {
                    if (confirm('Are you sure you want to add remaining as credit?')) {

                    }
                    else {
                        return 0;
                    }
                }

                if (payment == "credit" || payment == "cash") {

                    this.loadModal("show");

                    repair = this.selectedRepair[0];

                    const { data } = await axios.post('/pos/checkout', {
                        params: {
                            bill_no: repair['bill_no'],
                            cashin: cashin,
                        }
                    }).catch(function (error) {
                        if (error.response) {
                            this.loadModal("hide");
                        }
                    });
                    this.loadModal("hide");

                    if (data.error == "0") {
                        this.loadModal("hide");
                        toastr.success(data.msg, "Success");
                        printJS(data.invoiceURL);
                        this.getRepairs();
                        this.reloadPOS();
                    }
                    else {
                        this.loadModal("hide");
                        toastr.error(data.msg, "Error");
                    }
                    this.loadModal("hide");
                }
                else {
                    toastr.error("Please select a payment method", "Error");
                }
                this.loadModal("hide");
            }
            else {
                toastr.error("Please select products", "Error");
            }
            setTimeout(() => {
                this.loadModal('hide');
            }, 1000);
        },
        async FinishRepair() {
            if (this.finishOrderNo != 0) {
                var bill_No = this.finishOrderNo;
                var total = this.$refs.finish_total.value;
                var note = this.$refs.finish_note.value;
                var status = this.$refs.finish_status.value;
                var sparePro = [];
                var service_cost = 0;

                if (total.trim() == "") {
                    toastr.error("Please enter a total amount", "Error");
                    return;
                }

                if (status.trim() == "") {
                    toastr.error("Please select a status", "Error");
                    return;
                }

                if (this.spareCount > 0) {
                    for (let i = 1; i <= this.spareCount; i++) {
                        sparePro.push({
                            id: this.$refs["finish_spare_" + i][0].value,
                            qty: this.$refs["qty_finish_spare_" + i][0].value,
                        });
                    }
                }
                else if (this.$refs.finish_service_cost != undefined) {
                    service_cost = this.$refs.finish_service_cost.value;
                }

                for (let i = 1; i <= this.spareCount; i++) {
                    sparePro.push({
                        id: this.$refs["finish_spare_" + i][0].value,
                        qty: this.$refs["qty_finish_spare_" + i][0].value,
                    });
                }

                this.loadModal("show");

                const { data } = await axios.post('/pos/update', {
                    bill_no: this.finishOrderNo,
                    total: total,
                    note: note,
                    spares: sparePro,
                    service_cost: service_cost,
                    status: status,

                }).catch(function (error) {
                    this.loadModal("hide");
                });

                this.loadModal("hide");

                if (data.error == "0") {
                    this.loadModal("hide");
                    toastr.success(data.msg, "Success");
                    this.getRepairs();
                    this.reloadPOS();
                }
                else {
                    this.loadModal("hide");
                    toastr.error(data.msg, "Error");
                }

                setTimeout(() => {
                    this.loadModal("hide");
                    this.finishOrder("hide");
                }, 500);
            }
        },
        async PlaceOrder() {
            var total = this.$refs.total.value;
            var model_no = this.$refs.model_no.value;
            var serial_no = this.$refs.serial_no.value;
            var fault = this.$refs.fault.value;
            var advance = this.$refs.advance.value;
            var note = this.$refs.note.value;
            var note2 = "";
            var customer = this.$refs.customer.value;

            if (model_no.trim() == "") {
                toastr.error("Please enter model number", "Error");
                return;
            }

            if (customer.trim() == "") {
                toastr.error("Please select customer", "Error");
                return;
            }

            this.isDisabled = true;

            const checkboxes = document.querySelectorAll('input[type="checkbox"]');

            checkboxes.forEach((checkbox, index) => {
                if (checkbox.checked) {
                    if (index == 0) {
                        note2 += 'Has '+checkbox.value;
                    }
                    else {
                        note2 += '{break;}Has '+checkbox.value;
                    }
                }
            });

            note2 += "{break;}"+note.replace(/\r?\n/g, '{break;}');

            const { data } = await axios.post('/pos/new_order', {
                total: total,
                model_no: model_no,
                serial_no: serial_no,
                fault: fault,
                advance: advance,
                note: note2,
                customer: customer,
            }).catch(function (error) {
                if (error.response) {
                    this.loadModal("hide");
                }
            });
            this.loadModal("hide");
            this.isDisabled = false;

            if (data.error == "0") {
                this.loadModal("hide");
                toastr.success(data.msg, "Success");
                printJS(data.invoiceURL);
                this.$refs.total.value = "0";
                this.$refs.model_no.value = "";
                this.$refs.serial_no.value = "";
                this.$refs.fault.value = "";
                this.$refs.advance.value = "0";
                this.$refs.finish_note.value = "";
                this.newOrder('hide');
                this.getRepairs();
                this.reloadPOS();
                this.isDisabled = false;
            }
            else {
                this.loadModal("hide");
                toastr.error(data.msg, "Error");
                this.isDisabled = false;
            }
            this.loadModal("hide");
            this.isDisabled = false;
        },
        async createCustomer() {
            var name = this.$refs.cus_name.value;
            var mobile = this.$refs.cus_mobile.value;
            var address = this.$refs.cus_address.value;

            if (name.trim() == "") {
                toastr.error("Please enter customer name", "Error");
                return;
            }

            if (mobile.trim() == "") {
                toastr.error("Please enter customer number", "Error");
                return;
            }

            const { data } = await axios.post('/dashboard/customer/create', {
                name: name,
                phone: mobile,
                address: address
            }).catch(function (error) {
                if (error.response) {
                    this.loadModal("hide");
                }
            });
            this.loadModal("hide");

            if (data.error == "0") {
                this.loadModal("hide");
                this.$refs.cus_name.value = "";
                this.$refs.cus_mobile.value = "";
                this.$refs.cus_address.value = "";
                this.getCustomers();
                this.newOrder('hide');
            }
            else {
                this.loadModal("hide");
                toastr.error(data.msg, "Error");
            }
            this.loadModal("hide");
        },
        getStatus(status) {
            if (status == "Repaired") {
                return 'success'
            }

            if (status == "Pending") {
                return 'danger'
            }

            if (status == "Return") {
                return 'warning'
            }
        },
        spareCountUpdate(op) {
            if (op == '-' && this.spareCount >= 1) {
                this.spareCount--;
            }
        }
    },
    beforeMount() {
        this.getPosData();
        //document.getElementById("searchbar").addEventListener("keyup", this.searchProducts);
    },
    mounted() {
        //

    }
}
</script>
