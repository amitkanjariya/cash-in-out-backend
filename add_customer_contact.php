<?php
header('Content-Type: application/json');
include 'db.php';

// Check required fields
if (!isset($_POST['user_id']) || !isset($_POST['phone']) || !isset($_POST['name'])) {
    echo json_encode(["success" => false, "message" => "Missing user_id, phone, or name"]);
    exit;
}

$user_id = $_POST['user_id'];
$phone = $_POST['phone'];
$name = $_POST['name'];

// Validate phone number
if (!preg_match('/^[0-9]{10}$/', $phone)) {
    echo json_encode(["success" => false, "message" => "Invalid phone number"]);
    exit;
}

// Check if customer already exists
$checkSql = "SELECT id, name FROM customer_contacts WHERE user_id = ? AND phone = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("is", $user_id, $phone);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    $checkStmt->bind_result($existingId, $existingName);
    $checkStmt->fetch();
    $checkStmt->close();

    // Update name if different
    if ($existingName !== $name) {
        $updateSql = "UPDATE customer_contacts SET name = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $name, $existingId);
        if ($updateStmt->execute()) {
            echo json_encode(["success" => true, "message" => "Customer name updated"]);
        } else {
            echo json_encode(["success" => false, "message" => "Update failed: " . $updateStmt->error]);
        }
        $updateStmt->close();
    } else {
        echo json_encode(["success" => true, "message" => "Customer already exists with same name"]);
    }

    $conn->close();
    exit;
}
$checkStmt->close();

// Insert new customer
$insertSql = "INSERT INTO customer_contacts (user_id, phone, name, created_at) VALUES (?, ?, ?, NOW())";
$insertStmt = $conn->prepare($insertSql);
$insertStmt->bind_param("iss", $user_id, $phone, $name);

if ($insertStmt->execute()) {
    echo json_encode(["success" => true, "message" => "Customer added successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Insert failed: " . $insertStmt->error]);
}

$insertStmt->close();
$conn->close();
?>
