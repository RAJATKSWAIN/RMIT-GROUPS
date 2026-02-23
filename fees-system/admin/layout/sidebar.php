<?php
// Calculate the correct path to the root 'admin' folder dynamically
// This ensures that whether you are in /admin/ or /admin/students/, the links stay unique.
$adminRoot = "/fees-system/admin/"; 
$adminName = $_SESSION['admin_name'] ?? 'Admin';

$adminId   = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'];
$role      = $_SESSION['role_name'];
$inst_id   = $_SESSION['inst_id'];

// 1. Get the current URL path
$current_page = $_SERVER['SCRIPT_NAME'];

// 2. Determine the Module Title based on folder name
$module_title = "FMS ADMIN"; // Default title

if (str_contains($current_page, '/students/')) {
    $module_title = "STUDENT MANAGEMENT";
} elseif (str_contains($current_page, '/courses/')) {
    $module_title = "COURSE MANAGEMENT";
} elseif (str_contains($current_page, '/fees/')) {
    $module_title = "FEES CONFIGURATION";
} elseif (str_contains($current_page, '/payments/')) {
    $module_title = "COLLECTIONS";
} elseif (str_contains($current_page, '/reports/')) {
    $module_title = "REPORTS & ANALYTICS";
} elseif (str_contains($current_page, '/dashboard.php')) {
    $module_title = "DASHBOARD";
}

?>

<style>
    /* Desktop Sidebar width */
    :root { --sidebar-w: 260px; }

    /* Mobile Toggle Logic */
    @media (max-width: 992px) {
        .sidebar { left: -260px !important; transition: all 0.3s ease; }
        .sidebar.active { left: 0 !important; }
        .main { margin-left: 0 !important; }
        .mobile-top-nav { display: flex !important; align-items: center; justify-content: space-between; background: #fff; padding: 10px 15px; border-bottom: 1px solid #ddd; position: 		sticky; top: 0; z-index: 1000; }
        .sidebar-overlay.active { display: block !important; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1040; }
    }

    /* Keep your existing Sidebar & Main styles below */
    .sidebar { width: var(--sidebar-w); height: 100vh; position: fixed; background: #1f2937; color: #fff; overflow-y: auto; z-index: 1050; }
    .main { margin-left: var(--sidebar-w); padding: 25px; min-height: 100vh; }
    .mobile-top-nav { display: none; }
    .sidebar-overlay { display: none; }
    /* Ensure the whole page takes up full height */
html, body {
    height: 100%;
    margin: 0;
}

/* Update your .main class to be a Flexbox container */
.main {
    margin-left: var(--sidebar-w); 
    padding: 25px;
    min-height: 100vh; /* Minimum height of the full screen */
    display: flex;
    flex-direction: column; /* Stacks content and footer vertically */
    transition: all 0.3s ease;
}

/* Content area inside .main should grow to fill space */
.content-wrapper {
    flex: 1 0 auto;
}
</style>

<!-- =====================================================
   SIDEBAR (ALL FEATURES)
===================================================== -->
<div class="mobile-top-nav">
    <button class="btn btn-dark" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    
    <span class="fw-bold text-uppercase small" style="letter-spacing: 1px;">
        <?= $module_title ?>
    </span>
    
    <div style="width: 40px;"></div> 
</div>

<div class="sidebar-overlay" id="overlay"></div>

<div class="sidebar" id="sidebar">

  <!-- Brand Box -->
  <div class="sidebar-brand text-center p-0 border-bottom" style="background:#0d47a1; color:#fff;">
    <!-- Logo -->
    <img src="/images/logo.png" alt="College Logo" class="img-fluid mb-0" style="height:40px;width:auto;object-fit:contain;">
    <!-- Icon + Title -->
    <i class="bi bi-cash-stack fs-2 text-primary mb-0"></i>
    <h6 class="text-white mb-0">Fees Management System</h6>
    <small class="text-muted">Admin Dashboard</small>
  </div>

    <a href="<?= $adminRoot ?>dashboard.php">🏠 Dashboard</a>

    <div class="section-title">Students</div>
    <a href="<?= $adminRoot ?>students/add.php"><i class="bi bi-person-plus"></i> Add Student</a>
    <a href="<?= $adminRoot ?>students/list.php"><i class="bi bi-people"></i> View / Edit</a>
    <a href="<?= $adminRoot ?>students/profile.php"><i class="bi bi-person-badge"></i> View Profile</a>
    <a href="<?= $adminRoot ?>students/disable.php"><i class="bi bi-person-x"></i> Disable Student</a>
    <a href="<?= $adminRoot ?>students/bulk_upload.php"><i class="bi bi-upload"></i> CSV Upload</a>

    <div class="section-title">Courses</div>
    <a href="<?= $adminRoot ?>courses/add.php"><i class="bi bi-journal-plus"></i> Add Course</a>
    <a href="<?= $adminRoot ?>courses/list.php"><i class="bi bi-journal-text"></i> Edit Course</a>
    <a href="<?= $adminRoot ?>courses/bulk_upload.php"><i class="bi bi-upload"></i> Bulk Upload</a>
    
    <div class="section-title">Fees Configuration</div>
	<a href="<?= $adminRoot ?>fees/header_add.php"><i class="bi bi-wallet2"></i> Fee Header</a>
	<a href="<?= $adminRoot ?>fees/map_course.php"><i class="bi bi-link"></i> Assign Fee to Course</a>
	<a href="<?= $adminRoot ?>fees/bulk_map.php"><i class="bi bi-upload"></i> Bulk Mapping CSV</a>
	<a href="<?= $adminRoot ?>fees/copy_structure.php"><i class="bi bi-files"></i> Duplicate Fee Structure</a>

    <div class="section-title">Collections</div>
    <a href="<?= $adminRoot ?>payments/collect.php"><i class="bi bi-cash-stack"></i> Collect Payment</a>
	<a href="<?= $adminRoot ?>payments/receipt.php"><i class="bi bi-receipt"></i> Print Receipt</a>
    <a href="<?= $adminRoot ?>payments/receipt_history.php"><i class="bi bi-journal-text"></i> Receipt History</a>
	<a href="<?= $adminRoot ?>payments/bulk_bank_upload.php"><i class="bi bi-bank"></i> Bank/UPI Upload</a>
	<a href="<?= $adminRoot ?>payments/bulk_invoice.php"><i class="bi bi-file-earmark-text"></i> Bulk Invoice Print</a>

    <div class="section-title">Reports</div>
	<a href="<?= $adminRoot ?>reports/total.php"><i class="bi bi-bar-chart-line"></i> Total Collection</a>
	<a href="<?= $adminRoot ?>reports/course.php"><i class="bi bi-bar-chart"></i> Course Wise</a>
	<a href="<?= $adminRoot ?>reports/student.php"><i class="bi bi-person-lines-fill"></i> Student Wise</a>
	<a href="<?= $adminRoot ?>reports/daily.php"><i class="bi bi-calendar-day"></i> Daily Summary</a>
	<a href="<?= $adminRoot ?>reports/dues.php"><i class="bi bi-exclamation-triangle"></i> Pending Dues</a>

	<?php if ($_SESSION['role_name'] === 'SUPERADMIN'): ?>
		<div class="section-title">System Security</div>
		<a href="audit/view.php"><i class="bi bi-shield-lock"></i> Audit Log Viewer</a>
		<a href="backup/backup.php"><i class="bi bi-hdd"></i> Backup DB</a>
		<a href="backup/restore.php"><i class="bi bi-arrow-repeat"></i> Restore Backup</a>
		
		<div class="section-title">Global Settings</div>
		<a href="institutes/manage.php"><i class="bi bi-building"></i> Manage Institutes</a>
	<?php endif; ?>

    <hr>
    <a href="<?= $adminRoot ?>logout.php" class="text-danger mb-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div class="main">
    <h5 class="mb-3">Welcome, <?= htmlspecialchars($adminName) ?></h5>

<script>
    // Toggle Logic for Mobile
    const sBtn = document.getElementById('sidebarToggle');
    const sBar = document.getElementById('sidebar');
    const sOvr = document.getElementById('overlay');

    function toggleAction() {
        sBar.classList.toggle('active');
        sOvr.classList.toggle('active');
    }

    if(sBtn) sBtn.addEventListener('click', toggleAction);
    if(sOvr) sOvr.addEventListener('click', toggleAction);
</script>
