<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "flutter_db");

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$phone = $_POST['phone'] ?? '';
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';

$sql = "SELECT * FROM reports WHERE phone = ?";
$params = [$phone];

if (!empty($startDate) && !empty($endDate)) {
    $sql .= " AND date BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();

$result = $stmt->get_result();
$entries = [];
$netBalance = 0;

while ($row = $result->fetch_assoc()) {
    $row['gave'] = $row['gave'] ?: '';
    $row['got'] = $row['got'] ?: '';
    $row['note'] = $row['note'] ?: '';
    $row['balance'] = number_format($row['balance'], 2);
    $entries[] = $row;

    $netBalance = $row['balance'];  
}

echo json_encode([
    'entries' => $entries,
    'net_balance' => number_format($netBalance, 2)
]);

$conn->close();
?>
