<!--======================================================
    File Name   : receipt_template.php
    Project     : RMIT Groups - FMS - Fees Management System
    Description : DYNAMIC PAYMENT COLLECTIONS
    Developed By: TrinityWebEdge
    Date Created: 06-02-2026
    Last Updated: 26-02-2025
    Note        : This page defines the FMS - Fees Management System | PAYMENT COLLECTIONS Module of RMIT Groups website.
=======================================================-->
<?php
// 1. DATABASE CONNECTION & SCOPE SETTINGS
if (!isset($conn)) {
    global $conn;
}

// 2. FETCH DYNAMIC INSTITUTE & COURSE DETAILS
$studentId = $data['STUDENT_ID'] ?? 0;

if (!$conn) {
    die("Database Connection Error: Connection variable is null.");
}

$instQuery = $conn->query("
    SELECT 
        I.INST_NAME, I.BRAND_COLOR,
        D.CAMPUS_ADDRESS, D.CORPORATE_ADDRESS, D.LOGO_URL, D.OFFICIAL_EMAIL, D.WEBSITE_URL,
        C.COURSE_CODE, C.COURSE_NAME
    FROM STUDENTS S
    JOIN COURSES C ON S.COURSE_ID = C.COURSE_ID
    JOIN MASTER_INSTITUTES I ON C.INST_ID = I.INST_ID
    JOIN MASTER_INSTITUTE_DTL D ON I.INST_ID = D.INST_ID
    WHERE S.STUDENT_ID = '$studentId'
    LIMIT 1
");

$inst = $instQuery->fetch_assoc();

// --- DYNAMIC OVERRIDES (Replaces Hardcoded Mapping) ---
$collegeName      = $inst['INST_NAME'] ?? "HOLY GROUP OF INSTITUTIONS";
$brandLogo        = $inst['LOGO_URL'] ?? "https://via.placeholder.com/150x50?text=LOGO";
$campusAddress    = $inst['CAMPUS_ADDRESS'] ?? "N/A";
$corporateAddress = $inst['CORPORATE_ADDRESS'] ?? "N/A";
$brandColor       = $inst['BRAND_COLOR'] ?? "#1a3a5a"; // Dynamic Brand Theme
$courseName       = $inst['COURSE_NAME'] ?? $data['COURSE_NAME'];
$courseCode       = strtoupper($inst['COURSE_CODE'] ?? $data['COURSE_CODE'] ?? 'N/A');

/**
 * 3. REMARKS PARSING LOGIC (Remains exactly the same)
 */
$rawRemarks = $data['REMARKS'] ?? '';
$settledItems = [];

if (preg_match('/Settled:\s*\[?(.*?)(\]|User|$)/u', $rawRemarks, $matches)) {
    $itemsString = $matches[1]; 
    $parts = explode(',', $itemsString);
    $seenFees = [];

    foreach ($parts as $part) {
        $part = trim($part);
        if (preg_match('/[\{\[\(](.*?)[\]\}\)]\s*\(\D*\s*([\d,.]+)\)/u', $part, $itemMap)) {
            $feeName = strtoupper(trim($itemMap[1]));
            $feeAmount = (float)str_replace(',', '', $itemMap[2]);
            if (!isset($seenFees[$feeName])) {
                $settledItems[] = ['name' => $feeName, 'amount' => $feeAmount];
                $seenFees[$feeName] = true;
            }
        }
    }
}

$currentLevel = $data['APPLICABLE_LEVEL'] ?? 'COURSE';
$isServiceFee = in_array($currentLevel, ['COURSE','SEMESTER','YEAR','ONETIME','GLOBAL']);

if (!function_exists('getIndianCurrencyInWords')) {
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
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<style>
    @page { margin: 15px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.2; }
    .container { border: 1px solid #ccc; padding: 20px; position: relative; border-top: 4px solid <?= $brandColor ?>; }
    .header { border-bottom: 2px solid <?= $brandColor ?>; padding-bottom: 5px; margin-bottom: 10px; }
    .college-title { font-size: 18px; color: <?= $brandColor ?>; text-transform: uppercase; margin: 0; line-height: 1; }
    .address-box { font-size: 9px; color: #444; margin-top: 5px; line-height: 1.1; border-left: 1px solid #ccc; padding-left: 10px; }
    .receipt-header-table { border-bottom: 2px double #333; padding-bottom: 10px; margin-bottom: 10px; }
    .receipt-label { background: <?= $brandColor ?>; color: #fff; padding: 5px 10px; font-size: 10px; font-weight: bold; text-align: center; }
    .info-table { width: 100%; margin-top: 10px; margin-bottom: 15px; border-bottom: 1px dashed #eee; padding-bottom: 5px; border-collapse: collapse; }
    .items-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .info-label { font-size: 9px; color: #777; text-transform: uppercase; }
    .bold { font-weight: bold; }
    .items-table th { background: #f4f4f4; color: #333; padding: 8px 5px; font-size: 8.5px; border-top: 1px solid #000; border-bottom: 1px solid #000; text-transform: uppercase; }
    .items-table td { border-bottom: 1px solid #eee; padding: 8px 5px; font-size: 10px; vertical-align: middle; }
    .balance-box { padding: 5px; margin-top: 5px; font-size: 10.5px; border: 1px solid #ddd; border-radius: 4px; }
    .amount-words { font-style: italic; text-transform: capitalize; margin: 10px 0; border-bottom: 1px dashed #ccc; padding-bottom: 5px; }
    .ledger-box { background: #fffdf5; border-left: 4px solid #f39c12; }
    .service-box { background: #f0f7ff; border-left: 4px solid <?= $brandColor ?>; }
    .footer { margin-top: 10px; border-top: 1px solid #ccc; padding-top: 5px; font-size: 9px; text-align: center; color: #666; }
</style>
</head>
<body>

<div class="container">
    <table class="receipt-header-table" width="100%">
        <tr>
            <td width="15%"><img src="<?= $brandLogo ?>" style="max-width:100px;"></td>
            <td width="55%">
                <h1 class="college-title"><?= $collegeName ?></h1>
                <div class="address-box">
                    <strong>Campus : </strong> <?= $campusAddress ?><br>
                    <strong>Corporate Office : </strong> <?= $corporateAddress ?>
                </div>
            </td>
            <td width="30%" align="right">
                <div class="receipt-label">OFFICIAL RECEIPT</div>
                <div style="margin-top:8px;">
                    <span class="bold">No:</span> <?= $data['RECEIPT_NO'] ?><br>
                    <span class="bold">Date:</span> <?= date('d-M-Y', strtotime($data['PAYMENT_DATE'])) ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="55%">
                <div class="info-label">Student Information</div>
                <div class="bold" style="font-size:13px;"><?= strtoupper($data['FIRST_NAME'].' '.$data['LAST_NAME']) ?></div>
                <div class="bold text-muted">REG NO : <?= $data['REGISTRATION_NO'] ?></div>
            </td>
            <td width="45%" align="right">
                <div class="info-label">Academic Details</div>
                <div class="bold"><?= $courseName ?> (<?= $courseCode ?>)</div>
                <div class="bold" style="color:<?= $brandColor ?>;">Mode : <?= $data['PAYMENT_MODE'] ?> <?= $data['TXN_REF'] ? "({$data['TXN_REF']})" : '' ?></div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th align="left" width="70%">Description of Fee Particulars</th>
                <th align="right" width="30%">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($settledItems)): ?>
            <?php foreach ($settledItems as $item): ?>
            <tr>
                <td>
                    <div class="bold" style="color: <?= $brandColor ?>;"><?= htmlspecialchars($item['name']) ?></div>
                    <div style="font-size: 8px; color: #888; text-transform: uppercase;">
                        PAYMENT RECEIVED FOR <?= htmlspecialchars($item['name']) ?>
                    </div>
                </td>
                <td align="right" class="bold">
                    <?= number_format($item['amount'], 2) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td>
                    <div class="bold">ACADEMIC FEE COLLECTION</div>
                    <div style="font-size: 8px; color: #888;">GENERAL CONSOLIDATED FEES</div>
                </td>
                <td align="right" class="bold">
                    <?= number_format($data['PAID_AMOUNT'], 2) ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr style="background-color: #f8f9fa;">
            <td align="right" style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding-top: 12px;">
                <span class="bold" style="font-size: 12px;">GRAND TOTAL PAID : </span>
            </td>
            <td align="right" style="border-top: 2px solid #000; border-bottom: 2px solid #000; padding-top: 12px;">
                <span class="bold" style="font-size: 14px; color: <?= $brandColor ?>;">
                    &#8377; <?= number_format($data['PAID_AMOUNT'], 2) ?>
                </span>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="amount-words">
      <strong>Amount in words :</strong> 
        <?php echo (getIndianCurrencyInWords($data['PAID_AMOUNT'])); ?> only
    </div>

    <?php if ($isServiceFee): ?>
        <div class="balance-box service-box">
            <span class="bold" style="color:<?= $brandColor ?>;">PAYMENT STATUS :</span><br>
            <span>Your payment has been successfully adjusted against the selected fee headers. No further dues for these items this session.</span>
        </div>
    <?php else: ?>
        <div class="balance-box ledger-box">
            <table width="100%">
                <tr>
                    <td>
                        <span class="bold">ACCOUNT STATUS :</span><br>
                        <span style="color:#666; font-size:9px;">Current Outstanding Balance after this transaction.</span>
                    </td>
                    <td align="right">
                        <span class="bold" style="font-size:16px; color:#d35400;">&#8377; <?= number_format($data['BALANCE_AMOUNT'],2) ?></span>
                    </td>
                </tr>
            </table>
        </div>
    <?php endif; ?>

    <div class="footer">
        <div style="font-weight: bold; color: #555; margin-bottom: 5px;">* THIS IS A COMPUTER GENERATED RECEIPT. NO PHYSICAL SIGNATURE IS REQUIRED *</div>
        <strong>Note:</strong> Institutional fees once remitted are non-refundable and non-transferable.<br>
        <span style="font-size: 8px;">Generated via EduRemit™ FMS | Time: <?= date('d-M-Y H:i:s') ?></span>
    </div>
</div>
</body>
</html>
