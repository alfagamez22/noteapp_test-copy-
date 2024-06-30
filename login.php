<?php
session_start();
require_once 'db_config.php';

$email = $password = "";
$email_error = $password_error = $login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email)) {
        $email_error = "Please enter your email.";
    }

    if (empty($password)) {
        $password_error = "Please enter your password.";
    }

    if (empty($email_error) && empty($password_error)) {
        $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                header('Location: index.php');
                exit;
            } else {
                $login_error = "Invalid password.";
            }
        } else {
            $login_error = "No account found with that email.";
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <div class="auth">
        <div class="header"><h1>Login</h1></div>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <a href="menu.php" class="back-button">Back to Menu</a>
            <input type="text" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>">
            <span class="error"><?php echo $email_error; ?></span>
            <input type="password" name="password" placeholder="Password">
            <span class="error"><?php echo $password_error; ?></span>
            <input type="submit" value="Login">
            <span class="error"><?php echo $login_error; ?></span>
        </form>
    </div>
</body>
</html>
