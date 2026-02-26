<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

//checkLogin();

$payment_id = $_GET['id'] ?? null;
if (!$payment_id) die("Invalid Receipt Request.");

// 1. UPDATED SQL: Joined with MASTER_INSTITUTES and MASTER_INSTITUTE_DTL
$sql = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, s.ROLL_NO, s.STUDENT_ID,
               c.COURSE_NAME, c.COURSE_CODE, 
               a.FULL_NAME as COLLECTED_BY_NAME,
               I.INST_NAME, I.BRAND_COLOR,
               D.CAMPUS_ADDRESS, D.CORPORATE_ADDRESS, D.LOGO_URL
        FROM PAYMENTS p
        JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        JOIN ADMIN_MASTER a ON p.COLLECTED_BY = a.ADMIN_ID
        JOIN MASTER_INSTITUTES I ON c.INST_ID = I.INST_ID
        JOIN MASTER_INSTITUTE_DTL D ON I.INST_ID = D.INST_ID
        WHERE p.PAYMENT_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) die("Receipt not found.");

// --- DYNAMIC BRANDING LOGIC (Replaces Hardcoded if/else) ---
$courseCode       = strtoupper($data['COURSE_CODE']);
$collegeName      = $data['INST_NAME'];
$brandLogo        = $data['LOGO_URL'];
$campusAddress    = $data['CAMPUS_ADDRESS'];
$corporateAddress = $data['CORPORATE_ADDRESS'];
$brandColor       = $data['BRAND_COLOR'] ?? '#0d6efd'; // Use this for dynamic styling if desired

// --- REMARKS PARSER (Keep your existing logic) ---
$rawRemarks = $data['REMARKS'] ?? '';
$cleanRemarks = str_replace('?', '&#8377;', $rawRemarks);
$sanitized = str_replace(['{', '}', '(', ')'], '', $cleanRemarks);
$sanitized = str_replace(', ', ' || ', $sanitized);
$displayFeeName = "Academic Fees";
if (preg_match('/\[(.*?)\]/', $sanitized, $matches)) {
    $displayFeeName = $matches[1];
    $sanitized = trim(str_replace($matches[0], '', $sanitized));
}
$displayRemarks = $sanitized;

$verify_base_url = "https://rmitgroupsorg.infinityfree.me/fees-system/verify_receipt.php?id=";
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($verify_base_url . $data['PAYMENT_ID']);

// --- CURRENCY FUNCTION (Keep your existing logic) ---
function getIndianCurrencyInWords($number) {
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $digits_length = strlen($no);
    $i = 0; $str = array();
    $words = array(
        0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen',
        20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
    );
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt_<?= $data['RECEIPT_NO'] ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', Tahoma, sans-serif; border: 1px solid #ccc;}
        .container { border: 1px solid #ccc; padding: 15px; position: relative; height: 65%; }
        .receipt-card { 
            background: #fff; max-width: 850px; margin: 30px auto; padding: 40px; 
            position: relative; overflow: hidden; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        /*.receipt-card::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 6px; background: linear-gradient(to right, #004a99, #00d2ff); }*/
        .college-logo { max-width: 100px; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 150px; color: rgba(0,0,0,0.03); font-weight: 900; 
            pointer-events: none; }
        .info-label { font-size: 11px; text-transform: uppercase; color: #6c757d; font-weight: 600; }
        .digital-sign-box { border: 2px dashed #198754 !important; background-color: #f8fff9 !important; }
        .balance-box { padding: 8px; margin-top: 10px; font-size: 10.5px; border: 1px solid #ddd; border-radius: 4px; }
        .service-box { background: #f0f7ff; border-left: 4px solid #1a3a5a; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; margin: 0; }
            .receipt-card { box-shadow: none; margin: 0; width: 100%; padding: 20px; }
            .receipt-card::before { display: none; }
        }
    </style>
</head>
<body>
    
<div class="container no-print mt-4 d-flex justify-content-between" style="max-width: 850px;">
    <a href="collect.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Print Receipt</button>
</div>  

<div class="receipt-card">
    <div class="watermark">PAID</div>
        
    <div class="row align-items-start mb-4">
        <div class="col-2"><img src="<?= $brandLogo ?>" class="college-logo"></div>
        <div class="col-7">
            <h3 class="fw-bold text-primary mb-1"><?= $collegeName ?></h3>
            <p class="small text-muted mb-0"><i class="bi bi-geo-alt-fill"></i> <a> Campus At : <?= $campusAddress ?> </a> <br>
                							<i class="bi bi-building"></i> <a> Corporate Office : <?= $corporateAddress ?> </a>
            </p>
        </div>
        <div class="col-3 text-end">
            <span class="badge bg-primary-subtle text-primary border px-3 py-2 text-uppercase">Official Receipt</span>
            <p class="mb-0 mt-2 small"><strong>#<?= $data['RECEIPT_NO'] ?></strong></p>
            <p class="text-muted small"><?= date('d-M-Y', strtotime($data['PAYMENT_DATE'])) ?></p>
        </div>
    </div>
    <hr class="opacity-10">
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="p-3 bg-light rounded-3">
                <div class="info-label mb-1">Student Details</div>
                <h5 class="fw-bold mb-1"><?= strtoupper($data['FIRST_NAME'] . ' ' . $data['LAST_NAME']) ?></h5>
                <p class="mb-0 small">Reg No: <strong><?= $data['REGISTRATION_NO'] ?></strong></p>
                <p class="mb-0 small">Course: <?= $data['COURSE_NAME'] ?> (<?= $courseCode ?>)</p>
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="p-3 bg-light rounded-3 h-100">
                <div class="info-label mb-1">Payment Summary</div>
                <p class="mb-1 fw-bold">Method: <?= $data['PAYMENT_MODE'] ?></p>
                <p class="mb-0 small text-muted">Ref ID: <?= $data['TXN_REF'] ?: 'N/A' ?></p>
                <p class="mb-0 small text-muted">Collector: <?= $data['COLLECTED_BY_NAME'] ?></p>
            </div>
        </div>
    </div>
    <table class="table table-bordered border-secondary mb-4">
        <thead class="table-light"><tr><th>Description of Particulars</th><th class="text-end" style="width: 180px;">Amount (INR)</th></tr></thead>
        <tbody>
            <tr style="height: 120px;">
                <td><h6 class="fw-bold text-primary mb-1"><?= strtoupper($displayFeeName) ?></h6><p class="text-muted small"><?= $displayRemarks ?: 'Academic dues.' ?></p></td>
                <td class="text-end fw-bold fs-5">₹<?= number_format($data['PAID_AMOUNT'], 2) ?></td>
            </tr>
            <tr class="table-secondary"><td class="text-end fw-bold">GRAND TOTAL RECEIVED</td><td class="text-end fw-bold fs-5">₹<?= number_format($data['PAID_AMOUNT'], 2) ?></td></tr>
        </tbody>
    </table>
    
<div style="margin-top: 5px; padding: 2px; border-bottom: 1px solid #eee;">
    <span style="font-size: 10px; font-style: italic;">
        <strong>Rupees in words:</strong> 
        <?php echo strtoupper(getIndianCurrencyInWords($data['PAID_AMOUNT'])); ?> ONLY
    </span>
</div>
    
<div class="balance-box service-box">
   <span class="bold" style="color:#1a3a5a;">PAYMENT STATUS:</span><br>
   <span>Your payment has been successfully adjusted against the selected fee headers. No further dues for these items this session.</span>
</div>
    
    <div class="row align-items-center mt-5">
        <div class="col-4 text-center">
            <img src="<?= $qr_code_url ?>" alt="Verify Receipt" class="border p-1 bg-white mb-1" style="width: 90px;">
            <p class="text-muted" style="font-size: 8px;">Scan to Verify Receipt</p>
        </div>
        <div class="col-4 text-center">
            <svg class="barcode-gen" jsbarcode-value="<?= $data['RECEIPT_NO'] ?>" jsbarcode-height="40" jsbarcode-width="1.2" jsbarcode-fontSize="12">></svg>
        </div>
        <div class="col-4 text-center">
            <div class="digital-sign-box p-3 rounded">
                <p class="mb-0 text-success fw-bold" style="font-size: 11px;"><i class="bi bi-patch-check-fill"></i> DIGITALLY VERIFIED</p>
                <p class="mb-0 small fw-bold">Accounts Officer</p>
                <p class="mb-0 text-muted" style="font-size: 8px;"><?= date('d-M-Y H:i') ?></p>
            </div>
        </div>
        
        <div class="mt-4 pt-2 border-top text-center text-muted" style="font-size: 10px;">
        * This is a computer-generated academic receipt. No physical signature required.<br>
        <strong>Note:</strong> Fees once paid are non-refundable and non-transferable.<br>
        Document Generated on <?= date('d-M-Y H:i:s') ?>
    	</div>
        
    </div>
</div>
    
</div>
    
	
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>window.onload = function() { JsBarcode(".barcode-gen").init(); };</script>
</body>
</html>
