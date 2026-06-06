<?php
/**
 * Logout — logout.php
 * ─────────────────────────────────────────────────────────────
 * Destroys the admin session and redirects to the login page.
 * Accepts both GET and POST requests.
 */

session_start();

require_once __DIR__ . '/config/auth.php';

// Only destroy session if admin is actually logged in
if (isLoggedIn()) {
    logoutAdmin();
}

// Always redirect — no HTML output
header('Location: login.php?logged_out=1');
exit;
