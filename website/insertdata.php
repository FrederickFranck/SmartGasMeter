<?php

include 'dbconnect.php';
include 'functions.php';
$_POST = json_decode(file_get_contents('php://input'), true);

    if(isset($_POST['payload_raw'])){
        $payload = $_POST['payload_raw'];
        $payload = base64_decode($payload);
        $payload_json = json_decode($payload,true);
        add_reading($payload_json['userid'],$payload_json['value'],$connection);
    }
    else{
        header("Location: index.php");
    }

?>
