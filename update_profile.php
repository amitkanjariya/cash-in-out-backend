<?php
header('Content-Type: application/json');
include 'db.php';

// Use POST variables for text fields
$phone = $_POST['phone'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$gender = $_POST['gender'] ?? '';
$address = $_POST['address'] ?? '';
$state = $_POST['state'] ?? '';
$city = $_POST['city'] ?? '';
$dob = $_POST['dob'] ?? '';

if (empty($phone)) {
    echo json_encode(["success" => false, "message" => "Phone number is required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email address"]);
    exit;
}

$profileImageName = null;

// Check if a file was uploaded
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $targetDir = "upload/";
    $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
    $profileImageName = uniqid("IMG_", true) . "." . $extension;
    $targetFile = $targetDir . $profileImageName;

    // Move uploaded file
    if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
        echo json_encode(["success" => false, "message" => "Failed to upload image"]);
        exit;
    }
    
    // Also update profile_image if new image is uploaded
    $sql = "UPDATE users SET name=?, email=?, gender=?, address=?, state=?, city=?, dob=?, profile_image=? WHERE phone=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $name, $email, $gender, $address, $state, $city, $dob, $profileImageName, $phone);
} else {
    // Update without profile image
    $sql = "UPDATE users SET name=?, email=?, gender=?, address=?, state=?, city=?, dob=? WHERE phone=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $name, $email, $gender, $address, $state, $city, $dob, $phone);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Profile updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed"]);
}

$stmt->close();
$conn->close();
?>
