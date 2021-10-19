<canvas id="myChart" height="120"></canvas>

<script>

$(function () {
   var ctx = document.getElementById("myChart").getContext('2d');
   var myChart = new Chart(ctx, {
       type: 'line',
       data: {
           labels: {!! json_encode($data['labels']) !!},
           datasets: [{
               label: '下单数量',
               data: {{ json_encode($data['datasets'] )}},
               backgroundColor: 'rgba(75, 192, 192, 0.2)',
               borderColor: 'rgba(75, 192, 192, 1)',
               borderWidth: 1
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