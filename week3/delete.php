<?php
require_once "pdo.php";
session_start();
if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
}
if (empty($_GET["profile_id"])) {
    $_SESSION['id'] = "Bad value for profile";
    return header("Location: index.php");
} else {
    $sql = ('SELECT * FROM misc.profile WHERE profile_id = :id');
    $stmt = $conn->prepare($sql);
    $stmt->execute([":id" => $_GET["profile_id"]]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST["delete"]) && isset($_POST["id"])) 
{
    $sql = ('SELECT * FROM misc.profile WHERE profile_id = :id');
    $stmt = $conn->prepare($sql);
    $stmt->execute([":id" => $_POST["id"]]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $sql = ('DELETE FROM misc.profile WHERE profile_id = :id  ');
        $stmt = $conn->prepare($sql);
        $stmt->execute([":id" => $_POST["id"]]);
        $_SESSION['delete'] = "Profile deleted";
        return header("Location: index.php");
    } else {
        $_SESSION['id'] = "Bad value for id";
        return header("Location: index.php");
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
    <h1>Deleting Profile</h1>
    first name : <?= $row['first_name'] ?><br>
    last name : <?= $row['last_name'] ?><br>
    <form action="" method="post">
        <input type="hidden" value="<?= $row['profile_id'] ?>" name="id">
        <input type="submit" value="Delete" name="delete" onclick="alert('are you sure want to delete')">
    </form>
    <a href="index.php">Cancel</a>
</body>

</html>