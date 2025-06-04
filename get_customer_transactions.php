<?php
header('Content-Type: application/json');
require 'db.php'; // Use existing DB connection

// Validate input
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;

if ($user_id === 0 || $customer_id === 0) {
    echo json_encode(['success' => false, 'message' => 'user_id and customer_id are required']);
    exit;
}

// Fetch transactions where user_id = given user and to_id = given customer
$sql = "SELECT * FROM transactions 
        WHERE user_id = ? AND to_id = ? 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $transactions
]);

$stmt->close();
$conn->close();
?>
