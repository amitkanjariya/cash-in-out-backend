<?php
include 'db.php';

$user_id = $_POST['user_id'] ?? '';
$customer_id = $_POST['customer_id'] ?? '';

if (empty($user_id) || empty($customer_id)) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

$sql = "SELECT name, phone FROM customer_contacts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $customer_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["success" => true, "data" => $row]);
} else {
    echo json_encode(["success" => false, "message" => "Customer not found"]);
}
?>
