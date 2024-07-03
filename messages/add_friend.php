<?php
session_start();
require '../db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$friend_name = trim($_POST['friend_name'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $friend_name) {
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE username LIKE ?");
    $search = "%$friend_name%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
    $friends = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Friend</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
    <h1>Add Friend</h1>
    <form method="POST">
        <label for="friend_name">Friend's Name:</label>
        <input type="text" id="friend_name" name="friend_name" required>
        <button type="submit">Search</button>
    </form>

    <?php if (isset($friends) && count($friends) > 0): ?>
        <h2>Select a Friend to Add:</h2>
        <ul>
            <?php foreach ($friends as $friend): ?>
                <li>
                    <?= htmlspecialchars($friend['username']) ?>
                    <form method="POST" action="add_friend_process.php" style="display:inline;">
                        <input type="hidden" name="friend_id" value="<?= $friend['id'] ?>">
                        <button type="submit">Add Friend</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>No users found with that name.</p>
    <?php endif; ?>

    <a href="../menu.php">Back to Menu</a>
</body>
</html>
