<?php
header('Content-Type: application/json');
require_once 'db.php';

$response = [];

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

if ($user_id <= 0) {
    $response['success'] = false;
    $response['message'] = 'Invalid user_id';
    echo json_encode($response);
    exit;
}

$sql = "
    SELECT 
        t.amount, t.detail, t.type, t.created_at, t.to_id,
        c.name AS contact_name,
        c.phone AS contact_phone
    FROM transactions t
    LEFT JOIN customer_contacts c ON t.to_id = c.id
    WHERE t.user_id = ?
    ORDER BY t.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

$response['success'] = true;
$response['data'] = $transactions;

echo json_encode($response);
