<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    
    // Process friend request
    switch ($_POST['action']) {
        case 'accept_request':
            // Update friend request status to accepted
            $sql_accept = "UPDATE friend_requests SET status = 'accepted' WHERE request_id = ?";
            if ($stmt_accept = $conn->prepare($sql_accept)) {
                $stmt_accept->bind_param("i", $request_id);
                $stmt_accept->execute();
                $stmt_accept->close();
            }
            
            // Retrieve sender and receiver IDs
            $sql_select = "SELECT sender_id, receiver_id FROM friend_requests WHERE request_id = ?";
            if ($stmt_select = $conn->prepare($sql_select)) {
                $stmt_select->bind_param("i", $request_id);
                $stmt_select->execute();
                $stmt_select->bind_result($sender_id, $receiver_id);
                if ($stmt_select->fetch()) {
                    $stmt_select->close();
                    
                    // Insert sender as friend of receiver
                    $sql_insert_sender = "INSERT INTO friends (user_id, friend_id) VALUES (?, ?)";
                    if ($stmt_insert_sender = $conn->prepare($sql_insert_sender)) {
                        $stmt_insert_sender->bind_param("ii", $sender_id, $receiver_id);
                        $stmt_insert_sender->execute();
                        $stmt_insert_sender->close();
                    }
                    
                    // Insert receiver as friend of sender
                    $sql_insert_receiver = "INSERT INTO friends (user_id, friend_id) VALUES (?, ?)";
                    if ($stmt_insert_receiver = $conn->prepare($sql_insert_receiver)) {
                        $stmt_insert_receiver->bind_param("ii", $receiver_id, $sender_id);
                        $stmt_insert_receiver->execute();
                        $stmt_insert_receiver->close();
                    }
                } else {
                    // Handle error or no result found
                    $stmt_select->close();
                }
            }
            break;
        
        case 'reject_request':
            // Delete friend request
            $sql_reject = "DELETE FROM friend_requests WHERE request_id = ?";
            if ($stmt_reject = $conn->prepare($sql_reject)) {
                $stmt_reject->bind_param("i", $request_id);
                $stmt_reject->execute();
                $stmt_reject->close();
            }
            break;
        
        default:
            break;
    }
}

$conn->close();
header('Location: friends.php');
exit;
?>
