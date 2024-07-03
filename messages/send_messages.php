<?php
session_start();
require_once '../db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];

// Insert message into the database
$sql_insert = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("iis", $sender_id, $receiver_id, $message);

if ($stmt_insert->execute()) {
    echo "Message sent successfully.";
} else {
    echo "Error sending message: " . $stmt_insert->error;
}

$stmt_insert->close();
$conn->close();
?>
