<?php


require_once __DIR__ . '/database.php';

// ── Check if admin is logged in ──────────────────────────────

function isLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']) && is_int($_SESSION['admin_id']);
}

// ── Protect HTML pages ───────────────────────────────────────

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php';
        header('Location: login.php');
        exit;
    }
}

// ── Protect AJAX endpoints (return JSON, never redirect) ─────

function requireLoginAjax(): void
{
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode([
            'success'  => false,
            'message'  => 'Session expired. Please log in again.',
            'redirect' => 'login.php',
        ]);
        exit;
    }
}

// ── Attempt login using DB lookup ────────────────────────────


function attemptLogin(string $username, string $password): bool
{
    try {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, username, password FROM admin WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();
    } catch (Exception $e) {
        error_log('[Auth] DB error during login: ' . $e->getMessage());
        return false;
    }

    // No matching username found
    if (!$admin) {
        return false;
    }

    // Verify bcrypt hash
    if (!password_verify($password, $admin['password'])) {
        return false;
    }

    // ── Login successful ─────────────────────────────────────
    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id(true);

    $_SESSION['admin_id']       = (int) $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['csrf_token']     = bin2hex(random_bytes(32));

    return true;
}

// ── Logout ───────────────────────────────────────────────────

function logoutAdmin(): void
{
    // Clear all session data
    $_SESSION = [];

    // Expire the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}
