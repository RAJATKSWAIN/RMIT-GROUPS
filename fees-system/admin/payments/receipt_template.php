<?php
/**
 * ======================================================
 * File Name    : receipt_template.php
 * Project      : EduRemit™ - Fees Management System
 * Description  : CLEAN ACADEMIC PAYMENT RECEIPT
 * ======================================================
 */

// Critical fix for variable scope when called from InvoiceService
global $conn;
if (!isset($conn)) { $conn = $this->conn; }

/**
 * 1. FETCH DYNAMIC INSTITUTE & COURSE DETAILS
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

// Fallbacks
$collegeName      = $inst['INST_NAME'] ?? "HOLY GROUP OF INSTITUTIONS";
$brandLogo        = $inst['LOGO_URL'] ?? "";
$campusAddress    = $inst['CAMPUS_ADDRESS'] ?? "";
$corporateAddress = $inst['CORPORATE_ADDRESS'] ?? "";
$brandColor       = "#333"; // Forced to professional dark gray for Academic feel
$courseCode       = strtoupper($inst['COURSE_CODE'] ?? 'N/A');

/**
 * 2. REMARKS PARSING LOGIC
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
    @page { margin: 20px; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
    
    /* Academic Frame */
    .container { border: 2px solid #333; padding: 20px; position: relative; background: #fff; }
    
    /* Header Typography */
    .college-title { font-size: 18px; color: #000; text-transform: uppercase; margin: 0; font-weight: bold; letter-spacing: 1px; }
    .address-box { font-size: 9px; color: #444; margin-top: 5px; line-height: 1.2; border-left: 1px solid #ccc; padding-left: 10px; }
    
    /* Receipt Label - Professional Box */
    .receipt-header-table { border-bottom: 2px double #333; padding-bottom: 10px; margin-bottom: 15px; }
    .receipt-label { border: 1px solid #000; color: #000; padding: 5px 15px; font-size: 12px; font-weight: bold; text-align: center; display: inline-block; }
    
    /* Section Dividers */
    .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #666; margin-bottom: 5px; border-bottom: 1px solid #eee; }
    
    .info-table { width: 100%; margin-top: 10px; margin-bottom: 20px; }
    .bold { font-weight: bold; color: #000; }
    
    /* Table Styling */
    .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .items-table th { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 8px 5px; font-size: 10px; text-transform: uppercase; background: #fdfdfd; }
    .items-table td { border-bottom: 1px solid #eee; padding: 10px 5px; font-size: 11px; }
    
    .total-row td { border-top: 1px solid #000; border-bottom: 2px solid #000; padding: 10px 5px; background: #fafafa; }
    
    /* Boxed Status */
    .status-box { border: 1px solid #ccc; padding: 10px; margin-top: 15px; background: #f9f9f9; }
    .amount-words { font-style: italic; text-transform: capitalize; margin: 10px 0; border-bottom: 1px dashed #ccc; padding-bottom: 5px; }
    
    .footer { margin-top: 30px; font-size: 9px; text-align: center; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    .signature-space { margin-top: 40px; text-align: right; font-weight: bold; }
</style>
</head>
<body>

<div class="container">
    <table class="receipt-header-table" width="100%">
        <tr>
            <td width="15%"><img src="<?= $brandLogo ?>" style="max-width:100px;"></td>
            <td width="55%">
                <h1 class="college-title"><?= htmlspecialchars($collegeName) ?></h1>
                <div class="address-box">
                    Campus: <?= htmlspecialchars($campusAddress) ?><br>
                    Adm. Office: <?= htmlspecialchars($corporateAddress) ?>
                </div>
            </td>
            <td width="30%" align="right" valign="top">
                <div class="receipt-label">FEES RECEIPT</div>
                <div style="margin-top:10px; font-size: 10px;">
                    <strong>No:</strong> <?= $data['RECEIPT_NO'] ?><br>
                    <strong>Date:</strong> <?= date('d-M-Y', strtotime($data['PAYMENT_DATE'])) ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="50%" valign="top">
                <div class="section-title">Student Information</div>
                <div class="bold" style="font-size:15px;"><?= strtoupper($data['FIRST_NAME'].' '.$data['LAST_NAME']) ?></div>
                <div>Regd No: <span class="bold"><?= $data['REGISTRATION_NO'] ?></span></div>
            </td>
            <td width="50%" align="right" valign="top">
                <div class="section-title">Academic Details</div>
                <div class="bold"><?= $inst['COURSE_NAME'] ?></div>
                <div style="font-size: 10px;">Course Code: <?= $courseCode ?> | Mode: <span class="bold"><?= $data['PAYMENT_MODE'] ?></span></div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th align="left" width="75%">Description of Particulars</th>
                <th align="right" width="25%">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($settledItems)): ?>
                <?php foreach ($settledItems as $item): ?>
                <tr>
                    <td>
                        <div class="bold"><?= htmlspecialchars($item['name']) ?></div>
                        <span style="font-size: 9px; color: #666;">Standard academic fee component settlement</span>
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
                        <span style="font-size: 9px; color: #666;">Consolidated fee payment received</span>
                    </td>
                    <td align="right" class="bold">
                        <?= number_format($data['PAID_AMOUNT'], 2) ?>
                    </td>
                </tr>
            <?php endif; ?>

            <tr class="total-row">
                <td align="right" class="bold">TOTAL AMOUNT PAID :</td>
                <td align="right" class="bold" style="font-size: 14px;">
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
                <td width="70%">
                    <strong>PAYMENT STATUS:</strong> VERIFIED<br>
                    <span style="font-size: 9px; color: #666;">The above payment has been successfully credited to the institution account.</span>
                </td>
                <td width="30%" align="right">
                    <?php if (!$isServiceFee): ?>
                        <span style="font-size: 9px;">Balance Due:</span><br>
                        <span class="bold" style="font-size:13px;">₹ <?= number_format($data['BALANCE_AMOUNT'],2) ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        * This is a computer-generated academic document and does not require a physical signature.<br>
        <strong>Note:</strong> Fees once paid are non-refundable and non-transferable under any circumstances.<br>
        <span style="font-size: 8px;">Generated on: <?= date('d-M-Y H:i:s') ?> by EduRemit™ FMS</span>
    </div>

    <div class="signature-space">
        Accounts Department / Principal
    </div>
</div>

</body>
</html>
