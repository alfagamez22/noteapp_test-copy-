<?php
session_start();
require_once '../db_config.php';

header('Content-Type: application/json');

function sendError($message) {
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

if (!isset($_SESSION['user_id']) || !isset($_GET['friend_id'])) {
    sendError('Invalid request');
}

$user_id = $_SESSION['user_id'];
$friend_id = $_GET['friend_id'];

// First, check if they are actually friends
$check_friend_sql = "SELECT * FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)";
$check_friend_stmt = $conn->prepare($check_friend_sql);
$check_friend_stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
$check_friend_stmt->execute();
$friend_result = $check_friend_stmt->get_result();

if ($friend_result->num_rows == 0) {
    sendError('You are not friends with this user');
}

$sql = "SELECT m.*, u.name AS sender_name 
        FROM messages m 
        JOIN users u ON m.sender_id = u.user_id 
        WHERE (m.sender_id = ? AND m.receiver_id = ?) 
           OR (m.sender_id = ? AND m.receiver_id = ?) 
        ORDER BY m.sent_at ASC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messageClass = ($row['sender_id'] == $user_id) ? 'sent' : 'received';
        $status = ($row['status'] == 'unread') ? 'sent' : ($row['status'] == 'read' ? 'seen' : $row['status']); // Convert "unread" to "sent" and "read" to "seen"
        $messages[] = [
            'class' => $messageClass,
            'sender' => htmlspecialchars($row['sender_name']),
            'message' => htmlspecialchars($row['message']),
            'timestamp' => date('M j, Y g:i A', strtotime($row['sent_at'])),
            'status' => htmlspecialchars($status)
        ];
    }

    // Update status to 'seen' for all messages received by the logged-in user
    $update_query = "UPDATE messages SET status = 'seen' WHERE receiver_id = ? AND sender_id = ? AND status = 'unread'";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ii", $user_id, $friend_id);
    $update_stmt->execute();

    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (Exception $e) {
    sendError('Database error: ' . $e->getMessage());
}

$stmt->close();
$conn->close();
?>
