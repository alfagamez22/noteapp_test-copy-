<?php
session_start();
require_once 'db_config.php';

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

// Initialize variables
$error = $message = "";
$user_id = $_SESSION["user_id"];
$friends = [];
$messages = [];

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

// Process form data when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty(trim($_POST["message"]))) {
        $error = "Please enter a message.";
    } elseif (empty($_POST["receiver_id"])) {
        $error = "Please select a friend to send the message to.";
    } else {
        $message = trim($_POST["message"]);
        $receiver_id = intval($_POST["receiver_id"]);

        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iis", $user_id, $receiver_id, $message);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Get messages
$sql = "SELECT u.name AS username, m.message, m.sent_at 
        FROM messages m
        JOIN users u ON m.sender_id = u.user_id 
        WHERE m.receiver_id = ? 
        ORDER BY m.sent_at DESC";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Messages</h1>
    <form action="messages.php" method="post">
        <label>Send to:</label>
        <select name="receiver_id">
            <?php foreach ($friends as $friend): ?>
                <option value="<?php echo $friend['id']; ?>"><?php echo htmlspecialchars($friend['username']); ?></option>
            <?php endforeach; ?>
        </select>
        <label>Message:</label>
        <textarea name="message"></textarea>
        <span><?php echo $error; ?></span>
        <input type="submit" value="Send Message">
        <a href="index.php">Back</a>
    </form>
    
    <h2>Inbox</h2>
    <ul>
        <?php foreach ($messages as $msg): ?>
            <li>
                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                <?php echo htmlspecialchars($msg['message']); ?>
                <em><?php echo htmlspecialchars($msg['sent_at']); ?></em>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
