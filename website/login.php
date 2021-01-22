<html>

<?php include 'menu.php'; ?>
    <head>
        <title>Login Page</title>
    </head>
    <body background="">
        <h1>Welkom op de loginpagina</h1>

        <div class="wrap">
            <form id="loginform" method="post" class="formulieren" target="_self">
                <div class="logintext">e-mail:</div><input type="email" name="email"><br><br>
                <div class="logintext">password:</div><input type="password" name="password"><br><br>
                <input type="submit" name="btnLogin" value="login">
            </form>

            <form id="registerform" method="post" class="formulieren" target="_self">
                <div class="logintext">e-mail:</div><input type="email" name="email"><br><br>
                <div class="logintext">name:</div><input type="name" name="name"><br><br>
                <div class="logintext">password:</div><input type="password" name="password"><br><br>
                <div class="logintext">device id:</div><input type="name" name="deviceid"><br><br>
                <input type="submit" name="btnRegister" value="register">
            </form>

        </div>
        <?php
        if(isset($_POST['btnLogin'])){
            login($_POST['email'],$_POST['password'],$connection);
        }
        else if(isset($_POST['btnRegister'])) {
            register($_POST['email'],$_POST['name'],$_POST['password'],$connection);
        }
        else {
            ?><div class="text"></div><?php
        }?>
    </body>
</hmtl>
