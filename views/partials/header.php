<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'User Management', ENT_QUOTES, 'UTF-8') ?> | AdminPanel</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- TOP NAVBAR                                                  -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
    <div class="container-fluid px-3">

        <!-- Sidebar toggle (mobile) -->
        <button class="btn btn-sm btn-outline-light me-2 d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list fs-5"></i>
        </button>

        <!-- Brand -->
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
            <i class="bi bi-people-fill fs-4"></i>
            <span>UserAdmin</span>
        </a>

        <!-- Right side -->
        <div class="ms-auto d-flex align-items-center gap-3">
            <!-- Logged-in admin name -->
            <span class="text-white-50 small d-none d-sm-inline">
                <i class="bi bi-shield-lock-fill me-1 text-white-50"></i>
                <span class="text-white fw-semibold">
                    <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?>
                </span>
            </span>
            <div class="vr text-white-50"></div>
            <span class="text-white small d-none d-md-inline">
                <i class="bi bi-calendar3 me-1"></i>
                <?= date('M d, Y') ?>
            </span>
            <div class="vr text-white-50 d-none d-md-block"></div>
            <!-- Logout button -->
            <a href="logout.php"
               class="btn btn-sm btn-outline-light d-flex align-items-center gap-1"
               id="logoutBtn"
               title="Sign out">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </a>
        </div>

    </div>
</nav>

<!-- LAYOUT WRAPPER      -->
<div class="wrapper d-flex">

    <!-- ── SIDEBAR ──────────────────────────────────────────── -->
    <aside class="sidebar bg-dark text-white" id="sidebar">
        <div class="sidebar-header p-3 border-bottom border-secondary">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-circle bg-primary">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <div class="fw-semibold small">Administrator</div>
                    <div class="" style="font-size:.7rem text-secondary">Super Admin</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav p-2 mt-1">
            <div class="nav-section-label text-uppercase  px-2 mb-1" style="font-size:.65rem;letter-spacing:.08em">Main Menu</div>
            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a href="index.php"
                       class="nav-link text-white d-flex align-items-center gap-2 rounded <?= (!isset($_GET['action']) || $_GET['action'] === 'index') ? 'active bg-primary' : '' ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=index"
                       class="nav-link text-white d-flex align-items-center gap-2 rounded">
                        <i class="bi bi-people"></i> All Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?action=create"
                       class="nav-link text-white d-flex align-items-center gap-2 rounded <?= (isset($_GET['action']) && $_GET['action'] === 'create') ? 'active bg-primary' : '' ?>">
                        <i class="bi bi-person-plus"></i> Add User
                    </a>
                </li>
            </ul>

            
        </nav>

        
    </aside>
    <!-- ── END SIDEBAR ──────────────────────────────────────── -->

    <!-- ── MAIN CONTENT AREA ────────────────────────────────── -->
    <main class="main-content flex-grow-1">

        <!-- Overlay for mobile sidebar -->
        <div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>
