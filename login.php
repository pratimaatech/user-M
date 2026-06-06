<?php

/**
 * Login Page — login.php
 */

session_start();

require_once __DIR__ . '/config/auth.php';

// Already logged in → go straight to dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Ensure CSRF token exists for the form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error        = '';
$username_val = '';

// ── Handle POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. CSRF check
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Invalid security token. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Repopulate the username field on error
        $username_val = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

        if ($username === '' || $password === '') {
            $error = 'Username and password are both required.';
        } elseif (attemptLogin($username, $password)) {
            // 2. Successful login — redirect back to the originally requested URL
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);

            // Allow only safe relative URLs (prevent open redirect)
            if (!preg_match('/^[a-zA-Z0-9\/_\-\.\?=&]+$/', $redirect)) {
                $redirect = 'index.php';
            }

            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Incorrect username or password.';
            // Rotate CSRF token after each failed attempt
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | UserAdmin</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1a2a6c 0%, #0d6efd 50%, #1a2a6c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 1rem;
            border: none;
            box-shadow: 0 1.5rem 3rem rgba(0, 0, 0, .35);
            animation: slideUp .35s ease both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #0d6efd, #0056b3);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #fff;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 15px rgba(13, 110, 253, .4);
        }

        .form-control-lg {
            border-radius: .6rem;
            border: 1.5px solid #dee2e6;
            font-size: .9rem;
        }

        .form-control-lg:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .2);
        }

        .input-group-text {
            background: #f8f9fa;
            border: 1.5px solid #dee2e6;
            border-right: none;
            border-radius: .6rem 0 0 .6rem;
            color: #6c757d;
        }

        .input-group .form-control-lg {
            border-left: none;
            border-radius: 0 .6rem .6rem 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: #0d6efd;
        }

        .btn-toggle-pw {
            border: 1.5px solid #dee2e6;
            border-left: none;
            border-radius: 0 .6rem .6rem 0;
            background: #f8f9fa;
            color: #6c757d;
            cursor: pointer;
            transition: color .15s;
        }

        .btn-toggle-pw:hover {
            color: #0d6efd;
        }

        .btn-signin {
            padding: .7rem;
            border-radius: .6rem;
            font-size: .95rem;
            font-weight: 600;
            background: linear-gradient(135deg, #0d6efd, #0056b3);
            border: none;
            transition: opacity .2s, transform .1s;
        }

        .btn-signin:hover {
            opacity: .9;
        }

        .btn-signin:active {
            transform: scale(.98);
        }
    </style>
</head>

<body>

    <div class="login-card card">
        <div class="card-body p-4 p-sm-5">

            <!-- Logo + heading -->
            <div class="text-center mb-4">
                <div class="login-logo">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h4 class="fw-bold mb-0">Admin Login</h4>
                <p class="text-muted small mt-1">Sign in to access the dashboard</p>
            </div>

            <!-- Alerts -->
            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php elseif (isset($_GET['logged_out'])): ?>
                <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3 small" role="alert">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    You have been signed out successfully.
                </div>
            <?php endif; ?>

            <!-- Login form -->
            <form method="POST" action="login.php" id="loginForm" novalidate>

                <input type="hidden" name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label fw-semibold small">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text"
                            class="form-control form-control-lg"
                            id="username"
                            name="username"
                            value="<?= $username_val ?>"
                            placeholder="Enter username"
                            autocomplete="username"
                            maxlength="50"
                            required
                            autofocus>
                    </div>
                    <div class="text-danger small mt-1" id="usernameError"></div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password"
                            class="form-control form-control-lg"
                            id="password"
                            name="password"
                            placeholder="Enter password"
                            autocomplete="current-password"
                            maxlength="100"
                            required>
                        <button type="button" class="btn btn-toggle-pw" id="togglePw" aria-label="Show/hide password">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="text-danger small mt-1" id="passwordError"></div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-signin btn-primary w-100 text-white" id="loginBtn">
                    <span id="loginSpinner" class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                    <i class="bi bi-box-arrow-in-right me-1" id="loginIcon"></i>
                    Sign In
                </button>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            'use strict';

            // Show / hide password
            document.getElementById('togglePw').addEventListener('click', function() {
                var pwd = document.getElementById('password');
                var icon = document.getElementById('toggleIcon');
                if (pwd.type === 'password') {
                    pwd.type = 'text';
                    icon.className = 'bi bi-eye';
                } else {
                    pwd.type = 'password';
                    icon.className = 'bi bi-eye-slash';
                }
            });

            // Client-side validation + loading spinner
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                var username = document.getElementById('username').value.trim();
                var password = document.getElementById('password').value;
                var valid = true;

                document.getElementById('usernameError').textContent = '';
                document.getElementById('passwordError').textContent = '';

                if (!username) {
                    document.getElementById('usernameError').textContent = 'Username is required.';
                    valid = false;
                }
                if (!password) {
                    document.getElementById('passwordError').textContent = 'Password is required.';
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                    return;
                }

                document.getElementById('loginBtn').disabled = true;
                document.getElementById('loginSpinner').classList.remove('d-none');
                document.getElementById('loginIcon').classList.add('d-none');
            });
        })();
    </script>

</body>

</html>