<?php
require_once 'db_config.php';

$name = $lastname = $email = $birthdate = $password = $confirm_password = "";
$name_error = $lastname_error = $email_error = $birthdate_error = $password_error = $confirm_password_error = "";
$profile_image_error = "";
$profile_image_path = ""; // Initialize profile image path variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs here and set error messages

    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation for name
    if (empty($name)) {
        $name_error = "Name is required.";
    }

    // Validation for last name
    if (empty($lastname)) {
        $lastname_error = "Last Name is required.";
    }

    // Validation for email
    if (empty($email)) {
        $email_error = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format.";
    }

    // Validation for birthdate
    if (empty($birthdate)) {
        $birthdate_error = "Birthdate is required.";
    } // Add additional birthdate validation if necessary

    // Validation for password
    if (empty($password)) {
        $password_error = "Password is required.";
    } elseif (strlen($password) < 6) {
        $password_error = "Password must be at least 6 characters.";
    }

    // Validation for confirm password
    if (empty($confirm_password)) {
        $confirm_password_error = "Please confirm password.";
    } elseif ($password != $confirm_password) {
        $confirm_password_error = "Passwords do not match.";
    }

    // Profile image upload handling
    if ($_FILES["profile_image"]["name"]) {
        $target_dir = "profile_uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $profile_image_error = "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $profile_image_error = "File already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["profile_image"]["size"] > 500000) {
            $profile_image_error = "File is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $profile_image_error = "Only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $profile_image_error = "Sorry, your file was not uploaded.";
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                // File uploaded successfully
                $profile_image_path = $target_file; // Save this path in your database if needed
            } else {
                $profile_image_error = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // If there are no errors, proceed with inserting user into database
    if (empty($name_error) && empty($lastname_error) && empty($email_error) && empty($birthdate_error) && empty($password_error) && empty($confirm_password_error) && empty($profile_image_error)) {
        // Hash the password before inserting into database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into users table
        $stmt = $conn->prepare("INSERT INTO users (name, lastname, email, birthdate, password, profile_image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $lastname, $email, $birthdate, $hashed_password, $profile_image_path);
        $stmt->execute();

        // Retrieve the auto-generated user_id
        $user_id = $stmt->insert_id;

        // Close statement
        $stmt->close();

        // Redirect to login page or wherever appropriate
        header('Location: login.php');
        exit;
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth">
        <div class="header"><h1>Registration</h1></div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <a href="menu.php" class="back-button">Back to Menu</a>

            <!-- Profile Image Input -->
            <input type="file" name="profile_image">
            <span class="error"><?php echo $profile_image_error; ?></span>

            <!-- Other Form Inputs -->
            <input type="text" name="name" placeholder="Name" value="<?php echo htmlspecialchars($name); ?>">
            <span class="error"><?php echo $name_error; ?></span>

            <input type="text" name="lastname" placeholder="Last Name" value="<?php echo htmlspecialchars($lastname); ?>">
            <span class="error"><?php echo $lastname_error; ?></span>

            <input type="text" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>">
            <span class="error"><?php echo $email_error; ?></span>

            <input type="date" name="birthdate" placeholder="Birthdate" value="<?php echo htmlspecialchars($birthdate); ?>">
            <span class="error"><?php echo $birthdate_error; ?></span>

            <input type="password" name="password" placeholder="Password">
            <span class="error"><?php echo $password_error; ?></span>

            <input type="password" name="confirm_password" placeholder="Confirm Password">
            <span class="error"><?php echo $confirm_password_error; ?></span>

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
