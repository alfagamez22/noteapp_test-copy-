<?php
session_start();
require_once '../db_config.php';

// Check if the user is logged in, if not then redirect them to login page
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit;
}

// Initialize variables
$error = "";
$user_id = $_SESSION["user_id"];
$friends = [];

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["friend_id"])) {
        $error = "Please select a friend to remove.";
    } else {
        $friend_id = intval($_POST["friend_id"]);
        $sql = "DELETE FROM friends WHERE user_id = ? AND friend_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $user_id, $friend_id);
            $stmt->execute();
            $stmt->close();
            header("location: remove_friend.php");
            exit;
        } else {
            $error = "Error preparing SQL statement: " . $conn->error;
        }
    }
}

// Get user's friends
$sql = "SELECT u.user_id, u.name, u.lastname, u.email, u.created_at 
        FROM friends f
        JOIN users u ON f.friend_id = u.user_id 
        WHERE f.user_id = ?";
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

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remove Friend</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Remove a Friend</h1>
    <form action="remove_friend.php" method="post">
        <ul>
            <?php foreach ($friends as $friend): ?>
                <li>
                    <label>
                        <input type="radio" name="friend_id" value="<?php echo $friend['user_id']; ?>">
                        <?php echo htmlspecialchars($friend['name'] . ' ' . $friend['lastname']); ?>
                        (<?php echo htmlspecialchars($friend['email']); ?>)
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>
        <span><?php echo $error; ?></span>
        <input type="submit" value="Remove Friend">
    </form>
    <a href="../friends.php" class="button">Back to Friends List</a>
</body>
</html>
