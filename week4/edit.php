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
function validatePos()
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;

        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];

        if (strlen($year) == 0 || strlen($desc) == 0) {
            return "All fields are required";
        }

        if (!is_numeric($year)) {
            return "Position year must be numeric";
        }
    }
    return true;
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
        }elseif (validatePos() != true) {
            $_SESSION['valPos'] = validatePos();
            header("Location: edit.php");
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

            $stmt = $conn->prepare('DELETE FROM Position WHERE profile_id=:pid');
        $stmt->execute(array(':pid' => $_GET["profile_id"]));

        $rank = 1;
        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['year' . $i])) continue;
            if (!isset($_POST['desc' . $i])) continue;

            $year = $_POST['year' . $i];
            $desc = $_POST['desc' . $i];
            $stmt = $conn->prepare('INSERT INTO Position
    (profile_id, rank, year, description)
    VALUES ( :pid, :rank, :year, :desc)');

            $stmt->execute(array(
                    ':pid' => $_GET["profile_id"],
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
            );

            $rank++;

        }

        $_SESSION['success'] = 'Record updated';

            return header("Location: index.php");
        } else {
            return header("Location: edit.php?profile_id=" . $_GET["profile_id"]);
        }
    } else {
        $_SESSION["messageUpdate"] = "All fields are required";
        return header("Location: edit.php?profile_id=" . $_GET["profile_id"]);
    }
}
$stmt1 = $conn->prepare("SELECT * FROM Position where profile_id = :xyz");
$stmt1->execute(array(":xyz" => $_GET['profile_id']));
$rowOfPosition = $stmt1->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>648c26c8</title>
    <link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">

<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>
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
        <p>
            Position: <input type="submit" id="addPos" value="+">
        <div id="position_fields">
            <?php
 
            $rank = 1;
            foreach ($rowOfPosition as $row) {
                echo "<div id=\"position" . $rank . "\">
  <p>Year: <input type=\"text\" name=\"year1\" value=\"". $row['year'] ."\">
  <input type=\"button\" value=\"-\" onclick=\"$('#position". $rank ."').remove();return false;\"></p>
  <textarea name=\"desc". $rank ."\"').\" rows=\"8\" cols=\"80\">". $row['description'] ."</textarea>
</div>";
                $rank++;
            } ?>
        </div>
        <input type="submit" value="Save" name="submit">
        <button><a href="index.php">Cancel</a></button>
        </p>
    </form>
    <p>

    </form>
    <script>
            countPos = 0;

            // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
            $(document).ready(function () {
                window.console && console.log('Document ready called');
                $('#addPos').click(function (event) {
                    // http://api.jquery.com/event.preventdefault/
                    event.preventDefault();
                    if (countPos >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position " + countPos);
                    $('#position_fields').append(
                        '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
                });
            });
        </script>
</body>

</html>