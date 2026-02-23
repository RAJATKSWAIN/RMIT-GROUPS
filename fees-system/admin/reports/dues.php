<!--======================================================
    File Name   : dues.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS - Fees Management System | Total Due Page
    Developed By: TrinityWebEdge
    Date Created: 05-02-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note         : This page defines the FMS - Fees Management System | Report Page of RMIT Groups website.
=======================================================-->
<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

// 1. Role & Session Setup
$role       = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];

// 2. Filters
$filter_inst = $_GET['filter_inst'] ?? (($role === 'SUPERADMIN') ? 'ALL' : $sessInstId);
$course_filter = $_GET['course_id'] ?? '';

// 3. Build Query
$sql = "SELECT s.STUDENT_ID, s.REGISTRATION_NO, s.FIRST_NAME, s.LAST_NAME, s.MOBILE,
               c.COURSE_NAME, c.COURSE_CODE, l.TOTAL_FEE, l.BALANCE_AMOUNT, i.INST_NAME 
        FROM STUDENTS s
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
        JOIN MASTER_INSTITUTES i ON s.INST_ID = i.INST_ID
        WHERE l.BALANCE_AMOUNT > 0 AND s.STATUS = 'A'";

// Apply Institute Isolation
if ($role !== 'SUPERADMIN') {
    $sql .= " AND s.INST_ID = $sessInstId";
} elseif ($filter_inst !== 'ALL') {
    $sql .= " AND s.INST_ID = " . intval($filter_inst);
}

// Apply Course Filter
if ($course_filter) {
    $sql .= " AND s.COURSE_ID = " . intval($course_filter);
}

$sql .= " ORDER BY l.BALANCE_AMOUNT DESC";
$result = $conn->query($sql);

// Fetch dependent dropdown data
$colleges = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES") : null;
$courseSql = ($role !== 'SUPERADMIN') ? "SELECT * FROM COURSES WHERE INST_ID = $sessInstId" : "SELECT * FROM COURSES " . ($filter_inst !== 'ALL' ? "WHERE INST_ID = $filter_inst" : "");
$courses = $conn->query($courseSql);
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white d-flex flex-wrap justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-exclamation-octagon me-2"></i>Pending Dues Report</h5>
            
            <form class="d-flex gap-2 mt-2 mt-md-0" id="filterForm">
                <?php if ($role === 'SUPERADMIN'): ?>
                <select name="filter_inst" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="ALL">All Colleges</option>
                    <?php while($ins = $colleges->fetch_assoc()): ?>
                        <option value="<?= $ins['INST_ID'] ?>" <?= ($filter_inst == $ins['INST_ID']) ? 'selected' : '' ?>><?= $ins['INST_NAME'] ?></option>
                    <?php endwhile; ?>
                </select>
                <?php endif; ?>

                <select name="course_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    <?php while($c = $courses->fetch_assoc()): ?>
                        <option value="<?= $c['COURSE_ID'] ?>" <?= ($course_filter == $c['COURSE_ID']) ? 'selected' : '' ?>><?= $c['COURSE_CODE'] ?></option>
                    <?php endwhile; ?>
                </select>
                <a href="dues.php" class="btn btn-light btn-sm text-danger">Reset</a>
            </form>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table id="duesTable" class="table table-hover table-bordered align-middle">
                    <thead class="table-light text-uppercase small fw-bold">
                        <tr>
                            <?php if($role === 'SUPERADMIN'): ?><th>Institute</th><?php endif; ?>
                            <th>Student Name</th>
                            <th>Reg No</th>
                            <th>Course</th>
                            <th>Mobile</th>
                            <th class="text-end">Total Fee</th>
                            <th class="text-end">Outstanding Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <?php if($role === 'SUPERADMIN'): ?>
                                <td class="small fw-bold"><?= $row['INST_NAME'] ?></td>
                            <?php endif; ?>
                            <td class="fw-bold"><?= strtoupper($row['FIRST_NAME'].' '.$row['LAST_NAME']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= $row['REGISTRATION_NO'] ?></span></td>
                            <td class="small"><?= strtoupper($row['COURSE_CODE']) ?></td>
                            <td><?= $row['MOBILE'] ?></td>
                            <td class="text-end">₹<?= number_format($row['TOTAL_FEE'], 2) ?></td>
                            <td class="text-end fw-bold text-danger">₹<?= number_format($row['BALANCE_AMOUNT'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="<?= ($role === 'SUPERADMIN') ? '5' : '4' ?>" class="text-end">Page Total:</th>
                            <th id="page_total_fee" class="text-end">0.00</th>
                            <th id="page_total_due" class="text-end">0.00</th>
                        </tr>
                        <tr class="table-secondary text-dark">
                            <th colspan="<?= ($role === 'SUPERADMIN') ? '5' : '4' ?>" class="text-end">Grand Total (Filtered):</th>
                            <th id="grand_total_fee" class="text-end">0.00</th>
                            <th id="grand_total_due" class="text-end">0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    var feeCol = <?= ($role === 'SUPERADMIN') ? 5 : 4 ?>;
    var dueCol = <?= ($role === 'SUPERADMIN') ? 6 : 5 ?>;

    $('#duesTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'btn btn-success btn-sm me-1',
                text: '<i class="bi bi-file-earmark-excel"></i> Export Excel',
                footer: true,
                title: 'Pending_Dues_Report_<?= date('Y-m-d') ?>'
            },
            { 
                extend: 'print', 
                className: 'btn btn-info btn-sm', 
                text: '<i class="bi bi-printer"></i> Print',
                footer: true 
            }
        ],
        "pageLength": 25,
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();

            var getVal = function (i) {
                return typeof i === 'string' ? i.replace(/[^\d.-]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            // Calculate Fees
            var pageFee = api.column(feeCol, { page: 'current' }).data().reduce((a, b) => getVal(a) + getVal(b), 0);
            var grandFee = api.column(feeCol).data().reduce((a, b) => getVal(a) + getVal(b), 0);

            // Calculate Dues
            var pageDue = api.column(dueCol, { page: 'current' }).data().reduce((a, b) => getVal(a) + getVal(b), 0);
            var grandDue = api.column(dueCol).data().reduce((a, b) => getVal(a) + getVal(b), 0);

            // Update UI
            $('#page_total_fee').html('₹' + pageFee.toLocaleString(undefined, {minimumFractionDigits: 2}));
            $('#page_total_due').html('₹' + pageDue.toLocaleString(undefined, {minimumFractionDigits: 2}));
            $('#grand_total_fee').html('₹' + grandFee.toLocaleString(undefined, {minimumFractionDigits: 2}));
            $('#grand_total_due').html('₹' + grandDue.toLocaleString(undefined, {minimumFractionDigits: 2}));
        }
    });
});
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
