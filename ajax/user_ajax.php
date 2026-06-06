<?php
/**
 * AJAX Endpoint — user_ajax.php
 * --------------------------------
 * Handles all asynchronous user operations.
 * Always returns JSON.
 *
 * Expected POST/GET params:
 *   action  : create | update | delete | get | search
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// Allow only AJAX requests
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Direct access not allowed.']);
    exit;
}

// ── Auth guard for AJAX — returns JSON 401, never redirects ──
require_once __DIR__ . '/../config/auth.php';
requireLoginAjax();
// ─────────────────────────────────────────────────────────────

require_once __DIR__ . '/../models/User.php';

$userModel = new User();
$action    = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// ── CSRF token check (skip for GET read operations) ──────────
$writeActions = ['create', 'update', 'delete'];
if (in_array($action, $writeActions, true)) {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
        exit;
    }
}

// ────────────────────────────────────────────────────────────
// Route to the correct handler
// ────────────────────────────────────────────────────────────
try {
    switch ($action) {

        // ── CREATE ──────────────────────────────────────────
        case 'create':
            $data   = collectAndValidate();
            $errors = $data['errors'];

            // Duplicate email check
            if (empty($errors['email']) && $userModel->emailExists($data['email'])) {
                $errors['email'] = 'This email address is already registered.';
            }

            if (!empty($errors)) {
                echo json_encode(['success' => false, 'errors' => $errors]);
                break;
            }

            $newId = $userModel->create($data);
            $user  = $userModel->getById($newId);

            echo json_encode([
                'success' => true,
                'message' => 'User created successfully.',
                'user'    => sanitizeOutput($user),
            ]);
            break;

        // ── UPDATE ──────────────────────────────────────────
        case 'update':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
                break;
            }

            $data   = collectAndValidate();
            $errors = $data['errors'];

            // Duplicate email check (exclude current user)
            if (empty($errors['email']) && $userModel->emailExists($data['email'], $id)) {
                $errors['email'] = 'This email address is already in use by another account.';
            }

            if (!empty($errors)) {
                echo json_encode(['success' => false, 'errors' => $errors]);
                break;
            }

            $result = $userModel->update($id, $data);
            $user   = $userModel->getById($id);

            echo json_encode([
                'success' => $result,
                'message' => $result ? 'User updated successfully.' : 'Update failed. Please try again.',
                'user'    => $result ? sanitizeOutput($user) : null,
            ]);
            break;

        // ── DELETE ──────────────────────────────────────────
        case 'delete':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
                break;
            }

            $result = $userModel->delete($id);
            echo json_encode([
                'success' => $result,
                'message' => $result ? 'User deleted successfully.' : 'Delete failed. Please try again.',
            ]);
            break;

        // ── GET SINGLE ──────────────────────────────────────
        case 'get':
            $id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $user = $id ? $userModel->getById($id) : false;

            if (!$user) {
                echo json_encode(['success' => false, 'message' => 'User not found.']);
                break;
            }

            echo json_encode(['success' => true, 'user' => sanitizeOutput($user)]);
            break;

        // ── SEARCH / PAGINATE ────────────────────────────────
        case 'search':
            $search      = isset($_GET['search'])   ? trim($_GET['search'])   : '';
            $perPage     = isset($_GET['per_page'])  ? (int) $_GET['per_page'] : 10;
            $currentPage = isset($_GET['page'])      ? max(1, (int) $_GET['page']) : 1;

            $allowedPerPage = [5, 10, 25, 50];
            if (!in_array($perPage, $allowedPerPage, true)) $perPage = 10;

            $offset     = ($currentPage - 1) * $perPage;
            $totalUsers = $userModel->countAll($search);
            $totalPages = (int) ceil($totalUsers / $perPage);
            $users      = $userModel->getAll($search, $perPage, $offset);

            $safeUsers  = array_map('sanitizeOutput', $users);

            echo json_encode([
                'success'      => true,
                'users'        => $safeUsers,
                'total'        => $totalUsers,
                'total_pages'  => $totalPages,
                'current_page' => $currentPage,
                'per_page'     => $perPage,
            ]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }

} catch (Exception $e) {
    error_log('[AJAX] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'A server error occurred. Please try again.']);
}

// ─────────────────────────────────────────────────────────────
// Helper functions
// ─────────────────────────────────────────────────────────────

/**
 * Collect POST data and run server-side validation.
 * Returns the cleaned data array plus an 'errors' key.
 */
function collectAndValidate(): array
{
    $data = [
        'first_name' => isset($_POST['first_name']) ? trim($_POST['first_name']) : '',
        'last_name'  => isset($_POST['last_name'])  ? trim($_POST['last_name'])  : '',
        'email'      => isset($_POST['email'])       ? trim(strtolower($_POST['email'])) : '',
        'phone'      => isset($_POST['phone'])       ? preg_replace('/[^0-9]/', '', trim($_POST['phone'])) : '',
        'errors'     => [],
    ];

    // first_name
    if ($data['first_name'] === '') {
        $data['errors']['first_name'] = 'First name is required.';
    } elseif (!preg_match('/^[A-Za-z\s\'\-]+$/', $data['first_name'])) {
        $data['errors']['first_name'] = 'First name may only contain letters, spaces, hyphens, and apostrophes.';
    } elseif (strlen($data['first_name']) > 100) {
        $data['errors']['first_name'] = 'First name must not exceed 100 characters.';
    }

    // last_name
    if ($data['last_name'] === '') {
        $data['errors']['last_name'] = 'Last name is required.';
    } elseif (!preg_match('/^[A-Za-z\s\'\-]+$/', $data['last_name'])) {
        $data['errors']['last_name'] = 'Last name may only contain letters, spaces, hyphens, and apostrophes.';
    } elseif (strlen($data['last_name']) > 100) {
        $data['errors']['last_name'] = 'Last name must not exceed 100 characters.';
    }

    // email
    if ($data['email'] === '') {
        $data['errors']['email'] = 'Email address is required.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $data['errors']['email'] = 'Please enter a valid email address.';
    } elseif (strlen($data['email']) > 150) {
        $data['errors']['email'] = 'Email address must not exceed 150 characters.';
    }

    // phone — must be exactly 10 digits after stripping non-numeric chars
    if ($data['phone'] === '') {
        $data['errors']['phone'] = 'Phone number is required.';
    } elseif (strlen($data['phone']) !== 10) {
        $data['errors']['phone'] = 'Phone number must be exactly 10 digits.';
    }

    return $data;
}

/**

 */
function sanitizeOutput(array $user): array
{
    return array_map(
        fn($v) => is_string($v) ? htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $v,
        $user
    );
}
