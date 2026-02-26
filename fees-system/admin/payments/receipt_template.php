<?php
/**
 * ======================================================
 * File Name    : receipt_template.php
 * Project      : EduRemit™ - Fees Management System
 * Description  : DYNAMIC PAYMENT COLLECTIONS RECEIPT
 * Developed By : TrinityWebEdge
 * ======================================================
 */

require_once BASE_PATH . '/config/db.php';

/**
 * 1. FETCH DYNAMIC INSTITUTE & COURSE DETAILS
 * Uses the STUDENT_ID from the $data array passed to this template
 */
$studentId = $data['STUDENT_ID'] ?? 0;
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

// Fallbacks for safety
$collegeName      = $inst['INST_NAME'] ?? "HOLY GROUP OF INSTITUTIONS";
$brandLogo        = $inst['LOGO_URL'] ?? "https://via.placeholder.com/150x50?text=LOGO";
$campusAddress    = $inst['CAMPUS_ADDRESS'] ?? "Main Campus Address";
$corporateAddress = $inst['CORPORATE_ADDRESS'] ?? "Corporate Office Address";
$brandColor       = $inst['BRAND_COLOR'] ?? "#1a3a5a"; 
$courseCode       = strtoupper($inst['COURSE_CODE'] ?? 'N/A');

/**
 * 2. REMARKS PARSING LOGIC
 * Extracts individual fee headers from the "Settled:" block
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

/**
 * 3. CURRENCY CONVERSION LOGIC
 */
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
<html>
<head>
<meta charset="utf-8"/>
<style>
    @page { margin: 10px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; line-height: 1.2; }
    .container { border: 1px solid #ddd; padding: 15px; position: relative; border-top: 4px solid <?= $brandColor ?>; }
    .college-title { font-size: 15px; color: <?= $brandColor ?>; text-transform: uppercase; margin: 0; font-weight: bold; }
    .address-box { font-size: 8px; color: #666; margin-top: 3px; line-height: 1.3; }
    .receipt-label { background: <?= $brandColor ?>; color: #fff; padding: 3px 8px; font-size: 11px; font-weight: bold; text-align: center; border-radius: 2px; }
    .info-table { width: 100%; margin-top: 15px; border-bottom: 1px dashed #ddd; padding-bottom: 10px; }
    .info-label { font-size: 8px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; }
    .bold { font-weight: bold; }
    .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .items-table th { background: #f9f9f9; color: #333; padding: 6px; font-size: 9px; border-bottom: 2px solid <?= $brandColor ?>; text-transform: uppercase; }
    .items-table td { border-bottom: 1px solid #f0f0f0; padding: 8px 6px; font-size: 10px; }
    .balance-box { padding: 8px; margin-top: 10px; font-size: 11px; border-radius: 4px; }
    .ledger-box { background: #fffcf5; border: 1px solid #f39c12; color: #7d4e00; }
    .service-box { background: #f0f7ff; border: 1px solid #1a3a5a; color: #1a3a5a; }
    .footer { margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; font-size: 8px; text-align: center; color: #999; }
</style>
</head>
<body>

<div class="container">
    <table width="100%">
        <tr>
            <td width="20%"><img src="<?= $brandLogo ?>" style="max-width:120px; max-height: 60px;"></td>
            <td width="55%">
                <h1 class="college-title"><?= htmlspecialchars($collegeName) ?></h1>
                <div class="address-box">
                    <strong>Campus:</strong> <?= htmlspecialchars($campusAddress) ?><br>
                    <strong>Office:</strong> <?= htmlspecialchars($corporateAddress) ?>
                </div>
            </td>
            <td width="25%" align="right" valign="top">
                <div class="receipt-label">OFFICIAL RECEIPT</div>
                <div style="margin-top:10px; font-size: 9px;">
                    <span class="bold">No:</span> <?= $data['RECEIPT_NO'] ?><br>
                    <span class="bold">Date:</span> <?= date('d-M-Y', strtotime($data['PAYMENT_DATE'])) ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="60%">
                <div class="info-label">Candidate Particulars</div>
                <div class="bold" style="font-size:14px; color: #111;"><?= strtoupper($data['FIRST_NAME'].' '.$data['LAST_NAME']) ?></div>
                <div class="bold text-muted" style="margin-top: 2px;">REGISTRATION NO : <?= $data['REGISTRATION_NO'] ?></div>
            </td>
            <td width="40%" align="right">
                <div class="info-label">Academic Program</div>
                <div class="bold"><?= $inst['COURSE_NAME'] ?></div>
                <div class="bold" style="color:<?= $brandColor ?>; font-size: 9px;">CODE: <?= $courseCode ?> | MODE: <?= $data['PAYMENT_MODE'] ?></div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th align="left" width="70%">Description of Fees</th>
                <th align="right" width="30%">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($settledItems)): ?>
                <?php foreach ($settledItems as $item): ?>
                <tr>
                    <td>
                        <div class="bold" style="color: #333;"><?= htmlspecialchars($item['name']) ?></div>
                        <div style="font-size: 7.5px; color: #aaa; text-transform: uppercase;">Payment Settlement for Academic Session</div>
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
                        <div style="font-size: 8px; color: #aaa;">GENERAL CONSOLIDATED ACCOUNT</div>
                    </td>
                    <td align="right" class="bold">
                        <?= number_format($data['PAID_AMOUNT'], 2) ?>
                    </td>
                </tr>
            <?php endif; ?>

            <tr style="background-color: #fcfcfc;">
                <td align="right" style="border-top: 2px solid <?= $brandColor ?>; padding-top: 12px;">
                    <span class="bold" style="font-size: 11px;">TOTAL SETTLED : </span>
                </td>
                <td align="right" style="border-top: 2px solid <?= $brandColor ?>; padding-top: 12px;">
                    <span class="bold" style="font-size: 14px; color: <?= $brandColor ?>;">
                        &#8377; <?= number_format($data['PAID_AMOUNT'], 2) ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 15px; padding: 8px; background: #fbfbfb; border: 1px solid #eee;">
        <span style="font-size: 9px; text-transform: uppercase;">
            <strong>Amount in Words:</strong> 
            <?php echo getIndianCurrencyInWords($data['PAID_AMOUNT']); ?> only
        </span>
    </div>

    <?php if ($isServiceFee): ?>
        <div class="balance-box service-box">
            <span class="bold">PAYMENT STATUS : VERIFIED</span><br>
            <span style="font-size: 9px;">This transaction has been successfully adjusted against specific service fee headers.</span>
        </div>
    <?php else: ?>
        <div class="balance-box ledger-box">
            <table width="100%">
                <tr>
                    <td>
                        <span class="bold">OUTSTANDING BALANCE :</span><br>
                        <span style="font-size: 8.5px;">Remaining dues in the student ledger after this transaction.</span>
                    </td>
                    <td align="right">
                        <span class="bold" style="font-size:16px;">&#8377; <?= number_format($data['BALANCE_AMOUNT'],2) ?></span>
                    </td>
                </tr>
            </table>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p style="margin-bottom: 5px;">* This is a computer-generated document. It does not require a physical signature.</p>
        <strong>Note:</strong> Fees once paid are non-refundable and non-transferable.<br>
        Document Generated on <?= date('d-M-Y H:i:s') ?>
    </div>
</div>

</body>
</html>
