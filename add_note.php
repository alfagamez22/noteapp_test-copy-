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
    $user_id = $_SESSION['user_id'];
    
    // Handle file upload
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            echo "Error uploading file.";
        }
    }

    // Insert new note into database
    $stmt = $conn->prepare("INSERT INTO notes (title, content, user_id, image_url) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssis", $title, $content, $user_id, $image_url);
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
