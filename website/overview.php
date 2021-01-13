<html>

<?php include 'menu.php';
if($_SESSION['ID'] == "0"){
header("Location: index.php");
}
?>
    <head>
        <title>Overzicht</title>
    </head>

    <body background="">
        <h1>Welcome <?php echo $_SESSION['name']; ?></h1>
        <p>Here are your measurments</p>
        <table>
            <tr>
                <th>Value</th>
                <th>Measured on</th>
            </tr>
            <?php
            $sql = "SELECT Value,CreatedTimestamp FROM Readings WHERE isActive IS TRUE AND UserID = '".$_SESSION['ID']."' ORDER BY CreatedTimestamp DESC";
            $result = $connection->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    ?><tr>
                        <td> <?php echo $row['Value'] ?></td>
                        <td> <?php echo $row['CreatedTimestamp'] ?></td>
                    </tr><?php
                }
            }?>
        </table>

    </body>
</html>
