<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL & ~E_NOTICE);

session_start();

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    header("Location: /rmit-smartcampus/auth/login.php");
    exit;
}

/* ================= DB CONNECTION ================= */
$conn = new mysqli(
    "sql100.infinityfree.com",
    "if0_40697103",
    "rmitgroups123",
    "if0_40697103_rmit_smartcampus"
);

if ($conn->connect_error) {
    die("Database Connection Failed");
}

$conn->set_charset("utf8");

/* ================= USER & STAFF ================= */
$userId = $_SESSION['user']['id'];

/* Get staff ID */
$stmt = $conn->prepare("
    SELECT s.id, u.institute_code
    FROM staff s
    JOIN users u ON u.id = s.user_id
    WHERE s.user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Staff profile not found");
}

$staffId     = $data['id'];
$instituteCd = $data['institute_code'] ?? 'RMIT';

/* ================= THEME CONFIG ================= */
$themes = [
    'HIT'   => ['color'=>'#1e3c72','logo'=>'/rmit-smartcampus/assets/img/hit-logo.png'],
    'RMIT'  => ['color'=>'#667eea','logo'=>'/rmit-smartcampus/assets/img/rmit-logo.png'],
    'RMITC' => ['color'=>'#11998e','logo'=>'/rmit-smartcampus/assets/img/rmitc-logo.png'],
    'CPS'   => ['color'=>'#f46b45','logo'=>'/rmit-smartcampus/assets/img/cps-logo.png'],
];

$theme = $themes[$instituteCd] 
      ?? ['color'=>'#667eea','logo'=>'/rmit-smartcampus/assets/img/default-logo.png'];

/* ================= FORM HANDLING ================= */
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $courseCode = trim($_POST['course_code']);
    $courseName = trim($_POST['course_name']);
    $semester   = (int)$_POST['semester'];
    $program    = trim($_POST['program']);

    /* Insert course */
    $stmt = $conn->prepare("
        INSERT INTO staff_courses 
        (staff_id, course_name, program, semester)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("issi", $staffId, $courseName, $program, $semester);
    $stmt->execute();

    /* Activity log */
    $stmt = $conn->prepare("
        INSERT INTO staff_activity (staff_id, activity)
        VALUES (?, ?)
    ");
    $activity = "Created course: $courseName";
    $stmt->bind_param("is", $staffId, $activity);
    $stmt->execute();

    $msg = "Course created successfully!";
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
  <title>RMIT SmartCampus Portal | Faculty Dashboard | Create Courses |<?= htmlspecialchars($instituteCd) ?></title>

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
    
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,400i,700,800|Varela+Round" rel="stylesheet">

  <!-- CSS links -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
  <link href="/rmit-smartcampus/assets/css/footer.css?v=1.0" rel="stylesheet">
      
  <style>
  :root {
    --primary-color: <?= $theme['color'] ?>;
    --primary-gradient: linear-gradient(135deg, var(--primary-color), #764ba2);
    --success-gradient: linear-gradient(135deg,#4facfe,#00f2fe);
    --warning-gradient: linear-gradient(135deg,#43e97b,#38f9d7);
    --danger-gradient: linear-gradient(135deg,#ff512f,#dd2476);
    --light-bg: #f8f9fa;
    --card-radius: 12px;
	}

	/* Reset & Layout */
	body {
	font-family: 'Raleway', sans-serif;
	background-color: var(--primary-color);
	margin: 0;
	padding: 0;
	display: flex;
	min-height: 100vh;
	flex-direction: column;
	}
		
	.page-content {
	flex: 1;
	display: flex;
	flex-direction: column;
	}

  /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 2px 30px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
      
      .page-content {
 			 flex: 1;
  			display: flex;
  			flex-direction: column;
		}

		.section { 
            margin-top:60px ;
            margin-bottom:10px; }

</style>
    
</head>
    
<body>
    
<div class="page-content">
    
<!-- =================Start Navbar Header================= -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top" >
  <div class="container justify-content-center">
    <a class="navbar-brand d-flex align-items-center" href="/rmit-smartcampus/index.php">
      <img src="/rmit-smartcampus/images/rmitsclogo1.png" alt="RMIT Logo" style="height:45px; width:auto; "> 
        <span style="border-left:5px solid #ff2f00; height:30px; margin:0 20px; display:inline-block;"></span>
      <img src="<?= $theme['logo'] ?>" alt="Institute Logo" style="height:35px;">      
    </a>
      
    <!-- Push logout to right -->
    <a class="ms-auto">
      <a href="/rmit-smartcampus/auth/logout.php" class="btn btn-logout btn-sm">
    	<i class="fas fa-sign-out-alt"></i> Logout
  	  </a>
    </a>      
  </div>
</nav>
<!-- =================End Navbar Header================= -->
  
<!-- ================= Start Create Courses ================= -->    
<section class= "section">
<div class="container mt-5" style="max-width:600px; margin-top:10px;">
  <div class="card shadow p-4 rounded-4">
    <h4 class="mb-3">Create Course</h4>

    <?php if($msg): ?>
      <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Course Code</label>
        <input type="text" name="course_code" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Course Name</label>
        <input type="text" name="course_name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Semester</label>
        <input type="number" name="semester" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Program</label>
        <input type="text" name="program" class="form-control">
      </div>

      <button class="btn btn-primary w-100">Create Course</button>
    </form>

    <a href="dashboard.php" class="btn btn-link mt-3">← Back to Dashboard</a>
  </div>
</div>
</section>
<!-- ================= Start Create Courses ================= --> 
    
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

</body>
</html>
