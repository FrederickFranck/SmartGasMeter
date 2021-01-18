<?php

include 'dbconnect.php';
include 'functions.php';

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $value = get_latest_reading($id,$connection)[0];
        $date = reformat_short(get_latest_reading($id,$connection)[1]);
        echo $date." : ".$value."%";
    }
    else{
        header("Location: index.php");
    }

?>
