<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

// Get the institute ID from the session (populated during login)
$adminId   = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'];
$instId 	= $_SESSION['inst_id'];

// Filter Logic: Default to today if no date is picked
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Enhanced SQL to get more details for "Each and every detail"
$sql = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, s.SEMESTER, 
               c.COURSE_CODE, c.COURSE_NAME
        FROM PAYMENTS p
        JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        WHERE p.PAYMENT_STATUS = 'SUCCESS'
        AND c.INST_ID = s.INST_ID
        AND s.INST_ID = $instId
        AND DATE(p.PAYMENT_DATE) BETWEEN ? AND ?
        ORDER BY p.PAYMENT_DATE DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$total_collected = 0;

?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <div>
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-journal-text"></i> Detailed Collection Report</h5>
                <small class="text-muted">Period: <?= date('d M Y', strtotime($start_date)) ?> to <?= date('d M Y', strtotime($end_date)) ?></small>
            </div>
            <form class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">From</span>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">To</span>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
            </form>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table id="reportTable" class="table table-hover table-bordered align-middle" style="width:100%">
                    <thead class="table-light text-uppercase small fw-bold">
                        <tr>
                            <th>Date & Time</th>
                            <th>Receipt No</th>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th>Course/Sem</th>
                            <th>Fee Items (Remarks)</th>
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
                            <td class="fw-bold text-dark"><?= $row['RECEIPT_NO'] ?></td>
                            <td><?= $row['REGISTRATION_NO'] ?></td>
                            <td><?= strtoupper($row['FIRST_NAME'].' '.$row['LAST_NAME']) ?></td>
                            <td><?= $row['COURSE_CODE'] ?> <small class="text-muted">(Sem-<?= $row['SEMESTER'] ?>)</small></td>
                            <td class="small">
                                <?php 
                                    // Clean up any special characters from fee names stored in remarks
                                    echo str_replace(['[', ']', '"', '?'], ['', '', '', '₹'], $row['REMARKS']); 
                                ?>
                            </td>
                            <td><span class="badge bg-info text-dark"><?= $row['PAYMENT_MODE'] ?></span></td>
                            <td class="small text-muted"><?= $row['TRANSACTION_ID'] ?? 'N/A' ?></td>
                            <td class="fw-bold text-end">₹<?= number_format($row['PAID_AMOUNT'], 2) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="8" class="text-end">Total Collection for this Period:</th>
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
    var table = $('#reportTable').DataTable({
        "order": [[ 0, "desc" ]], // Show latest payments first
        "pageLength": 25,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'btn btn-success btn-sm me-1',
                text: '<i class="bi bi-file-earmark-excel"></i> Export Excel',
                title: 'Daily_Collection_Report_<?= date('Y-m-d') ?>',
                footer: true // Include the Total footer in Excel
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm me-1',
                text: '<i class="bi bi-file-earmark-pdf"></i> Export PDF',
                orientation: 'landscape', // Better for many columns
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
