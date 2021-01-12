<html>
    <?php
        include 'menu.php';
        ini_set('display_errors', 1);
    	ini_set('display_startup_errors', 1);
    	error_reporting(E_ALL);
        ob_start();
        $_SESSION['ID'] = "0";
        $_SESSION['name'] = "";
        header("Location: index.php");
    ?>

    <head><title>LOGOUT</title></head>

    <body>
    </body>
</html>
