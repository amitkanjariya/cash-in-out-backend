<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "cashinout";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "DB connection failed"]));
}

// $dql = "SELECT name, amount, subtitle,time, is_credit FROM customerTile";
// $result = $conn->query($sql);

// $customer = [];

// if($result->num_rows > 0){
//     while($row = $result->fetch_assoc()){
//         $customers[] = $row;
//     }
// }

// header('Content-Type: application/json');
// echo json_encode($customers);

$conn->set_charset("utf8");
?>