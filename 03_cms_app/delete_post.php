<?php
require_once "config.php";
require_once "User.php";
require_once "Post.php";

$user = new User();
$post = new Post();

// Auth check
if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$id = $_POST['id'] ?? null;

// CSRF check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

if (!$id) {
    die("Invalid post ID");
}

$currentPost = $post->getById($id);

if (!$currentPost) {
    die("Post not found.");
}

// Authorization check
if ($currentPost['author_id'] != $_SESSION['user_id']) {
    die("Unauthorized action");
}

if ($post->delete($id)) {
    header("Location: dashboard.php");
    exit;
} else {
    echo "Failed to delete post.";
}
