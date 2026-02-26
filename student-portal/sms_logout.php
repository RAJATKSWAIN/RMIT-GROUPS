<!--======================================================
    File Name   : sms_logout.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : SMS - Student Management Portal | Logout Page
    Developed By: TrinityWebEdge
    Date Created: 25-02-2026
    Last Updated: 26-02-2026
    Note        : This page defines the SMS - Student Management Portal | Dashboard Page.
=======================================================-->
<?php
// student-portal/sms_logout.php
require_once __DIR__ . '/../fees-system/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * We only clear student-specific session variables. 
 * This ensures that if an Admin is testing the student portal, 
 * they don't get logged out of the Admin panel simultaneously.
 */

// 1. Unset Student & Institute specific data
unset($_SESSION['student_id']);
unset($_SESSION['student_name']);
unset($_SESSION['role_name']);
unset($_SESSION['inst_id']);
unset($_SESSION['inst_name']);
unset($_SESSION['inst_logo']);
unset($_SESSION['brand_color']);
unset($_SESSION['last_action']);

// 2. Check if an Admin session exists. 
// If NO admin is logged in, we destroy the whole session for security.
if (!isset($_SESSION['admin_id'])) {
    session_destroy();
    
    // Clean up the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// 3. Redirect to the student login page with a success message
header("Location: " . STUDENT_URL . "sms_login.php?logout=success");
exit;
