<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL & ~E_NOTICE);


session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    header("Location: /rmit-smartcampus/auth/login.php");
    exit;
}

$dbConfig = [
    'host' => 'sql100.infinityfree.com',
    'user' => 'if0_40697103',
    'pass' => 'rmitgroups123',
    'name' => 'if0_40697103_rmit_smartcampus'
];

$conn = new mysqli($dbConfig['host'],$dbConfig['user'],$dbConfig['pass'],$dbConfig['name']);
if ($conn->connect_error) die("DB Error: ".$conn->connect_error);
$conn->set_charset("utf8");

$userId = $_SESSION['user']['id'];

/* PROFILE */
$p = $conn->prepare("
SELECT u.full_name,
       u.email,
       u.institute_code,
       i.name institute,
       s.id staff_id,
       s.department,
       s.phone,
       s.gender
FROM users u
JOIN staff s ON s.user_id=u.id
LEFT JOIN institutes i ON i.code=u.institute_code
WHERE u.id=?
");

$p->bind_param("i",$userId);
$p->execute();
$profile = $p->get_result()->fetch_assoc();
if (!$profile) die("Staff profile not found");
$staffId = $profile['staff_id'];

/* INSTITUTE CODE (FINAL FIX) */
$instituteCd = $profile['institute_code'] ?? 'RMIT';


/* ================= THEME ================= */
$themes = [
  'HIT'  => ['color'=>'#1e3c72','logo'=>'/rmit-smartcampus/assets/img/hit-logo.png'],
  'RMIT' => ['color'=>'#667eea','logo'=>'/rmit-smartcampus/assets/img/rmit-logo.png'],
  'RMITC'=> ['color'=>'#11998e','logo'=>'/rmit-smartcampus/assets/img/rmitc-logo.png'],
  'CPS'  => ['color'=>'#f46b45','logo'=>'/rmit-smartcampus/assets/img/cps-logo.png'],
];

$theme = $themes[$instituteCd] 
         ?? ['color'=>'#667eea','logo'=>'/rmit-smartcampus/assets/img/default-logo.png'];

/* CLASSES */
$cl = $conn->prepare("SELECT COUNT(*) FROM staff_classes WHERE staff_id=?");
$cl->bind_param("i",$staffId);
$cl->execute();
$classes = $cl->get_result()->fetch_row()[0] ?? 0;

/* STUDENTS */
$st = $conn->prepare("
SELECT COUNT(DISTINCT cs.student_id)
FROM class_students cs
JOIN staff_classes sc ON sc.id=cs.class_id
WHERE sc.staff_id=?
");
$st->bind_param("i",$staffId);
$st->execute();
$students = $st->get_result()->fetch_row()[0] ?? 0;

/* ACTIVITIES */
$act = $conn->prepare("
SELECT activity FROM staff_activity
WHERE staff_id=?
ORDER BY created_at DESC LIMIT 5
");
$act->bind_param("i",$staffId);
$act->execute();
$activities = $act->get_result();

/* COURSES */
$co = $conn->prepare("SELECT COUNT(*) FROM staff_courses WHERE staff_id=?");
$co->bind_param("i",$staffId);
$co->execute();
$courses = $co->get_result()->fetch_row()[0] ?? 0;

/* TASKS */
$tk = $conn->prepare("SELECT COUNT(*) FROM staff_tasks WHERE staff_id=? AND status='pending'");
$tk->bind_param("i",$staffId);
$tk->execute();
$tasks = $tk->get_result()->fetch_row()[0] ?? 0;

/* STUDENTS */
$st = $conn->prepare("
SELECT COUNT(DISTINCT cs.student_id)
FROM class_students cs
JOIN staff_classes sc ON sc.id=cs.class_id
WHERE sc.staff_id=?
");
$st->bind_param("i",$staffId);
$st->execute();
$students = $st->get_result()->fetch_row()[0] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- Mobile viewport optimized -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

  <!-- Page Title -->
  <title>RMIT SmartCampus Portal | Faculty Dashboard | <?= htmlspecialchars($instituteCd) ?></title>

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
	
	.dashboard{display:flex;gap:20px;margin-top:100px}
	
	.sidebar{width:280px;background:#fff;border-radius:20px;padding:25px;box-shadow:0 10px 25px rgba(0,0,0,.08)}
	
	.avatar{width:100px;height:100px;border-radius:50%;background:#667eea;color:#fff;display:flex;align-items:center;justify-content:center;font-size:40px;margin:0 auto 15px}
	
	.name{text-align:center;font-weight:bold;font-size:1.2rem}
	
	.meta{text-align:center;color:#666;font-size:.9rem}
	
	.main{flex:1;background:#fff;border-radius:20px;padding:30px;box-shadow:0 10px 25px rgba(0,0,0,.08)}
	
	.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;margin-bottom:25px}
	
	.card{border-radius:15px;padding:20px;color:#fff;text-align:center;font-weight:600}
	
	.att{background:linear-gradient(45deg,#43cea2,#185a9d)}
	
	.res{background:linear-gradient(45deg,#667eea,#764ba2)}
	
	.fee{background:linear-gradient(45deg,#ff9966,#ff5e62)}
	
	.box{background:#f8f9fc;padding:15px;border-radius:12px;margin-bottom:10px}
        
	.section { margin:10px 10px; }
        
	.section {  margin-bottom: 20px;}
		
	/* Navbar */
	.navbar {
	background: #fff;
	box-shadow: 0 2px 8px rgba(0,0,0,0.1);
	padding: 0.5rem 2rem;
	}
	.navbar-brand img {
	height: 40px;
	}
	.navbar .btn-logout {
	background: none;
	border: 1px solid var(--danger-gradient);
	color: var(--danger-gradient);
	border-radius: 6px;
	transition: 0.3s;
	}
	.navbar .btn-logout:hover {
	background: var(--danger-gradient);
	color: #fff;
	}
		
	/* ================= RESPONSIVE ================= */
	
	@media (max-width: 992px) {
	.dashboard {
		flex-direction: column;
		margin-top: 100px;
	}
	
	.sidebar {
		width: 100%;
	}
	.navbar-brand img {
		height: 25px;
	}
     
    .section { margin:20px 20px; }
        
	.section {  margin-bottom: 40px;}
        
	}
	
	@media (max-width: 576px) {
        
    .dashboard {
		flex-direction: column;
		margin-top: 150px;
	}
	
	.avatar {
		width: 80px;
		height: 80px;
		font-size: 32px;
	}
		
	.navbar-brand img {
		height: 25px;
		width: 180px;
		margin: 0 05px;
	}
	
	.grid {
		grid-template-columns: 1fr;
	}
	
	.card {
		padding: 15px;
		font-size: 14px;
	}
	
	.sidebar .btn {
		width: 100%;
	}
        
   .section { margin:20px 20px; }
        
	.section {  margin-bottom: 40px;}
	}
    

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

<section>
<div class="container dashboard">

<div class="sidebar">
  <div class="avatar"><?= strtoupper(substr($profile['full_name'],0,1)) ?></div>

  <div class="name"><?= htmlspecialchars($profile['full_name']) ?></div>
  <div class="meta"><?= htmlspecialchars($profile['institute']) ?></div>
  <hr>
  <div>Department: <?= htmlspecialchars($profile['department']) ?></div>
  <div>Email: <?= htmlspecialchars($profile['email']) ?></div>
  <div>Phone: <?= htmlspecialchars($profile['phone']) ?></div>
  <div>Gender: <?= htmlspecialchars($profile['gender']) ?></div>
  <a href="/rmit-smartcampus/auth/logout.php" class="btn btn-danger mt-3 w-100">Logout</a>
</div>

<div class="main">
    
<div class="section my-3">
  <h5 class="mb-3">Course Management</h5>
  <div style="display:flex; flex-wrap:wrap; gap:10px;">
    <a href="create_course.php" class="btn btn-primary flex-fill">➕ Create Course</a>
    <a href="add_assignment.php" class="btn btn-secondary flex-fill">📄 Add Assignment</a>
    <a href="mark_attendance.php" class="btn btn-success flex-fill">📝 Mark Attendance</a>
    <a href="upload_results.php" class="btn btn-warning flex-fill">📊 Upload Results</a>
  </div>
</div>
    

<div class="grid">
  <div class="card att">Courses<br><?= $courses ?></div>
  <div class="card res">Classes<br><?= $classes ?></div>
  <div class="card fee">Pending Tasks<br><?= $tasks ?></div>
  <div class="card res">Students<br><?= $students ?></div>
    
</div>



<h5>Recent Activities</h5>
<?php while($r=$activities->fetch_assoc()): ?>
  <div class="box">✔ <?= htmlspecialchars($r['activity']) ?></div>
<?php endwhile; ?>

</div>
</div>
</section>

</div>

<div class="container my-4">
  <h4 class="text-center p-3 bg-warning bg-gradient text-dark rounded shadow-sm">
    🚀 Coming Soon Features
  </h4>
</div>
<!-- ================= Start of Tools ================= -->  
<section class="main my-4">

  <div class="container">
    <div class="row g-4">

      <!-- 1st Column: Faculty Tools -->
      <div class="col-12 col-md-6 col-lg-2">
        <h5 class="mb-2">Faculty Tools</h5>
        <ol class="mb-3">
          <li><a href="timetable.php">📅 View Timetable</a></li>
          <li><a href="exam_management.php">📝 Exam Management</a></li>
          <li><a href="student_performance.php">📊 Student Performance</a></li>
          <li><a href="leave_management.php">🏖 Leave Management</a></li>
        </ol>
      </div>

      <!-- 2nd Column: Student Engagement -->
      <div class="col-12 col-md-6 col-lg-2">
        <h5 class="mb-2">Student Engagement</h5>
        <ol class="mb-3">
          <li><a href="feedback.php">💬 Feedback & Surveys</a></li>
          <li><a href="mentorship.php">👥 Mentorship</a></li>
          <li><a href="resources.php">📂 Upload Resources</a></li>
        </ol>
      </div>

      <!-- 3rd Column: Administration -->
      <div class="col-12 col-md-6 col-lg-2">
        <h5 class="mb-2">Administration</h5>
        <ol class="mb-3">
          <li><a href="announcements.php">📢 Announcements</a></li>
          <li><a href="collaboration.php">🤝 Collaboration</a></li>
          <li><a href="reports.php">📑 Reports</a></li>
        </ol>
      </div>

      <!-- 4th Column: Analytics -->
      <div class="col-12 col-md-6 col-lg-2">
        <h5 class="mb-2">Analytics & Reports</h5>
        <ol class="mb-3">
          <li><a href="attendance_analytics.php">📈 Attendance</a></li>
          <li><a href="result_analysis.php">📊 Results</a></li>
          <li><a href="task_tracker.php">✅ Task Tracker</a></li>
        </ol>
      </div>

      <!-- 5th Column: Profile & Research -->
      <div class="col-12 col-md-6 col-lg-2">
        <h5 class="mb-2">Profile & Research</h5>
        <ol class="mb-3">
          <li><a href="profile_settings.php">⚙️ Settings</a></li>
          <li><a href="publications.php">📚 Publications</a></li>
          <li><a href="awards.php">🏆 Awards</a></li>
        </ol>
      </div>

      <!-- 6th Column: Communication -->
      <div class="col-12 col-md-6 col-lg-2">
        <h5 class="mb-2">Communication</h5>
        <ol class="mb-3">
          <li><a href="messaging.php">💬 Messaging</a></li>
          <li><a href="video_links.php">🎥 Video Links</a></li>
          <li><a href="forums.php">🗨️ Forums</a></li>
        </ol>
      </div>

    </div>
  </div>

</section>
<!-- ================= End of Tools ================= -->

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
