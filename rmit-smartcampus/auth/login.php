<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dbConfig = [
    'host' => 'sql100.infinityfree.com',
    'user' => 'if0_40697103',
    'pass' => 'rmitgroups123',
    'name' => 'if0_40697103_rmit_smartcampus'
];

function connectDB($config) {
    $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
    if ($conn->connect_error) die("DB Error: " . $conn->connect_error);
    $conn->set_charset("utf8");
    return $conn;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['id'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['userType'] ?? '');

    if ($email && $password && in_array($role, ['admin','staff','student'])) {

        $conn = connectDB($dbConfig);

        $stmt = $conn->prepare("
            SELECT u.id, u.role, u.password_hash, u.full_name, u.status, i.name AS institute
            FROM users u
            JOIN institutes i ON i.code = u.institute_code
            WHERE u.email = ? AND u.role = ?
        ");
        $stmt->bind_param("ss", $email, $role);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($user = $res->fetch_assoc()) {

            if ($user['status'] !== 'active') die("Account inactive");
            if (!password_verify($password, $user['password_hash'])) die("Wrong password");

            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['full_name'],
                'role' => $user['role'],
                'institute' => $user['institute']
            ];

            $_SESSION['flash_success'] = "Login successful. Welcome, {$user['full_name']}!";

            if ($role === 'admin') header("Location: /rmit-smartcampus/admin/dashboard.php");
            if ($role === 'staff') header("Location: /rmit-smartcampus/faculty/dashboard.php");
            if ($role === 'student') header("Location: /rmit-smartcampus/student/dashboard.php");
            exit;
        } else {
            die("User not found");
        }
    } else {
        die("Invalid input");
    }
}
?>
<!-- Your existing HTML login UI remains unchanged -->

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
  <!--<link href="/rmit-smartcampus/assets/css/styles.css?v=1.0" rel="stylesheet">	-->
  <link href="/rmit-smartcampus/assets/css/footer.css?v=1.0" rel="stylesheet">
  <link href="/rmit-smartcampus/assets/css/rmitscstyles.css?v=1.1" rel="stylesheet">
    

<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}
    
body {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%), url('background.jpg') no-repeat center center fixed;
  background-size: cover;
  min-height: 100vh;
  font-family: 'Arial', sans-serif;
  display: flex;
  flex-direction: column;
}

html {
    height: 100%;
}

.page-content {
  flex: 1;
  display: flex;
  flex-direction: column;
		}


.main-content {
    height: calc(100vh - 76px);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 35px;
}

.login-card {
    width: 100%;
    max-width: 650px;
    padding: 2.5rem !important;
    margin-top:20px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.login-card h2 {
    color: #333;
    font-weight: 600;
    margin-bottom: 2rem;
}

.user-type-selection {
    margin-bottom: 1.5rem;
}

.user-type-selection .btn {
    border-radius: 25px;
    padding: 8px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
    margin: 0 5px;
}

.form-control {
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.password-toggle {
    position: relative;
}

.password-toggle .toggle-icon {
    position: absolute;
    right: 15px ;
    top: 70%;
    transform: translateY(-50%);
    cursor: pointer;
    user-select: none;
}

.login-links {
    margin-top: 1.5rem;
}

.login-links a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.login-links a:hover {
    color: #764ba2;
}
 
@media (max-width: 768px) {
   .main-content  {
		margin: 20px 0px;
		padding: 10px 10px;
		border-radius: 12px;
		max-width: 110%;
	}
	
	.login-card h2 {
		font-size: 1.3rem;
	}

    .user-type-selection .btn {
        margin: 5px 2px;
        font-size: 14px;
        padding: 6px 15px;
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

<div class="main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <h2 class="text-center">Welcome Back</h2>

                    <form method="POST">
                        <div class="user-type-selection text-center">
                            <button type="button" class="btn btn-outline-primary" onclick="selectUserType('admin')">
                                <i class="fas fa-user-shield me-1"></i>Admin
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="selectUserType('staff')">
                                <i class="fas fa-chalkboard-teacher me-1"></i>Faculty
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="selectUserType('student')">
                                <i class="fas fa-user-graduate me-1"></i>Student
                            </button>
                            <input type="hidden" name="userType" id="userType" value="admin" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="id" placeholder="Enter your email" required>
                        </div>

                        <div class="password-toggle mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                            <span class="toggle-icon" onclick="togglePassword(this.previousElementSibling)">👁️</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>

                        <div class="login-links text-center">
                            <a href="signup.php">Create account</a> | 
                            <a href="forgotpassword.php">Forgot password?</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
  
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

<script>
function togglePassword(input){
    if(input.type==="password"){input.type="text";}else{input.type="password";}
}

function selectUserType(role){
    document.getElementById('userType').value = role;
    const buttons = document.querySelectorAll('.user-type-selection .btn');
    buttons.forEach(btn => btn.classList.replace('btn-primary','btn-outline-primary'));
    const selected = Array.from(buttons).find(b => b.textContent.toLowerCase().includes(role));
    if(selected){selected.classList.replace('btn-outline-primary','btn-primary');}
}
document.addEventListener('DOMContentLoaded',()=>selectUserType('admin'));
</script>
    
</body>
    
</html>

