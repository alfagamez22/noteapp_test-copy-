<?php
session_start();
require_once '../db_config.php';

header('Content-Type: application/json');

function sendError($message) {
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    sendError('Invalid request');
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];

// Check if they are actually friends
$check_friend_sql = "SELECT * FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
$check_friend_stmt = $conn->prepare($check_friend_sql);
$check_friend_stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$check_friend_stmt->execute();
$friend_result = $check_friend_stmt->get_result();

if ($friend_result->num_rows == 0) {
    sendError('You are not friends with this user');
}

// Insert new message into the database with status 'unread'
$query = "INSERT INTO messages (sender_id, receiver_id, message, status) VALUES (?, ?, ?, 'unread')";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    
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
