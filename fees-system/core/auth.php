<?php
// core/auth.php
require_once __DIR__.'/../config/config.php';

// 1. Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if Admin is logged in
 */
function checkLogin()
{
    if (!isset($_SESSION['admin_id'])) {
        // Redirect to login if session is missing
        header("Location: " . BASE_URL . "admin/login.php");
        exit;
    }
    
    // Optional: Check for session timeout (e.g., 30 mins of inactivity)
    if (isset($_SESSION['last_action']) && (time() - $_SESSION['last_action'] > 1800)) {
        logout();
    }
    $_SESSION['last_action'] = time();
}

/**
 * Check if User has specific Role (e.g., 'MASTER')
 */
function authorize($required_role = 'MASTER')
{
    checkLogin();
    if ($_SESSION['role'] !== $required_role) {
        // Redirect to dashboard or error page if role doesn't match
        header("Location: " . BASE_URL . "admin/dashboard.php?error=unauthorized");
        exit;
    }
}

/**
 * Logout Logic
 */
function logout()
{
    $_SESSION = array(); // Clear session variables
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: " . BASE_URL . "admin/login.php?logout=success");
    exit;
}
?>