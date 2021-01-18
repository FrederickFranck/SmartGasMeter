<html>
 <?php include 'menu.php';  ?>
     <head>
         <title>Settings</title>
     </head>

     <body>
         <br>
         <h1>Update User Info</h1>
         <div id="update-forms" class="wrap">
             <form id="update-email-form" method="post" class="formulieren" target="_self">
                 <div class="logintext">Update e-mail:</div><input type="email" name="email"><br><br>
                 <input type="submit" name="update-email" value="Update">
             </form>

             <form id="update-name-form" method="post" class="formulieren" target="_self">
                 <div class="logintext">Update name:</div><input type="name" name="name"><br><br>
                 <input type="submit" name="update-name" value="Update">
             </form>

             <form id="update-password-form" method="post" class="formulieren" target="_self">
                 <div class="logintext">Update password:</div><input type="password" name="password"><br><br>
                 <input type="submit" name="update-password" value="Update">
             </form>
         </div>
         <br>
         <br>
         <h1>Notification Settings</h1>
         <div id="notification-forms" class="wrap">
             <form id="update-notification-form" method="post" class="formulieren" target="_self">

                 <label for="emailCheckbox" class="logintext">Notify via email</label>
                 <input type="checkbox" name="emailCheckboxname" id="emailCheckbox" value="email-notification">
                 <br><br>
                 <label for="everyreadingCheckbox" class="logintext">Notify me after every reading</label>
                 <input type="checkbox" name="everyreadingCheckboxname" id="everyreadingCheckbox" value="everyreading-notification">
                 <br><br>
                 <div class="logintext">Warning Limit</div>
                 <br>
                 <?php $warning = get_warning_value($_SESSION['ID'],$connection); ?>
                 <input type="range" name="warningLimitName" id="warningLimit" value=<?php echo $warning;?> min="0" max="100" step="5" oninput="warningLimitOutput.value = warningLimit.value">
                 <output name="warningLimitOutputName" id="warningLimitOutput"><?php echo $warning;?></output>
                 <br><br>
                 <input type="submit" name="update-notification" value="Save">
             </form>
     </div>



    <?php
        if(isset($_POST['update-email'])){
            update_email($_SESSION['ID'],$_POST['email'],$connection);
        }
        elseif(isset($_POST['update-name'])){
            update_name($_SESSION['ID'],$_POST['name'],$connection);
        }
        elseif(isset($_POST['update-password'])){
            update_password($_SESSION['ID'],$_POST['password'],$connection);
        }
        elseif(isset($_POST['update-notification'])){
            $email = "False";
            $always = "False";
            if(isset($_POST['emailCheckboxname'])){
                $email = "True";
            }
            if(isset($_POST['everyreadingCheckboxname'])){
                $always = "True";
            }
            update_settings($_SESSION['ID'],$email,$always,$_POST['warningLimitName'],$connection);
        }
    ?>



     </body>
</html>
