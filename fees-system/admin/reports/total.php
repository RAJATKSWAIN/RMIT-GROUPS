<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

// Filter Logic: Default to current month
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// 1. Get Total Summary (Mode-wise breakdown)
$summarySql = "SELECT PAYMENT_MODE, SUM(PAID_AMOUNT) as mode_total, COUNT(*) as txn_count 
               FROM PAYMENTS 
               WHERE PAYMENT_STATUS = 'SUCCESS'
               AND DATE(PAYMENT_DATE) BETWEEN ? AND ? 
               GROUP BY PAYMENT_MODE";
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

// 2. Get All Detailed Transactions
$sql = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, c.COURSE_CODE 
        FROM PAYMENTS p
        JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        WHERE p.PAYMENT_STATUS = 'SUCCESS'
        AND DATE(p.PAYMENT_DATE) BETWEEN ? AND ?
        ORDER BY p.PAYMENT_DATE DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-uppercase small">Grand Total Collection</h6>
                    <h2 class="fw-bold mb-0">₹<?= number_format($grand_total, 2) ?></h2>
                    <small>Total Transactions: <?= $result->num_rows ?></small>
                </div>
            </div>
        </div>
        <?php foreach($mode_data as $m): ?>
        <div class="col-md-2">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body py-2">
                    <small class="text-muted fw-bold"><?= $m['PAYMENT_MODE'] ?></small>
                    <h5 class="mb-0">₹<?= number_format($m['mode_total'], 0) ?></h5>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line text-primary"></i> Total Collection Report</h5>
            <form class="d-flex gap-2">
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $start_date ?>">
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $end_date ?>">
                <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            </form>
        </div>
        <div class="card-body">
            <table id="totalTable" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Receipt No</th>
                        <th>Student Details</th>
                        <th>Course</th>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($row['PAYMENT_DATE'])) ?></td>
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
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm', title: 'Total_Collection_Report' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', title: 'Total_Collection_Report' },
            { extend: 'print', className: 'btn btn-info btn-sm' }
        ],
        "order": [[ 0, "desc" ]]
    });
});
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>