<?php
# ensures the file is included only once, even if multiple classes include it.
require_once "Database.php";

class Post {
    # mysql connectoin object
    private $conn;

    public function __construct() {
        # 1st returns a single shared instance of the Database, then returns the mysqli connection.
        // $db = Database::getInstance();
        // $this->conn = $db->getConnection();
        $this->conn = Database::getInstance()->getConnection();

        # checks and updates the database table structure.
        $this->ensureSchema();
    }

    private function ensureSchema() {
        $cols = ['category','subcategory','icon','image_path'];
        foreach ($cols as $col) {
            // $colEsc = $this->conn->real_escape_string($col);
            $res = $this->conn->query("SHOW COLUMNS FROM `posts` LIKE '$col'");
            if ($res && $res->num_rows === 0) {
                $this->conn->query("ALTER TABLE `posts` ADD COLUMN `$col` VARCHAR(255) NULL");
            }

        }
    }

    public function create($title, $body, $author_id, $category = null, $subcategory = null, $icon = null, $image_path = null) {
        $title = trim($title);
        $body = trim($body);
        if (empty($title) || empty($body)) return false;

        $stmt = $this->conn->prepare("INSERT INTO posts (title, body, author_id, category, subcategory, icon, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissss", $title, $body, $author_id, $category, $subcategory, $icon, $image_path);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->conn->query(
            "SELECT posts.*, users.username AS author, users.id AS author_id 
             FROM posts JOIN users ON posts.author_id = users.id 
             ORDER BY posts.created_at DESC"
        );
        # Convert the result to an associative array
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare(
            "SELECT posts.*, users.username AS author, users.id AS author_id 
             FROM posts JOIN users ON posts.author_id = users.id 
             WHERE posts.id=?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $title, $body, $category = null, $subcategory = null, $icon = null, $image_path = null) {
        $title = trim($title);
        $body = trim($body);
        if (empty($title) || empty($body)) return false;

        $stmt = $this->conn->prepare("UPDATE posts SET title=?, body=?, category=?, subcategory=?, icon=?, image_path=? WHERE id=?");
        $stmt->bind_param("ssssssi", $title, $body, $category, $subcategory, $icon, $image_path, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM posts WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
