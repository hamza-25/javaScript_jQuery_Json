<?php
session_start();

if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

require_once "pdo.php";


if (
    isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
    && isset($_POST['headline']) && isset($_POST['summary'])
) {
    if (
        strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 ||
        strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1
    ) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;
    } else {
        $stmt = $conn->prepare('INSERT INTO misc.profile (user_id,first_name, last_name, email, headline, summary) VALUES (:user_id, :first_name, :last_name, :email, :headline,:summary)');
        $stmt->execute(
            array(
                ':user_id' => $_SESSION['user_id'],
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':email' => $_POST['email'],
                ':headline' => $_POST['headline'],
                ':summary' => $_POST['summary']
            )
        );

        $profile_id = $conn->lastInsertId();

        $rank = 1;
        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['year' . $i])) continue;
            if (!isset($_POST['desc' . $i])) continue;

            $year = $_POST['year' . $i];
            $desc = $_POST['desc' . $i];
            if (is_numeric($year)) {
                $stmt = $conn->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
                $stmt->execute(
                    array(
                        ':pid' => $_REQUEST['profile_id'],
                        ':rank' => $rank,
                        ':year' => $year,
                        ':desc' => $desc
                    )
                );

                $rank++;
            } else {
                $_SESSION['year'] = "year field must be a number";
                return header("Location: add.php");
                exit;
            }
        }

        $rank++;


        $rank = 1;
        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['edu_year' . $i])) continue;
            if (!isset($_POST['edu_school' . $i])) continue;

            $edu_year = $_POST['edu_year' . $i];
            $edu_school = $_POST['edu_school' . $i];

            $stmt = $conn->prepare("SELECT * FROM Institution where name = :xyz");
            $stmt->execute(array(":xyz" => $edu_school));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $institution_id = $row['institution_id'];
            } else {
                $stmt = $conn->prepare('INSERT INTO Institution (name) VALUES ( :name)');

                $stmt->execute(array(
                    ':name' => $edu_school,
                ));
                $institution_id = $conn->lastInsertId();
            }

            $stmt = $conn->prepare('INSERT INTO Education
    (profile_id, institution_id, year, rank)
    VALUES ( :pid, :institution, :edu_year, :rank)');


            $stmt->execute(array(
                    ':pid' => $profile_id,
                    ':institution' => $institution_id,
                    ':edu_year' => $edu_year,
                    ':rank' => $rank)
            );

            $rank++;

        }


        $_SESSION['success'] = "Profile added";
        header("Location: index.php");
        return;
    }
} else {
    $_SESSION['fail'] = "All values are required";
} ?>
<!DOCTYPE html>
<html>

<head>
    <title>648c26c8</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <h1>Adding Profile for <?= $_SESSION['name'] ?></h1>
        <?php
        // if (isset($_SESSION['fail'])) {
        //     echo('<p style="color: red;">' . htmlentities($_SESSION['fail']) . "</p>\n");
        //     unset($_SESSION['fail']);
        // }
        if (isset($_SESSION['error'])) {
            echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
            unset($_SESSION['error']);
        }
        if (isset($_SESSION["year"])) {
            echo $_SESSION["year"] . "<br>";
            unset($_SESSION["year"]);
        }
        ?>
        <form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60" />
            </p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" />
            </p>
            <p>Email:
                <input type="text" name="email" size="30" />
            </p>
            <p>Headline:<br />
                <input type="text" name="headline" size="80" />
            </p>
            <p>Summary:<br />
                <textarea name="summary" rows="8" cols="80"></textarea>
            <p>
                Education: <input type="submit" id="addEdu" value="+">
            <div id="education_fields">
                Position: <input type="submit" id="addPos" value="+">
                <div id="position_fields">
                    <input type="submit" value="Add">
                    <button><a href="index.php">Cancel</a></button>
                    </p>
        </form>
        <script>
            countPos = 0;
            countEdu = 0;

            $(document).ready(function() {
                window.console && console.log('Document ready called');
                $('#addPos').click(function(event) {
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
                $('#addEdu').click(function(event) {
                    event.preventDefault();
                    if (countEdu >= 9) {
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }
                    countEdu++;
                    window.console && console.log("Adding position " + countEdu);
                    $('#education_fields').prepend(
                        '<div id="education' + countEdu + '"> \
            <p>Year: <input type="text" name="edu_year' + countEdu + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#education' + countEdu + '\').remove();return false;"></p> \
            school: <input name="edu_school' + countEdu + '" class="school" >\
            </div>');
                });
                $(".school").autocomplete({
                    source: "school.php"
                });
             
            

                
                

            });
        </script>
    </div>
</body>

</html>