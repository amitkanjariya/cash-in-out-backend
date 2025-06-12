<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include 'db.php';

$transaction_id = $_POST['transaction_id'] ?? '';
$amount = $_POST['amount'] ?? '';
$detail = $_POST['detail'] ?? '';
$created_at = $_POST['created_at'] ?? '';

if (empty($transaction_id)) {
    echo json_encode(["success" => false, "message" => "Transaction ID is required"]);
    exit;
}

$fields = [];
$params = [];
$types = "";

if (!empty($amount)) {
    $fields[] = "amount=?";
    $params[] = (float)$amount;
    $types .= "d";
}
if (!empty($detail)) {
    $fields[] = "detail=?";
    $params[] = $detail;
    $types .= "s";
}
if (!empty($created_at)) {
    $fields[] = "created_at=?";
    $params[] = $created_at;
    $types .= "s";
}

if (empty($fields)) {
    echo json_encode(["success" => false, "message" => "No fields to update"]);
    exit;
}

$setClause = implode(", ", $fields);
$sql = "UPDATE transactions SET $setClause WHERE id=?";
$params[] = (int)$transaction_id;
$types .= "i";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Transaction updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
