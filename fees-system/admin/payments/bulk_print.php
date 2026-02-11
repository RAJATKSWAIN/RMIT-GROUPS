<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

$receipt_ids = $_POST['receipt_ids'] ?? [];

if (empty($receipt_ids)) {
    die("<script>alert('Please select at least one receipt to print.'); window.close();</script>");
}

$ids_string = implode(',', array_map('intval', $receipt_ids));

// Simplified SQL: We no longer strictly need the LEDGER join if we aren't showing balance, 
// but keeping it doesn't hurt performance much.
$sql = "SELECT p.*, s.FIRST_NAME, s.LAST_NAME, s.REGISTRATION_NO, 
               c.COURSE_NAME, c.COURSE_CODE, 
               a.FULL_NAME as COLLECTED_BY_NAME
        FROM PAYMENTS p
        JOIN STUDENTS s ON p.STUDENT_ID = s.STUDENT_ID
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        JOIN ADMIN_MASTER a ON p.COLLECTED_BY = a.ADMIN_ID
        JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
        WHERE p.PAYMENT_ID IN ($ids_string)
        ORDER BY p.PAYMENT_DATE DESC";


// 2. BRANDING LOGIC (Matching your template)
$courseCode = strtoupper($sql['COURSE_CODE'] ?? '');
$collegeName = "HOLY GROUP OF INSTITUTIONS";
$brandLogo = "https://via.placeholder.com/150x50?text=HOLY+GROUP"; 
$campusAddress = "Govindapur, Konisi, Berhampur (Gm.), Odisha - 761008";
$corporateAddress = "Baidanath Nagar , Near Sales Tax Office , Berahampur(10), Ganjan, Odisha";

if (in_array($courseCode, ['DME', 'DEE', 'DEC', 'DCSE'])) {
    $collegeName = "HOLY INSTITUTE OF TECHNOLOGY";
    $brandLogo = "https://rmitgroupsorg.infinityfree.me/hit/images/footerlogo.png"; 
    $campusAddress = "Govindapur, Konisi, Berhampur (Gm.), Odisha - 761008";
} elseif (in_array($courseCode, ['BCA', 'BES'])) {
    $collegeName = "RAJIV MEMORIAL INSTITUTE OF TECHNOLOGY";
    $brandLogo = "https://rmitgroupsorg.infinityfree.me/rmit/images/homelogo.png"; 
    $campusAddress = "Govindapur, Konisi, Berhampur (Gm.), Odisha - 761008";
} elseif (in_array($courseCode, ['FIT', 'WLD', 'EMC', 'ELT'])) {
    $collegeName = "RAJIV MEMORIAL INDUSTRIAL TRAINING CENTER";
    $campusAddress = "Bye Pass N.H.-5, Berhampur ,Ganjam , Odisha - 760010";
    $brandLogo = "https://rmitgroupsorg.infinityfree.me/rmitc/images/footerlogo.png"; 
}


function getIndianCurrencyInWords($number) {
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
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

$result = $conn->query($sql);
$verify_base_url = "https://rmitgroupsorg.infinityfree.me/fees-system/verify_receipt.php?id=";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Print Receipts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
    body { background: #f4f7f6; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;  border: 2px solid #000; /* width, style, color */}

        .receipt-card { 
            background: #fff; 
            max-width: 850px; 
            margin: 30px auto; 
            padding: 40px; 
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        .receipt-card::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 6px; }
        .college-logo { max-width: 100px; height: auto; }
        .watermark { 
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 120px; color: rgba(0,0,0,0.03); font-weight: 900; pointer-events: none; text-transform: uppercase;
        }
    .receipt-content { position: relative; z-index: 1; } /* Keeps text above watermark */
    .info-label { font-size: 11px; text-transform: uppercase; color: #6c757d; font-weight: 600; letter-spacing: 0.5px; }
    
    @media print {
        body { background: #fff; margin: 0; padding: 0; }
        .no-print { display: none; }
        .receipt-wrapper { margin: 0; border: none; width: 100%; page-break-after: always; box-shadow: none; }
    }
    .digital-sign-box { border: 2px dashed #198754 !important; background-color: #f8fff9 !important; }
    .balance-box { padding: 8px; margin-top: 10px; font-size: 10.5px; border: 1px solid #ddd; border-radius: 4px; }
    .service-box { background: #f0f7ff; border-left: 4px solid #1a3a5a; }
</style>
    
</head>
    
<body>

<div class="container no-print mt-4 text-center">
    <div class="alert alert-info">Ready to print <b><?= $result->num_rows ?></b> receipts.</div>
    <button onclick="window.print()" class="btn btn-primary btn-lg px-5"><i class="bi bi-printer"></i> Execute Bulk Print</button>
    <a href="receipt_history.php" class="btn btn-outline-secondary btn-lg">Back</a>
</div>

<?php while($data = $result->fetch_assoc()): 
    // --- BRANDING LOGIC ---
    $courseCode = strtoupper($data['COURSE_CODE']);
    $collegeName = "HOLY GROUP OF INSTITUTIONS";
    $brandLogo = "https://via.placeholder.com/150x50?text=HOLY+GROUP"; 
    
    if (in_array($courseCode, ['DME', 'DEE', 'DEC', 'DCSE'])) {
        $collegeName = "HOLY INSTITUTE OF HIT";
        $brandLogo = "https://rmitgroupsorg.infinityfree.me/hit/images/footerlogo.png"; 
    } elseif (in_array($courseCode, ['BCA', 'BES'])) {
        $collegeName = "RAJIV MEMORIAL INSTITUTE OF TECHNOLOGY";
        $brandLogo = "https://rmitgroupsorg.infinityfree.me/rmit/images/homelogo.png"; 
    } elseif (in_array($courseCode, ['FIT', 'WLD', 'EMC', 'ELT'])) {
    	$collegeName = "RAJIV MEMORIAL INDUSTRIAL TRAINING CENTER";
    	$campusAddress = "Bye Pass N.H.-5, Berhampur ,Ganjam , Odisha - 760010";
    	$brandLogo = "https://rmitgroupsorg.infinityfree.me/rmitc/images/footerlogo.png"; 
	}

    // --- REMARKS PARSER ---
    $rawRemarks = $data['REMARKS'] ?? '';
    
    // 1. Fix the '?' symbol
    $cleanRemarks = str_replace('?', '&#8377;', $rawRemarks);
    
    // 2. Remove {} and ()
    $sanitized = str_replace(['{', '}', '(', ')'], '', $cleanRemarks);
    
    // 3. Replace comma with pipe (|)
    $sanitized = str_replace(', ', ' || ', $sanitized);

    // 4. Extract Fee Name [Type]
    $displayFeeName = "Academic Fees";
    if (preg_match('/\[(.*?)\]/', $sanitized, $matches)) {
        $displayFeeName = $matches[1];
        $sanitized = trim(str_replace($matches[0], '', $sanitized));
    }
    $displayRemarks = $sanitized;

    $verification_link = $verify_base_url . $data['PAYMENT_ID'];
    $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . urlencode($verification_link);
?>

<div class="receipt-card">
    <div class="watermark">PAID</div>
    
    <div class="row align-items-start mb-4">
        <div class="col-2">
            <img src="<?= $brandLogo ?>" class="college-logo" alt="Logo">
        </div>
        <div class="col-7">
            <h3 class="fw-bold text-primary mb-1"> <?= $collegeName ?></h3>
            <p class="small text-muted mb-0">
                <i class="bi bi-geo-alt-fill"></i> <a>Campus At :  <?= $campusAddress ?> </a><br>
                <i class="bi bi-building"></i> <a>Corporate Office : <?= $corporateAddress ?> </a>
            </p>
        </div>
        <div class="col-3 text-end">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2 px-3 py-2 text-uppercase">Official Receipt</span>
            <p class="mb-0 small"><strong>#<?= $data['RECEIPT_NO'] ?></strong></p>
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
    <thead class="table-light">
        <tr>
            <th>Description of Particulars</th>
            <th class="text-end" style="width: 180px;">Amount (INR)</th>
        </tr>
    </thead>
    <tbody>
    <tr style="height: 120px;">
        <td>
            <h6 class="fw-bold mb-1"><?= strtoupper($displayFeeName) ?></h6>
            <p class="text-muted small mb-0" style="line-height: 1.5;">
                <?= !empty($displayRemarks) ? $displayRemarks : 'Payment received towards academic dues.' ?>
            </p>
        </td> 
        <td class="text-end fw-bold pt-3 fs-5">&#8377;<?= number_format($data['PAID_AMOUNT'], 2) ?></td>
    </tr>
    <tr class="table-secondary">
        <td class="text-end fw-bold">GRAND TOTAL RECEIVED</td>
        <td class="text-end fw-bold fs-5">&#8377;<?= number_format($data['PAID_AMOUNT'], 2) ?></td>
    </tr>
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
            <svg class="barcode-gen" 
                 jsbarcode-value="<?= $data['RECEIPT_NO'] ?>" 
                 jsbarcode-height="40" 
                 jsbarcode-width="1.2" 
                 jsbarcode-fontSize="12">
            </svg>
        </div>

        <div class="col-4 text-center">
            <div class="digital-sign-box p-3 rounded">
                <p class="mb-0 text-success fw-bold" style="font-size: 11px;">
                    <i class="bi bi-patch-check-fill"></i> DIGITALLY VERIFIED
                </p>
                <p class="mb-0 small fw-bold mt-1">Accounts Officer</p>
                <p class="mb-0 text-muted" style="font-size: 8px;">Generated: <?= date('d-M-Y H:i') ?></p>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-2 border-top text-center text-muted" style="font-size: 10px;">
        * This is a computer-generated academic receipt. No physical signature required.<br>
        <strong>Note:</strong> Fees once paid are non-refundable and non-transferable.<br>
        Document Generated on <?= date('d-M-Y H:i:s') ?>
    </div>
        
    </div>
</div>

<?php endwhile; ?>
    
    
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    // This will find all <svg> with the class 'barcode-gen' and render them
    window.onload = function() {
        JsBarcode(".barcode-gen").init();
    };
</script>     
</body>
</html>