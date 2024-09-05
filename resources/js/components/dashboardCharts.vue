<template>
    <div class="col-lg-12">
        <div class="card card-block card-stretch card-height-helf">
            <div class="card-body">
                <div class="d-flex align-items-top justify-content-between">
                    <div class="">
                        <p class="mb-0">Income</p>
                        <h5>{{ currency(totalIncome, "") }}</h5>
                    </div>
                    <div class="">
                        <p class="mb-0 text-end">Expenses</p>
                        <h5>{{ currency(totalExpense, "") }}</h5>
                    </div>
                </div>
                <apexchart height="300" :options="incomeChartOptions" :series="incomeSeries"></apexchart>
            </div>
        </div>
    </div>
</template>

<script>

import { currency } from "../custom";

export default {
    props: ['dashsales', 'expense', 'summary'],
    data() {
        return {
            name: 'dashboarchart',
            montharr: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            incomeChartOptions: {},
            incomeSeries: [],
            totalIncome: 0,
            totalExpense: 0,
            paidOrders: 0,
            creditOrders: 0,
            paidOrderPercent: 0,
            creditOrderPercent: 0,
        }
    },
    methods: {
        currency,
    },
    beforeMount() {

        var temp = this.dashsales;
        var temp3 = [];
        var temp4 = [];
        var expense = this.expense;

        this.montharr.forEach(element => {
            this.totalIncome += temp.hasOwnProperty(element) ? parseFloat(temp[element]) : 0;
            this.totalExpense += expense.hasOwnProperty(element) ? expense[element] : 0;
            temp3.push(temp.hasOwnProperty(element) ? parseFloat(temp[element]).toFixed(2) : 0);
            temp4.push(expense.hasOwnProperty(element) ? parseFloat(expense[element]).toFixed(2) : 0);
        });

        this.incomeChartOptions = {
            colors: ['#32BDEA', '#FF7E41'],
            chart: {
                height: 150,
                type: 'line',
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 12,
                    left: 1,
                    blur: 2,
                    opacity: 0.2
                },
                toolbar: {
                    show: false
                },
                sparkline: {
                    enabled: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            title: {
                text: '',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: this.montharr,
            }
        };

        this.incomeSeries = [
            {
                name: "Income",
                data: temp3
            },
            {
                name: "Expenses",
                data: temp4
            }
        ];
    },
    mounted() {

    }
}
</script>
