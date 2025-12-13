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
                <small>(<?= htmlspecialchars($p['category'] ?? '') ?><?= !empty($p['category']) && !empty($p['subcategory']) ? ' > ' : '' ?><?= htmlspecialchars($p['subcategory'] ?? '') ?>)</small><br>
            <?php endif; ?>
            <small>By <?= htmlspecialchars($p['author']) ?> at <?= htmlspecialchars($p['created_at']) ?></small><br>
            <a href="view_post.php?id=<?= $p['id'] ?>">Read More</a>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
