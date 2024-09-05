<template>
    <apexchart height="350" type="bar" :options="chartOptions" :series="series"></apexchart>
</template>

<script>

export default {
    props: ['sales', 'cost'],
    data() {
        return {
            name: 'revenewChart',
            chartOptions: {},
            series: [],
        }
    },
    methods: {
    },
    beforeMount() {
        var temp = this.sales;
        var temp2 = [];
        var temp3 = [];
        var temp4 = [];
        var cost = this.cost;
        temp = Object.keys(temp).map((key) => [key, temp[key]]);

        temp.forEach(([key, element]) => {
            temp2.push(key);
            temp3.push((parseFloat(element) - parseFloat(cost[key])).toFixed(2));
            temp4.push(cost[key]);
        });

        this.chartOptions = {
            chart: {
                zoom: {
                    enabled: false
                },
            },
            plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
              },
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              show: true,
              width: 2,
              colors: ['transparent']
            },
            xaxis: {
                categories: temp2
            },
        };

        this.series = [
            {
                name: "Profit",
                data: temp3,
            },
            {
                name: "Cost",
                data: temp4,
            },
        ];
    },
    mounted() {

    }
}
</script>
