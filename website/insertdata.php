<?php

include 'dbconnect.php';
include 'functions.php';

    if(isset($_POST['key'])){
        if($_POST['key'] == "frederick"){
            $userid = $_POST['userid'];
            $value = $_POST['value'];

            add_reading($userid,$value,$connection);
        }
    }
    else{
        header("Location: index.php");
    }

?>
