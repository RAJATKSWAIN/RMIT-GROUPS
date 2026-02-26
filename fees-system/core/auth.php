<?php
// core/auth.php
require_once __DIR__.'/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * 1. STAFF/ADMIN ACCESS CHECK
 * Use this for files inside /fees-system/admin/
 */
function checkLogin()
{
    // Ensure the user is logged in AND has an Admin-level role
    // This explicitly ignores anyone with only a student_id
    if (!isset($_SESSION['role_name']) || !isset($_SESSION['admin_id'])) {
        header("Location: " . BASE_URL . "/admin/login.php");
        exit;
    }
    
    // Session Timeout (30 Mins)
    if (isset($_SESSION['last_action']) && (time() - $_SESSION['last_action'] > 1800)) {
        logout();
    }
    $_SESSION['last_action'] = time();
}

/**
 * 2. STUDENT PORTAL ACCESS CHECK
 * Use this for files inside /student-portal/
 */
function checkStudentLogin()
{
    // Check for STUDENT role and student_id specifically
    if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== ROLE_STUDENT || !isset($_SESSION['student_id'])) {
        header("Location: " . STUDENT_URL . "sms_login.php");
        exit;
    }

    // Session Timeout
    if (isset($_SESSION['last_action']) && (time() - $_SESSION['last_action'] > 1800)) {
        // Redirect to student-portal login
        header("Location: " . STUDENT_URL . "sms_login.php?error=timeout");
        exit;
    }
    $_SESSION['last_action'] = time();
}

/**
 * 3. ADMIN ROLE AUTHORIZATION
 * Checks if staff has specific permission (ADMIN vs SUPERADMIN)
 */
function authorize($required_role = ROLE_SUPERADMIN)
{
    // First, verify they are staff
    checkLogin();
    
    // Superadmins bypass all checks
    if ($_SESSION['role_name'] === ROLE_SUPERADMIN) return true;

    // If the role doesn't match the requirement
    if ($_SESSION['role_name'] !== $required_role) {
        header("Location: " . BASE_URL . "/admin/dashboard.php?error=unauthorized");
        exit;
    }
}

/**
 * 4. LOGOUT LOGIC
 */
function logout()
{
    $is_student = (isset($_SESSION['role_name']) && $_SESSION['role_name'] === ROLE_STUDENT);
    
    $_SESSION = array(); 
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();

    // Redirect based on who was logged in
    if ($is_student) {
        header("Location: " . STUDENT_URL . "sms_login.php?logout=success");
    } else {
        header("Location: " . BASE_URL . "/admin/login.php?logout=success");
    }
    exit;
}
