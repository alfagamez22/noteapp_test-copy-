<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $noteId = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT title, content, image_url FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $noteId, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $note = $result->fetch_assoc();
        $title = $note['title'];
        $content = $note['content'];
        $image_url = $note['image_url'];
    } else {
        echo "Note not found.";
        exit;
    }

    $stmt->close();

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $noteId = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Handle file upload
    $image_url = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "profile_uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            echo "Error uploading file.";
        }
    }

    $stmt = $conn->prepare("UPDATE notes SET title = ?, content = ?, image_url = ? WHERE id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("sssii", $title, $content, $image_url, $noteId, $user_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0){
            echo "Note updated successfully.";
        } else {
            echo "Failed to update note.";
        }
        $stmt->close();
        header ('Location: index.php');
        exit;
    } else {
        echo "Error preparing statement: " . $conn->error; 
    }
} else {
    echo "Invalid request.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Note</title>
    <link rel="stylesheet" href="css/edit_notes.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Note</h2>
        <form action="edit_note.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($noteId); ?>">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($image_url); ?>">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required><br><br>
            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="4" cols="50" required><?php echo htmlspecialchars($content); ?></textarea><br><br>
            <label for="image">Upload New Image:</label><br>
            <input type="file" id="image" name="image"><br><br>
            <input type="submit" value="Save Changes">
            <a href="index.php" class="back_link">Back</a>
        </form>
    </div>
</body>
</html>
