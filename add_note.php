<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // Retrieve user_id from session

    // Insert new note into database
    $stmt = $conn->prepare("INSERT INTO notes (title, content, user_id) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssi", $title, $content, $user_id);
        $stmt->execute();
        $stmt->close();
        header('Location: index.php');
        exit;
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>
