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

$id = $_GET['id'] ?? null;
$currentPost = $post->getById($id);

if (!$currentPost) {
    die("Post not found.");
}

// Authorization check
if ($currentPost['author_id'] != $_SESSION['user_id']) {
    die("Unauthorized action");
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

    if (empty($title) || empty($body)) {
        $error = "Title and body cannot be empty.";
    } elseif ($post->update($id, $title, $body)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Failed to update post.";
    }
}
?>

<h2>Edit Post</h2>
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($currentPost['title']) ?>" required><br>
    Body: <textarea name="body" required><?= htmlspecialchars($currentPost['body']) ?></textarea><br>
    <button type="submit">Update</button>
</form>

<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<a href="dashboard.php">Back to Dashboard</a>
