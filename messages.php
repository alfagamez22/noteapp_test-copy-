<?php
session_start();
require_once 'db_config.php';

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

// Initialize variables
$user_id = $_SESSION["user_id"];
$friends = [];

// Get user's friends
$sql = "SELECT u.user_id AS id, u.name AS username
        FROM friends f
        JOIN users u ON f.friend_id = u.user_id 
        WHERE f.user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $friends = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages</title>
    <link rel="stylesheet" href="css/message.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div id="chat-container">
        <div id="friends-list">
            <h2>Friends</h2>
            <ul>
                <?php foreach ($friends as $friend): ?>
                    <li class="friend" data-id="<?php echo $friend['id']; ?>"><?php echo htmlspecialchars($friend['username']); ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="index.php" class="back_to_home">Back</a>
        </div>
        <div id="chat-window">
            <div id="messages-title">Messages</div>
            <div id="message-list"></div>
            <form id="message-form">
                <input type="hidden" id="receiver-id" name="receiver_id" value="">
                <textarea id="message-input" name="message" placeholder="Type your message..."></textarea>
                <button type="submit" name="submit-button"><img src="css/send.png" alt="Send" style="width: 20px; height: 20px;"></button>
            </form>
        </div>
    </div>
    <script src="message.js"></script>
</body>
</html>
