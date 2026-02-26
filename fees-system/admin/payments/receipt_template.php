<?php
/**
 * ======================================================
 * File Name    : receipt_template.php
 * Project      : EduRemit™ - Fees Management System
 * Description  : BRANDED ACADEMIC PAYMENT RECEIPT
 * Developed By : TrinityWebEdge
 * ======================================================
 */

// 1. Scope Fix: Ensure DB connection is available from Service or Global
if (!isset($conn)) {
    global $conn;
}

/**
 * 2. FETCH DYNAMIC INSTITUTE & COURSE DETAILS
 */
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

// Dynamic Branding
$collegeName      = $inst['INST_NAME'] ?? "INSTITUTE NAME";
$brandLogo        = $inst['LOGO_URL'] ?? "";
$campusAddress    = $inst['CAMPUS_ADDRESS'] ?? "";
$corporateAddress = $inst['CORPORATE_ADDRESS'] ?? "";
$brandColor       = $inst['BRAND_COLOR'] ?? "#1a3a5a"; // Use Institute's color
$courseCode       = strtoupper($inst['COURSE_CODE'] ?? 'N/A');

/**
 * 3. REMARKS PARSING LOGIC
 */
$rawRemarks = $data['REMARKS'] ?? '';
$settledItems = [];

// 1. Target the "Settled:" block exclusively
if (preg_match('/Settled:\s*\[?(.*?)(\]|User|$)/u', $rawRemarks, $matches)) {
    $itemsString = $matches[1]; 
    
    // 2. Split by comma to get individual fee entries
    $parts = explode(',', $itemsString);
    
    // 3. Temporary array to prevent duplicates if the string itself has them
    $seenFees = [];

    foreach ($parts as $part) {
        $part = trim($part);
        // Matches format: {FEE NAME} (Rs. 0.00) or similar
        if (preg_match('/[\{\[\(](.*?)[\]\}\)]\s*\(\D*\s*([\d,.]+)\)/u', $part, $itemMap)) {
            
            $feeName = strtoupper(trim($itemMap[1]));
            $feeAmount = (float)str_replace(',', '', $itemMap[2]);

            // Only add if we haven't processed this specific fee in this loop
            if (!isset($seenFees[$feeName])) {
                $settledItems[] = [
                    'name'   => $feeName,
                    'amount' => $feeAmount
                ];
                $seenFees[$feeName] = true;
            }
        }
    }
}

$currentLevel = $data['APPLICABLE_LEVEL'] ?? 'COURSE';
$isServiceFee = in_array($currentLevel, ['COURSE','SEMESTER','YEAR','ONETIME','GLOBAL']);

/**
 * 4. CURRENCY CONVERSION LOGIC
 */
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
    body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
    
    /* Academic Frame using Brand Color */
    .container { border: 1px solid #ddd; border-top: 5px solid <?= $brandColor ?>; padding: 25px; position: relative; background: #fff; }
    
    /* Header Branding */
    .college-title { font-size: 19px; color: <?= $brandColor ?>; text-transform: uppercase; margin: 0; font-weight: bold; letter-spacing: 0.5px; }
    .address-box { font-size: 9px; color: #666; margin-top: 5px; line-height: 1.3; }
    
    /* Official Badge */
    .receipt-header-table { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
    .receipt-label { background: <?= $brandColor ?>; color: #fff; padding: 5px 15px; font-size: 11px; font-weight: bold; text-align: center; border-radius: 3px; display: inline-block; text-transform: uppercase; }
    
    .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; color: <?= $brandColor ?>; margin-bottom: 5px; border-bottom: 1px solid #f0f0f0; }
    
    .info-table { width: 100%; margin-bottom: 20px; }
    .bold { font-weight: bold; color: #000; }
    
    /* Branded Table Styling */
    .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .items-table th { border-bottom: 2px solid <?= $brandColor ?>; padding: 10px 5px; font-size: 10px; text-transform: uppercase; color: #444; background: #fcfcfc; }
    .items-table td { border-bottom: 1px solid #f3f3f3; padding: 12px 5px; font-size: 11px; }
    
    .total-row td { border-top: 2px solid <?= $brandColor ?>; padding: 12px 5px; background: #fdfdfd; }
    
    /* Boxed Elements */
    .status-box { border: 1px solid #eee; padding: 12px; margin-top: 20px; background: #fafafa; border-left: 4px solid <?= $brandColor ?>; }
    .amount-words { font-style: italic; font-size: 10px; margin: 15px 0; padding: 8px; background: #f9f9f9; border-radius: 3px; }
    
    .footer { margin-top: 40px; font-size: 9px; text-align: center; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
</style>
</head>
<body>

<div class="container">
    <table class="receipt-header-table" width="100%">
        <tr>
            <td width="15%"><img src="<?= $brandLogo ?>" style="max-width:110px; max-height:70px;"></td>
            <td width="55%">
                <h1 class="college-title"><?= htmlspecialchars($collegeName) ?></h1>
                <div class="address-box">
                    <strong>Campus:</strong> <?= htmlspecialchars($campusAddress) ?><br>
                    <strong>Adm. Office:</strong> <?= htmlspecialchars($corporateAddress) ?>
                </div>
            </td>
            <td width="30%" align="right" valign="top">
                <div class="receipt-label">OFFICIAL RECEIPT</div>
                <div style="margin-top:12px; font-size: 10px;">
                    <strong>Receipt No:</strong> <?= $data['RECEIPT_NO'] ?><br>
                    <strong>Date:</strong> <?= date('d-M-Y', strtotime($data['PAYMENT_DATE'])) ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="55%" valign="top">
                <div class="section-title">Candidate Particulars</div>
                <div class="bold" style="font-size:16px; margin-bottom: 3px;"><?= strtoupper($data['FIRST_NAME'].' '.$data['LAST_NAME']) ?></div>
                <div style="color: #555;">Registration No: <span class="bold"><?= $data['REGISTRATION_NO'] ?></span></div>
            </td>
            <td width="45%" align="right" valign="top">
                <div class="section-title">Program of Study</div>
                <div class="bold" style="font-size:12px;"><?= $inst['COURSE_NAME'] ?></div>
                <div style="font-size: 10px; margin-top: 3px;">
                    Code: <span class="bold"><?= $courseCode ?></span> | 
                    Mode: <span class="bold" style="color:<?= $brandColor ?>;"><?= $data['PAYMENT_MODE'] ?></span>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th align="left" width="75%">Description of Fee Head</th>
                <th align="right" width="25%">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($settledItems)): ?>
                <?php foreach ($settledItems as $item): ?>
                <tr>
                    <td>
                        <div class="bold"><?= htmlspecialchars($item['name']) ?></div>
                        <span style="font-size: 9px; color: #888;">Transaction successfully settled against academic head.</span>
                    </td>
                    <td align="right" class="bold" style="font-size: 12px;">
                        <?= number_format($item['amount'], 2) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td>
                        <div class="bold">ACADEMIC FEE COLLECTION</div>
                        <span style="font-size: 9px; color: #888;">Consolidated payment towards institutional dues.</span>
                    </td>
                    <td align="right" class="bold" style="font-size: 12px;">
                        <?= number_format($data['PAID_AMOUNT'], 2) ?>
                    </td>
                </tr>
            <?php endif; ?>

            <tr class="total-row">
                <td align="right" class="bold" style="color: <?= $brandColor ?>; font-size: 11px;">NET SETTLED AMOUNT :</td>
                <td align="right" class="bold" style="font-size: 16px; color: <?= $brandColor ?>;">
                    ₹ <?= number_format($data['PAID_AMOUNT'], 2) ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="amount-words">
        <strong>Amount in words:</strong> 
        <?php echo getIndianCurrencyInWords($data['PAID_AMOUNT']); ?> only
    </div>

    <div class="status-box">
        <table width="100%">
            <tr>
                <td width="65%">
                    <strong style="color: <?= $brandColor ?>;">PAYMENT STATUS: VERIFIED</strong><br>
                    <span style="font-size: 9px; color: #777;">This transaction has been successfully processed and verified by the finance module.</span>
                </td>
                <td width="35%" align="right">
                    <?php if (!$isServiceFee): ?>
                        <span style="font-size: 9px; color: #666;">Remaining Balance:</span><br>
                        <span class="bold" style="font-size:14px; color: #d9534f;">₹ <?= number_format($data['BALANCE_AMOUNT'],2) ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <div style="font-weight: bold; color: #555; margin-bottom: 5px;">* THIS IS A COMPUTER GENERATED RECEIPT. NO PHYSICAL SIGNATURE IS REQUIRED *</div>
        <strong>Note:</strong> Institutional fees once remitted are non-refundable and non-transferable.<br>
        <span style="font-size: 8px;">Generated via EduRemit™ FMS | Time: <?= date('d-M-Y H:i:s') ?></span>
    </div>
</div>

</body>
</html>
