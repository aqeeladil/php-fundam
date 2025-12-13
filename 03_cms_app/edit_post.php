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
$categories = [
    'Technology' => ['AI','Web','Mobile','Cloud'],
    'Lifestyle' => ['Travel','Food','Health'],
    'Business' => ['Startup','Marketing','Finance'],
    'Education' => ['Tutorial','Research','Tips']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $category = $_POST['category'] ?? null;
    $subcategory = $_POST['subcategory'] ?? null;
    $icon = trim($_POST['icon'] ?? '');

    $image_path = $currentPost['image_path'] ?? null;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed)) {
            if (!is_dir(__DIR__ . '/uploads')) { mkdir(__DIR__ . '/uploads'); }
            $name = uniqid('img_', true) . '.' . $ext;
            $dest = __DIR__ . '/uploads/' . $name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $image_path = 'uploads/' . $name;
            }
        }
    }

    if (empty($title) || empty($body)) {
        $error = "Title and body cannot be empty.";
    } elseif ($post->update($id, $title, $body, $category, $subcategory, $icon, $image_path)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Failed to update post.";
    }
}
?>

<h2>Edit Post</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($currentPost['title']) ?>" required><br>
    Body: <textarea name="body" required><?= htmlspecialchars($currentPost['body']) ?></textarea><br>
    Category:
    <select name="category" id="category" required>
        <option value="">Select category</option>
        <?php foreach ($categories as $cat => $subs): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= ($currentPost['category'] ?? '') === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
    </select><br>
    Subcategory:
    <select name="subcategory" id="subcategory" required>
        <option value="">Select subcategory</option>
    </select><br>
    Icon: <input type="text" name="icon" value="<?= htmlspecialchars($currentPost['icon'] ?? '') ?>"><br>
    <?php if (!empty($currentPost['image_path'])): ?>
        <img src="<?= htmlspecialchars($currentPost['image_path']) ?>" alt="" style="max-width:200px; display:block; margin:6px 0;">
    <?php endif; ?>
    Image: <input type="file" name="image" accept="image/*"><br>
    <button type="submit">Update</button>
</form>

<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<a href="dashboard.php">Back to Dashboard</a>
<script>
const data = <?= json_encode($categories) ?>;
const cat = document.getElementById('category');
const sub = document.getElementById('subcategory');
function populateSubcats(c) {
  sub.innerHTML = '<option value="">Select subcategory</option>';
  if (data[c]) {
    data[c].forEach(function(s) {
      const opt = document.createElement('option');
      opt.value = s; opt.textContent = s; if ('<?= htmlspecialchars($currentPost['subcategory'] ?? '') ?>' === s) opt.selected = true; sub.appendChild(opt);
    });
  }
}
populateSubcats(cat.value);
cat.addEventListener('change', function() { populateSubcats(this.value); });
</script>
