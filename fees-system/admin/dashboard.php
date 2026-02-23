<!--======================================================
    File Name   : dashboard.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS - Fees Management System | Dashboard Page
    Developed By: TrinityWebEdge
    Date Created: 05-02-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the FMS - Fees Management System | Dashboard Page of RMIT Groups website.
=======================================================-->

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../core/auth.php";
require_once "../config/db.php";
require_once "../config/audit.php";

checkLogin();

$adminId   = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'];
$role      = $_SESSION['role_name'];
$inst_id   = $_SESSION['inst_id'];

/* =========================================================
   ROLE-AWARE FILTERING LOGIC
   ========================================================= */

// 1. Total Students
$sql_students = ($role === 'SUPERADMIN') 
    ? "SELECT COUNT(*) c FROM STUDENTS WHERE STATUS='A'" 
    : "SELECT COUNT(*) c FROM STUDENTS WHERE STATUS='A' AND INST_ID = $inst_id";
$students = $conn->query($sql_students)->fetch_assoc()['c'];

// 2. Total Courses
$sql_courses = ($role === 'SUPERADMIN') 
    ? "SELECT COUNT(*) c FROM COURSES WHERE STATUS='A'" 
    : "SELECT COUNT(*) c FROM COURSES WHERE STATUS='A' AND INST_ID = $inst_id";
$courses = $conn->query($sql_courses)->fetch_assoc()['c'];

// 3. Total Collected (Payments Join Students)
$sql_collected = ($role === 'SUPERADMIN') 
    ? "SELECT IFNULL(SUM(PAID_AMOUNT),0) s FROM PAYMENTS WHERE PAYMENT_STATUS='SUCCESS'" 
    : "SELECT IFNULL(SUM(P.PAID_AMOUNT),0) s FROM PAYMENTS P 
       JOIN STUDENTS S ON P.STUDENT_ID = S.STUDENT_ID 
       WHERE P.PAYMENT_STATUS='SUCCESS' AND S.INST_ID = $inst_id";
$collected = $conn->query($sql_collected)->fetch_assoc()['s'];

// 4. Pending Dues (Ledger Join Students)
$sql_pending = ($role === 'SUPERADMIN') 
    ? "SELECT IFNULL(SUM(BALANCE_AMOUNT),0) s FROM STUDENT_FEE_LEDGER" 
    : "SELECT IFNULL(SUM(L.BALANCE_AMOUNT),0) s FROM STUDENT_FEE_LEDGER L 
       JOIN STUDENTS S ON L.STUDENT_ID = S.STUDENT_ID 
       WHERE S.INST_ID = $inst_id";
$pending = $conn->query($sql_pending)->fetch_assoc()['s'];

// 5. Today's Collection
$sql_today = ($role === 'SUPERADMIN') 
    ? "SELECT IFNULL(SUM(PAID_AMOUNT),0) s FROM PAYMENTS WHERE DATE(PAYMENT_DATE)=CURDATE() AND PAYMENT_STATUS='SUCCESS'" 
    : "SELECT IFNULL(SUM(P.PAID_AMOUNT),0) s FROM PAYMENTS P 
       JOIN STUDENTS S ON P.STUDENT_ID = S.STUDENT_ID 
       WHERE DATE(P.PAYMENT_DATE)=CURDATE() AND P.PAYMENT_STATUS='SUCCESS' AND S.INST_ID = $inst_id";
$today = $conn->query($sql_today)->fetch_assoc()['s'];

/* =========================================================
   AUDIT & LOGS
   ========================================================= */
audit_log($conn, 'VIEW_DASHBOARD', 'ADMIN_MASTER', $adminId);

// Logs Visibility: Superadmin sees all logs, Admin sees only their own actions
$sql_logs = ($role === 'SUPERADMIN')
    ? "SELECT ACTION_TYPE, CREATED_AT FROM AUDIT_LOG ORDER BY CREATED_AT DESC LIMIT 5"
    : "SELECT ACTION_TYPE, CREATED_AT FROM AUDIT_LOG WHERE ADMIN_ID = ORDER BY CREATED_AT DESC LIMIT 5";
$logs = $conn->query($sql_logs);

/* =========================================================
   Fetch Branding: Superadmin (Global) vs Admin (Institute)
========================================================= */

if ($role === 'SUPERADMIN') {
    // Global System Branding for Superadmin
    $instBranding = [
        'INST_NAME'   => 'RMIT GROUP OF INSTITUTIONS',
        'INST_CODE'   => 'FMS ver-1.0.0',
        'LOGO_URL'    => 'https://rmitgroupsorg.infinityfree.me/fees-system/assets/logos/fms_logo1.png', // Main group logo
        'BRAND_COLOR' => '#1a3a5a'          // Corporate dark blue
    ];
} else {
    // Fetch Specific Institute Branding for Admins
    $stmt = $conn->prepare("
        SELECT i.INST_NAME, i.INST_CODE, d.LOGO_URL, i.BRAND_COLOR 
        FROM MASTER_INSTITUTES i 
        LEFT JOIN MASTER_INSTITUTE_DTL d ON i.INST_ID = d.INST_ID 
        WHERE i.INST_ID = ?
    ");
    $stmt->bind_param("i", $inst_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $instBranding = $result->fetch_assoc();

    // Fallback if institute details are missing
    if (!$instBranding) {
        $instBranding = [
            'INST_NAME'   => 'RMIT Group',
            'INST_CODE'   => 'RMIT',
            'LOGO_URL'    => 'images/default_logo.png',
            'BRAND_COLOR' => '#0d47a1'
        ];
    }
}

?>

<!DOCTYPE html>

<html>

<head>

<!-- Essential Meta -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- SEO Title -->
<title>Admin Dashboard | Fees Management System (FMS)</title>

<!-- SEO Meta Tags -->
<meta name="description" content="Secure Admin Dashboard for the Fees Management System (FMS). Manage students, fee collection, reports, and audits with a modern ERP interface.">
<meta name="keywords" content="Fees Management System, FMS, Admin Dashboard, Student Fees, ERP, College ERP, School ERP, Fee Collection, Education Management">
<meta name="author" content="TrinityWebEdge">

<!-- Retina PNG versions -->
<link rel="icon" href="images/favicon_32.png" sizes="32x32" type="image/png">
<link rel="icon" href="images/favicon_64.png" sizes="64x64" type="image/png">
<link rel="icon" href="images/favicon_180.png" sizes="180x180" type="image/png">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'> <text y='12' font-size='8' fill='white'>FMS</text></svg>">

<!-- Modern UI Frameworks -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link type="text/css" rel="stylesheet" href="css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">

	<style>
		:root {--sidebar-width: 260px;}
	
		body {background: #f4f6fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden;    }
	
		/* --- SIDEBAR LOGIC --- */
		.sidebar {
			width: var(--sidebar-width);
			height: 100vh;
			position: fixed;
			left: 0;
			top: 0;
			background: #1f2937;
			color: #fff;
			overflow-y: auto;
			transition: all 0.3s ease;
			z-index: 1050; /* Above everything */
		}
	
		/* --- MAIN CONTENT LOGIC --- */
		.main {
			margin-left: var(--sidebar-width);
			padding: 25px;
			min-height: 100vh; /* Minimum height of the full screen */
			display: flex;
    		flex-direction: column; /* Stacks content and footer vertically */
			transition: all 0.3s ease;
		}
	
		/* --- MOBILE STATES --- */
		@media (max-width: 992px) {
			.sidebar {
				left: calc(-1 * var(--sidebar-width));
			}
			.main { margin-left: 0;}
			/* When active, slide sidebar in */
			.sidebar.active {left: 0;}
			/* Overlay to darken background when sidebar is open */
			.sidebar-overlay { display: none;  position: fixed;  width: 100vw;  height: 100vh; background: rgba(0,0,0,0.5);  z-index: 1040; }
			.sidebar-overlay.active {display: block;}
		}
	
		/* Sidebar Styling */
		.section-title { font-size: 12px; margin-top: 3px; color: #6c757d; font-weight: 800; padding: 15px 20px 5px 20px; text-transform: uppercase; letter-spacing: 1.2px;}
		.sidebar a { display: block; color: #d1d5db; text-decoration: none; padding: 10px 15px; font-size: 12px; transition: 0.5s;}
		.sidebar a:hover { background: #374151; color: #fbbf24; padding-left: 22px; /*text-decoration: underline;  underline only on hover */ }
		.sidebar a i, .sidebar a bi { margin-right: 10px; width: 20px; text-align: center; }
	
		/* Top Mobile Bar */
		.mobile-top-nav {display: none; background: #fff; padding: 10px 15px; border-bottom: 1px solid #dee2e6; }
		@media (max-width: 992px) {
			.mobile-top-nav { display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1000; }
		}
		.card-box{ border-radius:14px;box-shadow:0 4px 10px rgba(0,0,0,.06)}
		.stat{font-size:22px;font-weight:600}
	</style>
	
</head>


<body>

<!-- =====================================================
   SIDEBAR (ALL FEATURES)
===================================================== -->
<div class="mobile-top-nav">
    <button class="btn btn-dark" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="fw-bold"></span>
    <div style="width: 40px;"></div> </div>

<div class="sidebar-overlay" id="overlay"></div>

<div class="sidebar" id="sidebar">

  <!-- Brand Box -->
  <div class="sidebar-brand text-center p-0 border-bottom" style="background:#0d47a1; color:#fff;">

    <!-- Logo -->
    <img src="/images/logo.png" alt="College Logo" class="img-fluid mb-0" style="height:50px;width:auto;object-fit:contain;">
    <!-- Icon + Title -->
    <i class="bi bi-cash-stack fs-2 text-primary mb-0"></i>
    <h6 class="text-white mb-0">Fees Management System</h6>
    <p class="badge bg-primary"> Ver 1.0.0 </p>
  </div>

	<a href="dashboard.php">🏠 Dashboard</a>
	
	<div class="section-title">Students</div>
	<a href="students/add.php"><i class="bi bi-person-plus"></i> Add Student</a>
	<a href="students/list.php"><i class="bi bi-people"></i> View / Edit</a>
	<a href="students/profile.php"><i class="bi bi-person-badge"></i> View Profile</a>
	<a href="students/disable.php"><i class="bi bi-person-x"></i> Disable Student</a>
	<a href="students/bulk_upload.php"><i class="bi bi-upload"></i> CSV Upload</a>
	<a href="students/template.php"><i class="bi bi-download"></i> Download Template</a>
	
	<div class="section-title">Courses</div>
	<a href="courses/add.php"><i class="bi bi-journal-plus"></i> Add Course</a>
	<a href="courses/list.php"><i class="bi bi-journal-text"></i> Edit Course</a>
	<a href="courses/bulk_upload.php"><i class="bi bi-upload"></i> Bulk Upload</a>
	
	<div class="section-title">Fees Configuration</div>
	<a href="fees/header_add.php"><i class="bi bi-wallet2"></i> Fee Header</a>
	<a href="fees/map_course.php"><i class="bi bi-link"></i> Assign Fee to Course</a>
	<a href="fees/bulk_map.php"><i class="bi bi-upload"></i> Bulk Mapping CSV</a>
	<a href="fees/copy_structure.php"><i class="bi bi-files"></i> Duplicate Fee Structure</a>
	
	<div class="section-title">Collections</div>
	<a href="payments/collect.php"><i class="bi bi-cash-stack"></i> Collect Payment</a>
	<a href="payments/receipt.php"><i class="bi bi-receipt"></i> Print Receipt</a>
	<a href="payments/receipt_history.php"><i class="bi bi-journal-text"></i>Receipt History</a>
	<a href="payments/bulk_bank_upload.php"><i class="bi bi-bank"></i> Bank/UPI Upload</a>
	<a href="payments/bulk_invoice.php"><i class="bi bi-file-earmark-text"></i> Bulk Invoice Print</a>
	
	<div class="section-title">Reports</div>
	<a href="reports/total.php"><i class="bi bi-bar-chart-line"></i> Total Collection</a>
	<a href="reports/course.php"><i class="bi bi-bar-chart"></i> Course Wise</a>
	<a href="reports/student.php"><i class="bi bi-person-lines-fill"></i> Student Wise</a>
	<a href="reports/daily.php"><i class="bi bi-calendar-day"></i> Daily Summary</a>
	<a href="reports/dues.php"><i class="bi bi-exclamation-triangle"></i> Pending Dues</a>
	
	<?php if ($_SESSION['role_name'] === 'SUPERADMIN'): ?>
		<div class="section-title">System Security</div>
		<a href="audit/view.php"><i class="bi bi-shield-lock"></i> Audit Log Viewer</a>
		<a href="backup/backup.php"><i class="bi bi-hdd"></i> Backup DB</a>
		<a href="backup/restore.php"><i class="bi bi-arrow-repeat"></i> Restore Backup</a>
		
		<div class="section-title">Global Settings</div>
		<a href="institutes/manage.php"><i class="bi bi-building"></i> Manage Institutes</a>
	<?php endif; ?>
	
	<hr>
	<a href="logout.php" class="text-danger mb-3"><i class="bi bi-box-arrow-right"></i> Logout</a>

	</div>

	<!-- Start Of Brand Heading-->
	<div class="portal-header-bg p-1" style=" background:#2c3e50; border-bottom: 5px solid #ffc107; text-align:right;">
	<a href="dashboard.php" 
		style="font-family:'Raleway',sans-serif; font-weight:800; text-decoration:none; display:inline-block; color:#ffffff; font-size:1rem;">
		Edu<span style="color:#ffc107;">Remit™</span>
	</a>
	<span style="display:block; font-size:0.45rem; letter-spacing:3px; text-transform:uppercase; opacity:0.5; font-weight:bold; margin-top:0; color:#ffffff;">
		By TrinityWebEdge
	</span>
	</div>
	<!-- End Of Brand Heading-->    

<!-- =====================================================
   MAIN CONTENT
===================================================== -->
<div class="main">

    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom shadow-none" style="border-bottom: 2px solid #eee !important;">
        <div class="d-flex align-items-center">
            <div class="me-4 p-0 bg-white border rounded-circle shadow-sm" style="width: 95px; height: 95px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <img src="<?= $instBranding['LOGO_URL'] ?>" alt="Institute Seal" style="max-width: 95%; max-height: 95%; object-fit: contain;">
            </div>
            
            <div>
                <h2 class="mb-1 fw-bold text-uppercase" style="color: #1a3a5a; letter-spacing: -0.5px; font-size: 1.6rem;">
                    <?= $instBranding['INST_NAME'] ?>
                </h2>
                
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <span class="text-secondary small fw-semibold">
                        <i class="bi bi-mortarboard-fill me-1"></i> <?= $instBranding['INST_CODE'] ?> Group
                    </span>
                    <span class="text-secondary small fw-semibold border-start ps-3">
                        <i class="bi bi-calendar-check me-1"></i> Session: <?= date('Y') ?>-<?= date('y')+1 ?>
                    </span>
                    <span class="text-secondary small fw-semibold border-start ps-3">
    					<i class="bi bi-shield-lock me-1"></i> Auth: <strong><?= htmlspecialchars($adminName) ?></strong> 
    					<span class="badge rounded-pill bg-dark ms-1" style="font-size: 0.65rem;">
        				<?= ($role === 'SUPERADMIN') ? "System Master" : $role ?>
    					</span>
					</span>
                </div>
            </div>
        </div>

        <div class="text-end d-none d-lg-block border-start ps-4">
            <div class="small text-uppercase text-muted fw-bold mb-1" style="letter-spacing: 1px;">Portal Status</div>
            <div class="d-flex align-items-center justify-content-end text-success fw-bold">
                <span class="spinner-grow spinner-grow-sm me-2" role="status"></span>
                SYSTEM ACTIVE
            </div>
            <div class="small text-muted mt-1"><?= date('l, d F Y') ?></div>
        </div>
    </div>

<!--<h3>Welcome, <?= $adminName ?></h3> -->
<h3 class="mb-1">Fees Management System Overview</h3><br>


<!-- ================= KPI CARDS ================= -->

<div class="row g-3">

<div class="col-md-3">
<div class="card card-box p-3">
<div>Total Students</div>
<div class="stat"><?= $students ?></div>
</div>
</div>

<div class="col-md-3">
<div class="card card-box p-3">
<div>Total Collection</div>
<div class="stat">₹<?= number_format($collected,2) ?></div>
</div>
</div>

<div class="col-md-3">
<div class="card card-box p-3">
<div>Pending Dues</div>
<div class="stat text-danger">₹<?= number_format($pending,2) ?></div>
</div>
</div>

<div class="col-md-3">
<div class="card card-box p-3">
<div>Today Collection</div>
<div class="stat text-success">₹<?= number_format($today,2) ?></div>
</div>
</div>

</div>



<!-- ================= QUICK ACTIONS ================= -->

<div class="card card-box mt-4 p-3">
<h6>Quick Actions</h6>

<a href="students/add.php" class="btn btn-primary btn-sm">Add Student</a>
<a href="payments/collect.php" class="btn btn-success btn-sm">Collect Payment</a>
<a href="fees/header_add.php" class="btn btn-warning btn-sm">Add Fee</a>
<a href="reports/daily.php" class="btn btn-info btn-sm">Daily Report</a>
</div>



<!-- ================= RECENT PAYMENTS ================= -->

<div class="card card-box mt-4 p-3">
    <h6>Recent Payments</h6>
    <table class="table table-sm table-bordered mt-2">
        <thead class="table-light">
            <tr>
                <th>Receipt</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Role-Aware Recent Payments Query
        $sql_recent = ($role === 'SUPERADMIN') 
            ? "SELECT P.RECEIPT_NO, CONCAT(S.FIRST_NAME,' ',S.LAST_NAME) STU, P.PAID_AMOUNT, P.PAYMENT_DATE 
               FROM PAYMENTS P 
               JOIN STUDENTS S ON S.STUDENT_ID = P.STUDENT_ID 
               ORDER BY P.PAYMENT_ID DESC LIMIT 10"
            : "SELECT P.RECEIPT_NO, CONCAT(S.FIRST_NAME,' ',S.LAST_NAME) STU, P.PAID_AMOUNT, P.PAYMENT_DATE 
               FROM PAYMENTS P 
               JOIN STUDENTS S ON S.STUDENT_ID = P.STUDENT_ID 
               WHERE S.INST_ID = $inst_id 
               ORDER BY P.PAYMENT_ID DESC LIMIT 10";

        $q_recent = $conn->query($sql_recent);

        if($q_recent->num_rows > 0):
            while($r = $q_recent->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($r['RECEIPT_NO']) ?></td>
                <td><?= htmlspecialchars($r['STU']) ?></td>
                <td>₹<?= number_format($r['PAID_AMOUNT'], 2) ?></td>
                <td><?= date('d-m-Y', strtotime($r['PAYMENT_DATE'])) ?></td>
            </tr>
        <?php 
            endwhile; 
        else:
            echo "<tr><td colspan='4' class='text-center text-muted'>No recent payments found.</td></tr>";
        endif;
        ?>
        </tbody>
    </table>
	</div>



<!-- ================= COURSE COLLECTION REPORT ================= -->

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Course Wise Collection</h6>
        <span class="badge bg-light text-dark"><?= date('d M Y') ?></span>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-start">Course</th>
                    <th>Students</th>
                    <th>Collection</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $totalStudents = 0;
            $totalAmount   = 0;

            // Role-Aware Course Report Query
            $sql_course_report = ($role === 'SUPERADMIN')
                ? "SELECT C.COURSE_NAME, COUNT(DISTINCT S.STUDENT_ID) students, IFNULL(SUM(P.PAID_AMOUNT),0) amount 
                   FROM COURSES C 
                   LEFT JOIN STUDENTS S ON S.COURSE_ID = C.COURSE_ID 
                   LEFT JOIN PAYMENTS P ON P.STUDENT_ID = S.STUDENT_ID AND P.PAYMENT_STATUS = 'SUCCESS'
                   GROUP BY C.COURSE_ID"
                : "SELECT C.COURSE_NAME, COUNT(DISTINCT S.STUDENT_ID) students, IFNULL(SUM(P.PAID_AMOUNT),0) amount 
                   FROM COURSES C 
                   LEFT JOIN STUDENTS S ON S.COURSE_ID = C.COURSE_ID 
                   LEFT JOIN PAYMENTS P ON P.STUDENT_ID = S.STUDENT_ID AND P.PAYMENT_STATUS = 'SUCCESS'
                   WHERE C.INST_ID = $inst_id 
                   GROUP BY C.COURSE_ID";

            $q_course = $conn->query($sql_course_report);

            while($r = $q_course->fetch_assoc()):
                $totalStudents += $r['students'];
                $totalAmount   += $r['amount'];
            ?>
                <tr>
                    <td class="text-start fw-semibold"><?= htmlspecialchars($r['COURSE_NAME']) ?></td>
                    <td><span class="badge bg-info"><?= $r['students'] ?></span></td>
                    <td class="text-success fw-bold">₹<?= number_format($r['amount'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
            <tfoot class="table-secondary fw-bold">
                <tr>
                    <td class="text-start">Total</td>
                    <td><?= $totalStudents ?></td>
                    <td class="text-success">₹<?= number_format($totalAmount, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>



<!-- ================= RECENT ADMIN ACTIVITY ================= -->

<div class="card shadow-sm border-0 mt-5">

    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fas fa-history me-2"></i> 
            <?= ($role === 'SUPERADMIN') ? "Global System Activity" : "My Recent Activity" ?>
        </h6>
        <i class="bi bi-shield-check"></i>
    </div>

    <div class="card-body p-0">

        <table class="table table-hover table-sm align-middle mb-0 text-center">
            <thead class="table-light">
                <tr>
                    <?php if ($role === 'SUPERADMIN'): ?>
                        <th class="text-start ps-3">Admin</th>
                    <?php endif; ?>
                    <th class="<?= ($role === 'SUPERADMIN') ? 'text-start' : '' ?>">Action</th>
                    <th>Time</th>
                </tr>
            </thead>

            <tbody>

            <?php
            // ROLE-AWARE LOG QUERY
            // Superadmin: See everything + Join with Admin table to see WHO did it
            // Admin: See only their own ID logs
            if ($role === 'SUPERADMIN') {
                $sql_logs = "SELECT L.ACTION_TYPE, L.CREATED_AT, A.FULL_NAME 
                             FROM AUDIT_LOG L 
                             JOIN ADMIN_MASTER A ON L.ADMIN_ID = A.ADMIN_ID 
                             ORDER BY L.CREATED_AT DESC LIMIT 5";
            } else {
                $sql_logs = "SELECT L.ACTION_TYPE, L.CREATED_AT, A.FULL_NAME 
                             FROM AUDIT_LOG L 
                             JOIN ADMIN_MASTER A ON L.ADMIN_ID = A.ADMIN_ID 
							 AND A.ADMIN_ID = $adminId 
                             ORDER BY L.CREATED_AT DESC LIMIT 5";
            }

            $q_logs = $conn->query($sql_logs);

            if ($q_logs && $q_logs->num_rows > 0):
                while($l = $q_logs->fetch_assoc()): 
            ?>
            <tr>
                <?php if ($role === 'SUPERADMIN'): ?>
                    <td class="text-start ps-3 small fw-bold text-primary">
                        <?= htmlspecialchars($l['FULL_NAME']) ?>
                    </td>
                <?php endif; ?>

                <td class="<?= ($role === 'SUPERADMIN') ? 'text-start' : '' ?>">
                    <span class="badge rounded-pill bg-secondary px-3" style="font-size: 0.7rem;">
                        <?= str_replace('_', ' ', htmlspecialchars($l['ACTION_TYPE'])) ?>
                    </span>
                </td>
                <td class="text-muted small">
                    <?= date('d M, h:i A', strtotime($l['CREATED_AT'])) ?>
                </td>
            </tr>
            <?php 
                endwhile; 
            else:
                echo "<tr><td colspan='3' class='py-3 text-muted'>No recent activity recorded.</td></tr>";
            endif;
            ?>

            </tbody>
        </table>
    </div>
    <?php if ($role === 'SUPERADMIN'): ?>
        <div class="card-footer bg-white text-center">
            <a href="audit/view.php" class="small text-decoration-none">View Full Audit Trail</a>
        </div>
    <?php endif; ?>
</div>
   
<!--========= Start of footer  ==========-->    
<footer class="mt-auto pt-5">
    <hr class="text-muted opacity-25">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted small">
                    &copy; <?= date('Y'); ?> 
                    <strong>
                        <span style="color: black;">RMIT</span> 
                        <span style="color: red;">GROUP OF INSTITUTIONS</span>
                    </strong>. All Rights Reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-black-50" style="font-size: 0.95rem; letter-spacing: 0.3px;">
            		&copy;<span class="fw-bold">EduRemit™</span> | 
            		Product of <a href="#" class="text-decoration-none text-secondary fw-semibold">TrinityWebEdge</a>
        		</small>
            </div>
        </div>
    </div>
</footer>
<!--========= End of footer  ==========-->        
</div> 
       
<!--========= All JS Script ==========--> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
    
<script>
    const btn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    function toggleMenu() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    btn.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu); // Close when clicking outside
</script>
    
</body>

</html>
