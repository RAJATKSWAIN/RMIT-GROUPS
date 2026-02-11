<?php
// 1. Database Connection (Replace with your actual credentials)
$host = 'sql100.infinityfree.com';
$db   = 'if0_40697103_rmit_smartcampus';
$user = 'if0_40697103';
$pass = 'rmitgroups123';
$charset = 'utf8mb4';


try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = "";

// 2. Handle the Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $new_pass = $_POST['new_password'];
    $conf_pass = $_POST['confirm_password'];

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND status = 'active'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "<div class='error'>Error: This email address is not registered or account is inactive.</div>";
    } elseif ($new_pass !== $conf_pass) {
        $message = "<div class='error'>Error: Passwords do not match!</div>";
    } elseif (strlen($new_pass) < 6) {
        $message = "<div class='error'>Error: Password must be at least 6 characters.</div>";
    } else {
        // Securely hash the password
        $hashed_password = password_hash($new_pass, PASSWORD_BCRYPT);
        
        // Update the database
        $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        if ($update->execute([$hashed_password, $email])) {
            $message = "<div class='success'>Success! Password updated for $email. <a href='login.php'>Login Now</a></div>";
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- Mobile viewport optimized -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

  <!-- Page Title -->
  <title>RMIT SmartCampus Portal | Academic Management System for Students, Faculty & Administration</title>

  <!-- Meta Description -->
  <meta name="description" content="RMIT SmartCampus Portal is the academic management system for RMIT Group of Institutions. Access student registration, faculty services, administration tools, admissions, 		and online campus resources in one secure platform.">

  <!-- Meta Keywords -->
  <meta name="keywords" content="RMIT SmartCampus, RMIT Group of Institutions, Academic Management System, Student Portal, Faculty Portal, Administration Portal, RMIT Registration, RMIT Admission, RMIT Online 	Services">

  <!-- Author -->
  <meta name="author" content="RMIT Group of Institutions">

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="/rmit-smartcampus/assets/img/favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="/rmit-smartcampus/assets/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="64x64" href="/rmit-smartcampus/images/favicon-64.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/rmit-smartcampus/assets/img/apple-touch-icon.png">
  <link rel="manifest" href="/rmit-smartcampus/assets/img/site.webmanifest">
  <link rel="mask-icon" href="/rmit-smartcampus/assets/img/safari-pinned-tab.svg" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#2d89ef">
  <meta name="theme-color" content="#ffffff">

  <!-- CSS links -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
  <link href="/rmit-smartcampus/assets/css/footer.css?v=1.0" rel="stylesheet">
  <link href="/rmit-smartcampus/assets/css/rmitscstyles.css?v=1.1" rel="stylesheet"> 
    
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --primary-blue: #003366; /* Defined in your logic */
}

body {
    background: var(--primary-gradient), url('background.jpg') no-repeat center center fixed;
    background-size: cover;
    min-height: 100vh;
    font-family: 'Arial', sans-serif;
    display: flex;
    flex-direction: column;
    margin: 0;
}

.page-content {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 100px 15px 40px; /* Space for fixed navbar */
}
        
/* The "Modern Box" Container */
.main-content {
    background: rgba(255, 255, 255, 0.98);
    width: 100%;
    max-width: 450px;
    padding: 1.5rem;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    text-align: center;
}

/* SmartCampus Portal Title in a Box Style */
.main-content h2 {
    color: var(--primary-blue);
    font-size: 1.6rem;
    font-weight: bold;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 3px solid #ff2f00; /* Your RMIT highlight color */
    display: inline-block;
}

/* Form Reset */
form {
    background: transparent;
    padding: 0;
    box-shadow: none;
    width: 100%;
    text-align: left;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #444;
}

input {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-sizing: border-box;
    font-size: 16px;
    background-color: #f9f9f9;
}

input:focus {
    outline: none;
    border-color: #764ba2; /* Part of your primary gradient */
    box-shadow: 0 0 0 3px rgba(118, 75, 162, 0.1);
}

/* Using your Primary Blue for the button */
button {
    width: 100%;
    padding: 14px;
    background-color: var(--primary-blue);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: all 0.3s ease;
}

button:hover {
    background-color: #002244;
    transform: translateY(-2px);
}

/* Messages */
.error { 
    color: #d9534f; 
    background: #f2dede; 
    padding: 12px; 
    border-radius: 8px; 
    margin-bottom: 20px; 
    font-size: 14px;
}

.success { 
    color: #3c763d; 
    background: #dff0d8; 
    padding: 12px; 
    border-radius: 8px; 
    margin-bottom: 20px; 
    font-size: 14px;
}

/* ===================== MOBILE RESPONSIVE ===================== */
@media (max-width: 768px) {

  body {
    overflow-x: hidden;
  }

  /* Navbar */
  /* Ensure brand + toggle align horizontally */
.navbar .navbar-brand {
  display: flex;
  align-items: center;
  margin-right: auto; /* push logos to the left */
}

/* Place toggle right next to logos in mobile */
.navbar .navbar-toggler {
  margin-left: 10px; /* small spacing */
  order: 2;          /* force toggle after logos */
}

/* Optional: adjust spacing between logos */
.navbar .navbar-brand img:first-child {
  margin-right: 10px;
}  
    
  .navbar-brand img {
    height: 18px !important;
  }
 
  .navbar-brand img {
		height: 25px;
		width: 180px;
		margin: 0 05px;
	}

  .navbar-nav {
    text-align: center;
    padding-top: 10px;
  }

  .navbar-nav .nav-item {
    margin-bottom: 8px;
  }

  .navbar .btn {
    width: 90%;
    margin: 10px auto;
  }
  
  .navbar-toggler{ 
     margin-left:05px;}  

  /* Remove blue highlight */
  .navbar-toggler:focus,
  .navbar-toggler:active,
  .navbar-toggler:hover {
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
  }

 
}

</style>

</head>

<body>

<div class="page-content">
    
<!-- =================Start Navbar Header================= -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" >
  <div class="container">
    
    <!-- Brand Logo + Text -->
    <a class="navbar-brand d-flex align-items-center" href="/rmit-smartcampus/index.php">
      <img src="/rmit-smartcampus/images/rmitsclogo1.png" alt="RMIT Logo" style="height:45px; width:auto; "> 
        <span style="border-left:5px solid #ff2f00; height:30px; margin:0 20px; display:inline-block;"></span>
      <img src="https://rmitgroups.org/images/logo.png" alt="RMIT Logo" style="height:20px; margin-right:10px;">      
    </a>
	
      
    <!-- Toggle Button (RIGHT side of logo) -->
    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Links -->
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/rmit-smartcampus/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/rmit-smartcampus/index.php#features">Features</a></li>
        <li class="nav-item"><a class="nav-link" href="/rmit-smartcampus/index.php#how-it-works">How It Works</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- =================End Navbar Header================= -->

<!-- =================Start Update Password================= -->    
<div class="main-content">
    <h2>SmartCampus Portal</h2>
    
    <?php echo $message; ?>

    <form method="POST" action="">
        <label>Registered Email Address</label>
        <input type="email" name="email" placeholder="e.g. s12345@student.rmit.edu" required>

        <label>New Password</label>
        <input type="password" name="new_password" placeholder="Min 6 characters" required>

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" placeholder="Repeat new password" required>

        <button type="submit">Update Password</button>
    </form>
    
    <div style="text-align: center; margin-top: 15px;">
        <a href="login.php" style="color: #003366; text-decoration: none; font-size: 14px;">Return to Login</a>
    </div>
</div>
<!-- =================End Update Password================= --> 
    
</div>  
<!-- ================= FOOTER ================= -->
<footer class="footer-main">
  <div class="container footer-container">

    <!-- Left -->
    <div class="footer-left">
      <span>
        © <script>document.write(new Date().getFullYear());</script>
        <a href="https://rmitgroupsorg.infinityfree.me/">RMIT 
           <span class="footer-highlight">GROUP OF INSTITUTIONS</span>
        </a>
        – Student Management Portal. All rights reserved.
      </span>
    </div>

    <!-- Right -->
    <div class="footer-right">
      <span class="dev-by">Developed by</span>
      <img src="/images/trinitywebedge.png" alt="TrinityWebEdge Logo" class="dev-logo">
      <a href="https://trinitywebedge.infinityfree.me" target="_blank" class="dev-link">
        TrinityWebEdge
      </a>
    </div>

  </div>
</footer>
<!-- ================= FOOTER END ================= -->

<!-- ================= All JS Scripts ================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>    
    
</body>

</html>