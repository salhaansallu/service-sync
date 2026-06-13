<template>
    <div class="pos-wrap bg-grey">
        <div class="products">
            <div class="searchbar">
                <div class="input">
                    <input type="text" ref="searchbar" placeholder="Search here" value=""
                        @keyup="this.searchProducts($event)">
                </div>
            </div>
            <div class="product-wrap">
                <div class="product add-product" @click="ProductModal('show')">
                    <div class="img">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div class="name text-center">Add product</div>
                </div>
                <div :class="'product ' + pro['status']" v-for="pro in products" :key="productSelectionKey(pro)"
                    :ref="productSelectionKey(pro)">
                    <div class="img" @click="selectProduct(productSelectionKey(pro))">
                        <img :src="productImage(pro)" alt="">
                    </div>
                    <div class="name" @click="selectProduct(productSelectionKey(pro))">{{ formatProductLabel(pro) }}</div>
                    <div class="sku" @click="selectProduct(productSelectionKey(pro))">Code: {{ pro['sku'] }} | QTY: {{ pro['qty'] }}</div>
                    <div class="price" @click="selectProduct(productSelectionKey(pro))">{{ currency(pro['price'], posData.currency) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="order">
            <div class="head">
                <h2>New Order</h2> <button @click="reloadPOS()"
                    class="primary-btn border-only submit-btn">Clear</button>
            </div>

            <div class="customers mx-2">
                <div class="customer-picker">
                    <select name="" ref="customer" class="select2-multiple">
                        <option value="">Select Customer</option>
                        <option v-if="posData.plan == 1" value="">-- Upgrade to premium --</option>
                        <option :value="user['id']" v-for="user in users">{{ user['name'] }} ({{ user['phone'] }})</option>
                    </select>
                    <button type="button" class="btn btn-link customer-add-btn" @click="toggleCustomerForm()">
                        {{ showCustomerForm ? 'Cancel' : 'Add Customer' }}
                    </button>
                </div>

                <div v-if="showCustomerForm" class="customer-form">
                    <input type="text" ref="cus_name" placeholder="Customer name">
                    <input type="text" ref="cus_mobile" placeholder="Phone number">
                    <input type="email" ref="cus_email" placeholder="Email (optional)">
                    <input type="text" ref="cus_address" placeholder="Address (optional)">
                    <button type="button" class="primary-btn submit-btn" @click="createCustomer()">Save customer</button>
                </div>
            </div>

            <div id="order-wrap" class="order-wrap" ref="order_wrap">
                <div class="orders" v-for="order in selectedProduct" :key="order['selection_key']">
                    <div class="row m-0">
                        <div class="col col-10">
                            <div class="orderlist">
                                <img :src="productImage(order)" alt="">
                                <div class="dtl">
                                    <div class="name">{{ formatProductLabel(order) }}</div>
                                    <div class="price"><input type="text" class="price border-0 w-100 text-primary" @change="productPriceChange($event, order['selection_key'])" style="background: none; outline: none;" :value="currency(parseFloat(order['price']), '')"></div>
                                    <!-- <div class="price" v-if="order['discount'] == 0">{{ currency(parseFloat(order['price']) * parseFloat(order['qty']), posData.currency) }}</div>
                                    <div class="price" v-if="order['discount'] > 0">{{ currency(parseFloat(order['discounted_price']) * parseFloat(order['qty']), posData.currency) }}</div> -->
                                </div>
                                <div class="qty">
                                    <i class="fa-solid fa-minus" @click="updateQTY(order['selection_key'], '-')"></i>
                                    <input type="number" :ref="'qty' + order['selection_key']" value="1"
                                        @focus="$event.target.select();" @keyup="directUpdateQty($event, order['selection_key'])">
                                    <i class="fa-solid fa-plus" @click="updateQTY(order['selection_key'], '+')"></i>
                                </div>
                            </div>

                            <!-- <div class="discount">
                                Discount <span><input type="number" :ref="'dis' + order['sku']"
                                        @keyup="discount(order['sku'])" value="0" @focus="$event.target.select();"></span>
                                <span>
                                    <select name="" :ref="'dis_mod' + order['sku']" @change="discount(order['sku'])">
                                        <option value="am">Amount</option>
                                        <option value="%">%</option>
                                    </select>
                                </span>
                            </div> -->
                        </div>
                        <div class="col col-2">
                            <i class="fa-solid fa-xmark" @click="removeProduct(order['selection_key'])"></i>
                        </div>
                    </div>
                </div>
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

                <div class="row row-cols-2" v-if="selectedCustomerDue > 0">
                    <div class="col">
                        Credit Due
                    </div>
                    <div class="col">
                        {{ currency(selectedCustomerDue, posData.currency) }}
                    </div>
                </div>
                <!-- <div class="row row-cols-2">
                    <div class="col col-6">
                        Service charge
                    </div>
                    <div class="col">
                        <input type="number" ref="service" value="0" @keyup="Enter($event, 'roundup')" @focus="$event.target.select();">
                    </div>
                </div> -->

                <!-- <div class="row row-cols-2" style="border-bottom: #e9e9e9 3px dotted;">
                    <div class="col">
                        Round up
                    </div>
                    <div class="col">
                        <input type="number" ref="roundup" value="0" @keyup="Enter($event, 'cashin')" @focus="$event.target.select();">
                    </div>
                </div> -->

                <div class="row row-cols-2 order-total">
                    <div class="col">
                        Total
                    </div>
                    <div class="col" ref="total">
                        LKR 0.00
                    </div>
                </div>

                <div class="row row-cols-2">
                    <div class="col">
                        Cash
                    </div>
                    <div class="col">
                        <input type="number" ref="cashin" value="0" @keyup="updateProductDetails(true)"
                            @focus="$event.target.select();">
                    </div>
                </div>

                <div class="row row-cols-2">
                    <div class="col">
                        Sale Type
                    </div>
                    <div class="col">
                        <select class="order_type" ref="sale_type" name="" id="">
                            <option value="instore">In Store</option>
                            <option value="online">Online</option>
                        </select>
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
                        <button class="primary-btn submit-btn" :disabled="isDisabled"
                            @click="proceed()">Checkout</button>
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

    <div class="modal fade" id="addProduct" tabindex="-1" role="dialog" aria-labelledby="addProductLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductLabel">Add temporary product</h5>
                </div>
                <div class="modal-body">
                    <form action="" class="product_form" onsubmit="return false;">
                        <!-- <label for="pro_img">
                            <i class="fa-solid fa-upload"></i><br>
                            <i>Click here to Upload product image</i>
                            <input type="file" name="" id="pro_img" class="d-none">
                        </label> -->

                        <div class="row row-cols-2 mt-4">
                            <div class="col">
                                <div class="input">
                                    <div class="label">Username</div>
                                    <input type="text" placeholder="Username" ref="pro_name"
                                        @keyup="Enter($event, 'price')">
                                </div>
                            </div>

                            <div class="col">
                                <div class="input">
                                    <div class="label">Product price</div>
                                    <input type="number" placeholder="Product price" value="0" ref="price"
                                        @keyup="Enter($event, 'cost')">
                                </div>
                            </div>

                            <div class="col mt-3">
                                <div class="input">
                                    <div class="label">Password</div>
                                    <input type="password" placeholder="Password" value="0" ref="cost"
                                        @keyup="$event.key == 'Enter' ? createProduct() : ''">
                                </div>
                            </div>

                        </div>
                        <div class="btns">
                            <button type="button" class="btn btn-secondary" @click="ProductModal('hide')">Close</button>
                            <button type="button" class="primary-btn submit-btn" @click="createProduct()">Save
                                changes</button>
                        </div>
                    </form>
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

export default {
    props: ['app_url', 'third_party_token'],
    data() {
        return {
            name: 'pos',
            products: [],
            tempProducts: [],
            savedOrders: [],
            savedOrdersBackup: [],
            categories: [],
            favourits: [],
            proBackup: [],
            selectedProduct: [],
            selectedCategory: [],
            users: [],
            posData: [],
            paymentMod: 'cash',
            keybuffer: [],
            timeHandler: 1,
            isDisabled: false,
            showCustomerForm: false,
            selectedCustomerId: 0,
        }
    },
    computed: {
        selectedCustomerDue() {
            if (!this.selectedCustomerId) {
                return 0;
            }

            const customer = this.users.find(item => item.id == this.selectedCustomerId);
            return customer && customer.due_balance ? parseFloat(customer.due_balance) : 0;
        },
    },
    methods: {
        currency,
        printJS,
        productSelectionKey(product) {
            return (product['source'] || 'local') + ':' + (product['id'] ?? product['sku']);
        },
        formatProductLabel(product) {
            return product['pro_name'] + (product['source'] === '3rd_party' ? ' (Fix AI)' : '');
        },
        loadModal(action) {
            $("#loadingModal").modal(action);
        },
        productImage(product) {
            if (product['pro_image'] && /^https?:\/\//.test(product['pro_image'])) {
                return product['pro_image'];
            }

            return '/assets/images/products/' + (product['pro_image'] || 'placeholder.svg');
        },
        async getPosData() {
            const { data } = await axios.post("/pos/pos_data");
            this.posData = data;
            if (this.posData.plan > 1) {
                this.getProducts();
                this.getCustomers();
            }
        },
        async getProducts() {
            const { data } = await axios.post("/pos/get_spares");
            this.products = data;

            try {
                const token = this.third_party_token;
                if (token) {
                    const { data: fixaiproducts } = await axios.get("https://fixai.wefixservers.xyz/api/products", {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    });

                    if (fixaiproducts.error == 0) {
                        fixaiproducts.data.forEach(pro => {
                            pro['source'] = '3rd_party';
                            pro['id'] = 'fixai_temp_' + pro['id'];
                            pro['pro_image'] = pro['pro_image'] || 'placeholder.svg';
                            this.products.push(pro);
                        });
                    }
                }
            } catch (error) {
                console.error('Failed to load third party products', error);
            }

            this.proBackup = [...this.products];
            this.tempProducts = this.proBackup
        },
        async getCustomers() {
            const { data } = await axios.post("/pos/get_customers");
            this.users = data;
            this.$nextTick(() => {
                this.initCustomerSelect();
            });
        },
        initCustomerSelect() {
            const customerSelect = $(this.$refs.customer);
            if (customerSelect.hasClass('select2-hidden-accessible')) {
                customerSelect.select2('destroy');
            }

            customerSelect.select2({
                dropdownParent: $(".customers"),
            });

            customerSelect.off('change.salePosCustomer').on('change.salePosCustomer', () => {
                this.selectedCustomerId = customerSelect.val() || 0;
            });
        },
        toggleCustomerForm() {
            this.showCustomerForm = !this.showCustomerForm;

            if (!this.showCustomerForm) {
                this.resetCustomerForm();
            }
        },
        resetCustomerForm() {
            if (this.$refs.cus_name) this.$refs.cus_name.value = "";
            if (this.$refs.cus_mobile) this.$refs.cus_mobile.value = "";
            if (this.$refs.cus_email) this.$refs.cus_email.value = "";
            if (this.$refs.cus_address) this.$refs.cus_address.value = "";
        },
        async createCustomer() {
            var name = this.$refs.cus_name.value;
            var mobile = this.$refs.cus_mobile.value;
            var email = this.$refs.cus_email.value;
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
                address: address,
                email: email
            }).catch(() => {
                toastr.error("Sorry something went wrong. Please try again", "Error");
                return { data: null };
            });

            if (!data) {
                return;
            }

            if (data.error == "0") {
                await this.getCustomers();
                const customer = this.users.find(item => item['phone'] == mobile && item['name'] == name);

                this.showCustomerForm = false;
                this.resetCustomerForm();

                if (customer) {
                    this.$refs.customer.value = customer['id'];
                    $(this.$refs.customer).val(customer['id']).trigger('change');
                    this.selectedCustomerId = customer['id'];
                }

                toastr.success(data.msg, "Success");
            }
            else {
                toastr.error(data.msg, "Error");
            }
        },
        Enter(e, target) {
            if (e.key == 'Enter') {
                this.$refs[target].select();
                this.updateProductDetails()
            }
        },
        productPriceChange(e, sku) {
            var newPrice = e.target.value;

            if (!isNaN(newPrice) && !isNaN(parseFloat(newPrice))) {
                this.updateOrder(sku, 'price', parseFloat(newPrice))
                this.updateProductDetails();
            }
            else {
                toastr.error('Invalid price format', 'Error');
            }
        },
        discount(sku) {
            // var mod = this.$refs['dis_mod' + sku][0].value;
            // var price = this.$refs['dis' + sku][0].value;
            // if (price == "") {
            //     price = 0;
            // }

            // this.selectedProduct.forEach(element => {
            //     if (element['sku'] == sku) {
            //         if (mod == 'am') {
            //             this.updateOrder(sku, "discount", parseFloat(price));
            //             this.updateOrder(sku, "discount_mod", mod);
            //             this.updateOrder(sku, "discounted_price", (parseFloat(element['price']) - parseFloat(price)));
            //         }
            //         if (mod == '%') {
            //             this.updateOrder(sku, "discount", parseFloat(price));
            //             this.updateOrder(sku, "discount_mod", mod);
            //             this.updateOrder(sku, "discounted_price", parseFloat(element['price'] - ((parseFloat(price) / 100) * element['price'])).toFixed(2));
            //         }
            //     }
            // });
        },
        updateOrder(sku, key, val) {
            this.selectedProduct.forEach(element => {
                if (element['selection_key'] == sku) {
                    element[key] = val;
                    return false;
                }
            });

            this.updateProductDetails();

        },
        searchProducts(e) {
            var pro = this.tempProducts;
            if (this.$refs['searchbar'].value == "") {
                this.products = this.tempProducts;
            }
            else if (e.key == "Enter") {
                pro = this.proBackup;
                var singlePro = pro.filter(item => item['sku'] == this.$refs['searchbar'].value);
                if (singlePro.length == 1) {
                    this.selectProduct(this.productSelectionKey(singlePro[0]));
                }
            }
            else {
                pro = this.tempProducts;
                this.products = pro.filter(item =>
                    item['pro_name'].toLowerCase().includes(this.$refs['searchbar'].value.toLowerCase()) ||
                    item['sku'].toLowerCase().includes(this.$refs['searchbar'].value.toLowerCase()) ||
                    this.formatProductLabel(item).toLowerCase().includes(this.$refs['searchbar'].value.toLowerCase())
                );
            }
        },
        removeProduct(pro) {
            this.selectedProduct = this.selectedProduct.filter(function (obj) {
                return obj.selection_key !== pro;
            });

            this.products.forEach(element => {
                if (this.productSelectionKey(element) == pro) {
                    element["status"] = "";
                    return false;
                }
            });
            this.updateProductDetails();
        },
        selectProduct(pro) {
            var has = this.selectedProduct.filter(item => item['selection_key'] == pro).length > 0;

            if (has) {
                this.removeProduct(pro);
            }
            else {
                this.products.forEach(element => {
                    if (this.productSelectionKey(element) == pro) {
                        var temp_pro = [];
                        element["status"] = "active";
                        temp_pro['pro_name'] = element['pro_name'];
                        temp_pro['pro_image'] = element['pro_image'];
                        temp_pro['id'] = element['id'];
                        temp_pro['sku'] = element['sku'];
                        temp_pro['price'] = element['price'];
                        temp_pro['cost'] = element['cost'];
                        temp_pro['source'] = element['source'] || 'local';
                        temp_pro['selection_key'] = this.productSelectionKey(element);
                        temp_pro['qty'] = 1;
                        temp_pro['discount'] = 0;
                        temp_pro['discounted_price'] = 0;
                        temp_pro['discount_mod'] = "am";

                        this.selectedProduct.push(temp_pro);
                        return false;
                    }
                });

                $("#order-wrap").animate({ scrollTop: $("#order-wrap").height() }, 500);
            }
        },
        directUpdateQty(e, sku) {
            var val = e.target.value;
            if (e.key == "Enter") {
                if (val == "") {
                    this.updateOrder(sku, 'qty', 0)
                    val = 0;
                    this.$refs.cashin.select();
                    return true;
                }
                this.updateOrder(sku, 'qty', e.target.value)
                this.$refs.cashin.select();
                return true;
            }
        },
        updateQTY(sku, oper) {
            var qtyinput = this.$refs['qty' + sku][0].value;
            if ((oper == '+' && qtyinput > 0) || (oper == '-' && qtyinput > 1)) {
                var qty = 0;
                if (oper == '+') {
                    qty = parseFloat(qtyinput) + 1;
                    this.$refs['qty' + sku][0].value = qty;
                }
                else if (oper == '-') {
                    qty = parseFloat(qtyinput) - 1;
                    this.$refs['qty' + sku][0].value = qty;
                }
                this.updateOrder(sku, 'qty', qty);
            }
        },
        reloadPOS() {
            this.selectedProduct = [];
            this.$refs.customer.value = '';
            $(this.$refs.customer).val('').trigger('change');
            this.selectedCustomerId = 0;
            this.showCustomerForm = false;
            this.resetCustomerForm();

            this.products.forEach(element => {
                element['status'] = '';
            });

            this.initCustomerSelect();

            // var today = new Date();
            // var dd = String(today.getDate()).padStart(2, '0');
            // var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            // var yyyy = today.getFullYear();
            //today = mm + '/' + dd + '/' + yyyy;

            //document.getElementById("posdate").value = today;
            this.$refs["cashin"].value = '0';
            //this.$refs["roundup"].value = '0';
            //this.$refs["service"].value = '0';
            this.$refs["total"].innerText = "LKR 0.00";
            this.$refs["subtotal"].innerText = "LKR 0.00";
            this.$refs["balance"].innerText = "LKR 0.00";
            this.paymentMethod("cash");
        },
        ProductModal(action) {
            $("#addProduct").modal(action);
        },
        createProduct() {
            var pro_name = this.$refs['pro_name'].value;
            var price = this.$refs['price'].value;
            var cost = this.$refs['cost'].value;
            if (!checkEmpty(pro_name) && !checkEmpty(price) && !checkEmpty(cost)) {
                var temp_pro = [];
                var rand = Math.floor(Math.random() * 1000);
                temp_pro['id'] = 'id-'+rand;
                temp_pro['pro_name'] = pro_name;
                temp_pro['pro_image'] = 'placeholder.svg';
                temp_pro['sku'] = "temp-"+rand;
                temp_pro['price'] = parseFloat(price);
                temp_pro['cost'] = parseFloat(cost);
                temp_pro['source'] = 'temp';
                temp_pro['selection_key'] = this.productSelectionKey(temp_pro);
                temp_pro['qty'] = 1;
                temp_pro['discount'] = 0;
                temp_pro['discounted_price'] = 0;
                temp_pro['discount_mod'] = 'am';
                this.selectedProduct.push(temp_pro);
                this.$refs['pro_name'].value = "";
                this.$refs['price'].value = "";
                this.$refs['cost'].value = "";
                this.ProductModal('hide');
            }
            else {
                toastr.error("All fields are required", "Error");
            }
        },
        get_total(final = false) {

            var price = 0.00;
            if (final) {
                var proprice = this.get_total();
                var service = this.$refs['service'].value == "" ? 0 : parseFloat(this.$refs['service'].value);
                var roundup = this.$refs['roundup'].value == "" ? 0 : parseFloat(this.$refs['roundup'].value);
                price = parseFloat((service + proprice) - roundup);
            }
            else {
                this.selectedProduct.forEach(product => {
                    if (product['discount'] == 0) {
                        price += (parseFloat(product['price']) * parseFloat(product['qty']));
                    }
                    else {
                        price += (parseFloat(product['discounted_price']) * parseFloat(product['qty']));
                    }
                });
            }
            return parseFloat(price);
        },
        updateProductDetails(totalOly = false) {
            if (totalOly == false) {
                this.selectedProduct.forEach(ele => {
                    var qty = this.$refs['qty' + ele['selection_key']][0];
                    //var dis = this.$refs['dis' + ele['sku']][0];
                    //var dis_mod = this.$refs['dis_mod' + ele['sku']][0];
                    var dis_mod = 'amt';

                    qty.value = ele['qty'];
                    //dis.value = ele['discount'];
                    //dis_mod.value = ele['discount_mod'];
                });
            }

            var price = this.get_total();
            var cashin = this.$refs['cashin'].value == "" ? 0 : parseFloat(this.$refs['cashin'].value);
            //var service = this.$refs['service'].value == "" ? 0 : parseFloat(this.$refs['service'].value);
            var service = 0;
            //var roundup = this.$refs['roundup'].value == "" ? 0 : parseFloat(this.$refs['roundup'].value);
            var roundup = 0;
            var total = (service + price) - roundup;

            this.$refs['total'].innerText = currency(total, this.posData.currency);

            this.$refs['subtotal'].innerText = this.currency(price, this.posData.currency);
            this.$refs['balance'].innerText = this.currency((cashin - total), this.posData.currency);
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

            if (Array.isArray(this.selectedProduct) && this.selectedProduct.length > 0) {
                var customer = this.$refs.customer.value == "" && this.users.filter(item => item['id'] == this.$refs.customer.value).length == 0 ? 0 : this.$refs.customer.value;
                //var service = this.$refs.service.value == "" ? 0 : this.$refs.service.value;
                var total = this.get_total();
                var payment = this.paymentMod;
                var cashin = this.$refs.cashin.value == "" ? 0 : this.$refs.cashin.value;
                var sale_type = this.$refs.sale_type.value == "" ? 0 : this.$refs.sale_type.value;
                //var roundup = this.$refs.roundup.value == "" ? 0 : this.$refs.roundup.value;
                var product = [];
                var cost = 0.00;

                if (parseFloat(cashin) < parseFloat(total) && (payment == "cash" || payment == "card")) {
                    toastr.error("Cash recieved is less than the total. If you want to add remaining as credit, select 'Credit' option and a 'Customer'");
                    return 0;
                }

                this.isDisabled = true;

                this.selectedProduct.forEach(arr => {
                    parseFloat(cost += arr['cost'] * arr['qty']);
                    product.push({
                        id: arr['id'] ?? arr['sku'],
                        pro_name: arr['pro_name'],
                        sku: arr['sku'],
                        price: arr['price'],
                        cost: arr['cost'],
                        qty: arr['qty'],
                        source: arr['source'] || 'local',
                    });
                });

                if (payment == "credit" || payment == "card" || payment == "cash") {

                    if (payment == "credit" && customer == 0) {
                        this.loadModal("hide");
                        toastr.error("Please select a customer", "Error");
                        this.isDisabled = false;
                        return 0;
                    }

                    const { data } = await axios.post('/pos/sales/checkout', {
                        params: {
                            products: product,
                            customer: customer,
                            cashin: cashin,
                            sale_type: sale_type,
                        }
                    }).catch(function (error) {
                        if (error.response) {
                            toastr.error("Sorry something went wrong. Please refresh the page and try again", "Error");
                        }
                    });

                    this.isDisabled = false;

                    if (data.error == "0") {
                        this.loadModal("hide");
                        toastr.success(data.msg, "Success");
                        printJS(data.invoiceURL);
                        // let objFra = document.createElement('iframe');
                        // objFra.style.visibility = 'hidden';
                        // objFra.src = '/invoice/' + data.invoice;
                        // document.body.appendChild(objFra);
                        // objFra.contentWindow.focus();
                        // objFra.contentWindow.print();
                        this.reloadPOS();
                        this.isDisabled = false;
                    }
                    else {
                        toastr.error(data.msg, "Error");
                    }
                }
                else {
                    toastr.error("Please select a payment method", "Error");
                }
            }
            else {
                toastr.error("Please select products", "Error");
            }
            this.isDisabled = false;
            setTimeout(() => {
                this.isDisabled = false;
            }, 1000);
        },
        shortcuts(event) {
            if (event.target.nodeName == 'BODY') {

                if (event.key == "S") {
                    this.$refs.searchbar.focus();
                }
                if (event.key == "R") {
                    this.reloadPOS();
                }
                if (event.key == "F") {
                    this.filterFavourits();
                }
                if (event.key == "+") {
                    this.$refs.service.select();
                }
            }
        },
        press(event) {
            if (event.which === 13) {
                this.selectProduct(this.keybuffer.join(""));
                this.keybuffer.length = 0;
                return true;
            }
            var number = event.which - 48;
            if (number < 0 || number > 9) {
                return;
            }

            if (this.timeHandler) {
                clearTimeout(this.timeHandler)
                this.keybuffer.push(number);
            }

            var root = this;
            this.timeHandler = setTimeout(function () {
                if (root.keybuffer.length <= 3) {
                    root.keybuffer.length = 0;
                    return
                }
            }, 50);
        },
    },
    beforeMount() {
        this.getPosData();
        //document.getElementById("searchbar").addEventListener("keyup", this.searchProducts);
    },
    mounted() {
        //this.$refs.searchbar[0].addEventListener("keyup", this.searchProducts);
        const order_wrap = this.$refs.order_wrap;
        const observer = new MutationObserver((mutationsList, observer) => {
            this.updateProductDetails();
        });
        const config = { childList: true, subtree: true, characterData: true };
        observer.observe(order_wrap, config);

        $(document).on("keypress", this.press);
        $(document).on("keyup", this.shortcuts);
        this.initCustomerSelect();
    }
}
</script>

<style scoped>
.product-wrap {
    height: 100%;
    align-content: flex-start
}

.order_type {
    width: 100%;
    outline: none;
    border: 0;
    padding: 3px 0;
}

.products {
    width: 100%;
    height: 100%;
}

.order {
    height: 100%;
}

.customer-picker {
    display: flex;
    align-items: center;
    gap: 10px;
}

.customer-add-btn {
    white-space: nowrap;
    padding: 0;
    text-decoration: none;
}

.customer-form {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-top: 12px;
}

.customer-form input {
    border: 1px solid #d7d7d7;
    border-radius: 6px;
    padding: 8px 10px;
    outline: none;
}

.customer-form button {
    grid-column: 1 / -1;
}

.pos-wrap {
    height: 90vh;
}

@media screen and (max-height: 917px) {
    .pos-wrap {
        height: 100vh;
    }
}

@media screen and (max-width: 768px) {
    .customer-picker {
        flex-direction: column;
        align-items: stretch;
    }

    .customer-form {
        grid-template-columns: 1fr;
    }
}
</style>
