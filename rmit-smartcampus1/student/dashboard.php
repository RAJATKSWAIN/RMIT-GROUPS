<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
    header("Location: /rmit-smartcampus/auth/login.php");
    exit;
}

$dbConfig = [
    'host' => 'sql100.infinityfree.com',
    'user' => 'if0_40697103',
    'pass' => 'rmitgroups123',
    'name' => 'if0_40697103_rmit_smartcampus'
];

function connectDB($config) {
    $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['name']);
    if ($conn->connect_error) die("DB Error");
    $conn->set_charset("utf8");
    return $conn;
}

$conn = connectDB($dbConfig);
$userId = $_SESSION['user']['id'];

/* PROFILE */
$p = $conn->prepare("
SELECT 
  u.full_name,
  u.email,
  u.institute_code,
  i.name AS institute_name,
  s.id AS student_id,
  s.roll_no,
  s.program,
  s.semester,
  s.section
FROM users u
JOIN students s ON s.user_id = u.id
LEFT JOIN institutes i ON i.code = u.institute_code
WHERE u.id = ?
");
$p->bind_param("i",$userId);
$p->execute();
$profile = $p->get_result()->fetch_assoc();
if (!$profile) die("Student profile not found");

$studentId   = $profile['student_id'];
$institute   = $profile['institute_name'] ?? 'Unknown Institute';
$instituteCd = $profile['institute_code'];

/* ATTENDANCE */
$a = $conn->prepare("SELECT ROUND(AVG(attendance_percent),2) v FROM student_attendance WHERE student_id=?");
$a->bind_param("i",$studentId); $a->execute();
$attendance = $a->get_result()->fetch_row()[0] ?? 0;

/* CGPA */
$c = $conn->prepare("SELECT ROUND(AVG(marks)/10,2) v FROM student_results WHERE student_id=?");
$c->bind_param("i",$studentId); $c->execute();
$cgpa = $c->get_result()->fetch_row()[0] ?? 0;

/* FEES */
$f = $conn->prepare("SELECT due_amount FROM student_fees WHERE student_id=?");
$f->bind_param("i",$studentId); $f->execute();
$fees = $f->get_result()->fetch_row()[0] ?? 0;

/* ACTIVITIES */
$act = $conn->prepare("SELECT activity FROM student_activity WHERE student_id=? ORDER BY created_at DESC LIMIT 5");
$act->bind_param("i",$studentId); $act->execute();
$activities = $act->get_result();

$instituteThemes = [
  'HIT'   => ['color' => '#1e3c72', 'logo' => '/rmit-smartcampus/assets/img/hit-logo.png'],
  'RMIT'  => ['color' => '#667eea', 'logo' => '/rmit-smartcampus/assets/img/rmit-logo.png'],
  'RMITC' => ['color' => '#11998e', 'logo' => '/rmit-smartcampus/assets/img/rmitc-logo.png'],
  'CPS'   => ['color' => '#f46b45', 'logo' => '/rmit-smartcampus/assets/img/cps-logo.png'],
];

$theme = $instituteThemes[$instituteCd] ?? [
  'color' => '#667eea',
  'logo'  => '/rmit-smartcampus/assets/img/default-logo.png'
];

function ordinal($n) {
  if (in_array(($n % 100), [11,12,13])) return $n . 'th';
  return match ($n % 10) {
    1 => $n . 'st',
    2 => $n . 'nd',
    3 => $n . 'rd',
    default => $n . 'th'
  };
}

$semNumber = (int)$profile['semester'];

if (in_array($instituteCd, ['HIT','RMIT'])) {
  $semesterLabel = ordinal($semNumber) . ' Sem';
} elseif ($instituteCd === 'RMITC') {
  $semesterLabel = ordinal($semNumber) . ' Year';
} else {
  $semesterLabel = 'Semester ' . $semNumber;
}

if ($instituteCd === 'RMITC') {
  $periodLabel = 'Year';
} elseif ($instituteCd === 'CPS') {
  $periodLabel = 'STD';
} else {
  $periodLabel = 'Semester';
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
    
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,400i,700,800|Varela+Round" rel="stylesheet">

  <!-- CSS links -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> 
  <link href="/rmit-smartcampus/assets/css/footer.css?v=1.0" rel="stylesheet">
      
  <style>
  :root {
  --primary-color: <?= $theme['color'] ?>;
  --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

      
  body {
  background: linear-gradient(135deg, var(--primary-color), #764ba2), url('background.jpg') no-repeat center center fixed;
  background-size: cover;
  min-height: 100vh;
  font-family: 'Arial', sans-serif;
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
            background: linear-gradient(135deg, var(--primary-color), #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

      .page-content {
 			 flex: 1;
  			display: flex;
  			flex-direction: column;
		}

.dashboard{display:flex;gap:20px;margin-top:100px}
      
.sidebar{width:280px;background:#fff;border-radius:20px;padding:25px;box-shadow:0 10px 25px rgba(0,0,0,.08)}
      
.avatar { background: var(--primary-color); }
      
.avatar{width:100px;height:100px;border-radius:50%;/*background:#667eea;*/color:#fff;display:flex;align-items:center;justify-content:center;font-size:40px;margin:0 auto 15px}
      
.name{text-align:center;font-weight:bold;font-size:1.2rem}
      
.meta{text-align:center;color:#666;font-size:.9rem}
      
.main{flex:1;background:#fff;border-radius:20px;padding:30px;box-shadow:0 10px 25px rgba(0,0,0,.08)}
      
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:15px;margin-bottom:25px}
      
.card{border-radius:15px;padding:20px;color:#fff;text-align:center;font-weight:600}
      
.att{background:linear-gradient(45deg,var(--primary-color),#185a9d)}

.res{background:linear-gradient(45deg,var(--primary-color),#764ba2)}

.fee{background:linear-gradient(45deg,#ff9966,#ff5e62)}
      
.box{background:#f8f9fc;padding:15px;border-radius:12px;margin-bottom:10px}
      
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
  <div class="meta"><?= htmlspecialchars($institute) ?></div>

  <hr>
  <div>Roll: <?= htmlspecialchars($profile['roll_no']) ?></div>
  <div>Email: <?= htmlspecialchars($profile['email']) ?></div>
  <div>Program: <?= htmlspecialchars($profile['program']) ?></div>
  <div><?= htmlspecialchars($periodLabel) ?>: <?= htmlspecialchars($semesterLabel) ?></div>
  <div>Section: <?= htmlspecialchars($profile['section']) ?></div>
  <a href="/rmit-smartcampus/auth/logout.php" class="btn btn-danger mt-3 w-100">Logout</a>
</div>

<div class="main">

<div class="grid">
  <div class="card att">Attendance<br><?= $attendance ?>%</div>
  <div class="card res">CGPA<br><?= $cgpa ?></div>
  <div class="card fee">Fees Due<br>₹<?= number_format($fees,2) ?></div>
</div>

<h5>Recent Activities</h5>
<?php while($r=$activities->fetch_assoc()): ?>
  <div class="box">✔ <?= htmlspecialchars($r['activity']) ?></div>
<?php endwhile; ?>

</div>
</div>
</section>
    
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
