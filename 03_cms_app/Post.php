<?php
require_once "Database.php";

class Post {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public function create($title, $body, $author_id) {
        $title = trim($title);
        $body = trim($body);
        if (empty($title) || empty($body)) return false;

        $stmt = $this->conn->prepare("INSERT INTO posts (title, body, author_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $body, $author_id);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->conn->query(
            "SELECT posts.*, users.username AS author, users.id AS author_id 
             FROM posts JOIN users ON posts.author_id = users.id 
             ORDER BY posts.created_at DESC"
        );
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

    public function update($id, $title, $body) {
        $title = trim($title);
        $body = trim($body);
        if (empty($title) || empty($body)) return false;

        $stmt = $this->conn->prepare("UPDATE posts SET title=?, body=? WHERE id=?");
        $stmt->bind_param("ssi", $title, $body, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM posts WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
