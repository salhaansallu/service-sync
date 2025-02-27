<template>
    <div class="col-12 mt-5">
        <div class="d-flex gap-4 mb-3">
            <span @click="productCountUpdate('+')" class="text-success" style="cursor: pointer;">
                <i class="fa-solid fa-plus"></i>
            </span>
            <span @click="productCountUpdate('-')" class="text-danger" style="cursor: pointer;">
                <i class="fa-solid fa-minus"></i>
            </span>
        </div>

        <table class="table mb-0 tbl-server-info">
            <thead class="bg-white text-uppercase">
                <tr class="ligth ligth-data">
                    <th class="text-start">Product</th>
                    <th class="text-start">Price in <span id="selectedCurrency"></span></th>
                    <th class="text-start">QTY</th>
                </tr>
            </thead>
            <tbody class="ligth-body">
                <tr v-for="(item, index) in productCount" :key="index">
                    <td class="text-start">
                        <select :name="'product_' + index" class="form-control select2" style="width: 300px;">
                            <option v-if="orderitems.length > index && orderitems[index]" :value="orderitems[index].id">
                                {{ orderitems[index].name }}
                            </option>
                            <option value="">-- Select Product --</option>
                            <option v-for="product in products" :value="product.id">
                                {{ product.pro_name }}
                            </option>
                        </select>
                    </td>
                    <td class="text-start">
                        <input type="text" class="form-control" placeholder="Enter Price" :name="'price_' + index"
                            v-model="formData[index].price">
                    </td>
                    <td class="text-start">
                        <input type="text" class="form-control" placeholder="Enter Quantity" :name="'qty_' + index"
                            v-model="formData[index].qty">
                    </td>
                </tr>
            </tbody>
        </table>

        <input type="hidden" name="product_count" :value="productCount">
    </div>
</template>

<script>

import { currency } from "../custom";
import select2 from "select2";

export default {
    props: ['products', 'orderitem'],
    data() {
        return {
            name: 'purchaseForm',
            productCount: 1,
            orderitems: this.orderitem,
            formData: []
        }
    },
    methods: {
        currency,

        productCountUpdate(op) {
            if (op === '+') {
                this.productCount++;
                this.formData.push({ name: "", id: "", price: 0, qty: "" });
            } else if (op === '-' && this.productCount > 1) {
                this.productCount--;
                this.formData.pop();
            }

            setTimeout(() => {
                $('.select2').select2();
            }, 100);
        },

        initializeFormData() {
            try {
                this.orderitems = JSON.parse(this.orderitems);
            } catch (error) { }

            if (!this.orderitems || this.orderitems.length == 0) {
                this.formData = [{ name: "", id: "", price: 0, qty: "" }];
                return;
            }

            this.formData = this.orderitems.map(item => ({
                name: item.name || "",
                id: item.id || "",
                price: item.price || 0,
                qty: item.qty || ""
            }));
        }
    },
    beforeMount() {
        this.initializeFormData();
    },
    mounted() {
        this.productCount = this.orderitems.length == 0 ? 1 : this.orderitems.length;

        $('#selectedCurrency').text($('#currencySelector').val());
        $('#currencySelector').change(function () {
            $('#selectedCurrency').text($('#currencySelector').val());
        });
        $('.select2').select2();
    }
}
</script>
