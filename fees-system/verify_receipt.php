<!--======================================================
    File Name   : verify_receipt.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : Verify Receipt of Invoice
    Developed By: TrinityWebEdge
    Date Created: 06-02-2025
    Last Updated: <?php echo date("d-m-Y"); ?>
    Note        : This page defines the FMS - Fees Management System | Verify Receipt Page of RMIT Groups website.
=======================================================-->

<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';

$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($payment_id > 0) {
    // SQL now includes the REMARKS field which contains the fee description
    $sql = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, s.ROLL_NO, c.COURSE_NAME, c.COURSE_CODE
            FROM PAYMENTS p
            JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID
            JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
            WHERE p.PAYMENT_ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        $courseCode = strtoupper($data['COURSE_CODE']);
        $collegeName = "HOLY GROUP OF INSTITUTIONS";
        $brandLogo = "https://via.placeholder.com/150x50?text=HOLY+GROUP"; 
        
        if (in_array($courseCode, ['DME', 'DEE', 'DEC', 'DCSE'])) {
            $collegeName = "HOLY INSTITUTE OF TECHNOLOGY";
            $brandLogo = "https://rmitgroupsorg.infinityfree.me/hit/images/footerlogo.png"; 
        } elseif (in_array($courseCode, ['BCA', 'BES'])) {
            $collegeName = "RAJIV MEMORIAL INSTITUTE OF TECHNOLOGY";
            $brandLogo = "https://rmitgroupsorg.infinityfree.me/rmit/images/homelogo.png"; 
        }

        // Logic to extract fee name from Remarks if you use the [FEETYPE] format
        // 1. Get the raw string and fix the '?' symbol
		$rawRemarks = $data['REMARKS'] ?? '';
		$cleanRemarks = str_replace('?', '&#8377;', $rawRemarks);

		// 2. Remove curly brackets {} and parentheses ()
		// We replace them with nothing ''
		$sanitized = str_replace(['{', '}', '(', ')'], '', $cleanRemarks);

		// 3. Replace the comma separator with a pipe separator for a cleaner look
		$sanitized = str_replace(', ', ' | ', $sanitized);

		// 4. Extract Fee Type if exists (e.g., [TUI_FEE])
		$displayFeeName = "Academic Fees";
			if (preg_match('/\[(.*?)\]/', $sanitized, $matches)) {
    	$displayFeeName = $matches[1];
    	$sanitized = trim(str_replace($matches[0], '', $sanitized));
		}

		// Result: "Settled: 1. Tuition Fee ₹ 18500.00 | 2. Admission Fee ₹ 7500.00..."
		$displayRemarks = $sanitized;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Receipt | Official Verification Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .verify-card { max-width: 550px; margin: 40px auto; border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .status-header { background: #198754; color: white; padding: 40px 20px; text-align: center; border-radius: 20px 20px 0 0; }
        .fee-details-box { background: #f8f9fa; border-left: 5px solid #198754; padding: 15px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #edf2f7; }
        .label { color: #718096; font-size: 0.85rem; font-weight: 600; }
        .value { color: #2d3748; font-weight: 700; }
    </style>
</head>
<body>

<div class="container">
    <div class="card verify-card">
        <?php if ($data): ?>
            <div class="status-header">
                <i class="bi bi-check-circle-fill" style="font-size: 3.5rem;"></i>
                <h3 class="fw-bold mt-2">VERIFICATION SUCCESSFUL</h3>
                <p class="mb-0 opacity-75">This payment is officially recorded in our system.</p>
            </div>
            
            <div class="card-body p-4">
                <div class="text-center mb-4 border-bottom pb-3">
                    <img src="<?= $brandLogo ?>" style="height: 45px;" class="mb-2" alt="Logo">
                    <h5 class="fw-bold text-uppercase mb-1"><?= $collegeName ?></h5>
                    <span class="text-muted small">Receipt #<?= $data['RECEIPT_NO'] ?></span>
                </div>
                
				<div class="fee-details-box">
    				<h6 class="text-success fw-bold small mb-1 text-uppercase"><i class="bi bi-info-circle"></i> Particulars :</h6>
    					<p class="mb-0 text-dark fw-medium" style="letter-spacing: 0.3px;">	<?= $displayRemarks ?></p>
				</div>
                
                <div class="detail-row">
                    <span class="label">STUDENT NAME</span>
                    <span class="value"><?= strtoupper($data['FIRST_NAME'] . ' ' . $data['LAST_NAME']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">REGISTRATION / ROLL</span>
                    <span class="value"><?= $data['REGISTRATION_NO'] ?> | <?= $data['ROLL_NO'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">COURSE</span>
                    <span class="value"><?= $data['COURSE_NAME'] ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">PAYMENT DATE</span>
                    <span class="value"><?= date('d-M-Y', strtotime($data['PAYMENT_DATE'])) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">PAYMENT MODE</span>
                    <span class="value"><?= $data['PAYMENT_MODE'] ?></span>
                </div>
                
                <div class="mt-4 p-3 bg-dark text-white rounded d-flex justify-content-between align-items-center">
    <span class="fw-bold">TOTAL AMOUNT PAID</span>
    <span class="fs-4 fw-bold text-warning">&#8377;<?= number_format($data['PAID_AMOUNT'], 2) ?></span>
</div>

                <div class="mt-4 text-center">
                    <p class="text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-lock-fill"></i> Secure Digital Verification Portal<br>
                        Timestamp: <?= date('d-M-Y H:i:s') ?>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="status-header bg-danger">
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 3.5rem;"></i>
                <h3 class="fw-bold mt-2">VERIFICATION FAILED</h3>
                <p class="mb-0">Invalid or forged receipt details.</p>
            </div>
            <div class="card-body p-5 text-center">
                <p class="text-muted">The verification ID provided does not match any records in our database. Please contact the college office immediately.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
