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

    $stmt = $conn->prepare("SELECT title, content FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $noteId, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $note = $result->fetch_assoc();
        $title = $note['title'];
        $content = $note['content'];
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

    $stmt = $conn->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ssii", $title, $content, $noteId, $user_id);
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
    <style>
        body { font-family: Arial, sans-serif; }
        .form-container { max-width: 600px; margin: auto; }
        .form-container h2 { text-align: center; }
        .form-container form { margin-top: 20px; }
        .form-container label, .form-container textarea { display: block; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Note</h2>
        <form action="edit_note.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($noteId); ?>">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" required><br><br>
            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="4" cols="50" required><?php echo htmlspecialchars($content); ?></textarea><br><br>
            <input type="submit" value="Save Changes">
        </form>
    </div>
</body>
</html>
