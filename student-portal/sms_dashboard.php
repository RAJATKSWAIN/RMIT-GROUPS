<!--======================================================
    File Name   : sms_dashboard.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : SMS - Student Management Portal | Dashboard Page
    Developed By: TrinityWebEdge
    Date Created: 25-02-2026
    Last Updated: 26-02-2026
    Note        : This page defines the SMS - Student Management Portal | Dashboard Page.
=======================================================-->

<?php
require_once __DIR__ . '/../fees-system/config/db.php';
require_once __DIR__ . '/../fees-system/config/config.php';
require_once __DIR__ . '/../fees-system/core/auth.php';

// Security check for students only
checkStudentLogin(); 

$sid = $_SESSION['student_id'];

// 1. Fetch Student Profile & Branding
$sql = "SELECT s.*, c.COURSE_NAME, i.INST_NAME, i.BRAND_COLOR 
        FROM STUDENTS s
        LEFT JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        LEFT JOIN MASTER_INSTITUTES i ON s.INST_ID = i.INST_ID
        WHERE s.STUDENT_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sid);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// 2. Fetch Financial Ledger
$ledger_sql = "SELECT * FROM STUDENT_FEE_LEDGER WHERE STUDENT_ID = ? LIMIT 1";
$l_stmt = $conn->prepare($ledger_sql);
$l_stmt->bind_param("i", $sid);
$l_stmt->execute();
$ledger = $l_stmt->get_result()->fetch_assoc();

// 3. Recent Transactions
$pay_sql = "SELECT * FROM PAYMENTS WHERE STUDENT_ID = ? ORDER BY PAYMENT_DATE DESC LIMIT 5";
$p_stmt = $conn->prepare($pay_sql);
$p_stmt->bind_param("i", $sid);
$p_stmt->execute();
$payments = $p_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | <?= APP_NAME ?></title>
    
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

        /* Layout */
        .wrapper { display: flex; align-items: stretch; }
        #sidebar { 
            min-width: var(--sidebar-width); 
            max-width: var(--sidebar-width); 
            min-height: 100vh; 
            background: white; 
            border-right: 1px solid #e2e8f0;
            padding: 2rem 1.5rem;
        }

        #content { width: 100%; padding: 2rem 3rem; }

        /* Navigation */
        .nav-link { 
            color: #64748b; 
            font-weight: 500; 
            padding: 0.8rem 1rem; 
            border-radius: 12px; 
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: 0.2s;
        }
        .nav-link:hover, .nav-link.active { 
            background: rgba(37, 99, 235, 0.05); 
            color: var(--brand-color); 
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        
        .icon-shape {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        /* Table */
        .table-container { background: white; border-radius: 20px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .table thead th { background: #f8fafc; border-top: none; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
        
        .btn-pay-now {
            background-color: var(--brand-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            transition: 0.3s;
        }
        .btn-pay-now:hover { background-color: #1d4ed8; color: white; transform: scale(1.02); }
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar">
        <div class="mb-5 px-2">
            <h5 class="fw-bold text-primary mb-0">
    			Edu<span style="color: #FFD700;">Remit&trade;</span>
			</h5>
            <small class="text-muted">Student Portal</small>
        </div>

        <ul class="nav flex-column">
            <li><a href="sms_dashboard.php" class="nav-link active"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
            <li><a href="sms_payonline.php" class="nav-link"><i class="bi bi-credit-card"></i> Pay Fees</a></li>
            <li><a href="sms_ledger.php" class="nav-link"><i class="bi bi-journal-text"></i> Fee History</a></li>
            <li><a href="sms_profile.php" class="nav-link"><i class="bi bi-person-circle"></i> My Profile</a></li>
            <li class="mt-4"><a href="sms_logout.php" class="nav-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        
        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <?php if(!empty($_SESSION['inst_logo'])): ?>
                    <img src="<?= $_SESSION['inst_logo'] ?>" 
                         alt="Logo" 
                         style="height: 50px; width: auto; object-fit: contain;"
                         onerror="this.onerror=null;this.src='https://cdn-icons-png.flaticon.com/512/2830/2830314.png';">
                <?php else: ?>
                    <div class="bg-primary text-white rounded p-2" style="width:45px; height:45px; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-building"></i>
                    </div>
                <?php endif; ?>
                
                <div>
                    <h5 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px;"><?= $_SESSION['inst_name'] ?></h5>
                    <small class="text-muted">Academic Session 2025-26</small>
                </div>
            </div>
            
            <div class="text-end d-none d-md-block">
                <span class="badge bg-white text-dark border rounded-pill px-3 py-2 shadow-sm">
                    <i class="bi bi-calendar3 me-2 text-primary"></i><?= date('D, d M Y') ?>
                </span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5" style="border-radius: 24px; background: white; overflow: hidden;">
            <div class="p-4">
                <div class="row align-items-center">
                    <div class="col-md-auto text-center mb-3 mb-md-0">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border border-4 border-white shadow-sm" style="width: 85px; height: 85px;">
                            <i class="bi bi-person-fill text-primary" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h3 class="fw-bold mb-0 text-dark">Hello, <?= htmlspecialchars($student['FIRST_NAME']) ?>! 👋</h3>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 small" style="font-size: 0.75rem;">
                                <i class="bi bi-patch-check-fill"></i> Verified Student
                            </span>
                        </div>
                        <p class="text-muted mb-0 fw-medium">
                            <?= htmlspecialchars($student['COURSE_NAME']) ?> • Sem <?= $student['SEMESTER'] ?> • Reg No: <?= $student['REGISTRATION_NO'] ?>
                        </p>
                    </div>
                    <div class="col-md-auto mt-3 mt-md-0">
                        <a href="sms_payonline.php" class="btn btn-pay-now px-4 py-2">
                            <i class="bi bi-credit-card me-2"></i>Pay Fees Online
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-shape bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                    <small class="text-muted d-block mb-1">Total Fees Paid</small>
                    <h3 class="fw-bold mb-0">₹<?= number_format($ledger['PAID_AMOUNT'] ?? 0, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-shape bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                    <small class="text-muted d-block mb-1">Outstanding Dues</small>
                    <h3 class="fw-bold mb-0">₹<?= number_format($ledger['BALANCE_AMOUNT'] ?? 0, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary"><i class="bi bi-calendar2-check"></i></div>
                    <small class="text-muted d-block mb-1">Current Status</small>
                    <h3 class="fw-bold mb-0">Semester <?= $student['SEMESTER'] ?></h3>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Recent Payment Activity</h5>
                <a href="sms_ledger.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Receipt ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($payments->num_rows > 0): while($p = $payments->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-medium"><?= date('d M, Y', strtotime($p['PAYMENT_DATE'])) ?></td>
                            <td><span class="text-muted">#<?= $p['RECEIPT_NO'] ?></span></td>
                            <td class="fw-bold text-success">₹<?= number_format($p['PAID_AMOUNT'], 2) ?></td>
                            <td><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Success</span></td>
                            <td class="text-end">
                                <a href="../fees-system/print/receipt.php?id=<?= $p['PAYMENT_ID'] ?>" target="_blank" class="btn btn-sm btn-light border rounded-pill px-3">
                                    <i class="bi bi-file-earmark-pdf"></i> Receipt
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-clock-history text-muted d-block mb-2" style="font-size: 2rem;"></i>
                                    <span class="text-muted">No recent payment transactions found.</span>
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
        
    </div> </div> </body>
</html>
