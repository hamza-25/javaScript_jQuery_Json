<?php
session_start();
require_once "pdo.php";
$stmt = $conn->query("select first_name, last_name , headline , profile_id from profile");


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>907754d4</title>
</head>

<body>
    <h1>hamza Resume Registry</h1>
    <!--  show login and logout   -->
    <?php
    //start ---- flash message
    if (isset($_SESSION["insert"])) {
        echo $_SESSION["insert"] . "<br>";
        unset($_SESSION["insert"]);
    }
    if (isset($_SESSION["update"])) {
        echo $_SESSION["update"] . "<br>";
        unset($_SESSION["update"]);
    }
    if (isset($_SESSION["delete"])) {
        echo $_SESSION["delete"] . "<br>";
        unset($_SESSION["delete"]);
    }  
     if (isset($_SESSION["success"])) {
        echo $_SESSION["success"] . "<br>";
        unset($_SESSION["success"]);
    }
    // end ----flash message

    if (!isset($_SESSION["user_id"])) {
    ?>
        <a href="login.php">Please log in</a>
    <?php
    } else {
    ?>
        <p>
            <a href='logout.php'>log out</a>
        </p>
        <p>
            <a href='add.php'>Add New Entry</a>
        </p>


    <?php
    }
    ?>

    <!-- end code show login and logout   -->
    <table border="1px">
        <tr>
            <th>name</th>
            <th>headline</th>
            <th>action</th>
        </tr>
        <?php

        while ($rows = $stmt->fetch()) {

        ?>
            <tr>
                <td><?= $rows['first_name'] . " " . $rows['last_name']; ?></td>
                <td><?= $rows['headline']; ?></td>
                <td>
                    <a href="edit.php?profile_id=<?= $rows['profile_id']; ?>">edit</a>
                    <a href="delete.php?profile_id=<?= $rows['profile_id']; ?>">delete</a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
    <p> <b>Note: </b> Your implementation should retain data across multiple logout/login sessions. This sample implementation clears all its data periodically - which you should not do in your implementation.</p>
</body>

</html>