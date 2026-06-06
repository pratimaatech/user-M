<?php
// UserController - Har request yahan aati hai aur decide hota hai kya karna hai

require_once __DIR__ . '/../models/User.php';

class UserController {

    private $user;

    public function __construct() {
        $this->user = new User();
    }

    // ── Dashboard: saare users dikhao ──────────────────────
    public function index() {
        $users      = $this->user->getAll();
        $totalUsers = $this->user->count();
        require __DIR__ . '/../views/users/index.php';
    }

    // ── Add user form dikhao ────────────────────────────────
    public function create() {
        $error = '';
        require __DIR__ . '/../views/users/create.php';
    }

    // ── Add user form submit ────────────────────────────────
    public function store() {
        $name  = trim($_POST['name']  ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!$name || !$email || !$phone) {
            $error = 'Saare fields zaroori hain.';
            require __DIR__ . '/../views/users/create.php';
            return;
        }

        $this->user->create($name, $email, $phone);
        $_SESSION['success'] = 'User successfully add ho gaya!';
        header('Location: index.php');
        exit;
    }

    // ── Edit user form dikhao ───────────────────────────────
    public function edit() {
        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->user->getById($id);

        if (!$user) {
            header('Location: index.php');
            exit;
        }

        $error = '';
        require __DIR__ . '/../views/users/edit.php';
    }

    // ── Edit user form submit ───────────────────────────────
    public function update() {
        $id    = (int)($_POST['id']    ?? 0);
        $name  = trim($_POST['name']   ?? '');
        $email = trim($_POST['email']  ?? '');
        $phone = trim($_POST['phone']  ?? '');

        if (!$name || !$email || !$phone) {
            $user  = $this->user->getById($id);
            $error = 'Saare fields zaroori hain.';
            require __DIR__ . '/../views/users/edit.php';
            return;
        }

        $this->user->update($id, $name, $email, $phone);
        $_SESSION['success'] = 'User successfully update ho gaya!';
        header('Location: index.php');
        exit;
    }

    // ── User delete karo ────────────────────────────────────
    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            $this->user->delete($id);
            $_SESSION['success'] = 'User successfully delete ho gaya!';
        }
        header('Location: index.php');
        exit;
    }

    // ── View single user ────────────────────────────────────
    public function view() {
        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->user->getById($id);

        if (!$user) {
            header('Location: index.php');
            exit;
        }

        require __DIR__ . '/../views/users/view.php';
    }

    // ── Login form dikhao ───────────────────────────────────
    public function loginForm() {
        $error = '';
        require __DIR__ . '/../views/auth/login.php';
    }

    // ── Login process karo ──────────────────────────────────
    public function login() {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $error    = '';

        if (!$username || !$password) {
            $error = 'Username aur password dono zaroori hain.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        // DB mein admin dhundo
        $admin = $this->user->findAdmin($username);

        // Hash verify karo
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: index.php');
            exit;
        }

        $error = 'Username ya password galat hai.';
        require __DIR__ . '/../views/auth/login.php';
    }

    // ── Logout ──────────────────────────────────────────────
    public function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}
