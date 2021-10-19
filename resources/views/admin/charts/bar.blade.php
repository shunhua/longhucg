<canvas id="myChartBar" height="120"></canvas>

<script>

$(function () {
   var ctx = document.getElementById("myChartBar").getContext('2d');
   var color = Chart.helpers.color;
   var myChart = new Chart(ctx, {
       type: 'bar',
       data: {
           labels: {!! json_encode($data['labels']) !!},
           datasets: [{
               label: '支出',
               backgroundColor: color('rgb(255, 99, 132)').alpha(0.5).rgbString(),
               borderColor: 'rgb(255, 99, 132)',
               borderWidth: 1,
               data: {!! json_encode($data['datasets']['decrease'] ) !!},
           }, {
                label: '收入',
                backgroundColor: color('rgb(54, 162, 235)').alpha(0.5).rgbString(),
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1,
                data: {!! json_encode($data['datasets']['increase'] ) !!}
           }]
       },
       options: {
           scales: {
               yAxes: [{
                   ticks: {
                       beginAtZero:true
                   }
               }]
           }
       }
   }); 
});
</script>