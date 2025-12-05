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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    if (empty($username) || empty($password) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        if ($user->register($username, $password)) {
            $success = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "Username already exists or registration failed.";
        }
    }
}
?>

<h2>Register</h2>
<form method="POST">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    Confirm Password: <input type="password" name="confirm" required><br>
    <button type="submit">Register</button>
</form>

<?php
if ($error) echo "<p style='color:red;'>$error</p>";
if ($success) echo "<p style='color:green;'>$success</p>";
?>
<p>Already have an account? <a href="login.php">Login</a></p>
