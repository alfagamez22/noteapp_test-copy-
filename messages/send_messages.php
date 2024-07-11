<?php
session_start();
require_once '../db_config.php';

header('Content-Type: application/json');

function sendError($message) {
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id'])) {
    sendError('Invalid request');
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'] ?? '';

// Check if they are actually friends
$check_friend_sql = "SELECT * FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
$check_friend_stmt = $conn->prepare($check_friend_sql);
$check_friend_stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$check_friend_stmt->execute();
$friend_result = $check_friend_stmt->get_result();

if ($friend_result->num_rows == 0) {
    sendError('You are not friends with this user');
}

// Handle image upload
$image_url = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "../uploads/messages/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check file size
    if ($_FILES["image"]["size"] > 5000000) { // 5MB limit
        sendError('Sorry, your file is too large.');
    }

    // Allow certain file formats
    $allowed_types = ['jpg', 'png', 'jpeg', 'gif'];
    if (!in_array($imageFileType, $allowed_types)) {
        sendError('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
    }

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        sendError('Sorry, there was an error uploading your file.');
    }

    $image_url = basename($_FILES["image"]["name"]); // Save only the file name
}

// Insert new message into the database with status 'sent'
$query = "INSERT INTO messages (sender_id, receiver_id, message, image_url, status) VALUES (?, ?, ?, ?, 'sent')";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $image_url);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        sendError('Failed to send message');
    }
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}

$stmt->close();
$conn->close();
?>
