<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$friends = [];

$user_id = $_SESSION['user_id'];
$sql = "SELECT u.user_id AS id, u.name AS username 
        FROM friends f 
        JOIN users u ON f.friend_id = u.user_id 
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $friends[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Friends List</title>
    <link rel="stylesheet" href="css/friends.css">
</head>
<body>
    <h1>Friends List</h1>
    <a href="add_friend.php">Add Friend</a>
    <ul>
        <?php foreach ($friends as $friend): ?>
            <li>
                <?php echo htmlspecialchars($friend['username']); ?>
                <a href="remove_friend.php?id=<?php echo $friend['id']; ?>">Remove</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="menu.php">Back to Menu</a>
</body>
</html>
