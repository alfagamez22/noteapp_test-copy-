<?php
session_start();
require_once '../db_config.php';

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

// Process selection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['friend_id'])) {
    $user_id = $_SESSION['user_id'];
    $friend_id = intval($_POST['friend_id']);

    $sql = "INSERT INTO friends (user_id, friend_id) VALUES (?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $user_id, $friend_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error preparing SQL statement: " . $conn->error;
    }

    // Redirect to friends.php after adding friend
    header('location: ../friends.php');
    exit;
}

// Fetch potential friends (users who are not yet friends)
$user_id = $_SESSION['user_id'];
$sql = "SELECT user_id, name FROM users WHERE user_id != ? AND user_id NOT IN (SELECT friend_id FROM friends WHERE user_id = ?)";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $potential_friends = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    echo "Error fetching potential friends: " . $conn->error;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Choose Friend</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Choose a Friend</h1>
    <form action="choose_friend.php" method="post">
        <ul>
            <?php foreach($potential_friends as $friend): ?>
                <li>
                    <label>
                        <input type="radio" name="friend_id" value="<?php echo $friend['user_id']; ?>">
                        <?php echo htmlspecialchars($friend['name']); ?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="../index.php">Back</a>
        <input type="submit" value="Add Selected Friend">
    </form>
</body>
</html>
