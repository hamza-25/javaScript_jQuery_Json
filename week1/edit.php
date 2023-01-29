<?php
require_once "pdo.php";
session_start();
if (!isset($_SESSION['name'])) {
    die("Not logged in");
}
if (empty($_GET["profile_id"])) {
    $_SESSION['id'] = "Bad value for profile";
    return header("Location: index.php");
}
$sql = ('SELECT * FROM misc.profile WHERE profile_id = :id');
$stmt = $conn->prepare($sql);
$stmt->execute([":id" => $_GET["profile_id"]]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    $_SESSION['norow'] = "Bad value for profile";
    return header("Location: index.php");
}
if (isset($_POST["submit"])) {
    if (strlen($_POST['first_name']) > 0 && strlen($_POST['last_name']) > 0 && strlen($_POST['email']) > 0 && strlen($_POST['headline']) > 0 && strlen($_POST['summary']) > 0) {

        $_SESSION["errors"] = [];
        // if (!is_numeric($_POST['year'])) {
        //     $_SESSION["errors"][] = "Year must be an integer";
        // }
        // if (!is_numeric($_POST['mileage'])) {
        //     $_SESSION["errors"][] = "mileage must be an integer";
        // }
        if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
            $_SESSION["errors"][]='must be an email';
        }
        if (count($_SESSION["errors"]) == 0) {
            $sql = ("UPDATE misc.profile SET first_name = :fn , last_name = :ln , email = :em , headline = :hd , summary = :sm  WHERE profile_id = :pId");
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ":pId" => $_GET["profile_id"],
                ":fn" => $_POST['first_name'],
                ":ln" => $_POST['last_name'],
                ":em" => $_POST['email'],
                ":hd" => $_POST['headline'],
                ":sm" => $_POST['summary']
            ]);
            $_SESSION["update"] = "Profile updated";
            return header("Location: index.php");
        } else {
            return header("Location: edit.php?profile_id=" . $_GET["profile_id"]);
        }
    } else {
        $_SESSION["messageUpdate"] = "All fields are required";
        return header("Location: edit.php?profile_id=" . $_GET["profile_id"]);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>85a7680f</title>
</head>

<body>
    <p style="color: red;">
        <?php
        if (isset($_SESSION['errors'])) {
            foreach ($_SESSION['errors'] as $value) {
                echo $value . "<br>";
            }
            unset($_SESSION['errors']);
        }
        if (isset($_SESSION['messageUpdate'])) {

            echo $_SESSION['messageUpdate'] . "<br>";

            unset($_SESSION['messageUpdate']);
        }

        ?>
    </p>
    <form method="post">
        first name : <input type="text" name="first_name" value="<?= $row['first_name']?>"><br>
        last name : <input type="text" name="last_name" value="<?= $row['last_name']?>"><br>
        email : <input type="text" name="email" value="<?= $row['email']?>"><br>
        headline : <input type="text" name="headline" value="<?= $row['headline']?>"><br>
        Summary : <textarea name="summary" id="" cols="30" rows="10"><?= $row['summary']?></textarea><br>
        <input type="submit" value="Save" name="submit">
        <button><a href="index.php">Cancel</a></button>

    </form>
</body>

</html>