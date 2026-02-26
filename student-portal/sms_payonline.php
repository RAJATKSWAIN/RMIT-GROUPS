<?php
require_once __DIR__ . '/../fees-system/config/db.php';
require_once __DIR__ . '/../fees-system/config/config.php';
require_once __DIR__ . '/../fees-system/core/auth.php';

checkStudentLogin(); 

$sid = $_SESSION['student_id'];

// 1. Fetch Student & Branding (Same as Dashboard for consistency)
$sql = "SELECT s.*, c.COURSE_NAME, i.INST_NAME, i.BRAND_COLOR 
        FROM STUDENTS s
        LEFT JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        LEFT JOIN MASTER_INSTITUTES i ON s.INST_ID = i.INST_ID
        WHERE s.STUDENT_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sid);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// 2. Fetch Fee Components (Example query - adjust table names as per your DB)
$fee_sql = "SELECT * FROM STUDENT_FEE_LEDGER WHERE STUDENT_ID = ? LIMIT 1";
$f_stmt = $conn->prepare($fee_sql);
$f_stmt->bind_param("i", $sid);
$f_stmt->execute();
$ledger = $f_stmt->get_result()->fetch_assoc();

$total_payable = $ledger['BALANCE_AMOUNT'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Fees | <?= APP_NAME ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🎓</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --brand-color: <?= $student['BRAND_COLOR'] ?? '#2563eb' ?>;
            --bg-body: #f8fafc;
            --sidebar-width: 260px;
        }

        body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: #1e293b; }
        .wrapper { display: flex; align-items: stretch; }
        
        #sidebar { 
            min-width: var(--sidebar-width); max-width: var(--sidebar-width); 
            min-height: 100vh; background: white; border-right: 1px solid #e2e8f0; padding: 2rem 1.5rem;
            position: sticky; top: 0;
        }

        #content { width: 100%; padding: 2rem 3rem; }

        .nav-link { 
            color: #64748b; font-weight: 500; padding: 0.8rem 1rem; border-radius: 12px; 
            margin-bottom: 0.5rem; display: flex; align-items: center; gap: 12px; transition: 0.2s;
        }
        .nav-link:hover, .nav-link.active { background: rgba(37, 99, 235, 0.05); color: var(--brand-color); }
        .nav-link.active { border-right: 3px solid var(--brand-color); border-radius: 12px 0 0 12px; }

        /* Payment UI Components */
        .fee-card { background: white; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .list-group-item { border: none; padding: 1.2rem 0; border-bottom: 1px dashed #e2e8f0; }
        .list-group-item:last-child { border-bottom: none; }

        .payment-option {
            border: 2px solid #f1f5f9; border-radius: 16px; padding: 1.5rem;
            cursor: pointer; transition: 0.3s; display: block; position: relative;
        }
        .payment-option:hover { border-color: var(--brand-color); background: rgba(37, 99, 235, 0.02); }
        
        /* Custom Radio Styling */
        .payment-check { position: absolute; top: 15px; right: 15px; width: 20px; height: 20px; }
        input[type="radio"]:checked + .payment-option { border-color: var(--brand-color); background: rgba(37, 99, 235, 0.04); }

        .summary-box { background: var(--brand-color); color: white; border-radius: 16px; padding: 2rem; }
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar">
        <div class="mb-5 px-2">
            <h5 class="fw-bold text-primary mb-0">Edu<span style="color: #FFD700;">Remit&trade;</span></h5>
            <small class="text-muted">Student Portal</small>
        </div>
        <ul class="nav flex-column">
            <li><a href="sms_dashboard.php" class="nav-link"><i class="bi bi-grid-1x2"></i> Dashboard</a></li>
            <li><a href="sms_payonline.php" class="nav-link active"><i class="bi bi-credit-card-fill"></i> Pay Fees</a></li>
            <li><a href="sms_ledger.php" class="nav-link"><i class="bi bi-journal-text"></i> Fee History</a></li>
            <li><a href="sms_profile.php" class="nav-link"><i class="bi bi-person-circle"></i> My Profile</a></li>
            <li class="mt-4"><a href="sms_logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Online Fee Payment</h4>
            <p class="text-muted">Complete your pending dues securely via our encrypted gateway.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="fee-card p-4 h-100">
                    <h6 class="fw-bold text-uppercase mb-4" style="letter-spacing: 1px; color: #64748b;">Fee Breakdown</h6>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-0 fw-semibold">Tuition & Academic Fees</p>
                                <small class="text-muted">Main Course Fee for Semester <?= $student['SEMESTER'] ?></small>
                            </div>
                            <span class="fw-bold">₹<?= number_format($total_payable * 0.8, 2) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-0 fw-semibold">Examination Fees</p>
                                <small class="text-muted">Internal & External Assessment</small>
                            </div>
                            <span class="fw-bold">₹<?= number_format($total_payable * 0.15, 2) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-0 fw-semibold">Library & Lab Dues</p>
                                <small class="text-muted">Access to digital & physical resources</small>
                            </div>
                            <span class="fw-bold">₹<?= number_format($total_payable * 0.05, 2) ?></span>
                        </li>
                    </ul>

                    <div class="mt-4 p-3 rounded-4 bg-light border d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">Net Amount Payable:</span>
                        <h4 class="fw-bold text-primary mb-0">₹<?= number_format($total_payable, 2) ?></h4>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <form action="process_payment.php" method="POST">
                    <div class="fee-card p-4 mb-4">
                        <h6 class="fw-bold text-uppercase mb-4" style="letter-spacing: 1px; color: #64748b;">Select Method</h6>
                        
                        <div class="mb-3">
                            <input type="radio" name="pay_method" id="method_upi" value="UPI" class="d-none" checked>
                            <label for="method_upi" class="payment-option">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-circle p-3" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-qr-code-scan fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-bold">UPI Payment</p>
                                        <small class="text-muted">GPay, PhonePe, Paytm</small>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="mb-3">
                            <input type="radio" name="pay_method" id="method_card" value="CARD" class="d-none">
                            <label for="method_card" class="payment-option">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-circle p-3" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-credit-card-2-front fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-bold">Cards / Netbanking</p>
                                        <small class="text-muted">Debit, Credit, All Banks</small>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow-sm mt-3">
                            Proceed to Secure Pay <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>

                    <div class="summary-box shadow-sm">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-lock-fill"></i>
                            <span class="small fw-bold text-uppercase">PCI-DSS Secure</span>
                        </div>
                        <p class="small mb-0 opacity-75">Your transaction is protected with 256-bit SSL encryption. We do not store your card details.</p>
                    </div>
                </form>
            </div>
        </div>

        <footer class="mt-5 pb-2">
            <div class="d-flex justify-content-end border-top pt-2">
                <p class="text-muted mb-0" style="font-size: 0.7rem; opacity: 0.8; letter-spacing: 0.3px;">
                    &copy; 2026 <strong>EduRemit&trade;</strong> <span class="mx-1">|</span> Product of <strong>TrinityWebEdge</strong>
                </p>
            </div>
        </footer>
    </div>
</div>

</body>
</html>