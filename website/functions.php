<?php

function register($email,$name,$password,$connection){
        if(!empty($email)){
            $sql = "SELECT * FROM Users WHERE IsActive IS TRUE AND Email = '".$email."' ";
            $result = $connection->query($sql);

            if (!$result) {
                trigger_error('Invalid query: ' . $connection->error);
            }
            if ($result->num_rows == 0) {
                $result = $connection->query("SELECT UUID()");
                $row = $result->fetch_assoc();
                $id = $row["UUID()"];
                $pw = password_hash($password,PASSWORD_BCRYPT);

                $sql = "INSERT INTO Users (ID, Name , Email , Password) VALUES ('".$id."','".$name."','".$email."','".$pw."')";
                $reg = $connection->query($sql);
                ?><div class="loginmsg">REGISTERED</div><?php
            }
            else{
                ?><div class="loginmsg">USER ALREADY REGISTERED</div><?php
            }
        }
    }

function login($email,$password,$connection){
    $emailRegex = '/.*@.*\..+/';

    if(!empty($email)){
        if(preg_match($emailRegex,$email)){
            $sql = "SELECT * FROM Users WHERE IsActive IS TRUE AND Email = '".$email."' ";
        }
        else{
            $sql = "SELECT * FROM Users WHERE IsActive IS TRUE AND Name = '".$email."' ";
        }
    }
    $result = $connection->query($sql);

    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if(password_verify($password,$row['Password'])){
                $_SESSION['ID'] = $row['ID'];
                $_SESSION['name'] = $row['Name'];
                ?><div class="loginmsg">USER IS LOGGED IN WITH ID <?php echo $_SESSION['ID'];
                header("Location: index.php");
            }
            else {
                ?><div class="loginmsg">WRONG PASSWORD <?php echo $_SESSION['ID'];?></div><?php
            }
        }
    }
    else {
        ?><div class="loginmsg">USER ACCOUNT DOESN'T EXIST <?php echo $_SESSION['ID'];?></div><?php
    }
}

function add_reading($userid,$value,$connection){

    //kijkt na of er een user bestaat met het meegegeven id
    $sql = "SELECT ID FROM Users WHERE isActive IS TRUE AND ID = '".$userid."'";
    $result = $connection->query($sql);

    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        //Haal een uniek id op uit de database
        $result = $connection->query("SELECT UUID()");
        $row = $result->fetch_assoc();
        $id = $row["UUID()"];

        //als de gebruiker bestaat wordt
        //de lezing toegevoegt aan de database
        $sql = "INSERT INTO Readings (ID, UserID , Value) VALUES ('".$id."','".$userid."','".$value."')";
        $result = $connection->query($sql);
        echo "Succesfully inserted data ";

    }
    else {
        echo "User does not exist or is inActive";
    }


}

function test($payload,$connection){
    $sql = "INSERT INTO TEST (Payload) VALUES ('".$payload."')";
    $result = $connection->query($sql);
    echo "Succesfully inserted data ";

}

function get_readings($id,$connection){

}

?>
