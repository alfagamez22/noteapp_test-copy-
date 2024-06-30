<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_config.php';

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT name, lastname FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $lastname);
$stmt->fetch();
$stmt->close();

// Fetch notes from database for the logged-in user
$sql = "SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Note Taking App</title>
    <style>
        /* CSS for styling, adjust as needed */
        body { font-family: Arial, sans-serif; }
        .note { margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; }
        .note h3 { margin-top: 0; }
        .note p { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h2>Simple Note Taking App</h2>
    
    <h3>Welcome: <?php echo htmlspecialchars($name) . ' ' . htmlspecialchars($lastname); ?></h3>
    <form action="add_note.php" method="post">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>
        <label for="content">Content:</label><br>
        <textarea id="content" name="content" rows="4" cols="50" required></textarea><br><br>
        <input type="submit" value="Add Note">
    </form>

    <hr>

    <h3>My Notes</h3>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="note">';
            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
            echo '<p>' . nl2br(htmlspecialchars($row['content'])) . '</p>';
            // Convert created_at timestamp to 12-hour format and bold
            $created_at_12hr = date('M j, Y h:i A', strtotime($row['created_at']));
            echo '<p><em>Created at: <strong>' . $created_at_12hr . '</strong></em></p>';
            echo '<a href="edit_note.php?id=' . $row['id'] . '">Edit Note</a> | ';
            echo '<a href="delete_note.php?id=' . $row['id'] . '">Delete Note</a>';
            echo '</div>';
        }
    } else {
        echo '<p>No notes found.</p>';
    }
    $stmt->close();
    $conn->close();
    ?>
    <a href="logout.php">Logout</a>
</body>
</html>
