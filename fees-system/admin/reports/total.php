<!--======================================================
    File Name   : total.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS - Fees Management System | Total Report Page
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

// Filter Logic
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$filter_inst = $_GET['filter_inst'] ?? 'ALL'; // New filter for Superadmin

$role    = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];

// Determine which ID to use for queries
// If Admin: always their session ID. If Superadmin: the filtered ID or 'ALL'
$targetInstId = ($role === 'SUPERADMIN') ? $filter_inst : $sessInstId;

// Fetch Institute Name for Headline
$headlineTitle = "Global System";
if ($targetInstId !== 'ALL') {
    $instQuery = $conn->query("SELECT INST_NAME FROM MASTER_INSTITUTES WHERE INST_ID = " . intval($targetInstId));
    if ($instQuery && $row = $instQuery->fetch_assoc()) {
        $headlineTitle = $row['INST_NAME'];
    }
}

/* =========================================================
   1. MODE-WISE SUMMARY QUERY (RE-FACTORED)
   ========================================================= */
$summarySql = "SELECT P.PAYMENT_MODE, SUM(P.PAID_AMOUNT) as mode_total, COUNT(*) as txn_count 
               FROM PAYMENTS P
               JOIN STUDENTS S ON P.STUDENT_ID = S.STUDENT_ID
               WHERE P.PAYMENT_STATUS = 'SUCCESS'
               AND DATE(P.PAYMENT_DATE) BETWEEN ? AND ?";

if ($targetInstId !== 'ALL') {
    $summarySql .= " AND S.INST_ID = " . intval($targetInstId);
}
$summarySql .= " GROUP BY P.PAYMENT_MODE";

$sStmt = $conn->prepare($summarySql);
$sStmt->bind_param("ss", $start_date, $end_date);
$sStmt->execute();
$summaryResult = $sStmt->get_result();

$grand_total = 0;
$mode_data = [];
while($row = $summaryResult->fetch_assoc()) {
    $mode_data[] = $row;
    $grand_total += $row['mode_total'];
}

/* =========================================================
   2. DETAILED TRANSACTIONS QUERY
   ========================================================= */
$sql = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, c.COURSE_CODE, i.INST_NAME
        FROM PAYMENTS p
        JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        JOIN MASTER_INSTITUTES i ON s.INST_ID = i.INST_ID
        WHERE p.PAYMENT_STATUS = 'SUCCESS'
        AND DATE(p.PAYMENT_DATE) BETWEEN ? AND ?";

if ($targetInstId !== 'ALL') {
    $sql .= " AND s.INST_ID = " . intval($targetInstId);
}
$sql .= " ORDER BY p.PAYMENT_DATE DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Fetch Institutes for Dropdown (Superadmin only)
$allInstitutes = [];
if ($role === 'SUPERADMIN') {
    $allInstitutes = $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES");
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h3 class="fw-bold text-dark mb-0"><?= htmlspecialchars($headlineTitle) ?></h3>
            <p class="text-muted mb-0">Collection Report from <?= date('d M Y', strtotime($start_date)) ?> to <?= date('d M Y', strtotime($end_date)) ?></p>
        </div>
        <div class="text-end">
             <span class="badge bg-primary px-3 py-2">FMS v1.0.0</span>
        </div>
    </div>

    <hr>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-uppercase small">Total Collected</h6>
                    <h2 class="fw-bold mb-0">₹<?= number_format($grand_total, 2) ?></h2>
                    <small>Transactions: <?= $result->num_rows ?></small>
                </div>
            </div>
        </div>
        <?php foreach($mode_data as $m): ?>
        <div class="col-md-2">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body py-2 text-center">
                    <small class="text-muted fw-bold"><?= $m['PAYMENT_MODE'] ?></small>
                    <h5 class="mb-0">₹<?= number_format($m['mode_total'], 0) ?></h5>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <form class="row g-2 align-items-center">
                <div class="col-auto">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-funnel text-primary"></i> Filters:</h5>
                </div>
                
                <div class="col-auto">
                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $start_date ?>">
                </div>
                <div class="col-auto">
                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $end_date ?>">
                </div>

                <?php if ($role === 'SUPERADMIN'): ?>
                <div class="col-auto">
                    <select name="filter_inst" class="form-select form-select-sm">
                        <option value="ALL">-- All Institutes --</option>
                        <?php while($inst = $allInstitutes->fetch_assoc()): ?>
                            <option value="<?= $inst['INST_ID'] ?>" <?= ($filter_inst == $inst['INST_ID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($inst['INST_NAME']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-auto">
                    <button type="submit" class="btn btn-dark btn-sm px-4">Apply Filter</button>
                    <a href="total.php" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table id="totalTable" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Institute</th>
                        <th>Receipt No</th>
                        <th>Student Details</th>
                        <th>Course</th>
                        <th>Mode</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($row['PAYMENT_DATE'])) ?></td>
                        <td class="small fw-bold text-primary"><?= $row['INST_NAME'] ?></td>
                        <td class="fw-bold"><?= $row['RECEIPT_NO'] ?></td>
                        <td>
                            <?= strtoupper($row['FIRST_NAME'].' '.$row['LAST_NAME']) ?><br>
                            <small class="text-muted"><?= $row['REGISTRATION_NO'] ?></small>
                        </td>
                        <td><?= $row['COURSE_CODE'] ?></td>
                        <td><span class="badge bg-secondary"><?= $row['PAYMENT_MODE'] ?></span></td>
                        <td class="fw-bold text-end">₹<?= number_format($row['PAID_AMOUNT'], 2) ?></td>
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
    $('#totalTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm', title: '<?= $headlineTitle ?>_Collection' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', orientation: 'landscape', title: '<?= $headlineTitle ?>_Collection' },
            { extend: 'print', className: 'btn btn-info btn-sm' }
        ],
        "order": [[ 0, "desc" ]]
    });
});
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
