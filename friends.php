<?php
session_start();
require_once 'db_config.php';

// Check if the user is logged in, if not redirect to login.php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize friends array
$friends = [];

// Get user's friends
$user_id = $_SESSION['user_id'];
$sql = "SELECT u.user_id, u.name, u.lastname, u.email, u.created_at 
        FROM friends f
        JOIN users u ON f.friend_id = u.user_id 
        WHERE f.user_id = ?";

// Prepare and execute SQL statement
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Fetch all rows into an associative array
            $friends = $result->fetch_all(MYSQLI_ASSOC);
        }
    } else {
        $error = "Error executing SQL statement: " . $stmt->error;
    }
    $stmt->close();
} else {
    $error = "Error preparing SQL statement: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Friends List</title>
    <link rel="stylesheet" href="css/notes.css">
</head>
<body>
    <div class="container">
        <h1>Your Friends</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif (empty($friends)): ?>
            <p>You haven't added any friends yet.</p>
        <?php else: ?>
            <ul class="friends-list">
                <?php foreach($friends as $friend): ?>
                    <li>
                        <?php echo htmlspecialchars($friend['name'] . ' ' . $friend['lastname']); ?>
                        (<?php echo htmlspecialchars($friend['email']); ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a href="messages/add_friend.php" class="button">Add a Friend</a>
        <a href="index.php" class="button">Back to Notes</a>
    </div>
</body>
</html>