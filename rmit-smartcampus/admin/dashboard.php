<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /rmit-smartcampus/auth/login.php");
    exit;
}

$dbConfig = [
  'host'=>'sql100.infinityfree.com',
  'user'=>'if0_40697103',
  'pass'=>'rmitgroups123',
  'name'=>'if0_40697103_rmit_smartcampus'
];

$conn = new mysqli($dbConfig['host'],$dbConfig['user'],$dbConfig['pass'],$dbConfig['name']);
if($conn->connect_error) die("DB Error");
$conn->set_charset("utf8");

$adminId = $_SESSION['user']['id'];

/* ================= ADMIN PROFILE ================= */
$stmt = $conn->prepare("
SELECT u.full_name,u.email,u.institute_code,i.name institute_name
FROM users u
LEFT JOIN institutes i ON i.code=u.institute_code
WHERE u.id=?
");
$stmt->bind_param("i",$adminId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
if(!$profile) die("Admin not found");

$instituteCd = $profile['institute_code'];
$institute   = $profile['institute_name'] ?? 'Unknown Institute';

/* ================= THEME ================= */
$themes = [
  'HIT'=>['color'=>'#1e3c72','logo'=>'/rmit-smartcampus/assets/img/hit-logo.png'],
  'RMIT'=>['color'=>'#667eea','logo'=>'/rmit-smartcampus/assets/img/rmit-logo.png'],
  'RMITC'=>['color'=>'#11998e','logo'=>'/rmit-smartcampus/assets/img/rmitc-logo.png'],
  'CPS'=>['color'=>'#f46b45','logo'=>'/rmit-smartcampus/assets/img/cps-logo.png'],
];
$theme = $themes[$instituteCd] ?? ['color'=>'#667eea','logo'=>'/rmit-smartcampus/assets/img/default-logo.png'];

/* ================= REUSABLE FUNCTION ================= */
function instCount($conn,$sql,$inst){
  $s=$conn->prepare($sql);
  $s->bind_param("s",$inst);
  $s->execute();
  return $s->get_result()->fetch_row()[0] ?? 0;
}

/* ================= KPI COUNTS ================= */
$users    = instCount($conn,"SELECT COUNT(*) FROM users WHERE institute_code=?",$instituteCd);
$students = instCount($conn,"SELECT COUNT(*) FROM students s JOIN users u ON s.user_id=u.id WHERE u.institute_code=?",$instituteCd);
$staff    = instCount($conn,"SELECT COUNT(*) FROM staff f JOIN users u ON f.user_id=u.id WHERE u.institute_code=?",$instituteCd);
$courses  = instCount($conn,"SELECT COUNT(*) FROM staff_courses c JOIN staff f ON c.staff_id=f.id JOIN users u ON f.user_id=u.id WHERE u.institute_code=?",$instituteCd);

/* ================= FINANCIAL METRICS ================= */
$fees = $conn->prepare("
SELECT 
  IFNULL(SUM(p.amount),0) collected,
  IFNULL(SUM(f.due_amount),0) due,
  IFNULL(SUM(f.total_fee),0) total
FROM student_fees f
JOIN students s ON f.student_id=s.id
JOIN users u ON s.user_id=u.id
LEFT JOIN payments p ON p.student_id=s.id
WHERE u.institute_code=?
");
$fees->bind_param("s",$instituteCd);
$fees->execute();
$feesData = $fees->get_result()->fetch_assoc();

$collected = $feesData['collected'];
$due       = $feesData['due'];
$totalFee  = $feesData['total'];

/* ================= STUDENT GROWTH ================= */
$growthStmt = $conn->prepare("
SELECT DATE(created_at) d, COUNT(*) c
FROM students s JOIN users u ON s.user_id = u.id
WHERE u.institute_code=?
GROUP BY DATE(created_at)
ORDER BY d
");
$growthStmt->bind_param("s",$instituteCd);
$growthStmt->execute();
$growthRes = $growthStmt->get_result();

$growthDates = [];
$growthCounts = [];
while ($r = $growthRes->fetch_assoc()) {
    $growthDates[] = $r['d'];
    $growthCounts[] = $r['c'];
}

/* ================= FEES TREND ================= */
$feesTrendStmt = $conn->prepare("
SELECT DATE_FORMAT(payment_date,'%b %Y') m, SUM(amount) total
FROM payments p
JOIN students s ON p.student_id = s.id
JOIN users u ON s.user_id = u.id
WHERE u.institute_code=?
GROUP BY m ORDER BY p.payment_date
");
$feesTrendStmt->bind_param("s",$instituteCd);
$feesTrendStmt->execute();
$feesTrendRes = $feesTrendStmt->get_result();
$feesTrend = [];
while($r = $feesTrendRes->fetch_assoc()) $feesTrend[] = $r;

/* ================= PERFORMANCE DISTRIBUTION ================= */
$gradeStmt = $conn->prepare("
SELECT sr.grade, COUNT(*) c
FROM student_results sr
JOIN students s ON sr.student_id=s.id
JOIN users u ON s.user_id=u.id
WHERE u.institute_code=?
GROUP BY sr.grade
");
$gradeStmt->bind_param("s",$instituteCd);
$gradeStmt->execute();
$gradeRes = $gradeStmt->get_result();
$gradeData = [];
while($r = $gradeRes->fetch_assoc()) $gradeData[] = $r;

/* ================= ACTIVITY FEED ================= */
$activityStmt = $conn->prepare("
SELECT 'student' type, a.activity, a.created_at, u.full_name
FROM student_activity a
JOIN students s ON a.student_id=s.id
JOIN users u ON s.user_id=u.id
WHERE u.institute_code=?

UNION ALL

SELECT 'staff' type, a.activity, a.created_at, u.full_name
FROM staff_activity a
JOIN staff f ON a.staff_id=f.id
JOIN users u ON f.user_id=u.id
WHERE u.institute_code=?

ORDER BY created_at DESC
LIMIT 10
");
$activityStmt->bind_param("ss",$instituteCd,$instituteCd);
$activityStmt->execute();
$activityFeed = $activityStmt->get_result();

/* ================= ALERTS ================= */
$lowAttendance = instCount($conn,"
SELECT COUNT(*) FROM student_attendance a
JOIN students s ON a.student_id=s.id
JOIN users u ON s.user_id=u.id
WHERE u.institute_code=? AND a.attendance_percent < 75
",$instituteCd);

$pendingTasks = instCount($conn,"
SELECT COUNT(*) FROM staff_tasks t
JOIN staff f ON t.staff_id=f.id
JOIN users u ON f.user_id=u.id
WHERE u.institute_code=? AND t.status='pending'
",$instituteCd);

$overdueFees = instCount($conn,"
SELECT COUNT(*) FROM student_fees f
JOIN students s ON f.student_id=s.id
JOIN users u ON s.user_id=u.id
WHERE u.institute_code=? AND f.due_amount > 0
",$instituteCd);

?>

<!DOCTYPE html>

<html lang="en">
    
<head>
    
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<title>RMIT SmartCampus | Admin Dashboard | <?= htmlspecialchars($instituteCd) ?></title>
    
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

<!-- Google Fonts & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="/rmit-smartcampus/assets/css/footer.css?v=1.0" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

/* Dashboard Layout */
.dashboard {
  display: flex;
  gap: 20px;
  padding: 20px;
  margin-top: 80px;
}
.sidebar {
  width: 250px;
  background: #fff;
  border-radius: var(--card-radius);
  padding: 20px;
  min-height: 100vh;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

/*.sidebar {
  background: linear-gradient(135deg, #0d1b4c, #6a1b1a);
  color: #fff;
  min-height: 100vh;
  padding: 20px;
  border-radius: 20px;
}*/
.btn-glass {
  backdrop-filter: blur(6px);
  border: 1px solid rgba(255,255,255,0.2);
  color: #200a78d2;
  transition: 0.3s;
}
.btn-glass:hover {
  transform: scale(1.03);
  box-shadow: 0 4px 12px rgba(255,255,255,0.2);
}
.sidebar h5 {
  margin-bottom: 0.5rem;
}
.sidebar .btn {
  margin-bottom: 10px;
}

/* Main Content */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Cards */
.card {
  border-radius: var(--card-radius);
  padding: 15px;
  color: #fff;
  gap: 20px;
  text-align: center;
  font-weight: 600;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  transition: transform 0.2s;
}
.card:hover {
  transform: translateY(-3px);
}
.kpi-users { background: var(--primary-gradient); }
.kpi-students { background: var(--success-gradient); }
.kpi-staff { background: var(--warning-gradient); }
.kpi-courses { background: var(--danger-gradient); }

/* Financial Cards */
.financial-card {
  color: #fff;
  padding: 15px;
  gap: 20px;
  border-radius: var(--card-radius);
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  text-align: center;
}
.fin-collected { background: var(--success-gradient); }
.fin-due { background: var(--warning-gradient); }
.fin-total { background: var(--danger-gradient); }

/* Charts */
.chart-card {
  background: #fff;
  border-radius: var(--card-radius);
  padding: 20px;
  gap: 20px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

/* Alerts */
.alert-card {
  border-radius: var(--card-radius);
  padding: 15px;
  color: #fff;
}
.alert-low { background: var(--warning-gradient); }
.alert-tasks { background: var(--primary-gradient); }
.alert-fees { background: var(--danger-gradient); }

/* Activity Feed */
.activity-card {
  background: #fff;
  border-radius: var(--card-radius);
  padding: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  max-height: 300px;
  overflow-y: auto;
}
.activity-item {
  border-bottom: 1px solid #eee;
  padding: 8px 0;
  display: flex;
  justify-content: space-between;
}
.activity-item:last-child { border-bottom: none; }

/* Shadow effect on hover */
.btn-hover-shadow:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    transition: box-shadow 0.3s ease;
}

/* Pulse effect */
.btn-pulse {
    position: relative;
    transition: transform 0.2s ease;
}
.btn-pulse:hover {
    transform: scale(1.05);
    animation: pulse 1s infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1.05); }
    50% { transform: scale(1); }
}

/* Gradient background */
.btn-gradient {
    background: linear-gradient(45deg, #6a11cb, #2575fc);
    color: #fff;
    border: none;
}
.btn-gradient:hover {
    opacity: 0.85;
}

/* Scale on hover */
.btn-scale {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.btn-scale:hover {
    transform: scale(1.08);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

/* Rotate icon on hover */
.btn-rotate-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-rotate-icon:hover::before {
    content: "🔐";
    display: inline-block;
    transform: rotate(360deg);
    transition: transform 0.5s ease;
}
    
.row {
  margin-left: -10px;
  margin-right: -10px;
}

.row > [class*="col-"] {
  padding-left: 10px;
  padding-right: 10px;
  margin-bottom: 20px;
}
    
/* ================= RESPONSIVE ================= */

/* Tablet (<= 992px) */
@media (max-width: 992px) {

  .dashboard {
    flex-direction: column;
    display: inline-block;
  	gap: 25px;
  	padding: 10px;
  	margin-top: 120px;
  }

  .sidebar {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  
  .navbar-brand img {
		height: 35px;
		width: 180px;
		margin: 0 05px;
	}

  .sidebar .btn {
    flex: 1 1 48%;
  }
}

/* Mobile (<= 576px) */
@media (max-width: 576px) {

  body {
    font-size: 14px;
  }

  /* Navbar */
  .navbar {
    padding: 0.5rem 1rem;
  }

  .navbar-brand img {
		height: 25px;
		width: 180px;
		margin: 0 05px;
	}

  /* Dashboard padding */
  .dashboard {
    padding: 15px;
    gap: 20px;
  }

  /* Sidebar buttons full width */
  .sidebar {
    padding: 15px;
  }

  .sidebar .btn {
    width: 100%;
    font-size: 14px;
  }

  /* Stack all cards */
  .row > [class*="col-"] {
    flex: 0 0 100%;
    max-width: 100%;
  }

  /* Reduce card padding */
  .card,
  .financial-card,
  .alert-card,
  .chart-card,
  .activity-card {
    padding: 12px;
  }
}

/* ================= SMOOTH UI ================= */
.card,
.financial-card,
.alert-card,
.chart-card,
.activity-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover,
.financial-card:hover,
.alert-card:hover,
.chart-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

</style>
    
</head>
    
<body>

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

<div class="dashboard container-fluid">

  <!-- ============== Sidebar ============== -->
	<div class="sidebar bg-gradient shadow-lg p-3 rounded-4">
  <div class="text-center mb-4">
    <img src="/images/admin-avatar.png" class="rounded-circle mb-2" width="60">
    <h5 class="fw-bold"><?= htmlspecialchars($profile['full_name']) ?></h5>
    <small class="text-light"><?= htmlspecialchars($profile['email']) ?></small>
    <div class="text-muted small"><?= htmlspecialchars($institute) ?></div>
  </div>

  <div class="d-grid gap-2">
    <a href="dashboard.php" class="btn btn-outline-light btn-glass">🏠 Dashboard</a>
    <a href="add_student.php" class="btn btn-primary btn-glass">➕ Add Student</a>
    <a href="view_students.php" class="btn btn-outline-primary btn-glass">👥 View Students</a>
    <a href="add_staff.php" class="btn btn-secondary btn-glass">➕ Add Staff</a>
    <a href="manage_courses.php" class="btn btn-success btn-glass">📚 Manage Courses</a>
    <a href="add_subject.php" class="btn btn-success btn-glass">📖 Assign Subject</a>
    <a href="attendance_panel.php" class="btn btn-warning btn-glass">🗓️ Attendance Panel</a>
    <a href="publish_result.php" class="btn btn-info btn-glass">📤 Publish Result</a>
    <a href="Download_Reports.php" class="btn btn-info btn-glass">📥 Download Reports</a>
    <a href="fee_collection.php" class="btn btn-danger btn-glass">💰 Fee Collection</a>
    <a href="fee_reports.php" class="btn btn-outline-danger btn-glass">📊 Fee Reports</a>
    <a href="notice_board.php" class="btn btn-outline-secondary btn-glass">📢 Notice Board</a>
    <a href="send_communication.php" class="btn btn-outline-dark btn-glass">✉️ Send SMS/Email</a>
    <a href="gov_reports.php" class="btn btn-outline-success btn-glass">📄 Govt Compliance</a>
    <a href="backup_restore.php" class="btn btn-outline-warning btn-glass">🗂️ Backup & Restore</a>
    <a href="portal_settings.php" class="btn btn-outline-light btn-glass">⚙️ Portal Settings</a>
    <a href="branding.php" class="btn btn-outline-light btn-glass">🎨 Theme & Branding</a>
    <a href="manage_roles.php" class="btn btn-dark btn-glass">🧑‍💼 Role Management</a>
    <a href="/rmit-smartcampus/auth/logout.php" class="btn btn-danger mt-3 btn-glass">🚪 Logout</a>
  </div>
</div>
  <!-- ============== Sidebar ============== -->

  <!-- ============== Main Content ==============-->
  <div class="main">

    <!-- KPI Cards -->
    <div class="row g-3 mb-3">
      <h5> Overview</h5>
      <div class="col card kpi-users">Users<br><?= $users ?></div>
      <div class="col card kpi-students">Students<br><?= $students ?></div>
      <div class="col card kpi-staff">Staff<br><?= $staff ?></div>
      <div class="col card kpi-courses">Courses<br><?= $courses ?></div>
    </div>

    <!-- ============== Financial Cards ============== -->
    <div class="row g-3 mb-3">
      <h5>Financial Overview</h5>
      <div class="col financial-card fin-collected">Collected<br>₹<?= number_format($collected) ?></div>
      <div class="col financial-card fin-due">Due<br>₹<?= number_format($due) ?></div>
      <div class="col financial-card fin-total">Total<br>₹<?= number_format($totalFee) ?></div>
    </div>

    <!-- ============== Charts ============== -->
    <div class="row g-3 mb-3">
      <div class="col-md-6 chart-card">
        <h6>Student Growth Trend</h6>
        <canvas id="growthChart"></canvas>
      </div>
      <div class="col-md-6 chart-card">
        <h6>Fees Collection Trend</h6>
        <canvas id="feesTrend"></canvas>
      </div>
    </div>

    <div class="chart-card mb-3">
      <h6>Performance Distribution</h6>
      <canvas id="gradeChart"></canvas>
    </div>

    <!-- ============== Alerts ============== -->
    <div class="row g-3 mb-3">
      <div class="col alert-card alert-low">⚠ Low Attendance: <?= $lowAttendance ?></div>
      <div class="col alert-card alert-tasks">🕒 Pending Tasks: <?= $pendingTasks ?></div>
      <div class="col alert-card alert-fees">💰 Overdue Fees: <?= $overdueFees ?></div>
    </div>

    <!-- ============== Recent Activity ============== -->
    <div class="activity-card mb-3">
      <h6>Recent Activity</h6>
      <?php while($a = $activityFeed->fetch_assoc()): ?>
      <div class="activity-item">
        <span><strong><?= htmlspecialchars($a['full_name']) ?></strong> (<?= $a['type'] ?>) - <?= htmlspecialchars($a['activity']) ?></span>
        <small class="text-muted"><?= date('d M H:i', strtotime($a['created_at'])) ?></small>
      </div>
      <?php endwhile; ?>
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

<!-- Chart.js Scripts -->
<script>
new Chart(document.getElementById('growthChart').getContext('2d'), {
  type: 'line',
  data: {
    labels: <?= json_encode($growthDates) ?>,
    datasets: [{
      label: 'Students Joined',
      data: <?= json_encode($growthCounts) ?>,
      borderColor: '<?= $theme['color'] ?>',
      backgroundColor: 'rgba(<?= hexdec(substr($theme['color'],1,2)) ?>,<?= hexdec(substr($theme['color'],3,2)) ?>,<?= hexdec(substr($theme['color'],5,2)) ?>,0.2)',
      fill: true,
      tension: 0.4
    }]
  },
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});

const feesData = <?= json_encode($feesTrend) ?>;
new Chart(document.getElementById('feesTrend'), {
  type:'line',
  data:{labels:feesData.map(d=>d.m),datasets:[{label:'Fees Collected',data:feesData.map(d=>d.total),borderColor:'#ff9900',backgroundColor:'rgba(255,153,0,0.2)',fill:true,tension:0.4}]},
  options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
});

const gradeData = <?= json_encode($gradeData) ?>;
new Chart(document.getElementById('gradeChart'), {
  type:'pie',
  data:{labels:gradeData.map(g=>g.grade),datasets:[{data:gradeData.map(g=>g.c),backgroundColor:['#4facfe','#00f2fe','#ff512f','#ff5e62','#667eea']}]},
  options:{responsive:true}
});
</script>

</body>


</html>
