<template>
    <div id="leftMenuToggle" class="action_icons left" @click="openMenu('leftMenu')"><i class="fa-solid fa-bars"></i>
    </div>
    <div id="rightMenuToggle" class="action_icons right" @click="openMenu('rightMenu')"><i
            class="fa-solid fa-cash-register"></i></div>

    <div class="pos-wrap">
        <div id="leftMenu" class="category">
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
                <button @click="goTo('/dashboard')" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-chart-line"></i>Dashboard</button>
            </div>

            <div class="favourits">
                <button @click="goTo('/other-pos')" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-wrench"></i>Other Repairs</button>
            </div>

            <div class="favourits">
                <button @click="newOrder('show')" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-plus"></i>New Order</button>
            </div>

            <div class="favourits">
                <button @click="finishOrder('show')" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-wrench"></i>Bulk Finish Order</button>
            </div>

            <div class="favourits">
                <button @click="newCustomer('show')" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-user-plus"></i>Customers</button>
            </div>

            <div class="favourits">
                <button @click="newSale('show')" class="primary-btn submit-btn border-only"><i
                        class="fa-solid fa-cash-register"></i>Sales</button>
            </div>

            <div class="favourits">
                <select @change="filterStatus()" name="" ref="statusFilter"
                    class="form-control border-0 outline-0 text-secondary text-center" style="box-shadow: none;">
                    <option value="">All Repairs</option>
                    <option value="Pending">Pending Repairs</option>
                    <option value="Repaired">Repaired Repairs</option>
                    <option value="Awaiting Parts">Awaiting Parts</option>
                    <option value="Customer Pending">Customer Pending</option>
                    <option value="Delivered">Delivered</option>
                </select>
            </div>

            <div class="favourits">
                <select @change="filterPartner()" name="" ref="partnerFilter"
                    class="form-control border-0 outline-0 text-secondary text-center" style="box-shadow: none;">
                    <option value="">-- Select Partner --</option>
                    <option :value="partner.id" v-for="partner in partners">{{ partner.name }}</option>
                </select>
            </div>

            <div class="favourits">
                <select @change="techieFilter()" name="" ref="techieField"
                    class="form-control border-0 outline-0 text-secondary text-center" style="box-shadow: none;">
                    <option value="">-- Select Technician --</option>
                    <option v-for="cashier in cashiers" :value="cashier.user_id">{{ cashier.fname }}</option>
                </select>
            </div>

            <div class="categories">
                <ul>
                    <!--  -->
                </ul>
            </div>
        </div>

        <div class="products">
            <div class="searchbar d-flex gap-2 justify-content-between">
                <div class="d-flex gap-2">
                    <div class="input">
                        <select name="" ref="searchType" class="h-100 rounded">
                            <option value="repairs">Repairs</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="input">
                        <input type="text" ref="searchbar" placeholder="Search here" value=""
                            @keyup="searchProducts($event)">
                    </div>
                </div>

                <div class="filter d-flex gap-2">
                    <div class="input">
                        <label for="" class="mx-1">From </label>
                        <input type="date" ref="ordersFromDate" style="width: 120px;" @change="FilterRepairs()">
                    </div>
                    <div class="input">
                        <label for="" class="mx-2">To </label>
                        <input type="date" ref="ordersToDate" style="width: 120px;" @change="FilterRepairs()">
                    </div>
                </div>
            </div>

            <div class="row row-wrap">
                <div class="col-12 mt-4 mb-2">
                    <div class="order-display">
                        <div class="row">
                            <div class="col-1 form-text">Bill No</div>
                            <div class="col-2 form-text">Model No</div>
                            <div class="col-2 form-text" style="width: 100px;">Serial No</div>
                            <div class="col-2 form-text">Fault</div>
                            <div class="col-2 form-text">Customer</div>
                            <div class="col-1 form-text" style="width: 200px;">Status</div>
                            <div class="col-2 form-text" style="width: 100px;">Technician</div>
                        </div>
                    </div>
                </div>

                <div v-for="repair in repairs"
                    :class="'col-12 mt-2 py-2 bg-light pos-status-' + getStatus(repair.status) + ' ' + repair.selectStatus"
                    style="border-radius: 5px;box-shadow: 0px 0px 2px 0px #00000029;"
                    @contextmenu.prevent="openContext(repair.bill_no, $event)">
                    <div class="order-display">
                        <div class="row">
                            <div style="cursor: pointer;"
                                class="col-1 form-text mt-0 d-flex align-items-center control-text-overflow text-primary">
                                <span>{{ repair.bill_no }}</span>
                            </div>
                            <div class="col-2 form-text mt-0 d-flex align-items-center control-text-overflow">{{
                                repair.model_no }}</div>
                            <div style="width: 100px;"
                                class="col-2 form-text mt-0 d-flex align-items-center control-text-overflow">{{
                                    repair.serial_no }}</div>
                            <div class="col-2 form-text mt-0 d-flex align-items-center control-text-overflow">{{
                                repair.fault }}</div>
                            <div class="col-2 form-text mt-0 d-flex align-items-center control-text-overflow">{{
                                searchCustomer(repair.customer)["phone"] }} ({{
                                    searchCustomer(repair.customer)["name"] }})</div>
                            <div class="col-1 form-text mt-0 d-flex align-items-center control-text-overflow"
                                style="width: 200px;"><span :class="'badge bg-' + getStatus(repair.status)">{{
                                    repair.status }}</span></div>

                            <div style="width: 100px;"
                                class="col-2 form-text mt-0 d-flex align-items-center control-text-overflow">
                                {{ searchTechnician(repair.techie)["fname"] }}</div>

                            <div class="col-1 form-text mt-0 d-flex align-items-center control-text-overflow"
                                style="width: 50px;" v-if="bulkInvoiceList.includes(repair.bill_no)"><i
                                    class="fa-solid fa-print"></i></div>
                        </div>
                        <div class="context_menu" :id="'order_wrap_' + repair.bill_no" style="display: none;">
                            <ul>
                                <li><a href="javascript:void(0)" @click="printInvoice(repair.invoice)">Open Invoice</a>
                                </li>
                                <li><a href="javascript:void(0)"
                                        @click="openWhatsapp(searchCustomer(repair.customer)['phone'], repair.invoice)">Send
                                        Invoice on WhatsApp</a></li>
                                <li @click="finishOrder('show', repair.bill_no)"
                                    v-if="repair.status == 'Pending' || repair.status == 'Awaiting Parts'"><a
                                        href="javascript:void(0)">Update Order Status</a></li>
                                <li v-if="repair.status == 'Pending'"><a href="javascript:void(0)"
                                    @click="selectForUpdate(repair.bill_no)">Select For Bulk Update</a></li>
                                <li v-if="repair.status == 'Return'"><a href="javascript:void(0)"
                                        @click="selectProduct(repair.bill_no)">Checkout Order</a></li>
                                <li v-if="repair.status == 'Repaired' || repair.status == 'Customer Pending'"><a
                                        href="javascript:void(0)" @click="selectProduct(repair.bill_no)">Checkout
                                        Order</a></li>

                                <!-- <li v-if="repair.status == 'Delivered'"><a href="javascript:void(0)">Re-service</a></li> -->
                                <li v-if="repair.status == 'Pending'"><a href="javascript:void(0)"
                                        @click="bulkInvoiceSelect(repair.bill_no)">{{
                                            bulkInvoiceList.includes(repair.bill_no) ? 'Remove From' : 'Select For' }} Bulk
                                        Invoicing</a></li>

                                <li v-if="bulkInvoiceList.length > 0"><a href="javascript:void(0)"
                                        @click="bulkInvoicePrint()">Print all selected invoice</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div v-if="repairs.length == 0" class="col-12 text-center form-text mt-5">No repairs found.</div>
            </div>
        </div>

        <div id="rightMenu" class="order">
            <div class="head">
                <h2>New Order</h2> <button @click="reloadPOS()"
                    class="primary-btn border-only submit-btn">Clear</button>
            </div>

            <div class="total bg-grey">
                <div class="row row-cols-2">
                    <div class="col">
                        Warranty
                    </div>
                    <div class="col">
                        <select name="" ref="order_warranty">
                            <option value="0">No warranty</option>
                            <option value="3">3 Months</option>
                            <option value="6">6 Months</option>
                            <option value="12">1 Years</option>
                        </select>
                    </div>
                </div>
                <div class="row row-cols-2">
                    <div class="col">
                        Delivery
                    </div>
                    <div class="col">
                        <input type="number" ref="order_delivery" value="0"
                            @keyup="$event.key == 'Enter' ? $refs.cashin.select() : updateOrder()"
                            @focus="$event.target.select();">
                    </div>
                </div>

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
                                    <label for="" class="mb-1">Bill Type</label>
                                    <select ref="bill_type" name="" class="" @change="changeBillType">
                                        <option value="new-order">New Order</option>
                                        <option value="re-service">Re-service</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mt-3" v-if="!new_bill">
                                <div class="input">
                                    <label for="" class="mb-1">Old Bill No</label>
                                    <input ref="parent_bill_no" type="text" placeholder="Old Bill No" value="">
                                </div>
                            </div>

                            <div class="col-6 mt-3" v-if="new_bill">
                                <div class="input">
                                    <label for="" class="mb-1">Model No</label>
                                    <input ref="model_no" type="text" placeholder="Model No" value="">
                                </div>
                            </div>

                            <div class="col-6 mt-3" v-if="new_bill">
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
                                    <label for="" class="mb-1">QTY</label>
                                    <input ref="new_order_qty" type="text" placeholder="Quantity" value="1">
                                </div>
                            </div>

                            <div class="col-6 mt-3" v-if="new_bill">
                                <div class="input">
                                    <label for="" class="mb-1">Customer</label>
                                    <select ref="customer" name="" class="select2-multiple">
                                        <option value=""></option>
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
                                <div class="col-12 mb-3">
                                    <div class="input">
                                        <label for="" class="mb-1">Partner</label>
                                        <select ref="partner" name="" class="select2-multiple">
                                            <option value=""></option>
                                            <option v-for="part in partners" :value="part.id">{{ part.company }}
                                                ({{
                                                    part.phone }})</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="input mt-3">
                                    <label for="" class="mb-1">Note</label>
                                    <textarea name="" ref="note" rows="4" placeholder="Note"></textarea>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <button :disabled="isDisabled" @click="getCashierModel('show')"
                                    class="primary-btn submit-btn">Save</button>
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

    <div class="modal fade" id="getCashier" tabindex="-1" role="dialog" aria-labelledby="getCashier" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body d-flex" style="justify-content: center;">
                    <div class="product-wrp mt-1">
                        <div class="row justify-content-between">

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Cashier No</label>
                                    <input ref="cashier_no" type="text" placeholder="Cashier No" value="">
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <button :disabled="isDisabled" @click="PlaceOrder()"
                                    class="primary-btn submit-btn">Save</button>
                                <button @click="getCashierModel('hide')"
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
                                            <select :ref="'finish_spare_' + spare" :name="'finish_spare_' + spare"
                                                class="select2-multiple">
                                                <option value="">-- Select spare --</option>
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
                                        <span @click="spareCountUpdate('+')" class="form-text mt-3"
                                            style="cursor: pointer;">+
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
                                        <option value="Awaiting Parts">Awaiting Parts</option>
                                        <option value="Customer Pending">Customer Pending</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Note</label>
                                    <textarea ref="finish_note" name="" rows="4" placeholder="Note"></textarea>
                                </div>
                            </div>

                            <div class="col-6 mt-3">
                                <div class="input">
                                    <label for="" class="mb-1">Technician</label>
                                    <select ref="techie" name="" class="select2-multiple">
                                        <option value=""></option>
                                        <option v-for="cashier in cashiers" :value="cashier.user_id">{{
                                            cashier.fname }}</option>
                                    </select>
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

    <div class="row m-0 p-3 pending-orders row-gap-5">
        <div class="col-12 fw-bold fs-3 mt-3 text-center">Pending Orders</div>
        <div class="col-2" v-for="order in pendingOrders">
            <div class="technician d-flex gap-3">{{ order['name'] }} <span style="cursor: pointer;"
                    @click="generateInvoice(order['id'], order['name'])"><i class="fa-solid fa-print"></i></span></div>
            <ul>
                <li style="margin: 5px 0;" v-for="invoice in order['repairs']"
                    @click="selectPendingOrder(invoice['id'])"
                    :class="(checkPechdingSelected(invoice['id']) ? 'border' : '') + ' border-success p-1 rounded cursor-pointer'">
                    <a href="javascript:void(0)" @click="printInvoice(invoice['invoice'])">{{ invoice['bill_no'] }} ({{
                        invoice['partner'] > 0 ? searchPartner(invoice['partner'])['name'] : '' }})</a>
                    - <div :class="'badge text-bg-' + (invoice['status'] == 'Pending' ? 'danger' : 'warning')">{{
                        invoice['status'] }}</div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>

import { ref } from 'vue';
import toastr from 'toastr';
import { validateName, checkEmpty, validateCountry, validatePhone, getUrlParam, currency, reformatPhoneNumbers } from '../custom';
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
            partners: [],
            posData: [],
            paymentMod: 'cash',
            spareCount: 1,
            finishOrderNo: 0,
            isDisabled: false,
            selectedCustomer: 0,
            cashiers: [],
            new_bill: true,
            bulkInvoiceList: [],
            pendingOrders: [],
            SelectedPendingOrders: [],
        }
    },
    methods: {
        currency,
        printJS,
        reformatPhoneNumbers,
        openMenu(menuID) {
            $('#' + menuID).toggle();
        },
        loadModal(action) {
            $("#loadingModal").modal(action);
        },
        goTo(location) {
            window.location.href = location;
        },
        newOrder(action) {
            $("#NewOrder").modal(action);

            const myModalEl = document.getElementById('NewOrder')
            myModalEl.addEventListener('shown.bs.modal', event => {
                $('.select2-multiple').select2({
                    dropdownParent: $("#NewOrder"),
                });
            })
        },
        newCustomer(action) {
            $("#NewCustomer").modal(action);
        },
        newSale(action) {
            $("#NewSale").modal(action);

            const myModalEl = document.getElementById('NewSale')
            myModalEl.addEventListener('shown.bs.modal', event => {
                $('.select2-multiple').select2({
                    dropdownParent: $("#NewSale"),
                });
            })
        },
        getCashierModel(action) {
            $("#getCashier").modal(action);
        },
        changeBillType() {
            this.new_bill = this.$refs.bill_type.value == 'new-order' ? true : false
        },
        finishOrder(action, bill_no = 0) {

            if (action == 'hide') {
                $("#FinishOrder").modal(action);
                return 0;
            }

            if (this.selectedRepair.length > 0) {
                this.$refs['finish_bill_no'].Readonly = true;
                this.$refs['finish_note'].style.display = "none";
                this.$refs['finish_total'].value = '0.00';
                this.finishOrderNo = [];

                this.selectedRepair.forEach(element => {
                    this.finishOrderNo.push(element['bill_no']);
                });
            }
            else if (bill_no != 0) {
                this.$refs['finish_bill_no'].value = bill_no;
                this.$refs['finish_note'].value = this.repairs.filter(item => item['bill_no'] == bill_no)[0].note.replace(/\s*<br>\s*/g, "\n");
                this.$refs['finish_total'].value = this.repairs.filter(item => item['bill_no'] == bill_no)[0].total;
                this.finishOrderNo = bill_no;
            }
            else {
                toastr.error('Please select orders to bulk update', "Error");
                return 0;
            }

            $("#FinishOrder").modal(action);

            const myModalEl = document.getElementById('FinishOrder')
            myModalEl.addEventListener('shown.bs.modal', event => {
                $('.select2-multiple').select2({
                    tags: true,
                    dropdownParent: $("#FinishOrder"),
                    createTag: function () {
                        // Disable tagging
                        return null;
                    }
                });
            })
        },
        bulkInvoiceSelect(bill_no) {
            if (this.bulkInvoiceList.includes(bill_no)) {
                const index = this.bulkInvoiceList.indexOf(bill_no);
                if (index > -1) {
                    this.bulkInvoiceList.splice(index, 1);
                }
            }
            else {
                this.bulkInvoiceList.push(bill_no);
            }
        },
        async bulkInvoicePrint() {
            if (this.bulkInvoiceList.length <= 0) {
                toastr.error("Please select invoice to print", "Error");
                return;
            }

            const { data } = await axios.post("/pos/bulk-print", {
                invoice: this.bulkInvoiceList
            });

            if (data.error == 0) {
                printJS(data.url);
                this.bulkInvoiceList = [];
            }
            else {
                toastr.error("Error generating invoice", "Error");
            }
        },
        async getPosData() {
            const { data } = await axios.post("/pos/pos_data");
            this.posData = data;
            if (this.posData.plan > 1) {
                this.getCustomers();
                this.getPartners();
                this.getRepairs();
                this.getSpares();
                this.getCashiers();
                this.getPendingRepairs();
            }
        },
        async getPendingRepairs() {
            const { data } = await axios.post("/pos/get_all_pending_repairs");
            this.pendingOrders = data;
        },
        async getRepairs() {
            this.FilterRepairs()
        },
        async getCustomers() {
            const { data } = await axios.post("/pos/get_customers");
            this.users = data;
        },
        async getPartners() {
            const { data } = await axios.post("/pos/get_partners");
            this.partners = data;
        },
        async getCashiers() {
            const { data } = await axios.post("/pos/get_cashiers");
            this.cashiers = data;
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

                var total = 0;
                var advance = 0;

                this.selectedRepair.forEach(element => {
                    total += element["total"];
                    advance += element["advance"];
                });

                total += parseFloat(this.$refs.order_delivery.value != "" ? this.$refs.order_delivery.value : 0);

                this.$refs["subtotal"].innerText = currency(total, this.posData.currency);
                this.$refs["order_advance"].innerText = currency(advance, this.posData.currency);
                this.$refs["order_total"].innerText = currency((total - advance), this.posData.currency);
                this.$refs["balance"].innerText = currency((total - advance) - this.$refs["cashin"].value, this.posData.currency);
                return;
            }

            this.$refs["subtotal"].innerText = currency("0.00", this.posData.currency);
            this.$refs["order_advance"].innerText = currency("0.00", this.posData.currency);
            this.$refs["order_total"].innerText = currency("0.00", this.posData.currency);
            this.$refs["balance"].innerText = currency("0.00", this.posData.currency);
            this.$refs["order_delivery"].innerText = currency("0.00", this.posData.currency);
        },
        searchProducts(e) {
            var pro = this.proBackup;
            if (this.$refs['searchbar'].value == "") {
                this.repairs = this.proBackup;
            }
            else {
                if (this.$refs.searchType.value == 'repairs') {
                    pro = this.proBackup;
                    this.repairs = pro.filter(item => item['model_no'].toLowerCase().includes(this.$refs['searchbar'].value.toLowerCase()) || item['bill_no'].toLowerCase().includes(this.$refs['searchbar'].value.toLowerCase()));
                }
                else {
                    pro = this.proBackup;
                    var customers = this.users.filter(item => item['name'].toLowerCase().includes(this.$refs['searchbar'].value.toLowerCase()) || item['phone'].toLowerCase().includes(this.$refs['searchbar'].value.toLowerCase()));
                    this.repairs = [];

                    customers.forEach(customer => {
                        const filteredItems = pro.filter(item => item['customer'] == customer.id);
                        this.repairs.push(...filteredItems);
                    });
                }
            }
        },
        searchCustomer(id) {
            var data = this.users.filter(item => item['id'] == id);
            if (data.length > 0) {
                return data[0];
            }

            return { "name": "N/A", "phone": "N/A", "email": "N/A" }
        },
        searchPartner(id) {
            var data = this.partners.filter(item => item['id'] == id);
            if (data.length > 0) {
                return data[0];
            }

            return { "name": "N/A", "phone": "N/A", "email": "N/A" }
        },
        searchTechnician(id) {
            var data = this.cashiers.filter(item => item['id'] == id);
            if (data.length > 0) {
                return data[0];
            }

            return { "name": "N/A", "phone": "N/A", "email": "N/A" }
        },
        removeProduct(pro) {
            this.selectedRepair = this.selectedRepair.filter(function (obj) {
                return obj.bill_no !== pro;
            });

            this.repairs.forEach(element => {
                if (element['bill_no'] == pro) {
                    element["selectStatus"] = "";
                    return false;
                }
            });
            this.updateOrder();
        },
        selectProduct(pro) {
            var newCustomer = 0;

            if (this.selectedRepair.length == 0) {
                newCustomer = true;
            }

            var has = this.selectedRepair.filter(item => item['bill_no'] == pro).length > 0;
            if (has) {
                this.removeProduct(pro);
            }
            else {
                this.repairs.forEach(element => {
                    if (element['bill_no'] == pro) {
                        if (newCustomer == false && element["customer"] != this.selectedCustomer) {
                            toastr.error("Bill No " + element['bill_no'] + " is not from the customer same as other selected bills", "Error");
                        }
                        else {
                            element["selectStatus"] = "active";
                            this.selectedRepair.push(element);
                            if (newCustomer) {
                                this.selectedCustomer = element["customer"];
                            }
                            return false;
                        }
                    }
                });
            }

            this.updateOrder();
        },
        selectForUpdate(pro) {
            var has = this.selectedRepair.filter(item => item['bill_no'] == pro).length > 0;
            if (has) {
                this.removeProduct(pro);
            }
            else {
                this.repairs.forEach(element => {
                    if (element['bill_no'] == pro) {
                        element["selectStatus"] = "active";
                        this.selectedRepair.push(element);
                    }
                });
            }
        },
        reloadPOS() {
            this.selectedRepair = [];

            this.repairs.forEach(element => {
                if (element["selectStatus"] != undefined) {
                    element["selectStatus"] = "";
                }
            });

            this.spareCount = 1;

            this.$refs["cashin"].value = '0';
            this.$refs["total"].innerText = "LKR 0.00";
            this.$refs["order_total"].innerText = "LKR 0.00";
            this.$refs["order_advance"].innerText = "LKR 0.00";
            this.$refs["subtotal"].innerText = "LKR 0.00";
            this.$refs["balance"].innerText = "LKR 0.00";
            this.$refs["order_delivery"].value = "LKR 0.00";
            this.$refs["order_warranty"].value = "0";
            this.paymentMethod("cash");
        },
        get_total() {

            var price = 0.00;

            if (this.selectedRepair.length > 0) {
                this.selectedRepair.forEach(element => {
                    price += (element["total"] - element["advance"])
                });
            }

            price += parseFloat(this.$refs.order_delivery.value != "" ? this.$refs.order_delivery.value : 0);

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
                var order_delivery = this.$refs.order_delivery.value == "" ? 0 : this.$refs.order_delivery.value;
                var order_warranty = this.$refs.order_warranty.value;

                if (parseFloat(cashin) < parseFloat(total) && (payment == "cash")) {
                    if (confirm('Are you sure you want to add remaining as credit?')) {

                    }
                    else {
                        return 0;
                    }
                }

                if (payment == "credit" || payment == "cash") {

                    this.loadModal("show");

                    this.selectedRepair.forEach(element => {
                        repair.push(element['bill_no']);
                    });

                    const { data } = await axios.post('/pos/checkout', {
                        params: {
                            bill_no: repair,
                            cashin: cashin,
                            delivery: order_delivery,
                            warranty: order_warranty,
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
                        //printJS(data.invoiceURL);
                        this.getRepairs();
                        this.reloadPOS();
                        window.open(data.invoiceURL, '_blank');
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
                var techie = this.$refs.techie.value;
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

                if (techie.trim() == "") {
                    toastr.error("Please select technician", "Error");
                    return;
                }

                if (this.spareCount > 0) {
                    for (let i = 1; i <= this.spareCount; i++) {
                        if (this.$refs["finish_spare_" + i][0].value != "") {
                            sparePro.push({
                                id: this.$refs["finish_spare_" + i][0].value,
                                qty: this.$refs["qty_finish_spare_" + i][0].value,
                            });
                        }
                        else {
                            toastr.error("Please select a product for all spares", "Error");
                            return;
                            break;
                        }

                    }
                }
                else if (this.$refs.finish_service_cost != undefined) {
                    service_cost = this.$refs.finish_service_cost.value;
                }

                // for (let i = 1; i < this.spareCount; i++) {
                //     sparePro.push({
                //         id: this.$refs["finish_spare_" + i][0].value,
                //         qty: this.$refs["qty_finish_spare_" + i][0].value,
                //     });
                // }

                this.loadModal("show");

                const { data } = await axios.post('/pos/update', {
                    bill_no: Array.isArray(this.finishOrderNo) ? this.finishOrderNo : [this.finishOrderNo],
                    total: total,
                    note: note,
                    spares: sparePro,
                    service_cost: service_cost,
                    techie: techie,
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
                    $(this.$refs.techie).val("").trigger("change");
                    this.finishOrderNo = 0;
                    this.selectedRepair = [];
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
            var model_no = this.new_bill == true ? this.$refs.model_no.value : '';
            var serial_no = this.new_bill == true ? this.$refs.serial_no.value : '';
            var fault = this.$refs.fault.value;
            var advance = this.$refs.advance.value;
            var note = this.$refs.note.value;
            var note2 = "";
            var customer = this.new_bill == true ? this.$refs.customer.value : '';
            var partner = this.$refs.partner.value;
            var cashier_no = this.$refs.cashier_no.value;
            var bill_type = this.$refs.bill_type.value;
            var parent_bill_no = this.new_bill == false ? this.$refs.parent_bill_no.value : '';
            var new_order_qty = this.$refs.new_order_qty.value;

            if (cashier_no.trim() == "") {
                toastr.error("Please enter cashier code", "Error");
                return;
            }
            else {
                if (this.cashiers.filter(item => item['cashier_code'] == cashier_no).length > 0) {
                    //
                }
                else {
                    toastr.error("Invalid cashier code", "Error");
                    return;
                }
            }

            if (this.new_bill) {
                if (model_no.trim() == "") {
                    toastr.error("Please enter model number", "Error");
                    return;
                }

                if (customer.trim() == "") {
                    toastr.error("Please select customer", "Error");
                    return;
                }
            }
            else {
                if (parent_bill_no.trim() == "") {
                    toastr.error("Please enter old bill number", "Error");
                    return;
                }
            }

            this.isDisabled = true;

            const checkboxes = document.querySelectorAll('input[type="checkbox"]');

            checkboxes.forEach((checkbox, index) => {
                if (checkbox.checked) {
                    if (index == 0) {
                        note2 += 'Has ' + checkbox.value;
                    }
                    else {
                        note2 += '\nHas ' + checkbox.value;
                    }
                }
            });

            note2 += "\n" + note.replace(/\r?\n/g, '\n');

            const { data } = await axios.post('/pos/new_order', {
                total: total,
                model_no: model_no,
                serial_no: serial_no,
                fault: fault,
                advance: advance,
                note: note2,
                customer: customer,
                partner: partner,
                cashier_no: cashier_no,
                bill_type: bill_type,
                parent_bill_no: parent_bill_no,
                new_order_qty: new_order_qty
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

                this.$refs.bill_type.value = "new-order";
                if (!this.new_bill) {
                    this.$refs.parent_bill_no.value = '';
                }
                else {
                    this.$refs.model_no.value = "";
                    this.$refs.serial_no.value = "";
                    this.$refs.customer.value = "";
                    $(this.$refs.customer).val("").trigger("change");
                }
                this.$refs.techie.value = "";
                this.$refs.partner.value = "";
                $(this.$refs.partner).val("").trigger("change");
                this.new_bill = true;
                this.$refs.total.value = "0";
                this.$refs.fault.value = "";
                this.$refs.advance.value = "0";
                this.$refs.new_order_qty.value = "1";
                this.$refs.finish_note.value = "";
                this.$refs.cashier_no.value = "";
                this.newOrder('hide');
                this.getCashierModel('hide');
                this.getRepairs();
                this.reloadPOS();
                this.isDisabled = false;
                if (data.sticker.generated) {
                    printJS(data.sticker.url);
                }
                window.open(data.invoiceURL, '_blank');
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
        async FilterRepairs() {
            var fromDate = this.$refs.ordersFromDate.value;
            var toDate = this.$refs.ordersToDate.value;

            try {
                fromDate = new Date(fromDate);
                toDate = new Date(toDate);

                if (fromDate <= toDate) {
                    const { data } = await axios.post("/pos/get_repairs", {
                        fromDate: fromDate,
                        toDate: toDate
                    });
                    this.repairs = data;
                    this.proBackup = data;
                }
                else {
                    toastr.error("'From date' should be lower or equals to 'To date'", "Error");
                }
            } catch (error) {
                toastr.error(error, "Error");
            }
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

            if (status == "Awaiting Parts") {
                return 'secondary'
            }

            if (status == "Customer Pending") {
                return 'warning'
            }

            if (status == "Delivered") {
                return 'info'
            }
        },
        spareCountUpdate(op) {
            if (op == '-' && this.spareCount >= 1) {
                this.spareCount--;
            }

            if (op == '+') {
                this.spareCount++;
            }

            setTimeout(() => {
                $('.select2-multiple').select2({
                    tags: true,
                    dropdownParent: $("#FinishOrder"),
                    createTag: function () {
                        // Disable tagging
                        return null;
                    }
                });
            }, 100);
        },
        printInvoice(invoice) {
            if (invoice == "") {
                toastr.error("Invoice not found", "Error");
                return;
            }

            if (invoice.includes('Invoice')) {
                printJS("/invoice/" + invoice.replace("Invoice", "Thermal-invoice"));
                return;
            }

            if (invoice.includes('Delivery')) {
                printJS("/invoice/" + invoice.replace("Delivery", "Thermal-delivery"));
                return;
            }

            printJS("/invoice/" + invoice);
        },
        openWhatsapp(number, invoice) {
            var text = "Click on the link to get your PDF invoice copy \nhttps://wefixservers.xyz/invoice/" + invoice;
            window.open('https://wa.me/' + reformatPhoneNumbers(number) + "?text=" + encodeURIComponent(text));
        },
        async filterStatus() {
            this.repairs = this.proBackup;
            if (this.$refs.statusFilter.value == "Delivered") {
                this.repairs = [];
                var fromDate = this.$refs.ordersFromDate.value;
                var toDate = this.$refs.ordersToDate.value;

                try {
                    fromDate = new Date(fromDate);
                    toDate = new Date(toDate);

                    if (fromDate <= toDate) {
                        const { data } = await axios.post("/pos/get_repairs", {
                            fromDate: fromDate,
                            toDate: toDate,
                            status: 'Delivered',
                        });
                        this.repairs = data;
                    }
                    else {
                        toastr.error("'From date' should be lower or equals to 'To date'", "Error");
                    }
                } catch (error) {
                    toastr.error(error, "Error");
                }

                return
            }

            if (this.$refs.statusFilter.value != "") {
                this.repairs = this.repairs.filter(item => item['status'] == this.$refs.statusFilter.value);
            }
        },
        filterPartner() {
            this.repairs = this.proBackup;
            if (this.$refs.partnerFilter.value != "") {
                this.repairs = this.repairs.filter(item => item['partner'] == this.$refs.partnerFilter.value);
            }
        },
        techieFilter() {
            this.repairs = this.proBackup;
            if (this.$refs.techieField.value != "") {
                this.repairs = this.repairs.filter(item => item['techie'] == this.$refs.techieField.value);
            }
        },
        openContext(bill, event) {
            document.querySelectorAll('.context_menu').forEach(element => {
                element.style.display = "none";
            });
            var menu = $('#order_wrap_' + bill);

            let mouseX = event.clientX;
            let mouseY = event.clientY;

            // Get menu dimensions
            $(menu).css('display', 'block');
            $(menu).css('opacity', '0');
            const menuWidth = $(menu).get(0).offsetWidth;
            const menuHeight = $(menu).get(0).offsetHeight;

            // Get viewport dimensions
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            // Adjust position if menu overflows the right or bottom of the screen
            if (mouseX + menuWidth > viewportWidth) {
                mouseX = viewportWidth - menuWidth; // Position it within the screen
            }

            if (mouseY + menuHeight > viewportHeight) {
                mouseY = viewportHeight - menuHeight; // Position it within the screen
            }
            $(menu).css('left', `${mouseX}px`);
            $(menu).css('top', `${mouseY}px`);
            $(menu).css('opacity', '1');
            //document.addEventListener("click", this.closeContextMenu('#order_wrap_' + bill));
        },
        async generateInvoice(id, name = null) {
            var postData = this.SelectedPendingOrders.length > 0 ? this.SelectedPendingOrders : id;

            const { data } = await axios.post('/pos/get_pending_report', {
                id: postData,
                name: name
            });

            if (data.error == 0) {
                printJS(data.report);
                this.SelectedPendingOrders = [];
            }
            else {
                toastr.error(data.message, 'Error');
            }
        },
        checkPechdingSelected(id) {
            var data;
            data = this.SelectedPendingOrders.filter(item => item['id'] == id);
            if (data.length > 0) {
                return true;
            }

            return false;
        },
        selectPendingOrder(pro) {
            var has = this.SelectedPendingOrders.filter(item => item['id'] == pro).length > 0;
            if (has) {
                this.removePendingOrder(pro);
            }
            else {
                this.pendingOrders.forEach(element => {
                    element['repairs'].forEach(element2 => {
                        if (element2['id'] == pro) {
                            this.SelectedPendingOrders.push(element2);
                            return false;
                        }
                    });
                });
            }
        },
        removePendingOrder(pro) {
            this.SelectedPendingOrders = this.SelectedPendingOrders.filter(function (obj) {
                return obj.id !== pro;
            });
        },
    },
    beforeMount() {
        this.getPosData();
        //document.getElementById("searchbar").addEventListener("keyup", this.searchProducts);
    },
    mounted() {
        this.$refs.ordersFromDate.value = new Date().toISOString().substr(0, 10);
        this.$refs.ordersToDate.value = new Date().toISOString().substr(0, 10);

        document.addEventListener('click', (e) => {
            const menus = document.querySelectorAll('.context_menu');
            menus.forEach(menu => menu.style.display = 'none');

            if (window.innerWidth < 1000) {
                if (!$("#leftMenu, #leftMenuToggle").is(e.target) && !$("#leftMenu, #leftMenuToggle").has(e.target).length) {
                    $("#leftMenu").hide();
                }

                if (!$("#rightMenu, #rightMenuToggle").is(e.target) && !$("#rightMenu, #rightMenuToggle").has(e.target).length) {
                    $("#rightMenu").hide();
                }
            }
        });
    }
}
</script>
