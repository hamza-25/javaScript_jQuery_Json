<?php 
session_start();
require_once "pdo.php";
if(isset($_POST["submit"])){
    $salt = "XyZzy12*_";
    $email = $_POST['email'];
    $userPss = md5($salt . $_POST['pass']);
    //$sql = "select user_id from users where email = :em and password  :pss ";
    $stmt = $conn->prepare("select *  from users where email = :em and password = :pss ");
   $stmt->execute([":em" => $email , ":pss" => $userPss]);
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   var_dump($row);
    //$storedPss = md5($salt . "php123");
    if(strlen($email) > 0 && strlen($userPss) > 0){
        $_SESSION["errors"] = [];
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $_SESSION["errors"][]='must be an email';
        }
        if(!$row){
            $_SESSION["errors"][]='Incorrect password';
        }
        if(count( $_SESSION["errors"])==0 && $row ){
            $_SESSION['email']= $email;
            $_SESSION['user_id']= $row['user_id'];
            $_SESSION['name'] = $row['name'];
            return header("Location: index.php");
        }else{
            return header("Location: login.php");
        }
    }else{
        $_SESSION['message'] = 'User name and password are required' ;
       return header("Location: login.php");
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
    <h1>Please Log In</h1>
    <p style="color: red;">
    <?php
    if(isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    if(isset( $_SESSION["errors"])){
        foreach( $_SESSION["errors"] as $value){
            echo $value . '<br>';
        }
        unset( $_SESSION["errors"] );
       
    }
    ?>
    </p>
    <form action="" method="post">
        email <input type="text" name="email"><br>
        password <input type="password" name="pass" id="id_1723"><br>
        <input type="submit" value="Log In" name="submit" onclick="return doValidate();">
        <a href="index.php">Cancel</a>
    </form>
    <p>For a password hint, view source and find a password hint in the HTML comments.</p>
    <script src="validate.js">

    </script>
</body>
</html>