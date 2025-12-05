<?php
require_once "config.php";
require_once "User.php";
include "navbar.php";

$user = new User();

// Redirect if already logged in
if ($user->isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($user->login($username, $password)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<h2>Login</h2>
<form method="POST">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>

<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<p>Don't have an account? <a href="register.php">Register</a></p>
