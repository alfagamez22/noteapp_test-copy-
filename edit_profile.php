<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_config.php';

$user_id = $_SESSION['user_id'];

// Fetch user details including profile image URL
$stmt = $conn->prepare("SELECT name, lastname, birthdate, profile_image FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $lastname, $birthdate, $profile_image);
$stmt->fetch();
$stmt->close();

// Update profile form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = $_POST['new_name'];
    $new_lastname = $_POST['new_lastname']; // Added for lastname update
    $new_birthdate = $_POST['new_birthdate'];
    $profile_image_path = $profile_image; // Default to existing path

    // Handle profile image upload if a new file is provided
    if (!empty($_FILES['new_profile_image']['name'])) {
        $target_directory = "profile_uploads/";
        $target_file = $target_directory . basename($_FILES["new_profile_image"]["name"]);
        if (move_uploaded_file($_FILES["new_profile_image"]["tmp_name"], $target_file)) {
            $profile_image_path = $target_file; // Update path to new upload
        }
    }

    // Update user's name, lastname, birthdate, and profile image in the database
    $stmt_update = $conn->prepare("UPDATE users SET name = ?, lastname = ?, birthdate = ?, profile_image = ? WHERE user_id = ?");
    $stmt_update->bind_param("ssssi", $new_name, $new_lastname, $new_birthdate, $profile_image_path, $user_id);
    $stmt_update->execute();
    $stmt_update->close();

    // Redirect to index.php after update
    header('Location: index.php');
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .profile-image { max-width: 100px; max-height: 100px; border-radius: 50%; }
    </style>
    <link rel="stylesheet" href="css/edit_profile.css">
</head>
<body>
    <div class="edit_profile">
        <h2>Edit Profile</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <label for="new_name">New Name:</label><br>
            <input type="text" id="new_name" name="new_name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>
            
            <label for="new_lastname">New Lastname:</label><br>
            <input type="text" id="new_lastname" name="new_lastname" value="<?php echo htmlspecialchars($lastname); ?>" required><br><br>
            
            <label for="new_birthdate">New Birthdate:</label><br>
            <input type="date" id="new_birthdate" name="new_birthdate" value="<?php echo htmlspecialchars($birthdate); ?>" required><br><br>
            
            <label for="new_profile_image">New Profile Image:</label><br>
            <input type="file" id="new_profile_image" name="new_profile_image"><br><br>
            
            <input type="submit" value="Update Profile">
            <a href="index.php" class="back_to_home">Back to Home</a>
    </div>
    
    </form>
</body>
</html>
