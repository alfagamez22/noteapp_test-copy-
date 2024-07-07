<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_config.php';

$user_id = $_SESSION['user_id'];

// Fetch user details including profile image URL
$stmt = $conn->prepare("SELECT name, lastname, profile_image FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $lastname, $profile_image);
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
    <link rel="stylesheet" href="css/notes.css">
</head>
<body>
    <div class="side-bar">
        <ul>
            <li><a href="friends.php">Friends</a></li>
            <li><a href="messages.php">Message</a></li>
            <li><a href="messages/choose_friend.php">Discover Friends</a></li>
        </ul>
    </div>

    <div class="container">
        <header class="header">
            <h2>Simple Note Taking App</h2>
            <h3>Welcome: <?php echo htmlspecialchars($name) . ' ' . htmlspecialchars($lastname); ?></h3>
        </header>

        <div class="profile_image">
            <?php if (!empty($profile_image)) : ?>
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Image" class="profile-image"><br><br>
            <?php endif; ?>
        </div>
        
        <a href="edit_profile.php" class="edit_profile">Edit profile</a>

        <section class="adding_notes">
            <form action="add_note.php" method="post" enctype="multipart/form-data">
                <label for="title">Title:</label><br>
                <input type="text" id="title" name="title"><br><br>
                <label for="content">Content:</label><br>
                <textarea id="content" name="content" rows="4" cols="50" ></textarea><br><br>
                <label for="image">Upload Image:</label><br>
                <input type="file" id="image" name="image"><br><br>
                <input type="submit" value="Add Note">
            </form>
        </section>

        <section class="my_notes">
            <h3>My Notes</h3>
            <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <article class="note">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                        <?php if (!empty($row['image_url'])) : ?>
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Note Image" style="max-width:100%; height:auto;"><br>
                        <?php endif; ?>
                        <?php
                        $created_at_12hr = date('M j, Y h:i A', strtotime($row['created_at']));
                        echo '<p><em>Created at: <strong>' . $created_at_12hr . '</strong></em></p>';
                        ?>
                        <a href="edit_note.php?id=<?php echo $row['id']; ?>">Edit Note</a> |
                        <a href="delete_note.php?id=<?php echo $row['id']; ?>">Delete Note</a>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No notes found.</p>
            <?php endif; ?>
        </section>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
