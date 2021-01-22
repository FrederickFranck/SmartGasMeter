<?php

function register($email,$name,$password,$deviceid,$connection){
        if(!empty($email)){
            $sql = "SELECT * FROM Users WHERE IsActive IS TRUE AND Email = '".$email."' ";
            $result = $connection->query($sql);

            if (!$result) {
                trigger_error('Invalid query: ' . $connection->error);
            }
            //Als de user nog niet geregistreert is
            if ($result->num_rows == 0) {

                $sql = "SELECT * FROM DeviceIDs WHERE IsActive IS TRUE AND ID = '".$deviceid."' ";
                $result = $connection->query($sql);

                if (!$result) {
                    trigger_error('Invalid query: ' . $connection->error);
                }
                //Als het deviceid nog niet in gebruik is
                if ($result->num_rows > 0) {
                    $result = $connection->query("SELECT UUID()");
                    $row = $result->fetch_assoc();
                    $newid = $row["UUID()"];
                    $pw = password_hash($password,PASSWORD_BCRYPT);
                    $sql = "INSERT INTO Users (ID, Name , Email , Password , DeviceID) VALUES ('".$newid."','".$name."','".$email."','".$pw."','".$deviceid."')";
                    $reg = $connection->query($sql);


                    $result = $connection->query("SELECT UUID()");
                    $row = $result->fetch_assoc();
                    $settingsid = $row["UUID()"];
                    $sql = "INSERT INTO Settings (ID, UserID) VALUES ('".$settingsid."','".$newid."')";
                    $reg = $connection->query($sql);
                    ?><div class="loginmsg">REGISTERED</div><?php
                }
                else{
                    ?><div class="loginmsg">DEVICE IS ALREADY IN USE</div><?php
                }
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
                header("Location: overview.php");
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

function login_api($email,$password,$connection){
    $emailRegex = '/.*@.*\..+/';

    if(!empty($email)){
        if(preg_match($emailRegex,$email)){
            $sql = "SELECT * FROM Users WHERE IsActive IS TRUE AND Email = '".$email."' ";
        }
    }
    $result = $connection->query($sql);

    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if(password_verify($password,$row['Password'])){
                return $row['ID'];
                //$_SESSION['name'] = $row['Name'];
            }
        }
    }
    else{
        return "401";
    }
}


function is_provider($userid,$connection){
        $sql = "SELECT ID FROM Users WHERE isActive IS TRUE AND ProviderID = '".$userid."'";
        $result = $connection->query($sql);
        if (!$result) {
            trigger_error('Invalid query: ' . $connection->error);
        }
        $clients = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($clients,$row['ID']);
            }
            return $clients;
        }
        else {
            return false;
        }
}

function send_mail($userid,$value,$connection,$warningLevel){
    $sql = "SELECT Name, Email FROM Users WHERE isActive IS TRUE AND ID = '".$userid."'";
    $result = $connection->query($sql);
    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['Email'];
        $name = $row['Name'];
    }

    if($warningLevel == 0){
        $subject = "SmartGasMeter Notification";
        $message = "Your current gaslevel is '".$value."' ";
    }

    elseif($warningLevel == 1){
        $subject = "SmartGasMeter Notification: CAUTION ";
        $message = "Your current gaslevel is '".$value."' this is almost at your warning level ";
    }

    elseif($warningLevel == 2){
        $subject = "SmartGasMeter Notification: WARNING !!";
        $message = "Your current gaslevel is '".$value."' this equal/below your warning level  ";
    }

    elseif($warningLevel == 3){
        $subject = "SmartGasMeter Notification: CALAMITOUS !!!!!!!";
        $message = "Your gaslevel is '".$value."' this might be a hardware error or your tank is empty ";
    }


    $success = mail($email, $subject, $message);


}


function mail_check($userid,$value,$connection){
    $warning_offset = 10;
    $sql = "SELECT EmailNotification, EveryNotification, WarningValue FROM Settings WHERE isActive IS TRUE AND UserID = '".$userid."'";
    $result = $connection->query($sql);

    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $emailNotification = $row['EmailNotification'];
        $everyNotification = $row['EveryNotification'];
        $warningValue = $row['WarningValue'];


        if($emailNotification){
            if($everyNotification){
                //normal
                send_mail($userid,$value,$connection,0);
            }
            elseif($value <= ($warningValue)){
                //warning
                send_mail($userid,$value,$connection,2);
            }
            elseif($value <= ($warningValue + $warning_offset)){
                //caution
                send_mail($userid,$value,$connection,1);
            }
            elseif ($value == 0) {
                //calamitous
                send_mail($userid,$value,$connection,3);
            }
        }
    }

}

function add_reading($deviceid,$value,$connection){

    //kijkt na of er een user bestaat met het meegegeven deviceid
    $sql = "SELECT ID FROM Users WHERE isActive IS TRUE AND DeviceID = '".$deviceid."'";
    $result = $connection->query($sql);

    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        //haalt het id op van de gebruiker
        $userid = $row['ID'];

        //Haal een uniek id op uit de database
        $result = $connection->query("SELECT UUID()");
        $row = $result->fetch_assoc();
        $newid = $row["UUID()"];

        //als de gebruiker bestaat wordt
        //de lezing toegevoegt aan de database
        mail_check($userid,$value,$connection);
        $sql = "INSERT INTO Readings (ID, DeviceID , Value) VALUES ('".$newid."','".$deviceid."','".$value."')";
        $result = $connection->query($sql);
        echo "Succesfully inserted data ";


    }
    else {
        echo "User does not exist or is inActive";
    }


}

function get_name($userid,$connection){
    $sql ="SELECT DISTINCT Name FROM Users WHERE isActive IS TRUE AND ID = '".$userid."'";
    $result = $connection->query($sql);
    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Name'];

    }
}

function get_latest_reading($userid,$connection){
    //haal het device id van de user op
    $sql = "SELECT DeviceID FROM Users WHERE isActive IS TRUE AND ID = '".$userid."'";
    $result = $connection->query($sql);
    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $deviceid = $row['DeviceID'];
    }

    //haal de laastse meting van het device op
    $sql ="SELECT Value,CreatedTimestamp FROM Readings WHERE isActive IS TRUE AND DeviceID = '".$deviceid."' ORDER BY CreatedTimestamp DESC LIMIT 1";
    $result = $connection->query($sql);
    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            return array($row["Value"],$row["CreatedTimestamp"]);
        }
    }
    else {
        echo "userid incorrect";
    }

}

function get_warning_value($userid,$connection){
    $sql = "SELECT WarningValue FROM Settings WHERE isActive IS TRUE AND UserID = '".$userid."'";
    $result = $connection->query($sql);
    if (!$result) {
        trigger_error('Invalid query: ' . $connection->error);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $warningValue = $row['WarningValue'];
        return $warningValue;
    }


}

//zet timestamp uit databank om naar leesbaar formaat zonder tijd
// "2021-01-12 20:06:08" ==> "12/01/2021"
function reformat_short($timestamp){
    $split_timestamp = explode(" ",$timestamp);
    $date = explode("-",$split_timestamp[0]);
    $reformated = $date[2]."/".$date[1]."/".$date[0];
    return $reformated;

}

//zet timestamp uit databank om naar leesbaar formaat met tijd
// "2021-01-12 20:06:08" ==> "20:06 12/01/2021"
function reformat_long($timestamp){
    $split_timestamp = explode(" ",$timestamp);
    $date = explode("-",$split_timestamp[0]);
    $date_r = $date[2]."/".$date[1]."/".$date[0];

    $time = explode(":",$split_timestamp[1]);
    $time_r = $time[0].":".$time[1];

    $reformated = $time_r." ".$date_r;
    return $reformated;

}

function update_email($userid,$email,$connection){
    $sql = "UPDATE Users SET Email = '".$email."',UpdatedTimestamp=now() WHERE isActive IS TRUE AND ID = '".$userid."'";
    $result = $connection->query($sql);
    echo "e-mail Succesfully Updated";

}

function update_name($userid,$name,$connection){
    $sql = "UPDATE Users SET Name = '".$name."',UpdatedTimestamp=now() WHERE isActive IS TRUE AND ID = '".$userid."'";
    $result = $connection->query($sql);
    echo "name Succesfully Updated";

}

function update_password($userid,$password,$connection){
    $pw = password_hash($password,PASSWORD_BCRYPT);
    $sql = "UPDATE Users SET Password = '".$pw."',UpdatedTimestamp=now() WHERE isActive IS TRUE AND ID = '".$userid."'";
    $result = $connection->query($sql);
    echo "password Succesfully Updated";

}

function update_settings($userid,$emailCB,$alwaysCB,$warning,$connection){

    $sql = "UPDATE Settings SET EmailNotification = $emailCB, EveryNotification = $alwaysCB, WarningValue=$warning WHERE isActive IS TRUE AND UserID = '".$userid."'";
    $result = $connection->query($sql);
    echo "Settings Succesfully Updated";
}

?>
