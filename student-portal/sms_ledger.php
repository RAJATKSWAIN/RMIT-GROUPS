<?php
require_once __DIR__ . '/../fees-system/config/db.php';
require_once __DIR__ . '/../fees-system/config/config.php';
require_once __DIR__ . '/../fees-system/core/auth.php';

checkStudentLogin(); 

$sid = $_SESSION['student_id'];

// 1. Fetch Student & Branding
$sql = "SELECT s.*, c.COURSE_NAME, i.INST_NAME, i.BRAND_COLOR 
        FROM STUDENTS s
        LEFT JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        LEFT JOIN MASTER_INSTITUTES i ON s.INST_ID = i.INST_ID
        WHERE s.STUDENT_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sid);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// 2. Fetch Full Ledger Details
$ledger_sql = "SELECT * FROM STUDENT_FEE_LEDGER WHERE STUDENT_ID = ? LIMIT 1";
$l_stmt = $conn->prepare($ledger_sql);
$l_stmt->bind_param("i", $sid);
$l_stmt->execute();
$ledger = $l_stmt->get_result()->fetch_assoc();

// 3. Fetch All Payments (History)
$pay_sql = "SELECT * FROM PAYMENTS WHERE STUDENT_ID = ? ORDER BY PAYMENT_DATE DESC";
$p_stmt = $conn->prepare($pay_sql);
$p_stmt->bind_param("i", $sid);
$p_stmt->execute();
$history = $p_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee History | <?= APP_NAME ?></title>
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

        /* Ledger Table Styles */
        .ledger-container { background: white; border-radius: 24px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .summary-pill { background: #f1f5f9; padding: 1rem 1.5rem; border-radius: 16px; }
        
        .status-badge {
            padding: 0.5rem 1rem; border-radius: 10px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;
        }

        .table thead th { 
            background: transparent; border-bottom: 2px solid #f1f5f9; 
            color: #64748b; font-size: 0.75rem; letter-spacing: 1px;
        }
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
            <li><a href="sms_payonline.php" class="nav-link"><i class="bi bi-credit-card"></i> Pay Fees</a></li>
            <li><a href="sms_ledger.php" class="nav-link active"><i class="bi bi-journal-text-fill"></i> Fee History</a></li>
            <li><a href="sms_profile.php" class="nav-link"><i class="bi bi-person-circle"></i> My Profile</a></li>
            <li class="mt-4"><a href="sms_logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h4 class="fw-bold mb-1">Fee Statement</h4>
                <p class="text-muted mb-0">Review all your academic transactions and dues.</p>
            </div>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                <i class="bi bi-printer me-2"></i>Print Statement
            </button>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="summary-pill d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle"><i class="bi bi-wallet2 fs-4"></i></div>
                    <div>
                        <small class="text-muted d-block">Total Payable</small>
                        <span class="fw-bold fs-5">₹<?= number_format($ledger['TOTAL_FEES'] ?? 0, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-pill d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle"><i class="bi bi-check-lg fs-4"></i></div>
                    <div>
                        <small class="text-muted d-block">Paid Amount</small>
                        <span class="fw-bold fs-5">₹<?= number_format($ledger['PAID_AMOUNT'] ?? 0, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-pill d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle"><i class="bi bi-clock-history fs-4"></i></div>
                    <div>
                        <small class="text-muted d-block">Net Balance</small>
                        <span class="fw-bold fs-5">₹<?= number_format($ledger['BALANCE_AMOUNT'] ?? 0, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="ledger-container">
            <h6 class="fw-bold mb-4">Transaction History</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>RECEIPT #</th>
                            <th>DESCRIPTION</th>
                            <th>METHOD</th>
                            <th>AMOUNT</th>
                            <th class="text-end">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($history->num_rows > 0): while($h = $history->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-medium text-dark"><?= date('d-m-Y', strtotime($h['PAYMENT_DATE'])) ?></td>
                            <td><code class="text-primary fw-bold"><?= $h['RECEIPT_NO'] ?></code></td>
                            <td>
                                <span class="d-block small fw-bold">Academic Fees</span>
                                <small class="text-muted">Semester <?= $student['SEMESTER'] ?> installment</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i><?= $h['PAYMENT_METHOD'] ?>
                                </span>
                            </td>
                            <td class="fw-bold text-success">+₹<?= number_format($h['PAID_AMOUNT'], 2) ?></td>
                            <td class="text-end">
                                <a href="../fees-system/print/receipt.php?id=<?= $h['PAYMENT_ID'] ?>" class="btn btn-sm btn-light rounded-pill border shadow-sm">
                                    <i class="bi bi-download me-1"></i> PDF
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" style="width: 80px; opacity: 0.3;" alt="No Data">
                                <p class="text-muted mt-3">No transactions found for this account.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="mt-4 pb-2">
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