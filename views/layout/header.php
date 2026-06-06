<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?? 'User Management' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body        { background: #f5f6fa; }
        .sidebar    { min-height: 100vh; background: #2c3e50; padding-top: 1rem; }
        .sidebar a  { color: #bdc3c7; text-decoration: none; display: block; padding: .5rem 1.2rem; border-radius: .4rem; margin: 2px 8px; }
        .sidebar a:hover, .sidebar a.active { background: #3498db; color: #fff; }
        .topbar     { background: #fff; border-bottom: 1px solid #e0e0e0; padding: .75rem 1.5rem; }
        .main       { padding: 1.5rem; }
        .brand      { color: #fff; font-size: 1.2rem; font-weight: 700; padding: .5rem 1.2rem 1rem; display: block; border-bottom: 1px solid #3d5166; margin-bottom: .5rem; }
    </style>
</head>
<body>
<div class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar" style="width:220px; flex-shrink:0">
        <span class="brand"><i class="bi bi-people-fill me-2"></i>UserAdmin</span>
        <a href="index.php" <?= (!isset($_GET['action']) || $_GET['action']==='index') ? 'class="active"' : '' ?>>
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a href="index.php?action=create" <?= (($_GET['action'] ?? '')  === 'create') ? 'class="active"' : '' ?>>
            <i class="bi bi-person-plus me-2"></i>Add User
        </a>
        <div style="position:absolute; bottom:1rem; width:204px; padding: 0 8px">
            <a href="index.php?action=logout" class="text-danger" style="background:#1a252f">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
        </div>
    </div>

    <!-- Main area -->
    <div class="flex-grow-1">
        <!-- Top bar -->
        <div class="topbar d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold"><?= $pageTitle ?? 'Dashboard' ?></h6>
            <span class="text-muted small">
                <i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
            </span>
        </div>
        <div class="main">

        <!-- Flash message -->
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
