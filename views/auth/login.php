<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 8px 32px rgba(0,0,0,.2);
        }
        .login-icon {
            width: 60px; height: 60px;
            background: #3498db;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; color: #fff;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="login-icon"><i class="bi bi-shield-lock-fill"></i></div>

    <h5 class="text-center fw-bold mb-1">Admin Login</h5>
    <p class="text-center text-muted small mb-3">User Management System</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small">
            <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?action=login_post">

        <div class="mb-3">
            <label class="form-label fw-semibold small">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" name="username" class="form-control"
                       placeholder="Enter username" required autofocus
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="password"
                       class="form-control" placeholder="Enter password" required>
                <button type="button" class="btn btn-outline-secondary"
                        onclick="togglePw()">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
        </button>

    </form>

    <p class="text-center text-muted mt-3 small">
        <i class="bi bi-info-circle me-1"></i>
        admin &nbsp;/&nbsp; admin123
    </p>
</div>

<script>
function togglePw() {
    var f = document.getElementById('password');
    var i = document.getElementById('eyeIcon');
    if (f.type === 'password') {
        f.type = 'text';
        i.className = 'bi bi-eye-slash';
    } else {
        f.type = 'password';
        i.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
