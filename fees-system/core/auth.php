<?php
// core/auth.php
require_once __DIR__.'/../config/config.php';

// 1. Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if User is logged in (Universal for all roles)
 */
function checkLogin()
{
    // 1. Basic Authentication Check
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role_name'])) {
        header("Location: " . BASE_URL . "admin/login.php");
        exit;
    }
    
    // 2. Session Timeout Logic
    if (isset($_SESSION['last_action']) && (time() - $_SESSION['last_action'] > 1800)) {
        logout();
    }
    $_SESSION['last_action'] = time();

    /* 3. Auto-Routing Logic:
       Ensures students cannot even view admin-level dashboard pages 
       unless specifically authorized.
    */
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($_SESSION['role_name'] === 'STUDENT' && $current_page === 'dashboard.php') {
        header("Location: " . BASE_URL . "admin/students/profile.php");
        exit;
    }
}

/**
 * Specific Authorization for Admin/Superadmin Features
 */
function authorize($required_role = 'SUPERADMIN')
{
    // Always check if logged in first
    checkLogin();
    
    // SUPERADMIN is the owner of the product - has access to everything
    if ($_SESSION['role_name'] === 'SUPERADMIN') {
        return true;
    }

    // Check if the current user matches the required role
    if ($_SESSION['role_name'] !== $required_role) {
        
        // Handle unauthorized access based on role
        if ($_SESSION['role_name'] === 'STUDENT') {
            header("Location: " . BASE_URL . "admin/students/profile.php?error=unauthorized");
        } else {
            // Usually for ADMIN trying to access SUPERADMIN-only areas
            header("Location: " . BASE_URL . "admin/dashboard.php?error=unauthorized");
        }
        exit;
    }
}

/**
 * Logout Logic
 */
function logout()
{
    $_SESSION = array(); 
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
