<?php

include 'dbconnect.php';
include 'functions.php';

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $value = get_latest_reading($id,$connection)[0];
        $date = reformat_long(get_latest_reading($id,$connection)[1]);
        $warning = get_warning_value($id,$connection);

        ?>
        <hmtl>
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
            <head>
                <title>Overzicht</title>
            </head>

            <body background="rsc/blue.png">
                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

                <div id="gauge">
                    <p style="color:white">Welcome <?php echo get_name($id,$connection);?> </p>
                    <p style="color:white">Latest measurement on <?php echo $date; ?></p>
                    <div id="gauge-chart"></div>
                </div>


                <script>

                google.charts.load('current', {'packages':['gauge']});
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                      ['Label', 'Value'],
                      ['GasPeil', <?php echo $value?>],
                    ]);

                    var options = {
                      width: 350, height: 400,
                      redFrom: 0, redTo: <?php echo $warning;?>,
                      yellowFrom:<?php echo $warning;?>, yellowTo: <?php echo ($warning + 10);?>,
                      minorTicks: 5
                    };
                    var chart = new google.visualization.Gauge(document.getElementById('gauge-chart'));
                    chart.draw(data, options);
                }

                </script>

            </body>
        </hmtl>





        <?php


    }
    else{
        header("Location: index.php");
    }

?>
