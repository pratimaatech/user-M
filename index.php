<?php

session_start();

require_once __DIR__ . '/controllers/UserController.php';

$action = $_GET['action'] ?? 'index';

// Login/logout pages ke liye auth check nahi
$publicActions = ['login', 'login_post'];

// Agar logged in nahi aur public page nahi → login pe bhejo
if (!isset($_SESSION['admin_id']) && !in_array($action, $publicActions)) {
    header('Location: index.php?action=login');
    exit;
}

// Agar logged in hai aur login page pe jaana chahta hai → dashboard pe bhejo
if (isset($_SESSION['admin_id']) && $action === 'login') {
    header('Location: index.php');
    exit;
}

$ctrl = new UserController();

// Request ko sahi method pe bhejo
switch ($action) {
    case 'login':      $ctrl->loginForm(); break;
    case 'login_post': $ctrl->login();     break;
    case 'logout':     $ctrl->logout();    break;
    case 'create':     $ctrl->create();    break;
    case 'store':      $ctrl->store();     break;
    case 'edit':       $ctrl->edit();      break;
    case 'update':     $ctrl->update();    break;
    case 'delete':     $ctrl->delete();    break;
    case 'view':       $ctrl->view();      break;
    default:           $ctrl->index();     break;
}
