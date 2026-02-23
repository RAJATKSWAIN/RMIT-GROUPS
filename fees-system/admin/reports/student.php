<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

// Get the institute ID from the session (populated during login)
$adminId   = $_SESSION['admin_id'];
$adminName = $_SESSION['admin_name'];
$instId 	= $_SESSION['inst_id'];

$student_id = $_GET['student_id'] ?? null;
$student_data = null;
$payments = [];

if ($student_id) {
    // 1. Get Student & Ledger Info
    $sSql = "SELECT s.*, c.COURSE_NAME, l.TOTAL_FEE, l.BALANCE_AMOUNT 
             FROM STUDENTS s 
             JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID 
             JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID 
             WHERE s.STUDENT_ID = ?
             AND c.INST_ID = s.INST_ID
             AND s.INST_ID = $instId ";
    $stmt = $conn->prepare($sSql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student_data = $stmt->get_result()->fetch_assoc();

    // 2. Get All Successful Payments
    $pSql = "SELECT * FROM PAYMENTS WHERE STUDENT_ID = ? AND PAYMENT_STATUS = 'SUCCESS' ORDER BY PAYMENT_DATE DESC";
    $pStmt = $conn->prepare($pSql);
    $pStmt->bind_param("i", $student_id);
    $pStmt->execute();
    $payments = $pStmt->get_result();
}

// Fetch all students for the dropdown search
$allStudents = $conn->query("SELECT STUDENT_ID, FIRST_NAME, LAST_NAME, REGISTRATION_NO FROM STUDENTS WHERE STATUS = 'A' AND INST_ID = $instId ORDER BY FIRST_NAME ASC");
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-bold">Select Student to View History</label>
                    <select name="student_id" class="form-select select2">
                        <option value="">-- Search by Name or Reg No --</option>
                        <?php while($s = $allStudents->fetch_assoc()): ?>
                            <option value="<?= $s['STUDENT_ID'] ?>" <?= ($student_id == $s['STUDENT_ID']) ? 'selected' : '' ?>>
                                <?= $s['FIRST_NAME'] ?> <?= $s['LAST_NAME'] ?> (<?= $s['REGISTRATION_NO'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4 mt-auto">
                    <button type="submit" class="btn btn-primary w-100">View Statement</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($student_data): ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white">Student Profile</div>
                <div class="card-body">
                    <h5><?= strtoupper($student_data['FIRST_NAME'].' '.$student_data['LAST_NAME']) ?></h5>
                    <p class="mb-1 text-muted"><?= $student_data['COURSE_NAME'] ?> (Sem-<?= $student_data['SEMESTER'] ?>)</p>
                    <p class="badge bg-primary"><?= $student_data['REGISTRATION_NO'] ?></p>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Total Fees:</span> <strong>₹<?= number_format($student_data['TOTAL_FEE'], 2) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between text-danger">
                        <span>Current Dues:</span> <strong>₹<?= number_format($student_data['BALANCE_AMOUNT'], 2) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between">
                    <h6 class="mb-0 fw-bold">Payment Transaction History</h6>
                </div>
                <div class="card-body">
                    <table id="studentTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Receipt No</th>
                                <th>Amount</th>
                                <th>Mode</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($p = $payments->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d-m-Y', strtotime($p['PAYMENT_DATE'])) ?></td>
                                <td class="fw-bold"><?= $p['RECEIPT_NO'] ?></td>
                                <td class="text-success fw-bold">₹<?= number_format($p['PAID_AMOUNT'], 2) ?></td>
                                <td><?= $p['PAYMENT_MODE'] ?></td>
                                <td class="small"><?= $p['REMARKS'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' }); // For searchable dropdown
    $('#studentTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });
});
</script>
<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
