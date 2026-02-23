<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

// Get the institute ID from the session (populated during login)
$adminId   = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'];
$instId 	= $_SESSION['inst_id'];

$start_date = $_GET['start_date'] ?? date('Y-01-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// 1. Summary SQL
$summarySql = "SELECT c.COURSE_CODE, c.COURSE_NAME, 
                      COUNT(p.PAYMENT_ID) as total_txns, 
                      SUM(p.PAID_AMOUNT) as total_revenue
               FROM COURSES c
               LEFT JOIN STUDENTS s ON c.COURSE_ID = s.COURSE_ID
               LEFT JOIN PAYMENTS p ON s.STUDENT_ID = p.STUDENT_ID 
                    AND p.PAYMENT_STATUS = 'SUCCESS' 
                    AND c.INST_ID = s.INST_ID
                    AND s.INST_ID = $instId
                    AND DATE(p.PAYMENT_DATE) BETWEEN ? AND ?
               GROUP BY c.COURSE_ID
               ORDER BY total_revenue DESC";

$sStmt = $conn->prepare($summarySql);
$sStmt->bind_param("ss", $start_date, $end_date);
$sStmt->execute();
$summaryResult = $sStmt->get_result();

// 2. Detail SQL
$detailSql = "SELECT s.REGISTRATION_NO, s.FIRST_NAME, s.LAST_NAME, c.COURSE_CODE, 
                     SUM(p.PAID_AMOUNT) as student_total
              FROM PAYMENTS p
              JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID
              JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
              WHERE p.PAYMENT_STATUS = 'SUCCESS'
              AND c.INST_ID = s.INST_ID
              AND s.INST_ID = $instId
              AND DATE(p.PAYMENT_DATE) BETWEEN ? AND ?
              GROUP BY s.STUDENT_ID
              ORDER BY c.COURSE_CODE ASC, student_total DESC";

$dStmt = $conn->prepare($detailSql);
$dStmt->bind_param("ss", $start_date, $end_date);
$dStmt->execute();
$detailResult = $dStmt->get_result();
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill text-primary"></i> Course-Wise Revenue Analysis</h4>
            <p class="text-muted small mb-0">Financial summary from <?= date('d M Y', strtotime($start_date)) ?> to <?= date('d M Y', strtotime($end_date)) ?></p>
        </div>
        <div class="col-md-6 text-end">
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm me-2">
                <i class="bi bi-printer"></i> Print Full Page
            </button>
            <form class="d-inline-flex gap-2 bg-white p-2 rounded shadow-sm border">
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $start_date ?>">
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $end_date ?>">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-table"></i> 1. Departmental Revenue Summary</h6>
        </div>
        <div class="card-body">
            <table id="summaryTable" class="table table-hover table-bordered align-middle w-100">
                <thead class="table-light">
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th class="text-center">Total Payments</th>
                        <th class="text-end">Revenue (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grand_total = 0;
                    while($row = $summaryResult->fetch_assoc()): 
                        $grand_total += $row['total_revenue'];
                    ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= $row['COURSE_CODE'] ?></td>
                        <td><?= $row['COURSE_NAME'] ?></td>
                        <td class="text-center"><?= $row['total_txns'] ?></td>
                        <td class="text-end fw-bold text-dark"><?= number_format($row['total_revenue'], 2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">Total Period Revenue:</td>
                        <td class="text-end text-success">₹<?= number_format($grand_total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-people"></i> 2. Student-Level Contribution Breakdown</h6>
        </div>
        <div class="card-body">
            <table id="courseDetailTable" class="table table-striped table-bordered w-100">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th class="text-end">Total Paid (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $detailResult->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?= $row['COURSE_CODE'] ?></span></td>
                        <td class="fw-bold"><?= $row['REGISTRATION_NO'] ?></td>
                        <td><?= strtoupper($row['FIRST_NAME'].' '.$row['LAST_NAME']) ?></td>
                        <td class="text-end fw-bold text-success"><?= number_format($row['student_total'], 2) ?></td>
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
    // 1. Initialize Summary Table with Search and Print
    $('#summaryTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm', text: 'Excel', title: 'Course_Summary' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', text: 'PDF', title: 'Course_Summary' },
            { extend: 'print', className: 'btn btn-info btn-sm', text: 'Print' }
        ],
        "paging": false, // Usually short enough to show all
        "searching": true // Added search for specific courses
    });

    // 2. Initialize Detailed Table
    $('#courseDetailTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm', text: 'Excel', title: 'Course_Student_Details' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', text: 'PDF', title: 'Course_Student_Details' },
            { extend: 'print', className: 'btn btn-info btn-sm', text: 'Print' }
        ],
        "pageLength": 25,
        "order": [[ 0, "asc" ], [ 3, "desc" ]]
    });
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
