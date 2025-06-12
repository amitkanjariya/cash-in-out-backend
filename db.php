<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "cashinout";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "DB connection failed"]));
}

$conn->set_charset("utf8");
?>