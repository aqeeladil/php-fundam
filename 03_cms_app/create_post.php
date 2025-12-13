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
    $author_id = $_SESSION['user_id'];
    $category = $_POST['category'] ?? null;
    $subcategory = $_POST['subcategory'] ?? null;
    $icon = trim($_POST['icon'] ?? '');
    $image_path = null;

    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed)) {
            if (!is_dir(__DIR__ . '/uploads')) { 
                mkdir(__DIR__ . '/uploads'); 
            }
            $name = uniqid('img_', true) . '.' . $ext;
            $dest = __DIR__ . '/uploads/' . $name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $image_path = 'uploads/' . $name;
            }
        }
    }

    if (empty($title) || empty($body)) {
        $error = "Title and body cannot be empty.";
    } elseif ($post->create($title, $body, $author_id, $category, $subcategory, $icon, $image_path)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Failed to create post.";
    }
}
?>

<h2>Create Post</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    Category:
    <select name="category" id="category" required>
        <option value="">Select category</option>
        <?php foreach ($categories as $cat => $subs): ?>
            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
    </select><br>
    Subcategory:
    <select name="subcategory" id="subcategory" required>
        <option value="">Select subcategory</option>
    </select><br>
    Icon: <input type="text" name="icon"><br>
    Image: <input type="file" name="image" accept="image/*"><br>
    Title: <input type="text" name="title" required><br>
    Body: <textarea name="body" required></textarea><br>
    <button type="submit">Create</button>
</form>

<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<a href="dashboard.php">Back to Dashboard</a>
<script>
const data = <?= json_encode($categories) ?>;
const cat = document.getElementById('category');
const sub = document.getElementById('subcategory');
cat.addEventListener('change', function() {
  sub.innerHTML = '<option value="">Select subcategory</option>';
  const v = this.value;
  if (data[v]) {
    data[v].forEach(function(s) {
      const opt = document.createElement('option');
      opt.value = s; opt.textContent = s; sub.appendChild(opt);
    });
  }
});
</script>
