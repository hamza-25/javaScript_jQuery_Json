<?php
session_start();
require_once 'pdo.php';
$stmt = $conn->prepare("select name from institution where  name like :pre");
$stmt->execute([":pre"=> $_REQUEST["term"] . "%"]);
$ret= [];
while($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
    $ret[] = $row["name"];
}
return (json_encode($ret, JSON_PRETTY_PRINT));