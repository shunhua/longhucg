<canvas id="doughnut" width="200" height="50%"></canvas>
<script>
$(function () {
    var config = {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [
                    {{ $gender['1'] }},
                    {{ isset($gender['2']) ?  $gender['2'] : 0}},
                    {{ isset($gender['3']) ?  $gender['3'] : 0}},
                    {{ isset($gender['4']) ?  $gender['4'] : 0}},
                ],
                backgroundColor: [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)',
                    'rgb(255, 205, 86)',
                    'rgb(255, 75, 86)'
                ]
            }],
            labels: [
                '普通会员',
                '黄金会员',
                '铂金会员',
                '钻石会员'
            ]
        },
        options: {
            maintainAspectRatio: false
        }
    };

    var ctx = document.getElementById('doughnut').getContext('2d');
    new Chart(ctx, config);
});
</script>