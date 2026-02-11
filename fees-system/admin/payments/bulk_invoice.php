<?php
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/fees-system');
require_once BASE_PATH.'/config/db.php';
require_once BASE_PATH.'/core/auth.php';

checkLogin();

// Filter logic: Filter by Course or search by Name/Reg No
$course_filter = $_GET['course'] ?? '';
$search = $_GET['search'] ?? '';

$where = "WHERE l.BALANCE_AMOUNT > 0";
$params = [];
$types = "";

if ($course_filter) {
    $where .= " AND s.COURSE_ID = ?";
    $params[] = $course_filter;
    $types .= "i";
}
if ($search) {
    $where .= " AND (s.FIRST_NAME LIKE ? OR s.REGISTRATION_NO LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

$sql = "SELECT s.*, c.COURSE_NAME, c.COURSE_CODE, l.BALANCE_AMOUNT, l.LAST_PAYMENT_DATE
        FROM STUDENTS s
        JOIN COURSES c ON s.COURSE_ID = c.COURSE_ID
        JOIN STUDENT_FEE_LEDGER l ON s.STUDENT_ID = l.STUDENT_ID
        $where
        ORDER BY c.COURSE_NAME ASC, s.FIRST_NAME ASC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$students = $stmt->get_result();

// Fetch courses for the filter dropdown
$courses = $conn->query("SELECT * FROM COURSES");
?>

<?php include BASE_PATH.'/admin/layout/header.php'; ?>
<?php include BASE_PATH.'/admin/layout/sidebar.php'; ?>

<style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .invoice-box {
            background: #fff;
            padding: 40px;
            margin-bottom: 30px;
            border: 1px solid #eee;
            position: relative;
            page-break-after: always; /* Each invoice on a new page when printing */
        }
        .invoice-header { border-bottom: 2px solid #004a99; padding-bottom: 15px; margin-bottom: 20px; }
        .due-label { color: #dc3545; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .invoice-box { border: none; margin: 0; padding: 0; }
        }
    </style>

<div class="container no-print mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="fw-bold"><i class="bi bi-file-earmark-ruled"></i> Generate Fee Invoices</h4>
            <form class="row g-3 mt-2">
                <div class="col-md-4">
                    <select name="course" class="form-select">
                        <option value="">All Courses</option>
                        <?php while($c = $courses->fetch_assoc()): ?>
                            <option value="<?= $c['COURSE_ID'] ?>" <?= $course_filter == $c['COURSE_ID'] ? 'selected' : '' ?>>
                                <?= $c['COURSE_NAME'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search Name or Reg No..." value="<?= $search ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
                    <button type="button" onclick="window.print()" class="btn btn-dark"><i class="bi bi-printer"></i> Print All Invoices</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <?php if ($students->num_rows == 0): ?>
        <div class="alert alert-warning no-print">No students found with pending dues for this selection.</div>
    <?php endif; ?>

    <?php while($row = $students->fetch_assoc()):
    
 		// 1. Sanitize Data
$payee_vpa = "7605943733@ybl"; 
// Remove any special characters from college name for the QR code
$clean_college = preg_replace('/[^A-Za-z0-9 ]/', '', $college); 
// Ensure amount is a clean number like 1250.00
$clean_amount = number_format((float)$row['BALANCE_AMOUNT'], 2, '.', '');
$txn_ref = "Fee" . $row['REGISTRATION_NO'];

// 2. Build UPI String
$upi_data = "upi://pay?pa=$payee_vpa&pn=" . urlencode($clean_college) . "&am=$clean_amount&tn=" . urlencode($txn_ref) . "&cu=INR";

// 3. Use QuickChart API (Modern, fast, and reliable)
$qr_final_url = "https://quickchart.io/qr?text=" . urlencode($upi_data) . "&size=200&margin=1";

    
        // Dynamic Branding Logic
        $college = "HOLY GROUP OF INSTITUTIONS";
        $logo = "https://via.placeholder.com/150x50?text=HOLY+GROUP";
        if (in_array($row['COURSE_CODE'], ['DME', 'DEE', 'DEC', 'DCSE'])) {
            $college = "HOLY INSTITUTE OF TECHNOLOGY";
            $logo = "https://rmitgroupsorg.infinityfree.me/hit/images/footerlogo.png";
        } elseif (in_array($row['COURSE_CODE'], ['BCA', 'BES'])) {
            $college = "RAJIV MEMORIAL INSTITUTE OF TECHNOLOGY";
            $logo = "https://rmitgroupsorg.infinityfree.me/rmit/images/homelogo.png";
        }
    ?>
    
    
    <div class="invoice-box shadow-sm">
        <div class="invoice-header d-flex justify-content-between align-items-center">
            <div>
                <img src="<?= $logo ?>" style="height: 50px;" alt="Logo" class="mb-2">
                <h5 class="fw-bold mb-0"><?= $college ?></h5>
                <small class="text-muted">Govindapur, Konisi, Berhampur, Odisha</small>
            </div>
           
            <!-- QR Code for Payment -->
            <div class="text-center p-3 border rounded bg-white shadow-sm" style="width: 120px; margin: 0 auto;">
    			<img src="<?= $qr_final_url ?>" 
         			alt="UPI QR Code" 
         				style="width: 100% ; height: auto; display: block;"
         				onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($upi_data) ?>'">
    
    			<div class="mt-2 text-uppercase fw-bold" style="font-size: 10px; color: #333;">
                    Scan & Pay: ₹<?= $clean_amount ?>
    			</div>
    			<div class="text-muted" style="font-size: 8px;">Payee: <?= $payee_vpa ?></div>
			</div>
            <!-- QR Code for Payment -->
            
            <div class="text-end">
                <h3 class="due-label">Fee Invoice</h3>
                <p class="mb-0">Date: <b><?= date('d-M-Y') ?></b></p>
                <p class="mb-0 text-muted small">Invoice Ref: INV-<?= $row['STUDENT_ID'] ?>-<?= date('my') ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <label class="text-muted small text-uppercase fw-bold">Bill To:</label>
                <h5 class="fw-bold mb-1"><?= strtoupper($row['FIRST_NAME'] . ' ' . $row['LAST_NAME']) ?></h5>
                <p class="mb-0">Reg No: <b><?= $row['REGISTRATION_NO'] ?></b></p>
                <p class="mb-0 text-muted">Course: <?= $row['COURSE_NAME'] ?></p>
            </div>
            <div class="col-6 text-end">
                <label class="text-muted small text-uppercase fw-bold">Account Status:</label>
                <p class="mb-0">Last Payment: <?= $row['LAST_PAYMENT_DATE'] ? date('d-M-Y', strtotime($row['LAST_PAYMENT_DATE'])) : 'No record' ?></p>
                <p class="mb-0 fw-bold text-danger">Status: PENDING DUES</p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Description of Institutional Fees</th>
                    <th class="text-end" style="width: 200px;">Amount Due</th>
                </tr>
            </thead>
            <tbody>
                <tr style="height: 120px;">
                    <td>
                        <p class="fw-bold mb-1">Current Outstanding Academic Fees</p>
                        <small class="text-muted">This includes tuition fees, library dues, and other institutional charges applicable for the current academic session.</small>
                    </td>
                    <td class="text-end fw-bold fs-5 pt-3">₹<?= number_format($row['BALANCE_AMOUNT'], 2) ?></td>
                </tr>
                <tr class="table-danger">
                    <td class="text-end fw-bold">TOTAL PAYABLE AMOUNT</td>
                    <td class="text-end fw-bold fs-4">₹<?= number_format($row['BALANCE_AMOUNT'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="row mt-4">
            <div class="col-8">
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold small mb-2"><i class="bi bi-info-circle"></i> Payment Instructions:</h6>
                    <ul class="mb-0 small" style="font-size: 11px;">
                        <li>Please clear your dues by the 10th of this month to avoid late fine.</li>
                        <li>Payments can be made via UPI (Scan at Office) or Bank Transfer.</li>
                        <li>Always mention your Registration Number (<b><?= $row['REGISTRATION_NO'] ?></b>) in payment remarks.</li>
                    </ul>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="mt-4" style="border-top: 1px solid #333; padding-top: 5px;">
                    <p class="mb-0 fw-bold small">Accounts Officer</p>
                    <small class="text-muted" style="font-size: 10px;">Computer Generated Bill</small>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    
    <?php include BASE_PATH.'/admin/layout/footer.php'; ?>
</div>