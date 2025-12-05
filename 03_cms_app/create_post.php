<?php
require_once "config.php";
require_once "User.php";
require_once "Post.php";
include "navbar.php";

$user = new User();
$post = new Post();

// Auth check
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $author_id = $_SESSION['user_id'];

    if (empty($title) || empty($body)) {
        $error = "Title and body cannot be empty.";
    } elseif ($post->create($title, $body, $author_id)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Failed to create post.";
    }
}
?>

<h2>Create Post</h2>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    Title: <input type="text" name="title" required><br>
    Body: <textarea name="body" required></textarea><br>
    <button type="submit">Create</button>
</form>

<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<a href="dashboard.php">Back to Dashboard</a>
