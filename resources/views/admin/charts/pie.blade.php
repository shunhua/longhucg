<canvas id="myChartPie" height="65px"></canvas>

<script>

$(function () {
    window.chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    };
    var ctx = document.getElementById("myChartPie").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($data['labels']) !!},
            datasets: [{
                data: {!! json_encode($data['data']) !!},
                backgroundColor: [
                    window.chartColors.red,
                    window.chartColors.orange,
                    window.chartColors.yellow,
                    window.chartColors.green,
                    window.chartColors.blue,
                    window.chartColors.purple,
                    'rgb(255, 99, 132)',
                    'rgb(200, 20, 200)',
                    'rgb(139, 101, 8)',
                    window.chartColors.grey
                ],
            }]
        },
    }); 
});
</script>