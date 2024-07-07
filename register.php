<?php
require_once 'db_config.php';

$name = $lastname = $email = $birthdate = $password = $confirm_password = "";
$name_error = $lastname_error = $email_error = $birthdate_error = $password_error = $confirm_password_error = "";
$profile_image_error = "";
$profile_image_path = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Improved validation for name
    if (empty($name)) {
        $name_error = "Name is required.";
    } elseif (!preg_match("/^[a-zA-Z '-]+$/", $name)) {
        $name_error = "Invalid format. Name should contain only letters, spaces, hyphens, and apostrophes.";
    }

    // Improved validation for last name
    if (empty($lastname)) {
        $lastname_error = "Last Name is required.";
    } elseif (!preg_match("/^[a-zA-Z '-]+$/", $lastname)) {
        $lastname_error = "Invalid format. Last name should contain only letters, spaces, hyphens, and apostrophes.";
    }

    // Improved validation for email
    if (empty($email)) {
        $email_error = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format.";
    } else {
        // Check if email already exists in the database
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $email_error = "This email address is already registered. Please use a different email.";
        }
        $stmt->close();
    }

    // Improved validation for birthdate
    if (empty($birthdate)) {
        $birthdate_error = "Birthdate is required.";
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $birthdate);
        $now = new DateTime();
        if (!$date || $date > $now) {
            $birthdate_error = "Invalid birthdate. Please enter a valid date in the past.";
        }
    }

    // Improved validation for password
    if (empty($password)) {
        $password_error = "Password is required.";
    } elseif (strlen($password) < 8) {
        $password_error = "Password must be at least 8 characters.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        $password_error = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
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
    $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = md5(time() . $_FILES["profile_image"]["name"]) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    $uploadOk = 1;

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $profile_image_error = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (5MB limit)
    if ($_FILES["profile_image"]["size"] > 5000000) {
        $profile_image_error = "File is too large. Maximum size is 5MB.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($file_extension, $allowed_extensions)) {
        $profile_image_error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $profile_image_error = "Error: File was not uploaded. " . $profile_image_error;
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image_path = $target_file;
        } else {
            $error_message = error_get_last();
            $profile_image_error = "Sorry, there was an error uploading your file. Error: " . ($error_message['message'] ?? 'Unknown error');
        }
    }
}

 // If there are no errors, proceed with inserting user into database
 // email duplicates validation
    if (empty($name_error) && empty($lastname_error) && empty($email_error) && empty($birthdate_error) && empty($password_error) && empty($confirm_password_error) && empty($profile_image_error)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("INSERT INTO users (name, lastname, email, birthdate, password, profile_image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $lastname, $email, $birthdate, $hashed_password, $profile_image_path);
            $stmt->execute();

            $user_id = $stmt->insert_id;
            $stmt->close();

            header('Location: login.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            // In case the email uniqueness check failed due to a race condition
            //1062 is error code for duplicate key violation which in this case yung key natin ay email
            if ($e->getCode() == 1062) {
                $email_error = "This email address is already registered. Please use a different email.";
            } else {
                // For any other database errors
                error_log("Database error: " . $e->getMessage());
                echo "An error occurred. Please try again later.";
                exit;
            }
        }
    }
}

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

            <input type="file" name="profile_image" accept="image/*">
            <span class="error"><?php echo $profile_image_error; ?></span>

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