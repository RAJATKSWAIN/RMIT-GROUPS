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

$adminName = $_SESSION['user']['name'] ?? 'Admin';
$adminEmail = $_SESSION['user']['email'] ?? '';
$institute = $_SESSION['user']['institute_code'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Download Reports</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root{
  --theme-color: <?= htmlspecialchars($theme['color']) ?>;
}

.card-hover:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0,0,0,.12);
  transition: .25s;
}

.btn-theme {
  background: var(--theme-color);
  color: #fff;
  border: none;
}
.btn-theme:hover {
  opacity: .9;
  color: #fff;
}
</style>
</head>

<body class="bg-light">

<div class="container mt-4">

  <!-- ================= INSTITUTE HEADER ================= -->
  <div class="card mb-4">
    <div class="card-body d-flex align-items-center">
      
      <!-- LOGO -->
      <img src="<?= htmlspecialchars($theme['logo']) ?>"
           alt="Institute Logo"
           style="height:60px"
           class="me-3">

      <!-- TITLE -->
      <div>
        <h4 class="mb-0" style="color:var(--theme-color)">
          📥 Download Reports
        </h4>
        <small class="text-muted">
          <?= htmlspecialchars($institute) ?> |
          Logged in as <b><?= htmlspecialchars($adminName) ?></b>
          (<?= htmlspecialchars($adminEmail) ?>)
        </small>
      </div>
    </div>
  </div>

  <!-- ================= REPORTS GRID ================= -->
  <div class="row g-3">

    <!-- STUDENT LOGIN -->
    <div class="col-md-4">
      <div class="card card-hover h-100">
        <div class="card-body">
          <h6>🎓 Student Login Credentials</h6>
          <p class="small text-muted">Username & default password</p>
          <a href="reports/student_credentials.php" class="btn btn-theme w-100">
            Download CSV
          </a>
        </div>
      </div>
    </div>

    <!-- STUDENT MASTER -->
    <div class="col-md-4">
      <div class="card card-hover h-100">
        <div class="card-body">
          <h6>📘 Student Master Report</h6>
          <p class="small text-muted">Program, semester, contact</p>
          <a href="reports/student_master.php" class="btn btn-outline-secondary w-100">
            Download CSV
          </a>
        </div>
      </div>
    </div>

    <!-- STAFF LOGIN -->
    <div class="col-md-4">
      <div class="card card-hover h-100">
        <div class="card-body">
          <h6>👩‍🏫 Staff Login Credentials</h6>
          <p class="small text-muted">Staff login details</p>
          <a href="reports/staff_credentials.php" class="btn btn-outline-success w-100">
            Download CSV
          </a>
        </div>
      </div>
    </div>

    <!-- STAFF MASTER -->
    <div class="col-md-4">
      <div class="card card-hover h-100">
        <div class="card-body">
          <h6>🧾 Staff Master Report</h6>
          <p class="small text-muted">Department & designation</p>
          <a href="reports/staff_master.php" class="btn btn-outline-dark w-100">
            Download CSV
          </a>
        </div>
      </div>
    </div>

    <!-- UPLOAD LOG -->
    <div class="col-md-4">
      <div class="card card-hover h-100">
        <div class="card-body">
          <h6>⬆ Bulk Upload Logs</h6>
          <p class="small text-muted">Audit & error tracking</p>
          <a href="reports/upload_log.php" class="btn btn-outline-info w-100">
            Download CSV
          </a>
        </div>
      </div>
    </div>

  </div>

  <!-- FOOTER -->
  <div class="mt-4 text-center text-muted small">
    All reports are institute-specific and exported in CSV format.
  </div>

</div>

</body>
</html>

