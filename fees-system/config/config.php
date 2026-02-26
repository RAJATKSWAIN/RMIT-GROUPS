<?php
/* -- htdocs/fees-system/config/config.php -- */

/* --- 1. APPLICATION SETTINGS & URLS --- */
// We now define separate URLs for the Admin side and Student side
define('BASE_URL', '/fees-system/');          // Staff/Admin Root
define('STUDENT_URL', '/student-portal/');    // New Student Portal Root

define('APP_NAME', 'EduRemit™ FMS - Fees Management System');
define('APP_VERSION', '1.0.0');
define('DEVELOPED_BY', 'TrinityWebEdge');
date_default_timezone_set('Asia/Kolkata');

define('SMS_APP_NAME', 'EduRemit™ SMS - Student Management System');
define('SMS_APP_VERSION', '1.0.0');

/* --- 2. PRODUCT OWNER / SYSTEM DEFAULTS --- */
define('ORG_NAME', 'TRINITYWEBEDGE');
define('SUPPORT_EMAIL', 'trinitywebedge@zohomail.in');
define('SYSTEM_CURRENCY', 'INR'); 

/* --- 3. SECURITY & ROLES --- */
define('ROLE_SUPERADMIN', 'SUPERADMIN');
define('ROLE_ADMIN', 'ADMIN');
define('ROLE_STUDENT', 'STUDENT');

/* --- 4. GLOBAL ALERT SETTINGS --- */
define('MSG_SUCCESS', 'success');
define('MSG_ERROR', 'danger');

/* --- 5. PHYSICAL PATHS (Optional but Recommended) --- */
// This helps in including files from the sibling folder safely
define('ROOT_PATH', dirname(dirname(__DIR__))); // Points to htdocs/

?>
