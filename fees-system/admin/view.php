<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

// 1. Get ID and prevent SQL Injection
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

//if ($id === 0) {
//    die("<div class='alert alert-danger'>Error: No Student ID provided.</div>");
//}

// 2. Fetch EVERYTHING: Student, Course, and Financial Ledger
$query = $conn->query("
    SELECT S.*, C.COURSE_NAME, 
           L.TOTAL_FEE, L.PAID_AMOUNT, L.BALANCE_AMOUNT
    FROM STUDENTS S
    JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID
    LEFT JOIN STUDENT_FEE_LEDGER L ON S.STUDENT_ID = L.STUDENT_ID
	");

$s = $query->fetch_assoc();

//if (!$s) {
//    die("<div class='alert alert-danger'>Record not found in database.</div>");
//}

// 3. Logic for "Pending Dues" Alert
$hasDues = ($s['BALANCE_AMOUNT'] > 0);
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<div class="container-fluid mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><?= htmlspecialchars($s['FIRST_NAME'].' '.$s['LAST_NAME']) ?></h2>
            <span class="text-muted">Registration: <?= $s['REGISTRATION_NO'] ?> | Roll: <?= $s['ROLL_NO'] ?></span>
        </div>
        <div class="btn-group">
    		<a href="/fees-system/admin/students/list.php" class="btn btn-outline-secondary">Back to List</a>
			<a href="/fees-system/admin/students/edit.php?id=<?= $id ?>" class="btn btn-warning">Edit Profile</a>
		</div>
    </div>

    <?php if($hasDues): ?>
        <div class="alert alert-warning shadow-sm border-start border-4 border-warning d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-envelope-exclamation-fill me-2"></i>
                <strong>Dues Pending:</strong> This student has an outstanding balance of <strong>₹<?= number_format($s['BALANCE_AMOUNT'], 2) ?></strong>.
            </div>
            <div>
                <a href="send_notif.php?type=email&id=<?= $id ?>" class="btn btn-sm btn-dark"><i class="bi bi-envelope"></i> Send Email</a>
                <a href="send_notif.php?type=sms&id=<?= $id ?>" class="btn btn-sm btn-success"><i class="bi bi-chat-dots"></i> Send SMS</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-success shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i> Fees are fully cleared for this student.
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Academic & Personal Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="text-muted small">Course</label>
                            <p class="fw-bold"><?= $s['COURSE_NAME'] ?></p>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-muted small">Status</label><br>
                            <span class="badge <?= $s['STATUS']=='A'?'bg-success':'bg-danger' ?>">
                                <?= $s['STATUS']=='A'?'Active Student':'Inactive / Disabled' ?>
                            </span>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-muted small">Email</label>
                            <p><?= $s['EMAIL'] ?></p>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="text-muted small">Mobile</label>
                            <p><?= $s['MOBILE'] ?></p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Address</label>
                            <p><?= $s['ADDRESS'] ?>, <?= $s['CITY'] ?>, <?= $s['STATE'] ?> - <?= $s['PINCODE'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm bg-primary text-white mb-4">
                <div class="card-body">
                    <h6 class="text-uppercase small opacity-75">Ledger Summary</h6>
                    <h1 class="display-5 fw-bold">₹<?= number_format($s['BALANCE_AMOUNT'], 2) ?></h1>
                    <p>Total Outstanding Dues</p>
                    <hr class="opacity-25">
                    <div class="d-flex justify-content-between">
                        <span>Total Fees:</span>
                        <span>₹<?= number_format($s['TOTAL_FEE'], 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Paid:</span>
                        <span class="text-info fw-bold">₹<?= number_format($s['PAID_AMOUNT'], 2) ?></span>
                    </div>
                </div>
                <div class="card-footer bg-dark bg-opacity-10 border-0">
                    <a href="../fees/pay.php?id=<?= $id ?>" class="btn btn-light btn-sm w-100 fw-bold">Collect Payment Now</a>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Recent Payment Transactions</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt #</th>
                                    <th>Mode</th>
                                    <th>Remarks</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $pay_q = $conn->query("SELECT * FROM PAYMENTS WHERE STUDENT_ID=$id ORDER BY PAYMENT_DATE DESC");
                                if($pay_q->num_rows > 0):
                                    while($p = $pay_q->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= date('d-M-Y', strtotime($p['PAYMENT_DATE'])) ?></td>
                                    <td><?= $p['RECEIPT_NO'] ?></td>
                                    <td><?= $p['PAYMENT_MODE'] ?></td>
                                    <td><?= $p['REMARKS'] ?></td>
                                    <td class="text-end fw-bold text-success">₹<?= number_format($p['PAID_AMOUNT'], 2) ?></td>
                                </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No payments found for this student.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH.'/admin/layout/footer.php'; ?>