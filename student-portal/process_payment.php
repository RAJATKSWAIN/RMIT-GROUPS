<?php
require_once __DIR__ . '/../fees-system/config/db.php';
require_once __DIR__ . '/../fees-system/config/config.php';
require_once __DIR__ . '/../fees-system/core/auth.php';

checkStudentLogin();

$sid = $_SESSION['student_id'];
$sql = "SELECT BALANCE_AMOUNT FROM STUDENT_FEE_LEDGER WHERE STUDENT_ID = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sid);
$stmt->execute();
$ledger = $stmt->get_result()->fetch_assoc();

$total_due = $ledger['BALANCE_AMOUNT'] ?? 0;
$min_payable = $total_due * 0.30; // 30% Minimum
$order_id = "EDU" . date('His') . $sid; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | EduRemit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary: #5f259f; --accent: #00d2ff; }
        body { background: #f4f7fe; font-family: 'Inter', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        
        .pay-card { width: 100%; max-width: 850px; background: white; border-radius: 24px; box-shadow: 0 30px 60px rgba(95,37,159,0.15); display: flex; overflow: hidden; position: relative; }
        
        /* Side Decoration */
        .pay-card::before { content: ""; position: absolute; top: 0; left: 0; width: 5px; height: 100%; background: var(--primary); }

        .pay-left { background: #fafbff; padding: 40px; width: 45%; text-align: center; border-right: 1px solid #f0f0f0; }
        .pay-right { padding: 40px; width: 55%; }

        .qr-wrapper { 
            background: white; padding: 15px; border-radius: 20px; display: inline-block; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee; position: relative;
        }
        
        /* Scan Animation */
        .qr-wrapper::after {
            content: ""; position: absolute; top: 15px; left: 15px; right: 15px; height: 2px;
            background: var(--primary); box-shadow: 0 0 15px var(--primary);
            animation: scan 3s infinite ease-in-out;
        }
        @keyframes scan { 0%, 100% { top: 15px; } 50% { top: calc(100% - 17px); } }

        .amount-badge { background: #eef2ff; color: var(--primary); padding: 5px 15px; border-radius: 100px; font-weight: 700; font-size: 0.8rem; }
        
        .step-pill { width: 24px; height: 24px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold; }
        
        .form-label { font-weight: 700; font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .utr-field { border: 2px solid #eef0f2; border-radius: 12px; padding: 12px; font-weight: 700; text-align: center; letter-spacing: 2px; }
        .utr-field:focus { border-color: var(--primary); box-shadow: none; }

        .amount-input-group { border: 2px solid #5f259f; border-radius: 12px; overflow: hidden; }
        .amount-input-group input { border: none; font-weight: 800; color: #5f259f; }
    </style>
</head>
<body>

<div class="pay-card">
    <div class="pay-left d-flex flex-column justify-content-center">
        <div class="mb-4 text-start px-3">
            <h5 class="fw-bold mb-0">Edu<span style="color: #FFD700;">Remit™</span></h5>
            <small class="text-muted">Digital Payment Gateway</small>
        </div>

        <div class="qr-wrapper mb-4">
            <img id="qr_code" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=upi://pay?pa=7605943733@ybl%26am=<?= $total_due ?>" alt="UPI QR" style="width: 170px; height: 170px;">
        </div>

        <div class="px-3">
            <span class="amount-badge mb-2 d-inline-block">Live Payable Amount</span>
            <h2 class="fw-bold mb-0" id="display_amount">₹<?= number_format($total_due, 2) ?></h2>
            <p class="small text-muted mt-2"><i class="bi bi-shield-check-fill text-success"></i> PCI-DSS Encrypted</p>
        </div>
    </div>

    <div class="pay-right">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="fw-bold mb-0">Payment Details</h6>
            <span class="badge bg-light text-dark border fw-normal">ID: <?= $order_id ?></span>
        </div>

        <form action="sms_verify_payment.php" method="POST">
            <input type="hidden" name="order_id" value="<?= $order_id ?>">

            <div class="mb-4">
                <label class="form-label d-block mb-3">1. Select or Enter Amount</label>
                <div class="amount-input-group input-group shadow-sm">
                    <span class="input-group-text bg-white border-0 fw-bold">₹</span>
                    <input type="number" name="paid_amount" id="paid_amount" class="form-control form-control-lg" 
                           value="<?= $total_due ?>" min="<?= $min_payable ?>" max="<?= $total_due ?>" step="1" required>
                    <button class="btn btn-dark btn-sm px-3" type="button" onclick="resetFull()">Full</button>
                </div>
                <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;">
                    * Min. installment required: <strong>₹<?= number_format($min_payable, 2) ?></strong> (30%)
                </small>
            </div>

            <div class="mb-4">
                <label class="form-label d-block mb-2">2. Enter 12-Digit UTR after payment</label>
                <input type="text" name="utr_no" class="form-control utr-field" placeholder="0000 0000 0000" required maxlength="12" pattern="\d{12}">
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow mb-3" style="background: #5f259f; border:none;">
                Complete Payment <i class="bi bi-arrow-right ms-2"></i>
            </button>

            <div class="text-center">
                <a href="sms_payonline.php" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Cancel and Go Back
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const upi_id = "7605943733@ybl";
    const order_id = "<?= $order_id ?>";
    const amountInput = document.getElementById('paid_amount');
    const displayAmount = document.getElementById('display_amount');
    const qrImg = document.getElementById('qr_code');

    // Function to update QR and Display Amount
    function updatePaymentUI() {
        let val = amountInput.value;
        if(val == "") val = 0;
        
        // Update text display
        displayAmount.innerText = "₹" + parseFloat(val).toLocaleString('en-IN', {minimumFractionDigits: 2});
        
        // Update QR Code
        let upi_link = `upi://pay?pa=${upi_id}&pn=EduRemit&am=${val}&tn=${order_id}&cu=INR`;
        qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(upi_link)}`;
    }

    function resetFull() {
        amountInput.value = <?= $total_due ?>;
        updatePaymentUI();
    }

    amountInput.addEventListener('input', updatePaymentUI);
</script>

</body>
</html>