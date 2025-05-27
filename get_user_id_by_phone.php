<?php
header('Content-Type: application/json');
include 'db.php';

// Validate input
if (!isset($_POST['phone'])) {
    echo json_encode(["success" => false, "message" => "Phone number is required"]);
    exit;
}

$phone = $_POST['phone'];

// Optional: validate 10-digit phone number format
if (!preg_match('/^[0-9]{10}$/', $phone)) {
    echo json_encode(["success" => false, "message" => "Invalid phone format"]);
    exit;
}

// Query to find user ID
$stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found"]);
} else {
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "user_id" => $row['id']
    ]);
}

$stmt->close();
$conn->close();
?>
