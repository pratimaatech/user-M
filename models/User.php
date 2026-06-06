<?php
// User Model - Database ke saath saari baat yahan hoti hai

require_once __DIR__ . '/../config/db.php';

class User {

    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    // Saare users lao
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    // Ek user ID se lao
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Naya user banao
    public function create($name, $email, $phone) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, phone) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$name, $email, $phone]);
    }

    // User update karo
    public function update($id, $name, $email, $phone) {
        $stmt = $this->db->prepare(
            "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $email, $phone, $id]);
    }

    // User delete karo
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Total users count
    public function count() {
        return $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }

    // Admin login check
    public function findAdmin($username) {
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
}
