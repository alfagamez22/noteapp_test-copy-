<?php
session_start();
if (isset($_SESSION['user_id'])){
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Note Taking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="auth">
        <div class="header"><h1>Note Taking</h1></div>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    </div>
</body>
</html>
