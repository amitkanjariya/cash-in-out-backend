<?php
// get_customer.php

$data = jason_decode(file_get_contents("php://input"));

$name = $data->name;
$amount = $data->amount;
$subtitle = $data->subtitle;
$time = $data->time;
$isCredit =$data->isCredit;

$sql = "INSERT INTO customer_tile(name, amount, subtitle, time, isCredit) VALUES ('$name', '$amount', '$subtitle', '$time', '$isCredit')";

if($conn->query($sql) === TRUE){
    echo json_encode(["message" => "Customer added successfully"]);
}else{
    echo json_encode(["error" => $conn->error]);
}

?>