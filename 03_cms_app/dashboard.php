<?php
require_once "config.php";
require_once "User.php";
require_once "Post.php";
include "navbar.php";

$user = new User();
$post = new Post();

if (!$user->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch only current user's posts
$allPosts = $post->getAll();
$posts = array_filter($allPosts, function($p) {
    return $p['author_id'] == $_SESSION['user_id'];
});
?>

<h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
<a href="create_post.php">Create Post</a>

<h2>Your Posts</h2>
<?php if (empty($posts)): ?>
    <p>You have not created any posts yet.</p>
<?php else: ?>
    <div style="display:flex; flex-wrap:wrap; gap:10px;">
    <?php foreach ($posts as $p): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; width:300px; box-sizing:border-box;">
            <?php if (!empty($p['image_path'])): ?>
                <img src="<?= htmlspecialchars($p['image_path']) ?>" alt="" style="max-width:100%; height:auto; display:block; margin-bottom:8px;">
            <?php endif; ?>
            <h3>
                <?php if (!empty($p['icon'])): ?><span style="margin-right:6px;"><?= htmlspecialchars($p['icon']) ?></span><?php endif; ?>
                <?= htmlspecialchars($p['title']) ?>
            </h3>
            <?php $bodyText = $p['body']; $excerpt = strlen($bodyText) > 160 ? substr($bodyText, 0, 160) . '...' : $bodyText; ?>
            <p><?= nl2br(htmlspecialchars($excerpt)) ?></p>
            <?php if (!empty($p['category']) || !empty($p['subcategory'])): ?>
                <small>(<?= htmlspecialchars($p['category'] ?? '') ?><?= !empty($p['category']) && !empty($p['subcategory']) ? ' > ' : '' ?><?= htmlspecialchars($p['subcategory'] ?? '') ?>)</small>
            <?php endif; ?>

            <br>
            <small>By <?= htmlspecialchars($p['author']) ?> at <?= htmlspecialchars($p['created_at']) ?></small>
            <br>
            <a href="view_post.php?id=<?= $p['id'] ?>">View</a> | 
            <a href="edit_post.php?id=<?= $p['id'] ?>">Edit</a>
            <form method="POST" action="delete_post.php" style="display:inline;" 
                  onsubmit="return confirm('Are you sure you want to delete this post?');">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <button type="submit">Delete</button>
            </form>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
