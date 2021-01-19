<?php

include 'dbconnect.php';
include 'functions.php';

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $value = get_latest_reading($id,$connection)[0];
        $date = reformat_short(get_latest_reading($id,$connection)[1]);
        echo $date." : ".$value."%";
    }
    elseif(isset($_POST['email'])){
        $id = $_POST['email'];
        $password = $POST['password'];
        echo login_api($id,$password,$connection);

    }

    else{
        header("Location: index.php");
    }

?>
