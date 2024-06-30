<?php
require_once 'db_config.php';

$name = $lastname = $email = $birthdate = $password = $confirm_password = "";
$name_error = $lastname_error = $email_error = $birthdate_error = $password_error = $confirm_password_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs here and set error messages

    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name)) {
        $name_error = "Name is required.";
    }
    // Add validations for other fields (lastname, email, birthdate, password, confirm_password)

    // Check if there are no errors, then insert user into database
    if (empty($name_error) && empty($lastname_error) && empty($email_error) && empty($birthdate_error) && empty($password_error) && empty($confirm_password_error)) {
        // Hash the password before inserting into database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into users table
        $stmt = $conn->prepare("INSERT INTO users (name, lastname, email, birthdate, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $lastname, $email, $birthdate, $hashed_password);
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
        <div class="header">Register</div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
