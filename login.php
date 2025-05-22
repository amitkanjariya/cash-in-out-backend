<?php
// login.php

require_once 'db.php'; // Include the database connection file

$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

if (empty($phone)) {
    http_response_code(400);
    echo "Phone number is required";
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (phone) VALUES (?)");
if ($stmt) {
    $stmt->bind_param("s", $phone);
    if ($stmt->execute()) {
        echo "Success";
    } else {
        http_response_code(500);
        echo "Insert failed: " . $stmt->error;
    }
    $stmt->close();
} else {
    http_response_code(500);
    echo "Prepare failed: " . $conn->error;
}

$conn->close();
?>
