<?php
require_once __DIR__ . '/../fees-system/config/db.php';
require_once __DIR__ . '/../fees-system/config/config.php';
require_once __DIR__ . '/../fees-system/core/auth.php';

checkStudentLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: sms_payonline.php");
    exit;
}

$sid = $_SESSION['student_id'];
$order_id = $_POST['order_id'];
$paid_amount = floatval($_POST['paid_amount']);
$utr_no = $_POST['utr_no'];

// 1. Basic Server-side Validation
if (strlen($utr_no) !== 12 || !is_numeric($utr_no)) {
    die("Invalid UTR Number. Must be 12 digits.");
}

/* 2. Database Transaction
   We use a transaction to ensure both the payment log and ledger update happen together.
*/
$conn->begin_transaction();

try {
    // A. Insert into PAYMENTS table
    $pay_sql = "INSERT INTO PAYMENTS (STUDENT_ID, RECEIPT_NO, PAID_AMOUNT, PAYMENT_DATE, PAYMENT_METHOD, TRANSACTION_REF, STATUS) 
                VALUES (?, ?, ?, NOW(), 'UPI', ?, 'PENDING')";
    $pay_stmt = $conn->prepare($pay_sql);
    $pay_stmt->bind_param("isds", $sid, $order_id, $paid_amount, $utr_no);
    $pay_stmt->execute();

    // B. (Optional) Update Ledger Balance 
    // Note: Some systems wait for Admin to verify UTR before updating this. 
    // If you want it instant, uncomment the lines below:
    /*
    $upd_sql = "UPDATE STUDENT_FEE_LEDGER SET BALANCE_AMOUNT = BALANCE_AMOUNT - ? WHERE STUDENT_ID = ?";
    $upd_stmt = $conn->prepare($upd_sql);
    $upd_stmt->bind_param("di", $paid_amount, $sid);
    $upd_stmt->execute();
    */

    $conn->commit();
    $success = true;
} catch (Exception $e) {
    $conn->rollback();
    $success = false;
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Received | EduRemit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f8fafc; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .success-card { max-width: 450px; width: 100%; background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .icon-box { width: 80px; height: 80px; background: #dcfce7; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2.5rem; }
    </style>
</head>
<body>

<div class="success-card">
    <?php if ($success): ?>
        <div class="icon-box"><i class="bi bi-check-lg"></i></div>
        <h3 class="fw-bold">Payment Submitted</h3>
        <p class="text-muted">Your transaction (UTR: <?= $utr_no ?>) has been logged successfully.</p>
        
        <div class="bg-light p-3 rounded-3 my-4 text-start">
            <div class="d-flex justify-content-between small mb-1">
                <span>Amount Paid:</span><span class="fw-bold text-dark">₹<?= number_format($paid_amount, 2) ?></span>
            </div>
            <div class="d-flex justify-content-between small">
                <span>Status:</span><span class="badge bg-warning text-dark">Pending Verification</span>
            </div>
        </div>

        <p class="small text-muted">You will be redirected to the dashboard in <span id="timer">5</span> seconds...</p>
        <a href="sms_dashboard.php" class="btn btn-primary w-100 py-2 fw-bold" style="background:#5469d4; border:none;">Go to Dashboard</a>
    <?php else: ?>
        <div class="icon-box" style="background:#fee2e2; color:#dc2626;"><i class="bi bi-exclamation-triangle"></i></div>
        <h3 class="fw-bold">System Error</h3>
        <p class="text-muted">We couldn't process your request. Please contact support.</p>
        <a href="process_payment.php" class="btn btn-secondary w-100 mt-3">Try Again</a>
    <?php endif; ?>
</div>

<script>
    let seconds = 5;
    const timerDisplay = document.getElementById('timer');
    
    if(timerDisplay) {
        const interval = setInterval(() => {
            seconds--;
            timerDisplay.innerText = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = "sms_dashboard.php";
            }
        }, 1000);
    }
</script>

</body>
</html>