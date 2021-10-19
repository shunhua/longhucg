<canvas id="doughnut" height="65px"></canvas>
<script>
$(function () {
    var config = {
        type: 'doughnut',
        data: {
            datasets: [{
                data: {!! json_encode($data['data']) !!},
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(150, 200, 200)',
                ],
                label: 'Dataset 1'
            }],
            labels:{!! json_encode($data['labels']) !!}
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            title: {
                display: false,
                text: 'Chart.js Doughnut Chart'
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    };
    var ctx = document.getElementById('doughnut').getContext('2d');
    new Chart(ctx, config);
});
</script>