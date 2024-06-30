<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db_config.php';

if (isset($_GET['id'])) {
    $noteId = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    if ($stmt){
        $stmt->bind_param("ii", $noteId, $user_id);
        $stmt->execute();
        $stmt->close();
        header('Location: index.php');
        exit;
    } else {
        echo "Error deleting statement: " . $conn->error;
    }
}

$conn->close();
?>
