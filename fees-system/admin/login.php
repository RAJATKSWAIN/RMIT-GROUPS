<!--======================================================
    File Name   : index.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS - Fees Management System | Login Page
    Developed By: TrinityWebEdge
    Date Created: 05-02-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the FMS - Fees Management System | Login Page of RMIT Groups website.
=======================================================-->

<?php
session_start();
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);


require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/config.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username && $password) {

        $stmt = $conn->prepare("
            SELECT ADMIN_ID, FULL_NAME, PASSWORD_HASH 
            FROM ADMIN_MASTER 
            WHERE USERNAME=? AND STATUS='A'
        ");

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res && password_verify($password, $res['PASSWORD_HASH'])) {

            // security
            session_regenerate_id(true);

            $_SESSION['admin_id']   = $res['ADMIN_ID'];
            $_SESSION['admin_name'] = $res['FULL_NAME'];
            
            // 🔥 ADD HERE
			require_once __DIR__.'/../config/audit.php';
			audit_log($conn, 'LOGIN', 'ADMIN_MASTER', $res['ADMIN_ID']);

            header("Location: dashboard.php");
            exit;
        }

        $error = "Invalid username or password";
    }
}
?>


<!DOCTYPE html>

<html lang="en">
    
<head>
    
    <!-- SEO Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure Admin Login for Fees Management System (FMS). Manage student fees, dashboards, and reports with ease.">
    <meta name="keywords" content="Fees Management System, FMS, Admin Login, ERP, College ERP, Student Fees">
    <meta name="author" content="TrinityWebEdge">
    
    <title>Admin Login | Fees Management System</title>
    
    <!-- Retina PNG versions -->
	<link rel="icon" href="images/favicon_32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="images/favicon_64.png" sizes="64x64" type="image/png">
	<link rel="icon" href="images/favicon_180.png" sizes="180x180" type="image/png">
    
    <link rel="icon" href="data:image/svg+xml,
  <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'>
    <!-- Rounded background -->
    <rect width='64' height='64' rx='50' ry='50' />
    <!-- Bold centered text -->
    <text x='50%' y='50%' font-size='18' font-weight='bold'
          text-anchor='middle' dominant-baseline='middle'
          fill='white'>FMS-1.0</text>
  </svg>">

    <!-- Modern UI Frameworks -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/style.css">
    

    <style>
        body {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-holder {
			position:relative;
			margin:0 auto;
			line-height:100px;
			max-width:150px;
		}
		/*Added  by rajat*/
		.logo-holder img {
			position:relative;
			margin:0 auto;
			line-height:100px;
			max-width:150px;
		}
        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.8s ease-in-out;
        }
        .login-card h2 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #4e73df;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.75rem;
        }
        .btn-login {
            background: #4e73df;
            color: #fff;
            font-weight: 600;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #2e59d9;
        }
        .error-msg {
            color: #e74a3b;
            font-size: 0.9rem;
            text-align: center;
            margin-top: 1rem;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
    
<body>
    <div class="login-card">
        
        	<!-- Logo--> 
            <div class="logo-holder">
                <a href="#" class="ajax"><img src="/images/logo.png" alt="RMIT Group of Institution logo" loading="lazy" decoding="async" ></a>
            </div>
        
        <h2><i class="fas fa-user-shield"></i> Admin Login</h2>
        <form method="post">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login w-100">Login</button>
            <?php if(isset($error)) echo "<div class='error-msg'>$error</div>"; ?>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
    
</html>