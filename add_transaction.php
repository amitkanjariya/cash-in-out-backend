<?php
header('Content-Type: application/json');
require_once 'db.php';

$response = [];

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$detail = isset($_POST['detail']) ? trim($_POST['detail']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
$to_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : null;

if ($user_id <= 0 || $amount <= 0 || !in_array($type, ['plus', 'minus'])) {
    $response['success'] = false;
    $response['message'] = 'Invalid input: user_id, amount, or type missing / incorrect.';
    echo json_encode($response);
    exit;
}

// If customer phone is provided, try to find their contact ID
if (!empty($customer_phone)) {
    $stmt = $conn->prepare("SELECT id FROM customer_contacts WHERE user_id = ? AND phone = ?");
    $stmt->bind_param("is", $user_id, $customer_phone);
    $stmt->execute();
    $stmt->bind_result($found_id);
    if ($stmt->fetch()) {
        $to_id = $found_id;
    }
    $stmt->close();
}

// Prepare the INSERT query
$stmt = $conn->prepare("INSERT INTO transactions (user_id, to_id, amount, detail, type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iisss", $user_id, $to_id, $amount, $detail, $type);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Transaction recorded successfully.';
} else {
    $response['success'] = false;
    $response['message'] = 'Failed to record transaction.';
}

$stmt->close();
$conn->close();

echo json_encode($response);
