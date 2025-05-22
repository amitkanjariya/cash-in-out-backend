<?php
header('Content-Type: application/json');
include 'db.php';

$phone = $_POST['phone'] ?? '';

if (empty($phone)) {
    echo json_encode(["success" => false, "message" => "Phone number is required"]);
    exit;
}

$sql = "SELECT name, email, gender, address, state, city, dob, profile_image FROM users WHERE phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Append full URL for profile image if it exists
    if (!empty($user['profile_image'])) {
        $user['profile_image'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/api/upload/' . $user['profile_image'];
    }

    echo json_encode(["success" => true, "data" => $user]);
} else {
    echo json_encode(["success" => false, "message" => "User not found"]);
}
$stmt->close();
$conn->close();
?>
