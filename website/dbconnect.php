<?php
$servername = "localhost";
$username = "GasAdmin";
$password = "***";
$dbname = "GasMeter";
//connect to database

$connection = mysqli_connect($servername,$username,$password,$dbname);
if(!$connection){
    die("Connection failed " . mysqli_connect_error());
}
?>
