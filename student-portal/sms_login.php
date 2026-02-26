<!--======================================================
    File Name   : sms_login.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : SMS - Student Management Portal | Login Page
    Developed By: TrinityWebEdge
    Date Created: 25-02-2026
    Last Updated: 26-02-2026
    Note        : This page defines the SMS - Student Management Portal | Dashboard Page.
=======================================================-->
<?php
// 1. Setup paths to reach the sibling folder (fees-system)
require_once __DIR__ . '/../fees-system/config/db.php';
require_once __DIR__ . '/../fees-system/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";

// 2. Handle Login POST with your updated Query
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : ''; 
    
    // Updated query to fetch Course, Institute Name, Brand Color, and Logo
    $stmt = $conn->prepare("
        SELECT S.*, 
               C.COURSE_NAME, 
               I.INST_NAME, 
               I.BRAND_COLOR, 
               MD.LOGO_URL
        FROM STUDENTS S
        LEFT JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID
        LEFT JOIN MASTER_INSTITUTES I ON S.INST_ID = I.INST_ID
        LEFT JOIN MASTER_INSTITUTE_DTL MD ON MD.INST_ID = I.INST_ID
        WHERE (S.EMAIL = ? OR S.MOBILE = ?) AND S.STATUS = 'A'
        LIMIT 1
    ");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Clear previous sessions
        session_unset();
        
        // Store Student & Institute details in Session
        $_SESSION['student_id']   = $user['STUDENT_ID'];
        $_SESSION['student_name'] = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];
        $_SESSION['role_name']    = ROLE_STUDENT; 
        $_SESSION['inst_id']      = $user['INST_ID'];
        $_SESSION['inst_name']    = $user['INST_NAME'];
        $_SESSION['inst_logo']    = $user['LOGO_URL']; // Used for Dashboard Header
        $_SESSION['brand_color']  = $user['BRAND_COLOR'];
        $_SESSION['last_action']  = time();

        header("Location: sms_dashboard.php");
        exit;
    } else {
        $error = "Access denied. Credentials not found or account is inactive.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal Login | <?= SMS_APP_NAME ?></title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #2563eb;
            --bg-light: #f4f7fa;
        }

        body { 
            background-color: var(--bg-light);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 24px;
            width: 100%;
            max-width: 440px;
            padding: 3rem 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
        }

        .brand-icon {
            width: 56px;
            height: 56px;
            background-color: var(--primary-blue);
            color: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.5rem;
            box-shadow: 0 8px 15px rgba(37, 99, 235, 0.2);
        }

        .portal-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .portal-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 2.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.6rem;
            display: block;
            text-align: left;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group-custom i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 10;
        }

        .form-control {
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .btn-continue {
            background-color: var(--primary-blue);
            border: none;
            padding: 0.85rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
        }

        .btn-continue:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .footer-text {
            margin-top: 2.5rem;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .footer-text a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
            border-radius: 10px;
            padding: 0.75rem;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

<div class="login-card text-center">
    <div class="brand-icon">
        <i class="bi bi-mortarboard-fill"></i>
    </div>

    <h2 class="portal-title">Student Portal</h2>
    <p class="portal-subtitle">Secure access to your academic fees</p>

    <?php if($error): ?>
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="text-start mb-4">
            <label class="form-label">Email or Mobile Number</label>
            <div class="input-group-custom">
                <i class="bi bi-person"></i>
                <input type="text" name="identifier" class="form-control" placeholder="Enter your registered details" required autofocus>
            </div>
        </div>

        <button type="submit" class="btn-continue">
            Continue to Dashboard <i class="bi bi-arrow-right"></i>
        </button>
    </form>

    <div class="footer-text">
        <p class="text-muted mb-0" style="font-size: 0.7rem; opacity: 0.8; letter-spacing: 0.3px;">
            &copy; 2026 <strong><?= SMS_APP_NAME ?> v<?= SMS_APP_VERSION ?></strong> 
            <span class="mx-1">|</span> Product of <strong>TrinityWebEdge</strong>
        </p>
        <p class="mb-0"> Powered by <strong>TrinityWebEdge</strong> </p>
    </div>
    
</div>

</body>
</html>
