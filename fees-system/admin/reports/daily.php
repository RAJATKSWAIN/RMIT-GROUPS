<!--======================================================
    File Name   : daily.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS - Fees Management System | Total Report Page
    Developed By: TrinityWebEdge
    Date Created: 05-02-2026
    Last Updated: 24-02-2026
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

// 2. Filter Logic
$start_date  = $_GET['start_date'] ?? date('Y-m-d');
$end_date    = $_GET['end_date'] ?? date('Y-m-d');
// Superadmin can pick a college; Admin is locked to their session ID
$filter_inst = $_GET['filter_inst'] ?? (($role === 'SUPERADMIN') ? 'ALL' : $sessInstId);

// 3. Build Role-Aware SQL
$sql = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, s.SEMESTER, 
               c.COURSE_CODE, c.COURSE_NAME, i.INST_NAME
        FROM PAYMENTS p
        JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        JOIN MASTER_INSTITUTES i ON s.INST_ID = i.INST_ID
        WHERE p.PAYMENT_STATUS = 'SUCCESS'
        AND DATE(p.PAYMENT_DATE) BETWEEN ? AND ?";

// Apply Institute Isolation Logic
if ($role !== 'SUPERADMIN') {
    $sql .= " AND s.INST_ID = $sessInstId";
} elseif ($filter_inst !== 'ALL') {
    $sql .= " AND s.INST_ID = " . intval($filter_inst);
}

$sql .= " ORDER BY p.PAYMENT_DATE DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$total_collected = 0;

// Fetch Colleges list only for Superadmin dropdown
$colleges = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES") : null;
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center border-bottom">
            <div class="mb-2 mb-md-0">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-journal-text"></i> Detailed Collection Report</h5>
                <small class="text-muted">
                    Period: <?= date('d M Y', strtotime($start_date)) ?> to <?= date('d M Y', strtotime($end_date)) ?>
                    <?php if($role === 'SUPERADMIN'): ?>
                        | College: <?= ($filter_inst === 'ALL') ? 'All Institutes' : 'Selected Institute' ?>
                    <?php endif; ?>
                </small>
            </div>
            
            <form class="row g-2 align-items-center">
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">From</span>
                        <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">To</span>
                        <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                    </div>
                </div>

                <?php if ($role === 'SUPERADMIN'): ?>
                <div class="col-auto">
                    <select name="filter_inst" class="form-select form-select-sm border-primary">
                        <option value="ALL">-- All Colleges --</option>
                        <?php while($ins = $colleges->fetch_assoc()): ?>
                            <option value="<?= $ins['INST_ID'] ?>" <?= ($filter_inst == $ins['INST_ID']) ? 'selected' : '' ?>>
                                <?= $ins['INST_NAME'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                </div>
            </form>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table id="reportTable" class="table table-hover table-bordered align-middle" style="width:100%">
                    <thead class="table-light text-uppercase small fw-bold">
                        <tr>
                            <th>Date & Time</th>
                            <?php if($role === 'SUPERADMIN'): ?>
                                <th>College</th>
                            <?php endif; ?>
                            <th>Receipt No</th>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th>Course/Sem</th>
                            <th>Fee Items</th>
                            <th>Mode</th>
                            <th>Transaction Ref</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): 
                            $total_collected += $row['PAID_AMOUNT'];
                        ?>
                        <tr>
                            <td style="white-space:nowrap;">
                                <?= date('d-m-Y', strtotime($row['PAYMENT_DATE'])) ?><br>
                                <small class="text-muted"><?= date('h:i A', strtotime($row['PAYMENT_DATE'])) ?></small>
                            </td>
                            <?php if($role === 'SUPERADMIN'): ?>
                                <td class="small fw-bold text-primary"><?= $row['INST_NAME'] ?></td>
                            <?php endif; ?>
                            <td class="fw-bold text-dark"><?= $row['RECEIPT_NO'] ?></td>
                            <td><?= $row['REGISTRATION_NO'] ?></td>
                            <td><?= strtoupper($row['FIRST_NAME'].' '.$row['LAST_NAME']) ?></td>
                            <td><?= $row['COURSE_CODE'] ?> <small class="text-muted">(S-<?= $row['SEMESTER'] ?>)</small></td>
                            <td class="small">
                                <?php echo str_replace(['[', ']', '"', '?'], ['', '', '', '₹'], $row['REMARKS']); ?>
                            </td>
                            <td><span class="badge bg-info text-dark"><?= $row['PAYMENT_MODE'] ?></span></td>
                            <td class="small text-muted"><?= $row['TRANSACTION_ID'] ?? 'N/A' ?></td>
                            <td class="fw-bold text-end">₹<?= number_format($row['PAID_AMOUNT'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="<?= ($role === 'SUPERADMIN') ? '9' : '8' ?>" class="text-end">Total Collection:</th>
                            <th class="text-end">₹<?= number_format($total_collected, 2) ?></th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#reportTable').DataTable({
        "order": [[ 0, "desc" ]],
        "pageLength": 25,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'btn btn-success btn-sm me-1',
                text: '<i class="bi bi-file-earmark-excel"></i> Export Excel',
                footer: true
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm me-1',
                text: '<i class="bi bi-file-earmark-pdf"></i> Export PDF',
                orientation: 'landscape',
                pageSize: 'A4',
                footer: true
            },
            {
                extend: 'print',
                className: 'btn btn-info btn-sm',
                text: '<i class="bi bi-printer"></i> Print',
                footer: true
            }
        ]
    });
});
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
