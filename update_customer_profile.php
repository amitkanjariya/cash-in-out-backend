<?php
header('Content-Type: application/json');
include 'db.php';

// Use POST variables for text fields
$phone = $_POST['phone'] ?? '';
$name = $_POST['name'] ?? '';
$customer_id = $_POST['customer_id'] ?? '';
$user_id = $_POST['user_id'] ?? '';

if (empty($user_id) || empty($customer_id)) {
    echo json_encode(["success" => false, "message" => "User ID and Customer ID are required"]);
    exit;
}

$profileImageName = null;

// Check if a file was uploaded
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $targetDir = "upload/";
    $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
    $profileImageName = "img_customer_" . $phone . "." . $extension;
    $targetFile = $targetDir . $profileImageName;

    // Move uploaded file
    if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
        echo json_encode(["success" => false, "message" => "Failed to upload image"]);
        exit;
    }
    
    // Also update profile_image if new image is uploaded
    $sql = "UPDATE customer_contacts SET name=?, profile_image=? WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $name, $profileImageName, $customer_id, $user_id);
} else {
    // Update without profile image
    $sql = "UPDATE customer_contacts SET name=? WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $name, $customer_id, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed"]);
}

$stmt->close();
$conn->close();
?>
