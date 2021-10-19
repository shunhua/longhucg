<div class="form-group">

    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        <div id="{{$id}}" style="width: 500px; height: 200px;">
            <canvas id="myChartUserLine" height="120"></canvas>
        </div>

    </div>
</div>

<script>

$(function () {
    var ctx = document.getElementById("myChartUserLine").getContext('2d');
    var color = Chart.helpers.color;
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($line_labels) !!},
            datasets: [{
                label: '',
                data: {!! json_encode($line_datas) !!},
                backgroundColor: color('rgb(255, 99, 132)').alpha(0.5).rgbString(),
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
        }
    }); 
});
</script>