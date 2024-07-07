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

    // Check if a friend request already exists
    $check_sql = "SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ? AND status = 'pending'";
    if ($stmt = $conn->prepare($check_sql)) {
        $stmt->bind_param("ii", $user_id, $friend_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "Friend request already sent.";
            exit;
        }
        $stmt->close();
    } else {
        echo "Error checking friend request: " . $conn->error;
        exit;
    }

    // Insert friend request
    $insert_sql = "INSERT INTO friend_requests (sender_id, receiver_id) VALUES (?, ?)";
    if ($stmt = $conn->prepare($insert_sql)) {
        $stmt->bind_param("ii", $user_id, $friend_id);
        $stmt->execute();
        $stmt->close();
        echo "Friend request sent successfully.";
    } else {
        echo "Error sending friend request: " . $conn->error;
    }

    // Redirect to friends.php or any other page after sending request
    header('location: ../friends.php');
    exit;
}

// Fetch potential friends (users who are not yet friends)
$user_id = $_SESSION['user_id'];
$sql = "SELECT user_id, name, profile_image FROM users WHERE user_id != ? AND user_id NOT IN (SELECT friend_id FROM friends WHERE user_id = ?)";
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
    <title>Discover Friends</title>
    <link rel="stylesheet" href="../css/friends.css">
    <style>
        .friend-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .friend-item img {
            width: 50px; /* Adjust size as needed */
            height: 50px; /* Adjust size as needed */
            border-radius: 50%; /* Ensures round images */
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Discover Friends</h1>
        <form action="choose_friend.php" method="post">
            <?php foreach($potential_friends as $friend): ?>
                <div class="friend-item">
                    <?php if (!empty($friend['profile_image'])): ?>
                        <img src="../<?php echo $friend['profile_image']; ?>" alt="Profile Image">
                    <?php else: ?>
                        <img src="../path_to_default_image/default_profile_image.png" alt="Default Profile Image">
                    <?php endif; ?>
                    <label>
                        <input type="radio" name="friend_id" value="<?php echo $friend['user_id']; ?>">
                        <?php echo htmlspecialchars($friend['name']); ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <a href="../index.php" class="button-link">Back</a>
            <input type="submit" value="Send Friend Request">
        </form>
    </div>
</body>
</html>
