<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

$student = null;
$search = $_GET['search'] ?? '';
$pay_ids = []; 
$pid = 0;

if (!empty($search)) {
    // Search by Registration No OR Roll No
    $stmt = $conn->prepare("
        SELECT S.*, C.COURSE_NAME, L.TOTAL_FEE, L.PAID_AMOUNT, L.BALANCE_AMOUNT 
        FROM STUDENTS S
        JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID
        LEFT JOIN STUDENT_FEE_LEDGER L ON S.STUDENT_ID = L.STUDENT_ID
        WHERE (S.REGISTRATION_NO = ? OR S.ROLL_NO = ?)
        AND S.STATUS = 'A'
    ");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    if ($student) {
        $pid = $student['STUDENT_ID'];
        // Fetch all payment IDs for the Print Full Statement feature
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
        <div class="card-body p-4 text-center">
            <h4 class="mb-3">🔍 Find Student Profile</h4>
            <form method="GET" class="row g-2 justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Enter Reg No or Roll No..." 
                           value="<?= htmlspecialchars($search) ?>" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary px-4">Search</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($search && !$student): ?>
        <div class="alert alert-warning text-center">No student found for: <strong><?= htmlspecialchars($search) ?></strong></div>
    <?php elseif ($student): ?>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                        </div>
                        <h4 class="mb-1"><?= $student['FIRST_NAME'].' '.$student['LAST_NAME'] ?></h4>
                        <span class="badge bg-success mb-3">Active</span>
                        <hr>
                        <div class="text-start">
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
                        <div class="row text-center g-3">
                            <div class="col-4 border-end">
                                <div class="text-muted small">Total Fee</div>
                                <div class="h5 mb-0">₹<?= number_format($student['TOTAL_FEE'], 2) ?></div>
                            </div>
                            <div class="col-4 border-end">
                                <div class="text-muted small">Paid</div>
                                <div class="h5 mb-0 text-success">₹<?= number_format($student['PAID_AMOUNT'], 2) ?></div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">Balance</div>
                                <div class="h5 mb-0 text-danger">₹<?= number_format($student['BALANCE_AMOUNT'], 2) ?></div>
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
                                    <i class="bi bi-printer-fill"></i> Print Full Statement
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
                                        <td class="text-end fw-bold text-dark">₹<?= number_format($p['PAID_AMOUNT'], 2) ?></td>
                                        <td class="text-center">
                                            <a href="../payments/receipt.php?id=<?= $p['PAYMENT_ID'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                        <tr><td colspan="5" class="text-center py-4 text-muted">No payments recorded yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> </div>
        </div>
    <?php endif; ?>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>