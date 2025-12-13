<?php
require_once "config.php";
require_once "Post.php";
include "navbar.php";

$postModel = new Post();
$id = $_GET['id'] ?? null;
$p = $id ? $postModel->getById((int)$id) : null;
if (!$p) { echo "Post not found."; exit; }
?>

<div style="border:1px solid #ccc; padding:10px; margin:10px auto; max-width:800px; box-sizing:border-box;">
    <?php if (!empty($p['image_path'])): ?>
        <img src="<?= htmlspecialchars($p['image_path']) ?>" alt="" style="max-width:100%; height:auto; display:block; margin-bottom:12px;">
    <?php endif; ?>
    <h2>
        <?php if (!empty($p['icon'])): ?><span style="margin-right:8px;"><?= htmlspecialchars($p['icon']) ?></span><?php endif; ?>
        <?= htmlspecialchars($p['title']) ?>
    </h2>
    <?php if (!empty($p['category']) || !empty($p['subcategory'])): ?>
        <small>(<?= htmlspecialchars($p['category'] ?? '') ?><?= !empty($p['category']) && !empty($p['subcategory']) ? ' > ' : '' ?><?= htmlspecialchars($p['subcategory'] ?? '') ?>)</small><br>
    <?php endif; ?>
    <small>By <?= htmlspecialchars($p['author']) ?> at <?= htmlspecialchars($p['created_at']) ?></small>
    <p><?= nl2br(htmlspecialchars($p['body'])) ?></p>
    <a href="index.php">Back</a>
</div>
