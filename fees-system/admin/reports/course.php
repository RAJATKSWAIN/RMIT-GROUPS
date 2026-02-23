<!--======================================================
    File Name   : course.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS - Fees Management System | Course Wise Report
    Developed By: TrinityWebEdge
    Date Created: 05-02-2026
    Last Updated: 24-02-2026
    Note         : This page defines the FMS - Fees Management System | Report Module of RMIT Groups website.
=======================================================-->
<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

// 1. Role & Filter Setup
$role = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];
$start_date = $_GET['start_date'] ?? date('Y-01-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$filter_inst = $_GET['filter_inst'] ?? 'ALL';

// Logic for target ID (Admin is locked, Superadmin can choose)
$targetInstId = ($role === 'SUPERADMIN') ? $filter_inst : $sessInstId;

// Fetch Title for Headline
$headlineTitle = "Global System";
if ($targetInstId !== 'ALL') {
    $instRes = $conn->query("SELECT INST_NAME FROM MASTER_INSTITUTES WHERE INST_ID = " . intval($targetInstId));
    if ($instRes && $row = $instRes->fetch_assoc()) {
        $headlineTitle = $row['INST_NAME'];
    }
}

/* =========================================================
   2. COURSE SUMMARY SQL
   ========================================================= */
$summarySql = "SELECT c.COURSE_CODE, c.COURSE_NAME, i.INST_NAME,
                      COUNT(p.PAYMENT_ID) as total_txns, 
                      SUM(p.PAID_AMOUNT) as total_revenue
               FROM COURSES c
               JOIN MASTER_INSTITUTES i ON c.INST_ID = i.INST_ID
               LEFT JOIN STUDENTS s ON c.COURSE_ID = s.COURSE_ID
               LEFT JOIN PAYMENTS p ON s.STUDENT_ID = p.STUDENT_ID 
               WHERE p.PAYMENT_STATUS = 'SUCCESS' 
               AND DATE(p.PAYMENT_DATE) BETWEEN ? AND ?";

if ($targetInstId !== 'ALL') {
    $summarySql .= " AND c.INST_ID = " . intval($targetInstId);
}
$summarySql .= " GROUP BY c.COURSE_ID ORDER BY total_revenue DESC";

$sStmt = $conn->prepare($summarySql);
$sStmt->bind_param("ss", $start_date, $end_date);
$sStmt->execute();
$summaryResult = $sStmt->get_result();

/* =========================================================
   3. STUDENT-LEVEL DETAIL SQL
   ========================================================= */
$detailSql = "SELECT s.REGISTRATION_NO, s.FIRST_NAME, s.LAST_NAME, c.COURSE_CODE, i.INST_NAME,
                     SUM(p.PAID_AMOUNT) as student_total
              FROM PAYMENTS p
              JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID
              JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
              JOIN MASTER_INSTITUTES i ON s.INST_ID = i.INST_ID
              WHERE p.PAYMENT_STATUS = 'SUCCESS'
              AND DATE(p.PAYMENT_DATE) BETWEEN ? AND ?";

if ($targetInstId !== 'ALL') {
    $detailSql .= " AND s.INST_ID = " . intval($targetInstId);
}
$detailSql .= " GROUP BY s.STUDENT_ID ORDER BY c.COURSE_CODE ASC, student_total DESC";

$dStmt = $conn->prepare($detailSql);
$dStmt->bind_param("ss", $start_date, $end_date);
$dStmt->execute();
$detailResult = $dStmt->get_result();

// Get Institutes for filter dropdown
$instList = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES") : null;
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-0"><?= htmlspecialchars($headlineTitle) ?></h3>
            <p class="text-muted small">Course-Wise Revenue Breakdown: <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?></p>
        </div>
        <div class="col-md-6 text-end d-print-none">
            <form class="d-inline-flex gap-2">
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $start_date ?>">
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $end_date ?>">
                <?php if ($role === 'SUPERADMIN'): ?>
                    <select name="filter_inst" class="form-select form-select-sm">
                        <option value="ALL">All Institutes</option>
                        <?php while($ins = $instList->fetch_assoc()): ?>
                            <option value="<?= $ins['INST_ID'] ?>" <?= ($filter_inst == $ins['INST_ID']) ? 'selected' : '' ?>>
                                <?= $ins['INST_NAME'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-grid-3x3-gap text-primary me-2"></i>Course Summary</h6>
        </div>
        <div class="card-body">
            <table id="summaryTable" class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <?php if($role === 'SUPERADMIN'): ?><th>Institute</th><?php endif; ?>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th class="text-center">Transactions</th>
                        <th class="text-end">Revenue (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grand_total = 0; while($row = $summaryResult->fetch_assoc()): $grand_total += $row['total_revenue']; ?>
                    <tr>
                        <?php if($role === 'SUPERADMIN'): ?><td><small><?= $row['INST_NAME'] ?></small></td><?php endif; ?>
                        <td class="fw-bold text-primary"><?= $row['COURSE_CODE'] ?></td>
                        <td><?= $row['COURSE_NAME'] ?></td>
                        <td class="text-center"><?= $row['total_txns'] ?></td>
                        <td class="text-end fw-bold">₹<?= number_format($row['total_revenue'], 2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <td colspan="<?= ($role === 'SUPERADMIN') ? '4' : '3' ?>" class="text-end">Grand Total:</td>
                        <td class="text-end">₹<?= number_format($grand_total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill text-primary me-2"></i>Student Breakdown</h6>
        </div>
        <div class="card-body">
            <table id="courseDetailTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <?php if($role === 'SUPERADMIN'): ?><th>Institute</th><?php endif; ?>
                        <th>Course</th>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th class="text-end">Total Paid (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $detailResult->fetch_assoc()): ?>
                    <tr>
                        <?php if($role === 'SUPERADMIN'): ?><td><small><?= $row['INST_NAME'] ?></small></td><?php endif; ?>
                        <td><span class="badge bg-secondary"><?= $row['COURSE_CODE'] ?></span></td>
                        <td><?= $row['REGISTRATION_NO'] ?></td>
                        <td><?= strtoupper($row['FIRST_NAME'].' '.$row['LAST_NAME']) ?></td>
                        <td class="text-end fw-bold text-success">₹<?= number_format($row['student_total'], 2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    const dtConfig = {
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm', title: 'Course_Revenue' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', orientation: 'landscape', title: 'Course_Revenue' },
            { extend: 'print', className: 'btn btn-info btn-sm' }
        ]
    };

    $('#summaryTable').DataTable({ ...dtConfig, "paging": false });
    $('#courseDetailTable').DataTable({ ...dtConfig, "pageLength": 25 });
});
</script>

<style>
@media print {
    .sidebar, .btn, form, .dataTables_filter, .dataTables_info, .dataTables_paginate, .dt-buttons {
        display: none !important;
    }
    .container-fluid { width: 100%; margin: 0; padding: 0; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
