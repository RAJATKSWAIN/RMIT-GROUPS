<!--======================================================
    File Name   : student.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : FMS - Fees Management System | Student Report Page
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

// Get Filters
$filter_inst = $_GET['filter_inst'] ?? (($role === 'SUPERADMIN') ? 'ALL' : $sessInstId);
$student_id  = $_GET['student_id'] ?? null;

$student_data = null;
$payments = [];

/* =========================================================
   2. SEARCH DROPDOWN DATA (Role-Aware)
   ========================================================= */
$searchSql = "SELECT STUDENT_ID, FIRST_NAME, LAST_NAME, REGISTRATION_NO FROM STUDENTS WHERE STATUS = 'A'";

// Apply Institute Isolation
if ($role !== 'SUPERADMIN') {
    $searchSql .= " AND INST_ID = $sessInstId";
} elseif ($filter_inst !== 'ALL') {
    $searchSql .= " AND INST_ID = " . intval($filter_inst);
}

$searchSql .= " ORDER BY FIRST_NAME ASC";
$allStudents = $conn->query($searchSql);

/* =========================================================
   3. SPECIFIC STUDENT DATA
   ========================================================= */
if ($student_id) {
    // Fetch Student with Institute Name for the profile header
    $sSql = "SELECT s.*, c.COURSE_NAME, l.TOTAL_FEE, l.BALANCE_AMOUNT, i.INST_NAME 
             FROM STUDENTS s 
             LEFT JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID 
             LEFT JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID 
             JOIN MASTER_INSTITUTES i ON s.INST_ID = i.INST_ID
             WHERE s.STUDENT_ID = ?";
    
    // Security: Prevent Admin from accessing students of other institutes via URL manipulation
    if ($role !== 'SUPERADMIN') {
        $sSql .= " AND s.INST_ID = $sessInstId";
    }

    $stmt = $conn->prepare($sSql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student_data = $stmt->get_result()->fetch_assoc();

    if ($student_data) {
        // Get All Successful Payments
        $pSql = "SELECT * FROM PAYMENTS WHERE STUDENT_ID = ? AND PAYMENT_STATUS = 'SUCCESS' ORDER BY PAYMENT_DATE DESC";
        $pStmt = $conn->prepare($pSql);
        $pStmt->bind_param("i", $student_id);
        $pStmt->execute();
        $payments = $pStmt->get_result();
    }
}

// Fetch Colleges list for Superadmin dropdown
$colleges = ($role === 'SUPERADMIN') ? $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES") : null;
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <?php if ($role === 'SUPERADMIN'): ?>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary">1. Select College</label>
                    <select name="filter_inst" class="form-select select2" onchange="this.form.submit()">
                        <option value="ALL">-- All Colleges --</option>
                        <?php while($ins = $colleges->fetch_assoc()): ?>
                            <option value="<?= $ins['INST_ID'] ?>" <?= ($filter_inst == $ins['INST_ID']) ? 'selected' : '' ?>>
                                <?= $ins['INST_NAME'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="<?= ($role === 'SUPERADMIN') ? 'col-md-6' : 'col-md-8' ?>">
                    <label class="form-label fw-bold">2. Select Student</label>
                    <select name="student_id" class="form-select select2">
                        <option value="">-- Search by Name or Reg No --</option>
                        <?php while($s = $allStudents->fetch_assoc()): ?>
                            <option value="<?= $s['STUDENT_ID'] ?>" <?= ($student_id == $s['STUDENT_ID']) ? 'selected' : '' ?>>
                                <?= $s['FIRST_NAME'] ?> <?= $s['LAST_NAME'] ?> (<?= $s['REGISTRATION_NO'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3 mt-auto">
                    <button type="submit" class="btn btn-dark w-100">View Statement</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($student_data): ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <span class="fw-bold">Student Profile</span>
                    <?php if($role === 'SUPERADMIN'): ?>
                        <small class="badge bg-white text-primary"><?= $student_data['INST_NAME'] ?></small>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="fw-bold mb-1"><?= strtoupper($student_data['FIRST_NAME'].' '.$student_data['LAST_NAME']) ?></h5>
                    <p class="mb-1 text-muted small"><?= $student_data['COURSE_NAME'] ?> | Sem-<?= $student_data['SEMESTER'] ?></p>
                    <p class="badge bg-light text-dark border mb-3"><?= $student_data['REGISTRATION_NO'] ?></p>
                    
                    <div class="p-3 bg-light rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Fee Cap:</span> <strong>₹<?= number_format($student_data['TOTAL_FEE'], 2) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between text-danger">
                            <span>Outstanding:</span> <strong>₹<?= number_format($student_data['BALANCE_AMOUNT'], 2) ?></strong>
                        </div>
                    </div>
                    <hr>
                    <small class="text-muted">Contact: <?= $student_data['MOBILE_NO'] ?></small>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Payment Transaction History</h6>
                </div>
                <div class="card-body">
                    <table id="studentTable" class="table table-bordered table-striped w-100">
                        <thead class="table-light">
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
                                <td class="fw-bold text-primary"><?= $p['RECEIPT_NO'] ?></td>
                                <td class="text-success fw-bold">₹<?= number_format($p['PAID_AMOUNT'], 2) ?></td>
                                <td><span class="badge bg-outline-secondary border text-dark"><?= $p['PAYMENT_MODE'] ?></span></td>
                                <td class="small"><?= $p['REMARKS'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php elseif($student_id): ?>
        <div class="alert alert-warning">No student found or you do not have permission to view this record.</div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({ theme: 'bootstrap-5' });
    $('#studentTable').DataTable({
        "order": [[ 0, "desc" ]],
        dom: 'Bfrtip',
        buttons: ['excel', 'pdf', 'print']
    });
});
</script>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
