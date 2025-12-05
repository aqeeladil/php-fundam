<?php
require_once "config.php";
require_once "Post.php";
include "navbar.php";

$post = new Post();
$posts = $post->getAll();   // fetches all posts by all authors
?>

<h1>Simple CMS</h1>

<h2>Posts</h2>
<?php if (empty($posts)): ?>
    <p>No posts available.</p>
<?php else: ?>
    <?php foreach ($posts as $p): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3><?= htmlspecialchars($p['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($p['body'])) ?></p>
            <small>By <?= htmlspecialchars($p['author']) ?> at <?= htmlspecialchars($p['created_at']) ?></small>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
