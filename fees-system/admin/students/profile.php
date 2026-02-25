<!--======================================================
    File Name   : profile.php
    Project     : RMIT Groups - FMS - Fees Management System
	Module		: STUDENT MANAGEMENT
    Description : Student Registration & Profile Management
    Developed By: TrinityWebEdge
    Date Created: 05-02-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note        : This page defines the FMS - Fees Management System | Student Module of RMIT Groups website.
=======================================================-->
<?php
// profile.php
// FMS V 1.0.0
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

$role       = $_SESSION['role_name'];
$sessInstId = $_SESSION['inst_id'];

$student = null;
$search  = $_GET['search'] ?? '';
$filter_inst = $_GET['inst_id'] ?? ''; // New Filter for Superadmin
$pay_ids = []; 
$pid = 0;
$current_year_due = 0;

// 1. Fetch Institutes list for the dropdown (Superadmin only)
$institutes = [];
if ($role === 'SUPERADMIN') {
    $inst_res = $conn->query("SELECT INST_ID, INST_NAME FROM MASTER_INSTITUTES ORDER BY INST_NAME ASC");
    while ($row = $inst_res->fetch_assoc()) {
        $institutes[] = $row;
    }
}

if (!empty($search)) {
    // 2. Base Query
    $sql = "SELECT S.*, C.COURSE_NAME, I.INST_NAME, 
                   L.TOTAL_FEE, L.PAID_AMOUNT, L.BALANCE_AMOUNT, L.PREVIOUS_DUES
            FROM STUDENTS S
            JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID
            JOIN MASTER_INSTITUTES I ON S.INST_ID = I.INST_ID
            LEFT JOIN STUDENT_FEE_LEDGER L ON S.STUDENT_ID = L.STUDENT_ID
            WHERE (S.REGISTRATION_NO = ? OR S.ROLL_NO = ?)
            AND S.STATUS = 'A'";

    // 3. Logic for Filtering
    if ($role === 'SUPERADMIN') {
        if (!empty($filter_inst)) {
            // Superadmin searching in a specific institute
            $sql .= " AND S.INST_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $search, $search, $filter_inst);
        } else {
            // Superadmin searching globally
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $search, $search);
        }
    } else {
        // Regular Admin constrained to their own institute
        $sql .= " AND S.INST_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $search, $search, $sessInstId);
    }

    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    
    if ($student) {
        $pid = $student['STUDENT_ID'];
        $current_year_due = (float)($student['BALANCE_AMOUNT'] ?? 0) - (float)($student['PREVIOUS_DUES'] ?? 0);

        $all_payments_res = $conn->query("SELECT PAYMENT_ID FROM PAYMENTS WHERE STUDENT_ID = $pid");
        if ($all_payments_res) {
            while($row = $all_payments_res->fetch_assoc()){
                $pay_ids[] = $row['PAYMENT_ID'];
            }
        }
    }
}
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <h4 class="text-center mb-4">🔍 Find Student Profile</h4>
            
            <form method="GET" class="row g-3 justify-content-center">
                <?php if ($role === 'SUPERADMIN'): ?>
                <div class="col-md-3">
                    <select name="inst_id" class="form-select">
                        <option value="">All Institutes (Global)</option>
                        <?php foreach ($institutes as $inst): ?>
                            <option value="<?= $inst['INST_ID'] ?>" <?= ($filter_inst == $inst['INST_ID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($inst['INST_NAME']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Enter Reg No or Roll No..." 
                           value="<?= htmlspecialchars($search) ?>" required>
                </div>
                
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary px-4">Search</button>
                    <?php if(!empty($search)): ?>
                        <a href="profile.php" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if ($search && !$student): ?>
        <div class="alert alert-warning text-center">
            No active student found for "<strong><?= htmlspecialchars($search) ?></strong>" 
            <?= (!empty($filter_inst)) ? "in the selected institute." : "across all institutes." ?>
        </div>
    <?php elseif ($student): ?>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="mb-1"><?= $student['FIRST_NAME'].' '.$student['LAST_NAME'] ?></h4>
                        <span class="badge bg-success mb-3">Active Student</span>
                        <hr>
                        
                        <div class="text-start">
                            <div class="alert alert-info py-2 px-3 mb-3">
                                <small class="d-block text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">Campus / Institute</small>
                                <span class="fw-bold"><?= $student['INST_NAME'] ?></span>
                            </div>

                            <p class="mb-2"><strong>Reg No:</strong> <span class="float-end text-muted"><?= $student['REGISTRATION_NO'] ?></span></p>
                            <p class="mb-2"><strong>Roll No:</strong> <span class="float-end text-muted"><?= $student['ROLL_NO'] ?></span></p>
                            <p class="mb-2"><strong>Course:</strong> <span class="float-end text-muted"><?= $student['COURSE_NAME'] ?></span></p>
                            <p class="mb-2"><strong>Mobile:</strong> <span class="float-end text-muted"><?= $student['MOBILE'] ?></span></p>
                            <p class="mb-0"><strong>Email:</strong> <span class="float-end text-muted small"><?= $student['EMAIL'] ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3"><h5 class="mb-0">💰 Fee Summary</h5></div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 border-end">
                                <p class="text-muted mb-1 small">Total Fee</p>
                                <h5 class="fw-bold">₹<?= number_format($student['TOTAL_FEE'], 2) ?></h5>
                            </div>
                            <div class="col-md-3 border-end">
                                <p class="text-muted mb-1 small">Paid</p>
                                <h5 class="fw-bold text-success">₹<?= number_format($student['PAID_AMOUNT'], 2) ?></h5>
                            </div>
                            <div class="col-md-3 border-end">
                                <p class="text-muted mb-1 small">Prev. Dues</p>
                                <h5 class="fw-bold text-warning">₹<?= number_format($student['PREVIOUS_DUES'], 2) ?></h5>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1 small">Net Balance</p>
                                <h5 class="fw-bold text-danger">₹<?= number_format($student['BALANCE_AMOUNT'], 2) ?></h5>
                                <small class="text-muted">Current: ₹<?= number_format($current_year_due, 2) ?></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📄 Recent Payments</h5>
                        <?php if (!empty($pay_ids)): ?>
                            <form action="../payments/bulk_print.php" method="POST" target="_blank" class="m-0">
                                <?php foreach($pay_ids as $id): ?>
                                    <input type="hidden" name="receipt_ids[]" value="<?= $id ?>">
                                <?php endforeach; ?>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-printer-fill"></i> Full Statement
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt #</th>
                                        <th>Mode</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $pay_q = $conn->query("SELECT * FROM PAYMENTS WHERE STUDENT_ID=$pid ORDER BY PAYMENT_DATE DESC LIMIT 5");
                                    if ($pay_q && $pay_q->num_rows > 0):
                                        while ($p = $pay_q->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?= date('d M Y', strtotime($p['PAYMENT_DATE'])) ?></td>
                                        <td><span class="badge bg-light text-dark border"><?= $p['RECEIPT_NO'] ?></span></td>
                                        <td><?= $p['PAYMENT_MODE'] ?></td>
                                        <td class="text-end fw-bold">₹<?= number_format($p['PAID_AMOUNT'], 2) ?></td>
                                        <td class="text-center">
                                            <a href="../payments/receipt.php?id=<?= $p['PAYMENT_ID'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No payments recorded.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>
