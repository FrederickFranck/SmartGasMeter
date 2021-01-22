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
        <h1>Welcome <?php echo get_name($_SESSION['ID'],$connection); ?></h1>
        <div id="buttons">
            <button onclick="showGauge()">Gauge</button>
            <button onclick="showHistory()">History</button>
            <?php
            $clients = is_provider($_SESSION['ID'],$connection);
            if($clients != false){
                ?><button onclick="showClients()">Show Clients</button><?php
            }
            ?>
        </div>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>


        <?php
        $sql ="SELECT * FROM Readings WHERE isActive IS TRUE AND DeviceID = (SELECT DeviceID FROM Users WHERE isActive IS TRUE AND ID = '".$_SESSION['ID']."' )";
        $result = $connection->query($sql);
        if (!$result) {
            trigger_error('Invalid query: ' . $connection->error);
        }
        if ($result->num_rows > 0) { ?>

        <div id="gauge">
            <p>Latest measurement on <?php echo reformat_long(get_latest_reading($_SESSION['ID'],$connection)[1]) ?></p>
            <div id="gauge-chart" style="width: 400px; height: 120px;"></div>
        </div>

        <div id="history" class="chart-container" style="position: relative;height:73vh; width:98.5vw;display:none">
            <p>Measurements graph</p>
            <canvas id="GasLevel"></canvas>
        </div>

        <div id="clients" style="display:none">
            <p>Here are your clients latest measurements</p>
            <table>
            <tr>
                <th>Name</th>
                <th>Value</th>
                <th>Measured on</th>
            </tr>
            <?php
            if($clients != false){
                foreach ($clients as $id) {
                    ?><tr>
                        <td> <?php echo get_name($id,$connection) ?></td>
                        <td> <?php echo get_latest_reading($id,$connection)[0]; ?></td>
                        <td> <?php echo reformat_long(get_latest_reading($id,$connection)[1]); ?></td>
                    </tr><?php
                }
            }?>
        </table>



        </div>

        <?php }?>





        <script>
        //Hide/unhide
        function showGauge(){
            document.getElementById('gauge').style.display = '';
            document.getElementById('history').style.display = 'none';
            document.getElementById('clients').style.display = 'none';
        }

        function showHistory(){
            document.getElementById('gauge').style.display = 'none';
            document.getElementById('history').style.display = '';
            document.getElementById('clients').style.display = 'none';
        }

        function showClients(){
            document.getElementById('gauge').style.display = 'none';
            document.getElementById('history').style.display = 'none';
            document.getElementById('clients').style.display = '';
        }

        //History Chart
        <?php
        $sql ="SELECT Value,CreatedTimestamp FROM Readings WHERE isActive IS TRUE AND DeviceID = (SELECT DeviceID FROM Users WHERE isActive IS TRUE AND ID = '".$_SESSION['ID']."' ) ORDER BY CreatedTimestamp";
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
                        echo "20 ,";
                    }
                    ?>
                ],
                label:"Alert",
                borderColor:"#FF0000",
                backgroundColor:"#FF0000",
                fill:false
            },{
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
        <?php
        $value = get_latest_reading($_SESSION['ID'],$connection)[0];
        $warning = get_warning_value($_SESSION['ID'],$connection);
        ?>

        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Label', 'Value'],
              ['GasPeil', <?php echo $value?>],
            ]);

            var options = {
              width: 900, height: 600,
              redFrom: 0, redTo: <?php echo $warning;?>,
              yellowFrom:<?php echo $warning;?>, yellowTo: <?php echo ($warning + 10);?>,
              minorTicks: 5
            };
            var chart = new google.visualization.Gauge(document.getElementById('gauge-chart'));
            chart.draw(data, options);
        }

        </script>
    </body>
</html>
