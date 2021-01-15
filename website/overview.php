<html>

<?php include 'menu.php';
if($_SESSION['ID'] == "0"){
    header("Location: index.php");
}
?>
    <head>
        <title>Overzicht</title>
    </head>

    <body background="">
        <h1>Welcome <?php echo $_SESSION['name']; ?></h1>
        <div id="buttons">
            <button onclick="showGauge()">Gauge</button>
            <button onclick="showHistory()">History</button>
        </div>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

        <div id="gauge">
            <p>Latest measurement on <?php echo reformat_long(get_latest_reading($_SESSION['ID'],$connection)[1]) ?></p>
            <div id="gauge-chart" style="width: 400px; height: 120px;"></div>
        </div>

        <div id="history" class="chart-container" style="position: relative;height:73vh; width:98.5vw;display:none">
            <p>Measurements graph</p>
            <canvas id="GasLevel"></canvas>
        </div>


        <script>
        //Hide/unhide
        function showGauge(){
            document.getElementById('gauge').style.display = '';
            document.getElementById('history').style.display = 'none';
        }

        function showHistory(){
            document.getElementById('gauge').style.display = 'none';
            document.getElementById('history').style.display = '';
        }

        //History Chart
        <?php
        $sql ="SELECT Value,CreatedTimestamp FROM Readings WHERE isActive IS TRUE AND UserID = '".$_SESSION['ID']."' ORDER BY CreatedTimestamp";
        $result = $connection->query($sql);
        if (!$result) {
            trigger_error('Invalid query: ' . $connection->error);
        }

        $values = array();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $values[reformat_long($row['CreatedTimestamp'])] = $row['Value'];
            }
        }
        ?>
        var ctx = document.getElementById('GasLevel');
        ctx.style.backgroundColor = 'rgba(255,255,255,10)';
        var options = {
            responsive: true,
            maintainAspectRatio:false,
            scales:{
            yAxes: [{
                display: true,
                ticks: {
                    beginAtZero: true,
                    min: 0,
                    max: 100,
                    stepSize: 10
            }
            }]
        }};

        data1 = {
            labels:[
                <?php
                foreach ($values as $key => $value) {
                    echo "'".$key."' ,";
                }
                ?>
            ],
            datasets:[{
                data:[
                    <?php
                    foreach ($values as $key => $value) {
                        echo "'".$value."' ,";
                    }
                    ?>
                ],
                label:"Value",
                borderColor:"#00FF00",
                backgroundColor:"#00FF00",
                fill:false
            }]
        };

        var chartInstance = new Chart(ctx,{
            type: 'line',
            data: data1,
            options: options
        });


        //Live gauge
        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Label', 'Value'],
              ['GasPeil', <?php echo get_latest_reading($_SESSION['ID'],$connection)[0];?>],
            ]);

            var options = {
              width: 900, height: 600,
              redFrom: 0, redTo: 20,
              yellowFrom:20, yellowTo: 30,
              minorTicks: 5
            };
            var chart = new google.visualization.Gauge(document.getElementById('gauge-chart'));
            chart.draw(data, options);
        }

        </script>
    </body>
</html>
