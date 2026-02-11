<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';
require_once BASE_PATH.'/services/PaymentService.php'; 
require_once BASE_PATH.'/services/InvoiceService.php';
require_once BASE_PATH.'/services/LedgerService.php';

checkLogin();


$ledgerService = new LedgerService($conn);

// 1. Fetch data with Student and Course details
$sql = "SELECT s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, s.ROLL_NO, 
               c.COURSE_CODE, l.TOTAL_FEE, l.PAID_AMOUNT, l.BALANCE_AMOUNT, l.LAST_PAYMENT_DATE
        FROM STUDENT_FEE_LEDGER l
        JOIN STUDENTS s ON l.STUDENT_ID = s.STUDENT_ID
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        WHERE l.BALANCE_AMOUNT > 0
        ORDER BY s.STUDENT_ID";

$result = $conn->query($sql);

?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="container-fluid">
        <h3 class="mt-4">Outstanding Fees Report</h3>
        <p class="text-muted">List of students with pending dues (calculated from Academic Fees).</p>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pending Balances</h6>
                <button onclick="window.print()" class="btn btn-sm btn-secondary">Print Report</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="reportTable" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Reg/Roll No</th>
                                <th>Course</th>
                                <th>Total Fee</th>
                                <th>Paid</th>
                                <th>Balance</th>
                                <th>Last Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= strtoupper($row['FIRST_NAME'] . ' ' . $row['LAST_NAME']) ?></td>
                                <td><?= $row['REGISTRATION_NO'] ?> / <br><small><?= $row['ROLL_NO'] ?></small></td>
                                <td><?= $row['COURSE_CODE'] ?></td>
                                <td>₹<?= number_format($row['TOTAL_FEE'], 2) ?></td>
                                <td class="text-success">₹<?= number_format($row['PAID_AMOUNT'], 2) ?></td>
                                <td class="text-danger font-weight-bold">₹<?= number_format($row['BALANCE_AMOUNT'], 2) ?></td>
                                <td><?= $row['LAST_PAYMENT_DATE'] ? date('d-M-Y', strtotime($row['LAST_PAYMENT_DATE'])) : 'N/A' ?></td>
                                <td>
                                    <a href="../fees/collect.php?reg=<?= $row['REGISTRATION_NO'] ?>" class="btn btn-sm btn-primary">Collect</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>