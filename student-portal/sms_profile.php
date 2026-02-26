<!--======================================================
    File Name   : sms_profile.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : SMS - Student Management Portal | Profile details Page
    Developed By: TrinityWebEdge
    Date Created: 25-02-2026
    Last Updated: 26-02-2026
    Note        : This page defines the SMS - Student Management Portal | Dashboard Page.
=======================================================-->
<?php
require_once __DIR__ . '/../fees-system/config/db.php';
require_once __DIR__ . '/../fees-system/config/config.php';
require_once __DIR__ . '/../fees-system/core/auth.php';

// Ensure the student is logged in
checkStudentLogin();

$sid = $_SESSION['student_id'];

/**
 * 1. FETCH STUDENT DATA
 * We join with Ledger for money and Courses for academic info
 */
$sql = "SELECT 
            s.*, 
            c.COURSE_NAME, c.DURATION_YEARS, c.COURSE_CODE,
            l.TOTAL_FEE as LEDGER_TOTAL, l.PAID_AMOUNT as LEDGER_PAID, l.BALANCE_AMOUNT as LEDGER_BALANCE,
            p.TXN_REF as LAST_UTR, p.PAYMENT_STATUS as LAST_PAY_STATUS, p.PAYMENT_DATE as LAST_PAY_DATE
        FROM STUDENTS s
        LEFT JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        LEFT JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
        LEFT JOIN (
            SELECT STUDENT_ID, TXN_REF, PAYMENT_DATE, PAYMENT_STATUS
            FROM PAYMENTS
            WHERE STUDENT_ID = ? AND PAYMENT_STATUS = 'SUCCESS'
            ORDER BY PAYMENT_DATE DESC LIMIT 1
        ) p ON s.STUDENT_ID = p.STUDENT_ID
        WHERE s.STUDENT_ID = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $sid, $sid);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Profile not found. Please contact administration.");
}

// Avatar Logic
$initials = urlencode(substr($student['FIRST_NAME'], 0, 1) . substr($student['LAST_NAME'], 0, 1));
$profile_pic = !empty($student['PHOTO']) ? 'assets/img/profiles/'.$student['PHOTO'] : "https://ui-avatars.com/api/?name=$initials&background=5f259f&color=fff";

// Payment Progress Calculation
$total = $student['LEDGER_TOTAL'] > 0 ? $student['LEDGER_TOTAL'] : 1;
$paid = $student['LEDGER_PAID'];
$perc = round(($paid / $total) * 100);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile View | <?= SMS_APP_NAME ?> </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary: #5f259f; --accent: #7c3aed; --bg: #f4f7fe; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; }
        
        .profile-container { margin-top: 40px; margin-bottom: 60px; }
        .card-custom { background: white; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 24px; overflow: hidden; }
        
        .header-banner { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%); 
            color: white; padding: 40px; border-radius: 20px; margin-bottom: 30px;
            position: relative;
        }

        .data-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .data-value { font-size: 15px; font-weight: 600; color: #1e293b; margin-bottom: 18px; display: block; }
        
        .progress { height: 8px; border-radius: 10px; background-color: #f1f5f9; }
        .progress-bar { background: linear-gradient(to right, #10b981, #34d399); }
        
        .section-title { font-size: 14px; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 20px; display: flex; align-items: center; }
        .section-title i { margin-right: 10px; font-size: 18px; }
        
        .status-badge { font-size: 10px; padding: 5px 12px; border-radius: 50px; text-transform: uppercase; font-weight: 700; }
    </style>
</head>
<body>

<div class="container profile-container">
    <div class="header-banner shadow-lg">
        <div class="row align-items-center">
            <div class="col-md-auto text-center mb-3 mb-md-0">
                <img src="<?= $profile_pic ?>" class="rounded-circle border border-4 border-white-50 shadow" style="width: 110px; height: 110px; object-fit: cover;">
            </div>
            <div class="col-md">
                <div class="d-flex align-items-center flex-wrap">
                    <h2 class="mb-1 fw-bold me-3"><?= htmlspecialchars($student['FIRST_NAME'] . ' ' . $student['LAST_NAME']) ?></h2>
                    <span class="status-badge bg-white text-success shadow-sm">
                        <i class="bi bi-patch-check-fill me-1"></i> Active Student
                    </span>
                </div>
                <p class="mb-2 opacity-75">
                    <i class="bi bi-bookmark-star me-1"></i> <?= $student['COURSE_NAME'] ?> | Class of <?= date('Y', strtotime($student['ADMISSION_DATE'] . " + {$student['DURATION_YEARS']} years")) ?>
                </p>
                <div class="d-flex gap-3 small opacity-90">
                    <span><i class="bi bi-hash me-1"></i> Reg: <b><?= $student['REGISTRATION_NO'] ?></b></span>
                    <span><i class="bi bi-person-vcard me-1"></i> Roll: <b><?= $student['ROLL_NO'] ?></b></span>
                </div>
            </div>
            <div class="col-md-auto text-md-end mt-3 mt-md-0">
                <a href="sms_dashboard.php" class="btn btn-light rounded-pill px-4 fw-bold text-primary">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom p-4">
                <div class="section-title"><i class="bi bi-person-badge"></i> General Information</div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="data-label">Full Name</label>
                        <span class="data-value"><?= strtoupper($student['FIRST_NAME'] . ' ' . $student['LAST_NAME']) ?></span>

                        <label class="data-label">Personal Contact</label>
                        <span class="data-value">+91 <?= $student['MOBILE'] ?></span>

                        <label class="data-label">Registered Email</label>
                        <span class="data-value"><?= strtolower($student['EMAIL']) ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="data-label">Date of Birth</label>
                        <span class="data-value"><?= date('d F, Y', strtotime($student['DOB'])) ?></span>

                        <label class="data-label">Gender</label>
                        <span class="data-value"><?= $student['GENDER'] ?? 'Not Specified' ?></span>

                        <label class="data-label">Permanent Address</label>
                        <span class="data-value small text-muted"><?= $student['ADDRESS'] ?></span>
                    </div>
                </div>
                
                <hr class="my-3 opacity-5">
                
                <div class="section-title mt-2"><i class="bi bi-people"></i> Family Details</div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="data-label">Father's Name</label>
                        <span class="data-value"><?= $student['FATHER_NAME'] ?? 'N/A' ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="data-label">Guardian Mobile</label>
                        <span class="data-value"><?= $student['FATHER_MOBILE'] ?? 'N/A' ?></span>
                    </div>
                </div>
            </div>

            <div class="card-custom p-4">
                <div class="section-title"><i class="bi bi-cash-stack"></i> Academic Fee Ledger</div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-4">
                            <label class="data-label">Total Fee</label>
                            <h5 class="fw-bold mb-0">₹<?= number_format($student['LEDGER_TOTAL'], 2) ?></h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-4">
                            <label class="data-label text-success">Total Paid</label>
                            <h5 class="fw-bold text-success mb-0">₹<?= number_format($student['LEDGER_PAID'], 2) ?></h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-4">
                            <label class="data-label text-danger">Due Balance</label>
                            <h5 class="fw-bold text-danger mb-0">₹<?= number_format($student['LEDGER_BALANCE'], 2) ?></h5>
                        </div>
                    </div>
                </div>
                
                <label class="data-label d-flex justify-content-between">
                    Payment Progress <span><?= $perc ?>% Paid</span>
                </label>
                <div class="progress mt-2">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $perc ?>%"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Success</h6>
                <?php if ($student['LAST_UTR']): ?>
                    <div class="p-3 border border-dashed rounded-4 bg-light">
                        <div class="text-center mb-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Verified Payment</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Ref ID:</span>
                            <span class="fw-bold text-dark"><?= $student['LAST_UTR'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Date:</span>
                            <span class="fw-bold text-dark"><?= date('d M, Y', strtotime($student['LAST_PAY_DATE'])) ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="bi bi-info-circle text-muted mb-2 d-block"></i>
                        <p class="small text-muted mb-0">No transaction history found.</p>
                    </div>
                <?php endif; ?>
                <a href="sms_ledger.php" class="btn btn-outline-primary btn-sm w-100 mt-3 rounded-pill">View Statement</a>
            </div>

            <div class="card-custom p-4 text-center bg-white border border-primary-subtle">
                <div class="bg-primary-subtle text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-headset fs-4"></i>
                </div>
                <h6 class="fw-bold">Profile Update?</h6>
                <p class="small text-muted">To change your registered mobile or photo, please submit a request to the office.</p>
                <button class="btn btn-primary w-100 rounded-pill fw-bold py-2">Open Support Ticket</button>
            </div>
        </div>

        <footer class="mt-4 pb-2">
            <div class="d-flex justify-content-end border-top pt-2">
                <p class="text-muted mb-0" style="font-size: 0.7rem; opacity: 0.8; letter-spacing: 0.3px;">
                    &copy; 2026 <strong><?= SMS_APP_NAME ?>  Ver  <?= SMS_APP_VERSION ?> </strong> <span class="mx-1">|</span> Product of <strong>TrinityWebEdge</strong>
                </p>
            </div>
        </footer>
        
    </div>
</div>

</body>
</html>
