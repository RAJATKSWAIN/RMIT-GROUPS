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

checkStudentLogin();

$sid = $_SESSION['student_id'];

// Comprehensive Join Query
$sql = "SELECT 
            s.*, 
            c.COURSE_NAME, c.DURATION_YEARS,
            l.TOTAL_FEE, l.BALANCE_AMOUNT,
            p.TXN_REF as LAST_UTR, p.PAYMENT_STATUS as LAST_PAY_STATUS, p.PAYMENT_DATE as LAST_PAY_DATE
        FROM STUDENTS s
        LEFT JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        LEFT JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
        LEFT JOIN (
            SELECT STUDENT_ID, PM.TXN_REF, PAYMENT_DATE , PAYMENT_STATUS
            FROM PAYMENTS PM
            WHERE STUDENT_ID = ?
            ORDER BY PAYMENT_DATE DESC LIMIT 1
        ) p ON s.STUDENT_ID = p.STUDENT_ID
        WHERE s.STUDENT_ID = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $sid, $sid);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Initials for Avatar
$initials = urlencode(substr($student['FIRST_NAME'] ?? 'S', 0, 1) . substr($student['LAST_NAME'] ?? 'U', 0, 1));
$profile_pic = !empty($student['PHOTO']) ? 'assets/img/profiles/'.$student['PHOTO'] : "https://ui-avatars.com/api/?name=$initials&background=5f259f&color=fff";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Real-Time Profile | <?= htmlspecialchars($student['FIRST_NAME']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary: #5f259f; --bg: #f8fafc; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; padding-top: 30px; }
        .card-custom { background: white; border-radius: 15px; border: 1px solid #eef2f6; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 20px; }
        .header-gradient { background: linear-gradient(135deg, #5f259f 0%, #7c3aed 100%); color: white; border-radius: 15px; padding: 30px; margin-bottom: 25px; }
        .data-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
        .data-value { font-size: 15px; font-weight: 600; color: #1e293b; display: block; margin-bottom: 15px; }
        .status-indicator { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-gradient shadow-sm d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <img src="<?= $profile_pic ?>" class="rounded-4 border border-2 border-white-50 me-4" style="width: 80px; height: 80px; object-fit: cover;">
            <div>
                <h3 class="mb-1 fw-bold"><?= htmlspecialchars($student['FIRST_NAME'] . ' ' . $student['LAST_NAME']) ?></h3>
                <p class="mb-0 opacity-75 small">
                    <i class="bi bi-mortarboard me-1"></i> <?= $student['COURSE_NAME'] ?> (<?= $student['DEPARTMENT'] ?>)
                </p>
            </div>
        </div>
        <div class="text-end">
            <span class="badge bg-white text-primary rounded-pill px-3 py-2">Roll No: <?= $student['ROLL_NO'] ?></span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom p-4">
                <h6 class="fw-bold mb-4 text-primary"><i class="bi bi-person-check-fill me-2"></i>Personal Profile</h6>
                <div class="row">
                    <div class="col-md-6">
                        <label class="data-label">Official Email</label>
                        <span class="data-value"><?= $student['EMAIL'] ?></span>
                        
                        <label class="data-label">Phone Number</label>
                        <span class="data-value"><?= $student['PHONE'] ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="data-label">Date of Birth</label>
                        <span class="data-value"><?= date('d M, Y', strtotime($student['DOB'])) ?></span>
                        
                        <label class="data-label">Address</label>
                        <span class="data-value small"><?= $student['ADDRESS'] ?></span>
                    </div>
                </div>
            </div>

            <div class="card-custom p-4">
                <h6 class="fw-bold mb-4 text-primary"><i class="bi bi-wallet2 me-2"></i>Financial Summary</h6>
                <div class="row text-center">
                    <div class="col-md-6 border-end">
                        <label class="data-label">Total Assigned Fees</label>
                        <h4 class="fw-bold text-dark">₹<?= number_format($student['TOTAL_FEES'], 2) ?></h4>
                    </div>
                    <div class="col-md-6">
                        <label class="data-label">Outstanding Balance</label>
                        <h4 class="fw-bold text-danger">₹<?= number_format($student['BALANCE_AMOUNT'], 2) ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-custom p-4">
                <h6 class="fw-bold mb-3">Last Transaction</h6>
                <?php if ($student['LAST_UTR']): ?>
                    <div class="p-3 bg-light rounded-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">UTR ID:</span>
                            <span class="small fw-bold"><?= $student['LAST_UTR'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">Status:</span>
                            <span class="badge <?= $student['LAST_PAY_STATUS'] == 'SUCCESS' ? 'bg-success' : 'bg-warning' ?> px-2">
                                <?= $student['LAST_PAY_STATUS'] ?>
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="small text-muted mb-0">No recent payments found.</p>
                <?php endif; ?>
            </div>

            <div class="card-custom p-4 bg-primary text-white">
                <h6 class="fw-bold mb-2">Need Help?</h6>
                <p class="small mb-3 opacity-75">Contact the administrative office for profile corrections.</p>
                <a href="mailto:support@eduremit.com" class="btn btn-sm btn-light w-100 rounded-pill">Contact Admin</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
