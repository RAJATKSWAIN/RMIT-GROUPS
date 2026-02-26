<!--======================================================
    File Name   : sms_transactions.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : SMS - Student Management Portal | Transaction Page
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

// Fetch all payment history for this student
$sql = "SELECT * FROM PAYMENTS 
        WHERE STUDENT_ID = ? 
        ORDER BY PAYMENT_DATE DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sid);
$stmt->execute();
$result = $stmt->get_result();

// Summary stats for the top cards
$stats_sql = "SELECT 
                SUM(CASE WHEN STATUS = 'SUCCESS' THEN PAID_AMOUNT ELSE 0 END) as total_paid,
                SUM(CASE WHEN STATUS = 'PENDING' THEN PAID_AMOUNT ELSE 0 END) as total_pending
              FROM PAYMENTS WHERE STUDENT_ID = ?";
$s_stmt = $conn->prepare($stats_sql);
$s_stmt->bind_param("i", $sid);
$s_stmt->execute();
$stats = $s_stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History | EduRemit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --primary: #5f259f; --bg: #f8fafc; }
        body { background: var(--bg); font-family: 'Inter', sans-serif; }
        .table-card { background: white; border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.03); }
        .status-badge { padding: 6px 12px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; }
        .bg-pending { background: #fef3c7; color: #92400e; }
        .bg-success { background: #dcfce7; color: #166534; }
        .bg-failed { background: #fee2e2; color: #991b1b; }
        .stat-card { border-radius: 15px; border: none; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Transaction History</h3>
            <p class="text-muted small">Track and manage your UPI fee payments</p>
        </div>
        <a href="sms_dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-house-door me-1"></i> Dashboard
        </a>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm p-3 bg-white border-start border-4 border-success">
                <small class="text-muted fw-bold">TOTAL VERIFIED</small>
                <h3 class="fw-bold mb-0">₹<?= number_format($stats['total_paid'] ?? 0, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm p-3 bg-white border-start border-4 border-warning">
                <small class="text-muted fw-bold">PENDING APPROVAL</small>
                <h3 class="fw-bold mb-0">₹<?= number_format($stats['total_pending'] ?? 0, 2) ?></h3>
            </div>
        </div>
    </div>

    <div class="card table-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-muted small">DATE</th>
                        <th class="py-3 text-muted small">REFERENCE (UTR)</th>
                        <th class="py-3 text-muted small">METHOD</th>
                        <th class="py-3 text-muted small">AMOUNT</th>
                        <th class="py-3 text-muted small">STATUS</th>
                        <th class="pe-4 py-3 text-muted small text-end">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="d-block fw-bold"><?= date('d M, Y', strtotime($row['PAYMENT_DATE'])) ?></span>
                                    <span class="text-muted small"><?= date('h:i A', strtotime($row['PAYMENT_DATE'])) ?></span>
                                </td>
                                <td class="fw-medium text-uppercase"><?= $row['TRANSACTION_REF'] ?></td>
                                <td><span class="badge bg-light text-dark border fw-normal">UPI</span></td>
                                <td class="fw-bold">₹<?= number_format($row['PAID_AMOUNT'], 2) ?></td>
                                <td>
                                    <?php 
                                        $statusClass = 'bg-pending';
                                        if($row['STATUS'] == 'SUCCESS') $statusClass = 'bg-success';
                                        if($row['STATUS'] == 'FAILED') $statusClass = 'bg-failed';
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                        <?= $row['STATUS'] ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <?php if($row['STATUS'] == 'SUCCESS'): ?>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill"><i class="bi bi-download"></i> Receipt</button>
                                    <?php else: ?>
                                        <span class="text-muted small italic">Verifying...</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
