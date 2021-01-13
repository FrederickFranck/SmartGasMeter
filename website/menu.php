<?php session_start();

    if(!isset($_SESSION['ID'])){
        $_SESSION['ID'] = "0";
    }

    //error messages
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ob_start();

    include 'dbconnect.php';
    include 'functions.php';

?>

<html>

<link rel="stylesheet" type="text/css" href="style.css">
<link rel="shortcut icon" href="rsc/favicon.ico" type="image/x-icon">
<link rel="icon" href="rsc/favicon.ico" type="image/x-icon">

    <div id="navbar">
        <a href="index.php">HOME</a>

        <?php
        if($_SESSION['ID'] == "0"){
            ?><a href="login.php">LOGIN</a><?php
        }
        else {
            ?><a href="logout.php">LOGOUT</a>
            <a href=overview.php>OVERVIEW</a>
            <br>
            <br>
        <?php }
            ?>

    </div>
     <body background="rsc/CNIOT.jpg">
     <br><br>
</html>
