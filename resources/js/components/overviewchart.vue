<template>
    <apexchart height="350" type="area" :options="chartOptions" :series="series"></apexchart>
</template>

<script>

export default {
    props: ['overviewsales'],
    data() {
        return {
            name: 'overviewchart',
            chartOptions: {},
            series: [],
        }
    },
    methods: {
    },
    beforeMount() {
        var temp = this.overviewsales;
        var temp2 = [];
        var temp3 = [];
        temp = Object.keys(temp).map((key) => [key, temp[key]]);
        temp.forEach(element => {
            temp2.push(element[0]);
            temp3.push(element[1]);
        });

        

        this.chartOptions = {
            chart: {
                zoom: {
                    enabled: false
                },
            },
            dataLabels: {
              enabled: false
            },
            xaxis: {
                categories: temp2
            },
        };

        this.series = [
            {
                name: "Sales",
                data: temp3,
            },
        ];
    },
    mounted() {

    }
}
</script>
