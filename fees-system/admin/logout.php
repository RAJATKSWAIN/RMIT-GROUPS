<?php
session_start();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/audit.php";

/* ---------------------------------
   Audit before destroying session
----------------------------------*/
if (isset($_SESSION['admin_id'])) {
    audit_log(
        $conn,
        'LOGOUT',            // action
        'ADMIN_MASTER',      // table
        $_SESSION['admin_id']
    );
}

/* ---------------------------------
   Destroy session
----------------------------------*/
$_SESSION = [];
session_unset();
session_destroy();

/* ---------------------------------
   Redirect
----------------------------------*/
header("Location: login.php");
exit;
