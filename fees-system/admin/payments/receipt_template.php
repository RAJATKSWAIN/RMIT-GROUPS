<?php
/**
 * Modern Academic Receipt Template (DOMPDF Compatible)
 */
$courseCode = strtoupper($data['COURSE_CODE'] ?? '');
$corporateAddress = "Baidyanath Nagar, Near Sales Tax Square, Berhampur-10 (Gm.), Odisha - 760010";
$collegeName = "HOLY GROUP OF INSTITUTIONS";
$brandLogo = "https://via.placeholder.com/150x50?text=HOLY+GROUP"; 
$campusAddress = "Main Campus, Academic Row, City Center";

// Mapping Logic (HIT/RMIT/RMITC)
if (in_array($courseCode, ['DME','DEE','DEC','DCSE'])) {
    $collegeName = "HOLY INSTITUTE OF TECHNOLOGY";
    $brandLogo = "https://rmitgroupsorg.infinityfree.me/hit/images/footerlogo.png"; 
    $campusAddress = "Govindapur, Konisi, Berhampur (Gm.), Odisha - 761008";
} elseif (in_array($courseCode, ['BCA','BES'])) {
    $collegeName = "RAJIV MEMORIAL INSTITUTE OF TECHNOLOGY";
    $brandLogo = "https://rmitgroupsorg.infinityfree.me/rmit/images/homelogo.png"; 
    $campusAddress = "Govindapur, Konisi, Berhampur (Gm.), Odisha - 761008";
} elseif (in_array($courseCode, ['FIT','WLE','EMC','ELT'])) {
    $collegeName = "RAJIV MEMORIAL INDUSTRIAL TRAINING CENTER";
    $brandLogo = "https://rmitgroupsorg.infinityfree.me/rmitc/images/footerlogo.png"; 
    $campusAddress = "Skill Campus: Annex Building B, Sector 5, Salt Lake City, PIN-700091";
}

/**
 * NEW LOGIC: Parsing Distributed Fee Items
 * Format: [Settled: Fee Name (₹Amount), Fee Name (₹Amount)] User Remark
 */
$rawRemarks = $data['REMARKS'] ?? '';
$settledItems = [];
$userNote = $rawRemarks;

// 1. Extract the "Settled:" portion
// We use a more flexible regex that handles potential encoding issues with the Rupee symbol
if (preg_match('/Settled:\s*(.*?)(\]|$)/u', $rawRemarks, $matches)) {
    $itemsString = $matches[1];
    
    // The text after the last item might contain the user's manual remarks
    // We'll clean that up later if needed.

    // 2. Split by comma to get each numbered fee item
    $parts = explode(',', $itemsString);
    foreach ($parts as $part) {
        /**
 		* REFINED LOGIC: Parsing Distributed Fee Items
 		* This regex now ignores the currency symbol (whether it's ₹, ?, or Rs.)
 		*/
		if (preg_match('/Settled: (.*?)(\]|$)/', $rawRemarks, $matches)) {
    		$itemsString = $matches[1];
    		$parts = explode(',', $itemsString);
    
    		foreach ($parts as $part) {
        	// Updated Regex: \D* matches any non-digit (like ?, ₹, or Rs.) 
        	// then captures the decimal number
        	if (preg_match('/\{(.*?)\}\s*\(\D*\s*([\d\.]+)\)/u', trim($part), $itemMap)) {
            	$settledItems[] = [
                	'name' => strtoupper($itemMap[1]),
                	'amount' => $itemMap[2]
            	];
        	  }
    		}
		}
    }
}

$currentLevel = $data['APPLICABLE_LEVEL'] ?? 'COURSE';
$isServiceFee = in_array($currentLevel, ['COURSE','SEMESTER','YEAR','ONETIME','GLOBAL']);

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

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<style>
    @page { margin: 15x; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #333; line-height: 1.2; }
    .rupee-sign {font-family: 'DejaVu Sans';}
    .container { border: 1px solid #ccc; padding: 12px; position: relative; height: 98%; }
    .header { border-bottom: 2px solid #1a3a5a; padding-bottom: 5px; margin-bottom: 10px; }
    .college-title { font-size: 15px; color: #1a3a5a; text-transform: uppercase; margin: 0;  line-height: 1.1}
    .address-box { font-size: 8.5px; color: #555; margin-top: 2px; }
    .receipt-label { background: #1a3a5a; color: #fff; padding: 3px 6px; font-size: 11px; font-weight: bold; text-align: center; }
    .info-table, .items-table { width: 100%; border-collapse: collapse; margin-top: 10px;  }
    .info-table { width: 100%; margin-top: 10px; border-bottom: 1px dashed #eee; padding-bottom: 8px; }
    .info-label { font-size: 8px; color: #777; text-transform: uppercase; }
    .bold { font-weight: bold; }
    .items-table th { background: #f4f4f4; color: #333; padding: 6px; font-size: 9px; border-bottom: 2px solid #1a3a5a; text-transform: uppercase; }
    .items-table td { border-bottom: 1px solid #eee; padding: 6px 8px; font-size: 9px; vertical-align: top; }
    .balance-box { padding: 8px; margin-top: 10px; font-size: 10.5px; border: 1px solid #ddd; border-radius: 4px; }
    .ledger-box { background: #fffdf5; border-left: 4px solid #f39c12; }
    .service-box { background: #f0f7ff; border-left: 4px solid #1a3a5a; }
    .footer { margin-top: 15px; border-top: 1px solid #ccc; padding-top: 8px; font-size: 9px; text-align: center; color: #666; }
    .fee-badge { background: #eee; padding: 2px 5px; border-radius: 3px; font-size: 9px; margin-bottom: 3px; display: inline-block; }
</style>
    
</head>
    
<body>
    
<div class="container">
    <table width="100%">
        <tr>
            <td width="20%"><img src="<?= $brandLogo ?>" style="width:110px;"></td>
            <td width="50%">
                <h1 class="college-title"><?= $collegeName ?></h1>
                <div class="address-box">
                    <strong>Campus:</strong> <?= $campusAddress ?><br>
                    <strong>Corporate:</strong> <?= $corporateAddress ?>
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
                <div class="info-label">Student Details</div>
                <div class="bold" style="font-size:13px;"><?= strtoupper($data['FIRST_NAME'].' '.$data['LAST_NAME']) ?></div>
                <div class="bold text-muted">REG NO: <?= $data['REGISTRATION_NO'] ?></div>
            </td>
            <td width="45%" align="right">
                <div class="info-label">Academic Info</div>
                <div class="bold"><?= $data['COURSE_NAME'] ?> (<?= $courseCode ?>)</div>
                <div class="bold" style="color:#1a3a5a;">Mode: <?= $data['PAYMENT_MODE'] ?> <?= $data['TXN_REF'] ? "({$data['TXN_REF']})" : '' ?></div>
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
                    <div class="bold" style="color: #1a3a5a;"><?= $item['name'] ?></div>
                    <div style="font-size: 8px; color: #888; text-transform: uppercase;">
                        Payment received for <?= $item['name'] ?>
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
            <td align="right" style="border-top: 2px solid #1a3a5a; padding-top: 12px;">
                <span class="bold" style="font-size: 12px;">GRAND TOTAL PAID:</span>
            </td>
            <td align="right" style="border-top: 2px solid #1a3a5a; padding-top: 12px;">
                <span class="bold" style="font-size: 14px; color: #1a3a5a;">
                    &#8377; <?= number_format($data['PAID_AMOUNT'], 2) ?>
                </span>
            </td>
        </tr>
    </tbody>
</table>

<div style="margin-top: 10px; padding: 5px; border-bottom: 1px solid #eee;">
    <span style="font-size: 10px; font-style: italic;">
        <strong>Rupees in words:</strong> 
        <?php echo strtoupper(getIndianCurrencyInWords($data['PAID_AMOUNT'])); ?> ONLY
    </span>
</div>

    <?php if ($isServiceFee): ?>
        <div class="balance-box service-box">
            <span class="bold" style="color:#1a3a5a;">PAYMENT STATUS:</span><br>
            <span>Your payment has been successfully adjusted against the selected fee headers. No further dues for these items this session.</span>
        </div>
    <?php else: ?>
        <div class="balance-box ledger-box">
            <table width="100%">
                <tr>
                    <td>
                        <span class="bold">ACCOUNT STATUS:</span><br>
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
        * This is a computer-generated academic receipt. No physical signature required.<br>
        <strong>Note:</strong> Fees once paid are non-refundable and non-transferable.<br>
        Document Generated on <?= date('d-M-Y H:i:s') ?>
    </div>
</div>
        
</body>
</html>